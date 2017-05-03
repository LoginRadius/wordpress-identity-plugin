<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('LR_Social_Login')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Social_Login {

        /**
         * LR_Social_Login class instance
         *
         * @var string
         */
        private static $instance;

        /**
         * Mininmum required version of WordPress for this plug-in to function correctly.
         *
         * @var string
         */
        public static $wp_min_version = "3.5";

        /**
         * Get singleton object for class LR_Social_Login
         *
         * @return object LR_Social_Login
         */
        public static function get_instance() {

            if (!isset(self::$instance) && !( self::$instance instanceof LR_Social_Login )) {
                self::$instance = new LR_Social_Login();
            }
            return self::$instance;
        }

        /**
         * Construct and start plug-in's other functionalities
         */
        public function __construct() {

            if (!$this->is_requirements_met()) {
                //Return if requirements are not met.
                return;
            }

            //load dependencies
            $this->load_dependencies();
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);
            add_action('lr_admin_page', array('LR_Social_Login', 'create_loginradius_menu'), 2);
        }

        public static function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Social Login Settings', 'Social Login', 'manage_options', 'SocialLogin', array('LR_Social_Login_Admin', 'options_page'));
        }

        /**
         * Checks that the WordPress setup meets the plugin requirements
         *
         * @global string $wp_version
         *
         * @return boolean
         */
        private function is_requirements_met() {
            global $wp_version;

            if (!version_compare($wp_version, self:: $wp_min_version, '>=')) {
                add_action('admin_notices', array($this, 'notify_admin'));
                return false;
            }
            return true;
        }

        /**
         * Display admin notice if requirements are not made
         */
        public static function notify_admin() {
            echo '<div id="message" class="error"><p><strong>';
            echo __('Sorry, This LoginRadius plugin requires WordPress ' . self::$wp_min_version . ' or higher. Please upgrade your WordPress setup', 'lr-plugin-slug');
            echo '</strong></p></div>';
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginRadiusSettings, loginRadiusObject
         */
        private function load_dependencies() {
            global $loginradius_api_settings, $loginRadiusSettings, $socialLoginObject, $loginRadiusLoginIsBpActive, $apiClient_class;
            $apiClient_class = '\LoginRadiusSDK\Clients\WPHttpClient';
            //Load required files.
            $loginRadiusSDKs = array('LoginRadius', 'LoginRadiusException',
                'Clients/IHttpClient', 'Clients/DefaultHttpClient',
                'SocialLogin/GetProvidersAPI', 'SocialLogin/SocialLoginAPI',
                'CustomerRegistration/AccountAPI', 'CustomerRegistration/CustomObjectAPI', 'CustomerRegistration/UserAPI'
            );
            foreach ($loginRadiusSDKs as $fileName) {
                require_once ( LR_ROOT_DIR . 'lr-social-login/lib/LoginRadiusSDK/' . $fileName . '.php' );
            }
            require_once ( LR_ROOT_DIR . 'lr-social-login/lib/WPHttpClient.php' );
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? $loginradius_api_settings['LoginRadius_apikey'] : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? $loginradius_api_settings['LoginRadius_secret'] : '';
            try {
                $socialLoginObject = new \LoginRadiusSDK\SocialLogin\SocialLoginAPI($apikey, $secret, array('authentication' => false, 'output_format' => 'json'));
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            if (!empty($apikey) && !empty($secret)) {

                //Load required files.
                require_once( LR_ROOT_DIR . 'lr-social-login/common/class-loginradius-common.php' );
                require_once( LR_ROOT_DIR . 'lr-social-login/common/loginradius-ajax.php' );
                require_once( LR_ROOT_DIR . 'lr-social-login/widgets/lr-social-login-widget.php' );
                require_once( LR_ROOT_DIR . 'lr-social-login/widgets/lr-social-linking-widget.php' );
                require_once( LR_ROOT_DIR . 'lr-social-login/public/inc/login/class-login-helper.php' );

                // Get LoginRadius plugin options.
                $loginRadiusSettings = get_option('LoginRadius_settings');

                $loginRadiusLoginIsBpActive = false;

                add_action('bp_include', array('Login_Helper', 'set_budddy_press_status_variable'));

                // Admin Panel
                // load admin functionality
                require_once( LR_ROOT_DIR . 'lr-social-login/admin/class-loginradius-admin.php' );

                // Load public functionality
                require_once( LR_ROOT_DIR . 'lr-social-login/public/class-loginradius-front.php' );
            }
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install($blog_id) {
            require_once ( dirname(__FILE__) . '/install.php' );
            LR_Social_Login_Install::set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                delete_blog_option($blog_id, 'LoginRadius_settings');
            } else {
                delete_option('LoginRadius_settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">' . __('Login settings have been reset and default values loaded', 'lr-plugin-slug') . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

    }

    new LR_Social_Login();
}
