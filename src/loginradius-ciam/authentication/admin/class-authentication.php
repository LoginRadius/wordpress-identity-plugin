<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

use LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI;

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('CIAM_Authentication_Admin')) {

    class CIAM_Authentication_Admin {
        /*
         * Class constructor function
         */

        public function __construct() {
            add_action('admin_init', array($this, 'admin_init'));
            add_action('init', array($this, 'init'));
        }

        /**
         * Init Section To call Wordpress Hooks
         */
        function init() {
            add_action('delete_user', array($this, 'delete_user'));
            
            if (is_multisite()) {
                add_action('wpmu_new_user', array($this, 'save_profile'));
                add_action('wpmu_delete_user', array($this, 'delete_user'));
            }
            
            /* Save Profile field on cIAM */
            add_action('personal_options_update', array($this, 'save_profile'));
            add_action('edit_user_profile_update', array($this, 'save_profile'));
            /* Save Profile field on cIAM */
            /* Save cIAM profile data on wp db */
            add_action('user_register', array($this, 'user_register'), 10, 1);
            /* Save cIAM profile data on wp db */
            if (current_user_can('manage_options')) {
                add_filter('user_profile_update_errors', array($this, 'add_profile'), 10, 3);
            }
        }

        /**
         * 
         * @global type $ciamUserProfile
         * @param type $user_id
         */
        public function user_register($user_id) {
            global $ciamUserProfile;
            if (isset($ciamUserProfile->Uid)) {
                add_user_meta($user_id, 'ciam_uid', sanitize_text_field($ciamUserProfile->Uid));
            }
            if (isset($ciamUserProfile->ID)) {
                add_user_meta($user_id, 'ciam_id', sanitize_text_field($ciamUserProfile->ID));
            }
        }

        /**
         * 
         * @global type $ciam_credentials
         * @global type $ciamUserProfile
         * @global \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI $accoutObj
         * @param type $errors
         * @param type $update
         * @param type $user
         * @return type
         */
        public function add_profile($errors, $update, $user) {
            global $ciam_credentials, $ciamUserProfile, $accoutObj;
            if(isset($ciam_setting['apirequestsigning']) && $ciam_setting['apirequestsigning'] != '' && $ciam_setting['apirequestsigning'] == 1)
            {
            $accoutObj = new \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI(['api_request_signing'=>'true']);
            }
            else
            {
                $accoutObj = new \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI();
           
            }
            $params = array(
                'UserName' => isset($user->user_login) ? $user->user_login : '',
                'FirstName' => isset($user->first_name) ? $user->first_name : '',
                'LastName' => isset($user->last_name) ? $user->last_name : '',
                'EmailVerified' => true
            );
            if (isset($user->user_pass)) {
                $params['Password'] = isset($user->user_pass) ? $user->user_pass : '';
            }
            if (false == $update) {
                $params['Email'] = array(array('Type' => 'Primary', 'Value' => $user->user_email));
                if (!isset($params['Password'])) {
                    $params['Password'] = wp_generate_password();
                }
                try {
                    $ciamUserProfile = $accoutObj->createAccount($params);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $ciamUserProfile = '';
                    $errors->add('user_creation_error', $e->getErrorResponse()->description);
                    return;
                }
            } else {
                $this->save_profile($user->ID);
            }
        }

        /**
         * 
         * @global type $pagenow
         * @global type $ciam_credentials
         * @global type $ciamUserProfile
         * @global \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI $accoutObj
         * @param type $user_id
         * @return boolean
         */
        public function save_profile($user_id) {
            global $pagenow, $ciam_credentials, $ciamUserProfile, $accoutObj;
            if (!current_user_can('edit_user', $user_id)) {
                return false;
            }
            if(isset($ciam_setting['apirequestsigning']) && $ciam_setting['apirequestsigning'] != '' && $ciam_setting['apirequestsigning'] == 1)
            {
            $accoutObj = new \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI(['api_request_signing'=>'true']);
            }
            else{
                $accoutObj = new \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI();           
            }
            $user = get_userdata($user_id);
            $params = array(
                'FirstName' => isset($_POST['first_name']) && !empty($_POST['first_name']) ? $_POST['first_name'] : $user->first_name,
                'LastName' => isset($_POST['last_name']) && !empty($_POST['last_name']) ? $_POST['last_name'] : $user->last_name,
            );
            $accountId = get_user_meta($user_id, 'ciam_uid', true);
            if (!empty($accountId)) {
                if (isset($_POST['pass1']) && !empty($_POST['pass1'])) {
                    $password = $_POST['pass1'];
                    try {
                        $accoutObj->setAccountPasswordByUid($password, $accountId);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $ciamUserProfile = '';
                        wp_redirect($pagenow . '?error=1');
                        exit();
                    }
                }
                try {
                    $ciamUserProfile = $accoutObj->updateAccountByUid($params, $accountId);
                    $this->user_register($user_id);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $ciamUserProfile = '';
                    wp_redirect($pagenow . '?error=1');
                    exit();
                }
            } else {
                $ciamUserProfile = $accoutObj->getAccountProfileByEmail($user->user_email);
                if (isset($ciamUserProfile->Uid)) {//update profile
                    $this->user_register($user_id);
                } else {//create profile on lr
                    $params = array(
                        'UserName' => isset($user->user_login) ? $user->user_login : '',
                        'FirstName' => isset($user->first_name) ? $user->first_name : '',
                        'LastName' => isset($user->last_name) ? $user->last_name : '',
                        'Password' => isset($_POST['pass1']) && !empty($_POST['pass1']) ? $_POST['pass1'] : wp_generate_password(),
                        'EmailVerified' => true,
                        'Email' => array(array('Type' => 'Primary', 'Value' => $user->user_email))
                    );
                    try {
                        $ciamUserProfile = $accoutObj->createAccount($params);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $ciamUserProfile = '';
                        return;
                    }
                }
            }
        }

        /**
         * delete user at cIAM
         * 
         * @param type $user_id
         */
        public function delete_user($user_id) {
            global $ciam_credentials, $accoutObj;
            $accoutObj = new \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI();

            $uid = get_user_meta($user_id, 'ciam_uid', true);
            if (!empty($uid)) {
                try {
                    $accoutObj->deleteAccountByUid($uid);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    error_log($e->getErrorResponse()->Description);
                }
            }
        }

        /*
         * This function will get initiallised when wordpress admin init initialised....
         */

        public function admin_init() {
            global $ciam_credentials, $message;
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'), 5);
            $ciam_message = false;
            $user_id = get_current_user_id();
            $authAPI = new AuthenticationAPI();

            $accessToken = get_user_meta($user_id, 'accesstoken', true);

            /* checking for the change password form is checked or not */

            $passform = isset($_POST['passform']) ? $_POST['passform'] : '';
            if (($passform == 1)) {
                $oldpassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '';
                $newpassword = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
                $confirmpassword = isset($_POST['confirmnewpassword']) ? $_POST['confirmnewpassword'] : '';
                if (!empty($oldpassword) && !empty($newpassword) && ($newpassword === $confirmpassword)) {
                    try {
                        $authAPI->changePassword($accessToken, $_POST['newpassword'], $_POST['oldpassword']);
                        // saving wordpress data to lr on profile updation....
                        $metas = array(
                            'NickName' => isset($_POST['nickname']) ? $_POST['nickname'] : '',
                            'FirstName' => isset($_POST['first_name']) ? $_POST['first_name'] : '',
                            'LastName' => isset($_POST['last_name']) ? $_POST['last_name'] : '',
                            'ImageUrl' => isset($_POST['url']) ? $_POST['url'] : '',
                        );
                        $authAPI->updateProfileByAccessToken($accessToken, json_encode($metas));
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $message = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : _e("Opps Something Went Wrong !");
                        add_user_meta($user_id, 'ciam_pass_error', sanitize_text_field($message));
                        $ciam_message_password = true;
                        $_POST = array();
                    }
                } else {
                    add_user_meta($user_id, 'ciam_pass_error', sanitize_text_field('Please make sure that your new and confirm password are same.'));
                    $ciam_message_password = true;

                    $_POST = array();
                }
            } elseif (isset($_POST['first_name']) && !empty($_POST['first_name'])) {
                try {
                    // saving wordpress data to lr on profile updation....
                    $metas = array(
                        'NickName' => isset($_POST['nickname']) ? $_POST['nickname'] : '',
                        'FirstName' => isset($_POST['first_name']) ? $_POST['first_name'] : '',
                        'LastName' => isset($_POST['last_name']) ? $_POST['last_name'] : '',
                        'ImageUrl' => isset($_POST['url']) ? $_POST['url'] : '',
                    );
                    $authAPI->updateProfileByAccessToken($accessToken, $metas);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    $message = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : _e("Opps Something Went Wrong !");
                    add_user_meta($user_id, 'ciam_pass_error', sanitize_text_field($message));
                    $ciam_message = true;
                    $_POST = array();
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
            } elseif (!empty(get_user_meta($user_id, 'ciam_pass_error', true))) {
                ob_start();
                ?>
                <div class="updated notice is-dismissible">
                    <p><strong><?php echo get_user_meta($user_id, 'ciam_pass_error', true); ?></strong></p>
                    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                </div>
                <?php
                delete_user_meta($user_id, 'ciam_pass_error');
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * This function validate the post data.
         */

        function validation($settings) {
            if(get_option('ciam_authentication_settings')){
            $settings = array_merge(get_option('ciam_authentication_settings'), $settings);
            }
            if (!isset($settings['enable_hostedpage']) || $settings['enable_hostedpage'] != '1') {
                if (isset($settings['ciam_autopage']) && $settings['ciam_autopage'] == '1') {
                    // Enable ciam.
                    // Create new pages and get array of page ids.
                    $options = $this->create_pages($settings);                 
                    // Merge new page ids with settings array.

                    $settings = array_merge($settings, $options);                 
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $settings);

            return $settings;
        }

        /*
         * Adding Javascript/Jquery for admin settings page
         */

        public function load_scripts() {

              wp_enqueue_script('ciam', '//auth.lrcontent.com/v2/js/LoginRadiusV2.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
 
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /**
         * create ciam custom pages
         * 
         * @param type $settings
         * @return type
         */
        public function create_pages($settings) {

            $user_id = get_current_user_id();           
            // Create Login Page.
            if (!isset($settings['login_page_id']) || $settings['login_page_id'] == '') {
                
                $loginPage = array (
                    'post_title' => 'Login',
                    'post_content' => '[ciam_login_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => $user_id,
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
                    'post_author' => $user_id,
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
                    'post_author' => $user_id,
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
                    'post_content' => '[ciam_forgot_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => $user_id,
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
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $output);
            return $output;
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once CIAM_PLUGIN_DIR . "authentication/admin/views/settings.php";

            $args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'hierarchical' => 1,
                'exclude' => '',
                'include' => '',
                'meta_key' => '',
                'meta_value' => '',
                'authors' => '',
                'child_of' => 0,
                'parent' => -1,
                'exclude_tree' => '',
                'number' => '',
                'offset' => 0,
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $obj_ciam_authentication_settings = new ciam_authentication_settings();
            $obj_ciam_authentication_settings->render_options_page($args);

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

    }

    new CIAM_Authentication_Admin();
}
