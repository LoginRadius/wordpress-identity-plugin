<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

use LoginRadiusSDK\Utility\SOTT;

/**
 * The front function class of LoginRadius Ciam.
 */
if (!class_exists('CIAM_Hooks')) {

    class CIAM_Hooks {

        public function __construct() {
            add_action('init', array($this, 'init'));
        }

        public function init() {

            global $ciam_credencials, $ciam_setting;

            if (!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])) {
                return;
            }
            add_action('wp_enqueue_scripts', array($this, 'form_scripts'));


            if (!empty($ciam_setting)) {
                add_filter('login_url', array($this, 'custom_login_page'), 100);
                add_filter('register_url', array($this, 'custom_registration_page'), 100);
                add_filter('lostpassword_url', array($this, 'custom_forgot_page'), 100);
                
                
                
            }
            add_action('wp_head', array($this, 'ciam_hook_commonoptions'));
            add_action('admin_head', array($this, 'ciam_hook_commonoptions'));


            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        /*
         * Adding Javascript/Jquery for admin settings page
         */

        public function form_scripts() {

            wp_enqueue_script('ciam', '//auth.lrcontent.com/v2/js/LoginRadiusV2.js', array('jquery'), CIAM_PLUGIN_VERSION, false);

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        /*
         * Ciam Hook for Common Option ....
         */

        public function ciam_hook_commonoptions() {
            global $ciam_credencials, $ciam_setting;

            if (isset($ciam_setting) && !empty($ciam_setting['login_page_id'])) {
                $verificationurl = get_permalink($ciam_setting['login_page_id']);

                $forgoturl = get_permalink($ciam_setting['change_password_page_id']);

                if ((!isset($ciam_credencials['apikey']) && empty($ciam_credencials['apikey'])) || (!isset($ciam_credencials['secret']) && empty($ciam_credencials['secret']))) {

                    return;
                }
                new LoginRadiusSDK\Utility\Functions($ciam_credencials['apikey'], $ciam_credencials['secret']);
                $sott = new SOTT();
                $encrypt = $sott->encrypt(10, true);
                ?>
                <script>

                    var commonOptions = {};
                    commonOptions.apiKey = "<?php echo $ciam_credencials['apikey'] ?>";
                    commonOptions.appName = "<?php echo $ciam_credencials['sitename'] ?>";
                    commonOptions.formValidationMessage = true;
                    commonOptions.hashTemplate = true;
                    commonOptions.loginOnEmailVerification = true;
                    commonOptions.forgotPasswordUrl = '<?php echo $forgoturl ?>';
                    commonOptions.resetPasswordUrl = '<?php echo $forgoturl ?>';
                    commonOptions.debugMode = false;
                    commonOptions.sott = '<?php echo urlencode($encrypt) ?>';
                    commonOptions.verificationUrl = '<?php echo $verificationurl; ?>';
                    var LRObject = new LoginRadiusV2(commonOptions);


                </script>

                <?php
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        /*
         * Hook for forgot password seciton
         */






        /*
         * social login end....
         */
        
        
        
        
        /*
         * change authentication link for the login page....
         */

        public function custom_login_page() {
            global $ciam_setting ;
            $login_page = $this->get_redirect_to_params(get_permalink($ciam_setting['login_page_id']));
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $login_page);
            return $login_page;
        }

        public function get_redirect_to_params($redirectParam) { 
            global $ciam_setting;
            
            if (isset($_GET['redirect_to']) && !empty(isset($_GET['redirect_to']))) {
                if (strpos($redirectParam, "?") > 0) {
                    $redirectParam .= '&';
                } else {
                    $redirectParam .= '?';
                }
                $redirectParam .= 'redirect_to=' . urlencode($_GET['redirect_to']);
            } elseif (is_single() || is_page()) { 
            
            //condition to check the url host with the site host....
                
            $urlhost = parse_url(get_permalink());
              if($urlhost['host'] == $_SERVER['HTTP_HOST']){ 
                if (get_permalink() && !in_array(get_permalink(), array(get_permalink($ciam_setting['login_page_id']), get_permalink($ciam_setting['registration_page_id']),get_permalink($ciam_setting['change_password_page_id']),get_permalink($ciam_setting['lost_password_page_id'])))) {
                    if (strpos($redirectParam, "?") > 0) { 
                        $redirectParam .= '&';
                    } else { 
                        $redirectParam .= '?';
                    }
                    $redirectParam .= 'redirect_to=' . urlencode(get_permalink());
                    
                }
            }
            }
            return $redirectParam;
        }

        /*
         * change authentication link for the forgotpassword page....
         */

        public function custom_register_page() {
            global $ciam_setting;
            $forgot_page = get_permalink($ciam_setting['lost_password_page_id']);
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $forgot_page);
            return $forgot_page;
        }

        /*
         * change authentication link for the forgotpassword page....
         */

        public function custom_forgot_page() {
            global $ciam_setting;
            $forgot_page = get_permalink($ciam_setting['lost_password_page_id']);

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $forgot_page);
            return $forgot_page;
        }

        /*
         * change authentication link for registration page....
         */

        public function custom_registration_page() {
            global $ciam_setting;
            $register_page = get_permalink($ciam_setting['registration_page_id']);
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $register_page);
            return $register_page;
        }

    }

    new CIAM_Hooks();
}