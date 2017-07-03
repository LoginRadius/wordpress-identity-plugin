<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius Ciam.
 */
if (!class_exists('CIAM_Social_Login')) {

    class CIAM_Social_Login {


        /**
         * Load necessary scripts and CSS.
         * 
         * @global type $wpdb
         */
        public static function init() {
           
            
            global $loginradius_api_settings,$ciam_credencials;
          
            if(!isset($ciam_credencials['apikey']) && empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) && empty($ciam_credencials['secret'])){
                 return;   
             }
            $loginradius_api_settings = get_option('LoginRadius_API_settings');
            add_action('parse_request', array(get_class(), 'connect'));
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
          
        }


        /**
         * Redirect the page to ciam shortcode page on directly opening the default wordpress page.
         *
         */
        public static function custom_page_redirection() {  
            global $pagenow, $ciam_setting;
          
            
            $login_page_id = !empty($ciam_setting['login_page_id']) ? $ciam_setting['login_page_id'] : '';
            $register_page_id = !empty($ciam_setting['registration_page_id']) ? $ciam_setting['registration_page_id'] : '';
            $lost_pass_page_id = !empty($ciam_setting['lost_password_page_id']) ? $ciam_setting['lost_password_page_id'] : '';

            if ('wp-login.php' == $pagenow && !is_user_logged_in()) {
                $url = get_permalink($login_page_id);
                
                if (isset($_GET['action']) && 'register' == $_GET['action']) {
                    $url = get_permalink($register_page_id);
                } elseif (isset($_GET['action']) && 'lostpassword' == $_GET['action']) {
                    $url = get_permalink($lost_pass_page_id);
                }

                if ($url) {
                    wp_redirect($url);
                    exit();
                } else {
                    error_log('USER REGISTRATION NOT CONFIGURED CORRECTLY: Login, Registration or Lost Password page(s) are not set');
                }
            }
            
             /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

        /**
         * Check for the query string variables and authenticate user.
         */
        public static function connect() { 
            // check if permission is provided
            if (isset($_POST['newpassword']) && !empty($_POST['newpassword'])) {
                self::change_password($_POST);
            } else if (isset($_POST['password']) && !empty($_POST['password'])) {
                self::set_password($_POST);
            }
             /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

    }
    
    new CIAM_Social_Login();

}