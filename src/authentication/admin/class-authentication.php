<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

use LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI;

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('CIAM_Authentication_Admin')) {

    class CIAM_Authentication_Admin {

        public function __construct() { 
            global $ciam_credencials, $ciam_setting;
            
            $ciam_credencials = get_option('Ciam_API_settings');

            $ciam_setting = get_option('Ciam_Authentication_settings');
            if (!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])) {
                return;
            }
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'), 5);

             
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function admin_init() {

            global $ciam_credencials, $message;
            $ciam_message = false;
            $user_id = get_current_user_id();
            $UserAPI = new UserAPI($ciam_credencials['apikey'], $ciam_credencials['secret']);
            add_user_meta($user_id, 'ciam_message', '$message');
            
            /* checking for the change password form is checked or not */
            
            $passform = isset($_POST['passform']) ? $_POST['passform'] : '';

            $oldpassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '';
            $newpassword = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
            $confirmpassword = isset($_POST['confirmnewpassword']) ? $_POST['confirmnewpassword'] : '';
            if (isset($passform) && ($passform == 1)) {
                if (!empty($oldpassword) && !empty($newpassword)) {
                      
                    $accessToken = get_user_meta($user_id);
                    try {
                        $UserAPI->changeAccountPassword($accessToken['accesstoken'][0], $_POST['oldpassword'], $_POST['newpassword']);
                        
                        // saving wordpress data to lr on profile updation....
                $metas = array(
                    'NickName' => $_POST['nickname'],
                    'FirstName' => $_POST['first_name'],
                    'LastName' => $_POST['last_name'],
                    'ImageUrl' => $_POST['url'],
                    
                );
                    
                   $UserAPI->updateProfile($accessToken['accesstoken'][0], json_encode($metas));
                        
                        
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) { 


                        $message = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : _e("Opps Something Went Wrong !");
                        add_user_meta($user_id, 'ciam_pass_error', $message);
                        $ciam_message_password = true;
                        
                        $_POST = array();
                        
                      
                    }
                }
            }elseif(isset($_POST['first_name']) && !empty($_POST['first_name'])){
                $accessToken = get_user_meta($user_id);
                try{
                   
                // saving wordpress data to lr on profile updation....
                $metas = array(
                    'NickName' => $_POST['nickname'],
                    'FirstName' => $_POST['first_name'],
                    'LastName' => $_POST['last_name'],
                    'ImageUrl' => $_POST['url'],
                    
                );
                
                   $UserAPI->updateProfile($accessToken['accesstoken'][0], json_encode($metas));
                 
                    
                }catch(\LoginRadiusSDK\LoginRadiusException $e){
                    
                    $message = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : _e("Opps Something Went Wrong !");
                        add_user_meta($user_id, 'ciam_pass_error', $message);
                        $ciam_message = true;
                    
                }
            }
            register_setting('ciam_authentication_settings', 'ciam_authentication_settings', array($this, 'validation'));

            if (isset($_GET['updated']) && $ciam_message == false) {
                if (!empty(get_user_meta($user_id, 'ciam_pass_error', true))) {
                    ob_start();
                    ?>
                    <div class="updated notice is-dismissible">
                        <p><strong><?php echo get_user_meta($user_id, 'ciam_pass_error', true); ?></strong></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>


                    <?php
                    delete_user_meta($user_id, 'ciam_pass_error');
                }
            }elseif(!empty(get_user_meta($user_id, 'ciam_pass_error', true))){
                
                
                add_action("ciam_password_message", array($this, "pass_message"), 10);
                
                do_action("ciam_password_message");
                
                
                    ?>
                    


                    <?php
                    
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        
        function pass_message(){ 
            $user_id = get_current_user_id();
            ob_start();
            ?>
            <div class="updated notice is-dismissible">
                        <p><strong><?php echo get_user_meta($user_id, 'ciam_pass_error', true); ?></strong></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>
            <?php    
           
            delete_user_meta($user_id, 'ciam_pass_error');
        }
        
        
        
        function validation($settings) {
            if (!isset($settings['enable_hostedpage']) || $settings['enable_hostedpage'] != '1') {
                if (isset($settings['ciam_autopage']) && $settings['ciam_autopage'] == '1') {
                    // Enable ciam.
                    // Create new pages and get array of page ids.
                    $options = self::create_pages($settings);

                    // Merge new page ids with settings array.
                    $settings = array_merge($settings, $options);
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $settings);

            return $settings;
        }

        /*
         * Adding Javascript/Jquery for admin settings page
         */

        public function load_scripts() {
            wp_enqueue_script('ciam_functions', CIAM_PLUGIN_URL . 'authentication/assets/js/custom.min.js', array('jquery'), CIAM_PLUGIN_VERSION);

            wp_enqueue_script('ciam', '//auth.lrcontent.com/v2/js/LoginRadiusV2.js', array('jquery'), CIAM_PLUGIN_VERSION, false);


            wp_enqueue_style('ciam-style', CIAM_PLUGIN_URL . 'authentication/assets/css/style.min.css', CIAM_PLUGIN_VERSION);

            wp_enqueue_style('ciam-style', CIAM_PLUGIN_URL . 'authentication/assets/css/style.css', CIAM_PLUGIN_VERSION);
            
            wp_enqueue_style('ciam-style-fancybox', CIAM_PLUGIN_URL . 'authentication/assets/css/jquery.fancybox.css', CIAM_PLUGIN_VERSION);
            
            wp_enqueue_script('ciam_fancybox', CIAM_PLUGIN_URL . 'authentication/assets/js/jquery.fancybox.pack.js', array('jquery'), CIAM_PLUGIN_VERSION);

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /**
         * create ciam custom pages
         * 
         * @param type $settings
         * @return type
         */
        public static function create_pages($settings) {

            // Create Login Page.
            if (!isset($settings['login_page_id']) || $settings['login_page_id'] == '') {
                $loginPage = array(
                    'post_title' => 'Login',
                    'post_content' => '[ciam_login_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $loginPageId = wp_insert_post($loginPage);
            } else {
                $loginPageId = $settings['login_page_id'];
            }

            // Create Registration Page.
            if (!isset($settings['registration_page_id']) || $settings['registration_page_id'] == '') {
                $registrationPage = array(
                    'post_title' => 'Registration',
                    'post_content' => '[ciam_registration_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $registrationPageId = wp_insert_post($registrationPage);
            } else {
                $registrationPageId = $settings['registration_page_id'];
            }

            // Create Reset Password Page.
            if (!isset($settings['change_password_page_id']) || $settings['change_password_page_id'] == '') {
                $changePasswordPage = array(
                    'post_title' => 'Reset Password',
                    'post_content' => '[ciam_password_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $changePasswordPageId = wp_insert_post($changePasswordPage);
            } else {
                $changePasswordPageId = $settings['change_password_page_id'];
            }

            // Create Forgot Password Page.
            if (!isset($settings['lost_password_page_id']) || $settings['lost_password_page_id'] == '') {
                $lostPasswordPage = array(
                    'post_title' => 'Forgot Password',
                    'post_content' => '[ciam_forgotten_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $lostPasswordPageId = wp_insert_post($lostPasswordPage);
            } else {
                $lostPasswordPageId = $settings['lost_password_page_id'];
            }

            

            $output = array(
                'login_page_id' => trim($loginPageId),
                'registration_page_id' => trim($registrationPageId),
                'change_password_page_id' => trim($changePasswordPageId),
                'lost_password_page_id' => trim($lostPasswordPageId)
            );


            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), $output);




            return array(
                'login_page_id' => trim($loginPageId),
                'registration_page_id' => trim($registrationPageId),
                'change_password_page_id' => trim($changePasswordPageId),
                'lost_password_page_id' => trim($lostPasswordPageId)
            );
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once CIAM_PLUGIN_DIR . "authentication/admin/views/settings.php";
            ciam_authentication_settings::render_options_page();

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

    }

    new CIAM_Authentication_Admin();
}
