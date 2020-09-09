<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

use LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI;
use LoginRadiusSDK\Utility\Functions;
/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('CIAM_Activation_Admin')) {

    class CIAM_Activation_Admin {
        /*
         * Constructor for class CIAM_Social_Login_Admin
         */

        public function __construct() {        

            add_action('init', array($this, 'init'), 101);
           
        }

        /*
         * Initialise when constructor get called....
         */

        public function init() {

            
            $this->register_hook_callbacks();
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * Register admin hook callbacks
         */

        public function register_hook_callbacks() {
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'), 5);
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /**
         * Callback for admin_menu hook,
         * Register CIAM_settings and its sanitization callback. Add Login Radius meta box to pages and posts.
         */
        public function admin_init() {
            register_setting('ciam_api_settings', 'ciam_api_settings', array($this, 'ciam_activation_validation'));
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }
        
        /**
         * Get response from LoginRadius api
         */
        public function api_validation_response($apiKey, $apiSecret) {
            global $currentErrorCode, $currentErrorResponse;

            $data = [];
            try {        
                $queryParam = [
                  'apikey' => $apiKey,
                  'apisecret' => $apiSecret,
                ];
                              
                $resourcePath = 'https://api.loginradius.com/api/v2/app/validate';
                                          
                $response = Functions::_apiClientHandler('GET', $resourcePath, $queryParam);       
                  
                if (isset($response->Status) && $response->Status) {              
                    /* action for debug mode */
                    //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');          
                    return true;
                } else {
                    /* action for debug mode */
                    //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');            

                  $errorMessage = array(
                    "API_KEY_NOT_VALID" => "LoginRadius API key is invalid. Get your LoginRadius API key from LoginRadius account",
                    "API_SECRET_NOT_VALID" => "LoginRadius API Secret is invalid. Get your LoginRadius API Secret from LoginRadius account",
                    "API_KEY_NOT_FORMATED" => "LoginRadius API Key is not formatted correctly",
                    "API_SECRET_NOT_FORMATED" => "LoginRadius API Secret is not formatted correctly",
                );
            
                foreach ($response->Messages as $value) {
                    $data['message'] = $errorMessage["$value"];
                    $data['status'] = 'error';
                    break;
                  }                  
                  $currentErrorResponse = $data['message'];
                  return false;
                }                
              }
              catch (LoginRadiusException $e) {
                $currentErrorCode = '0';
                $currentErrorResponse = "Something went wrong1: " . $e->getErrorResponse()->description;
                return false;
              }  
        }
        

        /*
         * This function will validate the activation settings.
         */

        function ciam_activation_validation($settings) {
               
            $settings['apikey'] = sanitize_text_field($settings['apikey']);
            $settings['secret'] = sanitize_text_field($settings['secret']);     
        

            if (empty($settings['apikey']) && empty($settings['secret'])) {
                $message = 'LoginRadius API Key and API Secret are blank. Get your LoginRadius API Key and API Secret from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('ciam_api_settings', esc_attr('settings_updated'), $message, 'error');

                /* action for debug mode */
               // do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
                return $settings;
            }

            if (empty($settings['apikey'])) {                
                $message = 'LoginRadius API Key is blank. Get your LoginRadius API Key from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('ciam_api_settings', esc_attr('settings_updated'), $message, 'error');

                /* action for debug mode */
                //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
                return $settings;
            }

            if (empty($settings['secret'])) {
                $message = 'LoginRadius API Secret is blank. Get your LoginRadius API Secret from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('ciam_api_settings', esc_attr('settings_updated'), $message, 'error');
                /* action for debug mode */
                //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
                return $settings;
            }
           

            if (isset($settings['apikey']) && isset($settings['secret'])) {

                $encodeString = 'settings';
                $config_api = get_option('ciam_api_settings') ? get_option('ciam_api_settings') : '';  
                
                    if((!empty($config_api) && isset($config_api['secret']) && $config_api['secret'] != '') && ($config_api['secret'] == $settings['secret'])){             
                        $secret_key = $this->encrypt_and_decrypt( $settings['secret'], $settings['apikey'], $settings['apikey'], 'd' );
                    }else{
                        $secret_key = $settings['secret'];
                    }

                if ($this->api_validation_response($settings['apikey'], $secret_key)) {          

                    $encrypted_key = $this->encrypt_and_decrypt( $secret_key, $settings['apikey'], $settings['apikey'], 'e' );                  
                    $decrypted_key = $this->encrypt_and_decrypt( $encrypted_key, $settings['apikey'], $settings['apikey'], 'd' );  
                                 
                    Functions::setDefaultApplication($settings['apikey'], $decrypted_key); 
                    $configObject = new ConfigurationAPI();
                    $config = $configObject->getConfigurations();          
                    $ciam_settings = get_option('ciam_authentication_settings');        
                
                    $config_options = array();
                    if(isset($config->AppName))
                    {
                        $settings['sitename'] = $config->AppName;           
                        $settings['secret'] = $encrypted_key;             
                    }

                    if(isset($config->IsUserNameLogin) && !isset($ciam_settings['login_type'])) {
                    $config_options['login_type'] =  $config->IsUserNameLogin;
                    }                                        
               
                    if(isset($config->AskEmailIdForUnverifiedUserLogin) && !isset($ciam_settings['askEmailForUnverifiedProfileAlways'])){
                    $config_options['askEmailForUnverifiedProfileAlways'] =  $config->AskEmailIdForUnverifiedUserLogin;
                    }
                    if(isset($config->AskRequiredFieldsOnTraditionalLogin) && !isset($ciam_settings['AskRequiredFieldsOnTraditionalLogin'])){
                    $config_options['AskRequiredFieldsOnTraditionalLogin'] =  $config->AskRequiredFieldsOnTraditionalLogin;
                    }
                    if(isset($config->AskPasswordOnSocialLogin) && !isset($ciam_settings['prompt_password'])){
                    $config_options['prompt_password'] =  $config->AskPasswordOnSocialLogin;
                    }
                    if(isset($config->CheckPhoneNoAvailabilityOnRegistration) && !isset($ciam_settings['existPhoneNumber'])){
                    $config_options['existPhoneNumber'] =  $config->CheckPhoneNoAvailabilityOnRegistration;
                    }
                    if(isset($config->IsInstantSignin->EmailLink) && !isset($ciam_settings['onclicksignin'])){
                    $config_options['onclicksignin'] =  $config->IsInstantSignin->EmailLink;
                    }
                    if(isset($config->IsInstantSignin->SmsOtp) && !isset($ciam_settings['instantotplogin'])){
                    $config_options['instantotplogin'] =  $config->IsInstantSignin->SmsOtp;
                    }
                    if(isset($ciam_settings['apirequestsigning'])){
                        $config_options['apirequestsigning'] =  $config->ApiRequestSigningConfig->IsEnabled;
                    }
                    if(isset($config->ApiRequestSigningConfig->IsEnabled) && !isset($ciam_settings['apirequestsigning'])){                       
                    $config_options['apirequestsigning'] =  $config->ApiRequestSigningConfig->IsEnabled;
                    }
                    if(get_option('ciam_authentication_settings')){
                    $config_options = array_merge(get_option('ciam_authentication_settings') , $config_options);
                     update_option('ciam_authentication_settings' , $config_options);
                    }
                    else{
                       add_option('ciam_authentication_settings' , $config_options);
                    }  
                    
                    /* action for debug mode */
                    //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');                    
                   
                    return $settings;
                } else {
                  
                    // Api or Secret is not valid or something wrong happened while getting response from LoginRadius api
                    $message = 'Please recheck your LoginRadius details';
                    global $currentErrorCode, $currentErrorResponse;
                  
                    add_settings_error('LR_Ciam_API_settings', esc_attr('settings_updated'), $currentErrorResponse, 'error');
                
                }
            } else {

                add_settings_error('LR_Ciam_API_settings', esc_attr('settings_updated'), 'Settings Updated', 'updated');
                /* action for debug mode */
               // do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');

                return $settings;
            }

            /* action for debug mode */
            //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /**
        * Encrypt and decrypt
        *
        * @param string $string string to be encrypted/decrypted
        * @param string $action what to do with this? e for encrypt, d for decrypt
        */
     
        public function encrypt_and_decrypt( $string, $secretKey, $secretIv, $action) {
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

        /*
         * Adding Javascript/Jquery for admin settings page
         */

        public function load_scripts() {
            global $ciam_js_in_footer, $ciam_setting;

            wp_enqueue_script('ciam_activation_options', CIAM_PLUGIN_URL . 'activation/assets/js/script.js', array('jquery'), CIAM_PLUGIN_VERSION, $ciam_js_in_footer);
            wp_register_style('ciam-admin-style', CIAM_PLUGIN_URL . 'activation/assets/css/style.min.css', array(), CIAM_PLUGIN_VERSION);
            wp_enqueue_style('ciam-admin-style');
            
            /* action for debug mode */
            //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once CIAM_PLUGIN_DIR . "activation/admin/views/settings.php";
            $obj_CIAM_Activation_Settings = new CIAM_Activation_Settings;
            $obj_CIAM_Activation_Settings->render_options_page();

            /* action for debug mode */
            //do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), '');
        }

    }

    new CIAM_Activation_Admin();
}
