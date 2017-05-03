<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Raas')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Raas {

        /**
         * Constructor
         */
        public function __construct() {
            global $loginradius_api_settings;
            if (!class_exists('LR_Social_Login') || !isset($loginradius_api_settings['raas_enable']) || $loginradius_api_settings['raas_enable'] != '1') {
                return;
            }
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);
            // load dependencies.
            $this->load_dependencies();

            add_action('wp_enqueue_scripts', array($this, 'enqueue_front_scripts'));

            //remove sociallogin
            remove_action('lr_admin_page', array('LR_Social_Login', 'create_loginradius_menu'), 2);
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 2);

            //remove sociallogin widgets
            add_action('widgets_init', function() {
                unregister_widget('LR_Social_Linking_Widget');
            });
            add_action('widgets_init', function() {
                unregister_widget('LR_Social_Login_Widget');
            });
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'User Registration Settings', 'User Registration', 'manage_options', 'User_Registration', array('LR_Raas_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plugin is activating.
         */
        public static function install($blog_id) {
            require_once ( dirname(__FILE__) . '/install.php' );
            LR_Raas_Install::set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                global $wpdb;
                delete_option('LR_Raas_Settings');
                delete_option('LoginRadius_settings');                
                $wpdb->query('DROP TABLE IF EXISTS `' . $wpdb->base_prefix . 'lr_custom_fields_data`');
            } else {
                delete_option('LR_Raas_Settings');
                delete_option('LoginRadius_settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                do_action('lr_raas_reset_setting');
                echo '<p style="display:none;" class="lr-alert-box lr-notif">' . __('User Registration settings have been reset and default values loaded', 'lr-plugin-slug') . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        public static function enqueue_front_scripts() {
            global $lr_js_in_footer;
            wp_register_script('lr-raas', '//cdn.loginradius.com/hub/prod/js/LoginRadiusRaaS.js', array('jquery', 'lr-social-login'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_register_script('lr-raas-front-script', LR_ROOT_URL . 'lr-raas/assets/js/loginradiusfront.js', array('jquery-ui-datepicker'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_register_style('lr-raas-style', LR_ROOT_URL . 'lr-raas/assets/css/lr-raas-style.css', array(), LR_PLUGIN_VERSION);
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        private function load_dependencies() {
            global $lr_raas_settings, $LR_Raas_Social_Login, $loginradius_api_settings, $accountAPIObject, $userAPIObject;

            // Get LoginRadius commenting settings
            $lr_raas_settings = get_option('LR_Raas_Settings');
            $loginradius_api_settings = get_option('LoginRadius_API_settings');
            require_once( LR_ROOT_DIR . "lr-raas/public/inc/class-lr-raas-social-login.php" );
            $LR_Raas_Social_Login = new LR_Raas_Social_Login;
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? trim($loginradius_api_settings['LoginRadius_apikey']) : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? trim($loginradius_api_settings['LoginRadius_secret']) : '';
            try {
                $accountAPIObject = new \LoginRadiusSDK\CustomerRegistration\AccountAPI($apikey, $secret, array('authentication' => true, 'output_format' => 'json'));
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            try {
                $userAPIObject = new \LoginRadiusSDK\CustomerRegistration\UserAPI($apikey, $secret, array('authentication' => true, 'output_format' => 'json'));
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                
            }

            // Init ShortCodes
            require_once( LR_ROOT_DIR . "lr-raas/includes/front/class-lr-raas-wp-default-login.php" );
            require_once( LR_ROOT_DIR . "lr-raas/includes/front/class-lr-raas-function.php" );

            // Load required files.
            require_once( LR_ROOT_DIR . "lr-raas/admin/class-lr-raas-admin.php" );
        }

    }

    new LR_Raas();
}

