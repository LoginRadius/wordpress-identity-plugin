<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('LR_Activation_Admin')) {

    class LR_Activation_Admin {

        /**
         * LR_Activation_Admin class instance
         *
         * @var string
         */
        private static $instance;

        /**
         * Get singleton object for class LR_Activation_Admin
         *
         * @return object LR_Activation_Admin
         */
        public static function get_instance() {
            if (!isset(self::$instance) && !( self::$instance instanceof LR_Activation_Admin )) {
                self::$instance = new LR_Activation_Admin();
            }
            return self::$instance;
        }

        /*
         * Constructor for class LR_Social_Login_Admin
         */

        public function __construct() {
            $this->global_variables();
            $this->js_in_footer();
            // Registering hooks callback for admin section.            
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'), 5);
        }

        // Create Api Options if not already created.
        public function global_variables() {
            global $loginradius_api_settings;
            $loginradius_api_settings = get_option('LoginRadius_API_settings');
        }

        public static function js_in_footer() {
            global $loginradius_api_settings, $lr_js_in_footer;

            // Set js in footer bool.
            $lr_js_in_footer = isset($loginradius_api_settings['scripts_in_footer']) && $loginradius_api_settings['scripts_in_footer'] == '1' ? true : false;
        }

        /**
         * Callback for admin_menu hook,
         * Register LoginRadius_settings and its sanitization callback. Add Login Radius meta box to pages and posts.
         */
        public function admin_init() {

            register_setting('loginradius_api_settings', 'LoginRadius_API_settings', array($this, 'validate_options'));

            // Replicate Social Login configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_settings_to_new_blog'));
            }
        }

        /*
         * Adding Javascript/Jquery for admin settings page
         */

        public function load_scripts($hook) {
            global $lr_js_in_footer;

            if ($hook != 'toplevel_page_LoginRadius') {
                return;
            }
            wp_enqueue_script('lr_activation_options', LR_ROOT_URL . 'lr-core/assets/js/lr-activation.js', array('jquery'), LR_PLUGIN_VERSION, $lr_js_in_footer);
        }

        /**
         * Get response from LoginRadius api
         */
        public static function api_validation_response($apiKey, $apiSecret, $string) {
            global $currentErrorCode, $currentErrorResponse;

            try {
                $object = new \LoginRadiusSDK\Clients\WPHttpClient($apiKey, $apiSecret, array('authentication' => false));
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {

                $currentErrorCode = '0';
                $currentErrorResponse = "Something went wrong: " . $e->getMessage();
                return false;
            }


            $options['method'] = 'post';
            $options['post_data'] = array('addon' => 'WordPress', 'version' => LR_PLUGIN_VERSION, 'agentstring' => $_SERVER['HTTP_USER_AGENT'], 'clientip' => $_SERVER['REMOTE_ADDR'], 'configuration' => $string);
            try {

                $response = json_decode($object->request("https://api.loginradius.com/api/v2/app/validate", array("apikey" => $apiKey, "apisecret" => $apiSecret), $options));

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
        }

        public static function validate_options($settings) {
            $settings['sitename'] = sanitize_text_field($settings['sitename']);
            $settings['LoginRadius_apikey'] = sanitize_text_field($settings['LoginRadius_apikey']);
            $settings['LoginRadius_secret'] = sanitize_text_field($settings['LoginRadius_secret']);
            if (empty($settings['sitename'])) {
                $message = 'LoginRadius Site Name is blank. Get your LoginRadius Site Name from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), $message, 'error');
            }

            if (empty($settings['LoginRadius_apikey']) && empty($settings['LoginRadius_secret'])) {
                $message = 'LoginRadius API Key and API Secret are blank. Get your LoginRadius API Key and API Secret from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), $message, 'error');
                return $settings;
            }

            if (empty($settings['LoginRadius_apikey'])) {
                $message = 'LoginRadius API Key is blank. Get your LoginRadius API Key from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), $message, 'error');
                return $settings;
            }

            if (empty($settings['LoginRadius_secret'])) {
                $message = 'LoginRadius API Secret is blank. Get your LoginRadius API Secret from <a href="http://www.loginradius.com" target="_blank">LoginRadius</a>';
                add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), $message, 'error');
                return $settings;
            }

            if (isset($settings['LoginRadius_apikey']) && isset($settings['LoginRadius_secret'])) {

                $encodeString = 'settings';

                if (self::api_validation_response($settings['LoginRadius_apikey'], $settings['LoginRadius_secret'], $encodeString)) {
                    if (!isset($settings['raas_enable']) || ($settings['raas_enable'] == '1')) {
                        $socialProfileSetting = get_option('LoginRadius_Social_Profile_Data_settings');
                        $socialProfileSetting['enable_custom_popup'] = '0';
                        update_option('LoginRadius_Social_Profile_Data_settings', $socialProfileSetting);
                    }
                    return $settings;
                } else {

                    // Api or Secret is not valid or something wrong happened while getting response from LoginRadius api
                    $message = 'please check your php.ini settings to enable CURL or FSOCKOPEN';
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

                            add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), $errorMessage[$currentErrorCode[0]], 'error');
                            add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), $errorMessage[$currentErrorCode[1]], 'error');
                            return $settings;
                        } else {
                            $message = $errorMessage[$currentErrorCode[0]];
                        }
                    }
                    add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), $message, 'error');

                    return $settings;
                }
            } else {

                add_settings_error('LoginRadius_API_settings', esc_attr('settings_updated'), 'Settings Updated', 'updated');
                return $settings;
            }
        }

        // Replicate the social login config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog($blogId) {
            global $loginradius_api_settings;
            add_blog_option($blogId, 'LoginRadius_API_settings', $loginradius_api_settings);
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            require_once LR_ROOT_DIR . "lr-core/admin/views/class-activation-settings-view.php";
            LR_Activation_Settings::render_options_page();
        }

    }

}

LR_Activation_Admin::get_instance();
