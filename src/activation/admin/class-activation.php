<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */



if (!class_exists('CIAM_Activation_Admin')) {

    class CIAM_Activation_Admin {

        /*
         * Constructor for class CIAM_Social_Login_Admin
         */

        public function __construct() {
             
            // Registering hooks callback for admin section.
            $this->register_hook_callbacks();
            
             /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /*
         * Register admin hook callbacks
         */

        public function register_hook_callbacks() {
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'), 5);
            add_action('admin_enqueue_scripts', array($this, 'register_ciam_admin_style'));
             /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /**
         * Callback for admin_menu hook,
         * Register CIAM_settings and its sanitization callback. Add Login Radius meta box to pages and posts.
         */
        public function admin_init() {
            register_setting('Ciam_API_settings', 'Ciam_API_settings', array($this,'ciam_activation_validation'));
             /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }


        
        /**
         * Get response from LoginRadius api
         */
        public static function api_validation_response($apiKey, $apiSecret, $string) {
            global $currentErrorCode, $currentErrorResponse;

            require_once CIAM_PLUGIN_DIR.'authentication/lib/LoginRadiusSDK/Clients/IHttpClient.php';
            require_once CIAM_PLUGIN_DIR.'authentication/lib/LoginRadiusSDK/Clients/DefaultHttpClient.php';
            require_once CIAM_PLUGIN_DIR.'authentication/lib/LoginRadiusSDK/Utility/Functions.php';
               
            
            $options['method'] = 'post';
            $options['post_data'] = array('addon' => 'WordPress', 'version' => CIAM_PLUGIN_VERSION, 'agentstring' => $_SERVER['HTTP_USER_AGENT'], 'clientip' => $_SERVER['REMOTE_ADDR'], 'configuration' => $string);
            try { 
                $client = new \LoginRadiusSDK\Clients\DefaultHttpClient; 
                 $query_array = array('apikey' => $apiKey,'apisecret' => $apiSecret);
                $response = json_decode($client->request("https://api.loginradius.com/api/v2/app/validate", $query_array, $options));

                if (isset($response->Status) && $response->Status) { 

                    return true;
                } else {

                    $currentErrorCode = $response->Messages;
                    return false;
                }
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {

                $currentErrorCode = '0';
                $currentErrorResponse = "Something went wrong: " . $e->getErrorResponse()->description;
                return false;
            }
             /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), '');
        }
        
        function ciam_activation_validation($settings) { 
            
            $settings['sitename'] = sanitize_text_field($settings['sitename']);
            $settings['apikey'] = sanitize_text_field($settings['apikey']);
            $settings['secret'] = sanitize_text_field($settings['secret']);
            if (empty($settings['sitename'])) {
                $message = 'LoginRadius Site Name is blank. Get your LoginRadius Site Name from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('Ciam_API_settings', esc_attr('settings_updated'), $message, 'error');
            }

            if (empty($settings['apikey']) && empty($settings['secret'])) {
                $message = 'LoginRadius API Key and API Secret are blank. Get your LoginRadius API Key and API Secret from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('Ciam_API_settings', esc_attr('settings_updated'), $message, 'error');
                return $settings;
            }

            if (empty($settings['apikey'])) {
                $message = 'LoginRadius API Key is blank. Get your LoginRadius API Key from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('Ciam_API_settings', esc_attr('settings_updated'), $message, 'error');
                return $settings;
            }

            if (empty($settings['secret'])) {
                $message = 'LoginRadius API Secret is blank. Get your LoginRadius API Secret from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('Ciam_API_settings', esc_attr('settings_updated'), $message, 'error');
                return $settings;
            }
            
             if (isset($settings['apikey']) && isset($settings['secret'])) {

                $encodeString = 'settings';

                if (self::api_validation_response($settings['apikey'], $settings['secret'], $encodeString)) {
                    
                    return $settings;
                } else { 

                    // Api or Secret is not valid or something wrong happened while getting response from LoginRadius api
                    $message = 'Please recheck your LoginRadius details';
                    global $currentErrorCode, $currentErrorResponse;

                    $errorMessage = array(
                        "API_KEY_NOT_VALID" => 'LoginRadius API key is invalid. Get your LoginRadius API Key from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>',
                        'API_SECRET_NOT_VALID' => 'LoginRadius API Secret is invalid. Get your LoginRadius API Secret from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>',
                        'API_KEY_NOT_FORMATED' => 'LoginRadius API Key is not formatted correctly.',
                        'API_SECRET_NOT_FORMATED' => 'LoginRadius API Secret is not formatted correctly.',
                    );

                    if ($currentErrorCode[0] == '0') {
                        $message = $currentErrorResponse;
                    } else {
                        if (count($currentErrorCode) > 1) { 

                            add_settings_error('LR_Ciam_API_settings', esc_attr('settings_updated'), $errorMessage[$currentErrorCode[0]], 'error');
                            add_settings_error('LR_Ciam_API_settings', esc_attr('settings_updated'), $errorMessage[$currentErrorCode[1]], 'error');
                       
                        } else {
                            $message = $errorMessage[$currentErrorCode[0]];
                        }
                    }
                 
                    add_settings_error('LR_Ciam_API_settings', esc_attr('settings_updated'), $message, 'error');

                   
                }
            } else {

                add_settings_error('LR_Ciam_API_settings', esc_attr('settings_updated'), 'Settings Updated', 'updated');
                return $settings;
            }
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /*
         * Adding Javascript/Jquery for admin settings page
         */

        public function load_scripts() {
            global $ciam_js_in_footer;

            wp_enqueue_script('ciam_activation_options', CIAM_PLUGIN_URL . 'activation/assets/js/script.js', array('jquery'), CIAM_PLUGIN_VERSION, $ciam_js_in_footer);
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /* Load Admin Css */

        public function register_ciam_admin_style() {
            wp_register_style('ciam-admin-style', CIAM_PLUGIN_URL . 'activation/assets/css/style.min.css', array(), CIAM_PLUGIN_VERSION);
            wp_register_style('ciam-admin-style', CIAM_PLUGIN_URL . 'activation/assets/css/style.css', array(), CIAM_PLUGIN_VERSION);
            wp_enqueue_style('ciam-admin-style');
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once CIAM_PLUGIN_DIR . "activation/admin/views/settings.php";

            CIAM_Activation_Settings::render_options_page();
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), '');
        }

    }
    new CIAM_Activation_Admin();
}
