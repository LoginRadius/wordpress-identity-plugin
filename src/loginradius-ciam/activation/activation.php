<?php



// Exit if called directly

if (!defined('ABSPATH')) {

    exit();

}



if (!class_exists('CIAM_Activation')) {

    class CIAM_Activation {
         /*
         * this function will call first when the object is initiallised....
         */

        function __construct() {
            global $ciam_credentials, $ciam_setting, $apiClientClass;
            // getting all the setting in global variable....
            $ciam_credentials = get_option('ciam_api_settings');
            $ciam_setting = get_option('ciam_authentication_settings');            
            $apiClientClass = 'LoginRadiusSDK\Clients\WPHttpClient';  
          
            $api_key = isset($ciam_credentials['apikey']) ? $ciam_credentials['apikey'] : '';
            $api_secret = isset($ciam_credentials['secret']) ? $ciam_credentials['secret'] : ''; 
            $api_request_signing = (isset($ciam_setting['apirequestsigning']) && $ciam_setting['apirequestsigning'] != '') ? $ciam_setting['apirequestsigning'] : '';
            
            if($api_key != ''){
                define('LR_API_KEY', $api_key);
            }
            if($api_secret != ''){
               $decrypted_secret_key = $this->encrypt_and_decrypt( $api_secret, $api_key, $api_key, 'd' );     
               define('LR_API_SECRET', $decrypted_secret_key);
            }
            if($api_request_signing == 1){            
                define('API_REQUEST_SIGNING', true);
            }
            $this->install();
            add_action('activate_plugin', array($this, 'version_detection'), 10, 2);
            add_action('init', array($this, 'init'));
        }

        /**
        * Encrypt and decrypt
        *
        * @param string $string string to be encrypted/decrypted
        * @param string $action what to do with this? e for encrypt, d for decrypt
        */
     
        public static function encrypt_and_decrypt( $string, $secretKey, $secretIv, $action) {
            // you may change these values to your own
            $secret_key = $secretKey;
            $secret_iv = $secretIv;
            $output = false;
            $encrypt_method = "AES-256-CBC";
            $key = hash( 'sha256', $secret_key );
            $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
            if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
            }
            else if( $action == 'd' ){           
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
            }
            return $output;
            }

         /**
         * Function for setting default options while plgin is activating.
         */
        public static function install() {
            global $wpdb;
            require_once ( dirname(__FILE__) . '/install.php' );
            if ( function_exists( 'is_multisite' ) && is_multisite() ) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    Activation_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                Activation_Install::set_default_options();
            }
        }


        /*
         * This function will called all the dependencies as constructor will called....
         */

        public function init() {
            /* adding action for ciam debug */

            add_action("ciam_debug", array($this, "debug_mode"), 10, 4);
            add_filter("plugin_action_links_" . CIAM_SETTING_LINK, array($this, 'settings_link'));
            add_action('admin_menu', array($this, 'menu'));
            $this->load_dependencies();

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        function settings_link($links) {
            $settings_link = '<a href="admin.php?page=ciam-activation">' . __('Settings', 'ciam') . '</a>';
            array_unshift($links, $settings_link);

            /* action for debug mode */

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
            return $links;
        }

        /* Creating Plugin Admin Menu */

        public function menu() {
            add_menu_page('CIAM', 'CIAM', 'manage_options', 'ciam-activation', array('CIAM_Activation_Admin', 'options_page'), CIAM_PLUGIN_URL . 'activation/assets/images/favicon.png');
            add_submenu_page('ciam-activation', 'Activation Settings', 'Activation', 'manage_options', 'ciam-activation', array('CIAM_Activation_Admin', 'options_page'));

            // Customize Menu based on do_action order
            do_action('ciam_admin_menu');
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }
        

        function version_detection($plugin, $network_wide) {
            if (is_plugin_active('advanced-user-registration-and-management/advanced-user-registration-and-management.php')) {
                wp_die('<p style="color:red;">Please Disable Plugin "Advanced User Registration and Management" to Active <b>LoginRadius CIAM</b> Plugin.</p>');
            }
        }


        /**
         * Loads PHP files that required by the plug-in
         *
         * @global CIAM_activationsettings, loginRadiusObject
         */

        private function load_dependencies() {

            require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Clients/IHttpClientInterface.php';
            require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Clients/DefaultHttpClient.php';
            require_once CIAM_PLUGIN_DIR . 'authentication/lib/WPHttpClient.php';
            require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/LoginRadiusException.php';
            require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Utility/Functions.php';            
            require_once CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Advanced/ConfigurationAPI.php';

            // Activation settings class.
            require_once( CIAM_PLUGIN_DIR . 'activation/admin/class-activation.php' );
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * this function will create log file on the function will get called.
        */
        public function debug_mode($function_name, $args, $class_name, $output = "") {

            global $ciam_credentials, $ciam_setting;

            if (!isset($ciam_credentials['apikey']) || empty($ciam_credentials['apikey']) || !isset($ciam_credentials['secret']) || empty($ciam_credentials['secret'])) {
                return;
            }

            if (defined('WP_DEBUG') && true === WP_DEBUG) {
                /* removing the credencials value from the arg array. */
            
                if(is_array($args) && !empty($args)) {

                  for ($i = 0; $i <= count($args); $i++) {

                    if (isset($args[$i]) && is_array($args[$i])) {

                        if (in_array($ciam_credentials['sitename'], $args[$i])) {
                            $pos = array_search($ciam_credentials['sitename'], $args[$i]);
                            unset($args[$i][$pos]);
                        }

                        if (in_array($ciam_credentials['apikey'], $args[$i])) {
                            $pos = array_search($ciam_credentials['apikey'], $args[$i]);
                            unset($args[$i][$pos]);
                        }

                        if (in_array($ciam_credentials['secret'], $args[$i])) {
                            $pos = array_search($ciam_credentials['secret'], $args[$i]);
                            unset($args[$i][$pos]);
                        }
                    }
                }
            }
                /* removing the credencials value from the return output. */

                if (isset($output) && !empty($output)) {
                    if (is_array($output)) {
                        for ($i = 0; $i <= count($output); $i++) {
                            if (isset($output[$i]) && is_array($output[$i])) {

                                if (in_array($ciam_credentials['sitename'], $output[$i])) {
                                    $pos = array_search($ciam_credentials['sitename'], $output[$i]);
                                    unset($output[$i][$pos]);
                                }

                                if (in_array($ciam_credentials['apikey'], $output[$i])) {
                                    $pos = array_search($ciam_credentials['apikey'], $output[$i]);
                                    unset($output[$i][$pos]);
                                }


                                if (in_array($ciam_credentials['secret'], $output[$i])) {
                                    $pos = array_search($ciam_credentials['secret'], $output[$i]);
                                    unset($output[$i][$pos]);
                                }

                            } else {

                                if (in_array($ciam_credentials['sitename'], $output)) {
                                    $pos = array_search($ciam_credentials['sitename'], $output);
                                    unset($output[$pos]);
                                }

                                if (in_array($ciam_credentials['apikey'], $output)) {
                                    $pos = array_search($ciam_credentials['apikey'], $output);
                                    unset($output[$pos]);
                                }

                                if (in_array($ciam_credentials['secret'], $output)) {
                                    $pos = array_search($ciam_credentials['secret'], $output);
                                    unset($output[$pos]);
                                }
                            }
                        }
                    } elseif (($output === $ciam_credentials['sitename']) || ($output === $ciam_credentials['apikey']) || ($output === $ciam_credentials['secret'])) {
                        unset($output);
                    }

                } else {
                    $output = "";
                }

                $log_message = '[' . date("F j, Y, g:i a e O") . ']' . "Class Name :" . "\r\n" . $class_name . "\r\n" . "Function Name :" . "\r\n" . $function_name . "\r\n" . "Function Args :" . "\r\n" . json_encode($args) . "\r\n" . "Function Output :" . "\r\n" . json_encode($output) . "\r\n";
                error_log($log_message, 3, CIAM_PLUGIN_DIR . 'ciam_debug.log');

              
            }
        }
    }

    new CIAM_Activation();

}



