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
            $this->install();
            // Declare constants and load dependencies.
            $this->define_constants();
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
        public static function install() {
            global $wpdb;
            require_once ( dirname(__FILE__) . '/install.php' );
            if (function_exists('is_multisite') && is_multisite()) {
                if (!isset($_GET['page']) || !in_array($_GET['page'], array('LoginRadius', 'SocialLogin', 'User_Registration', 'loginradius_sso', 'loginradius_share', 'loginradius_commenting', 'loginradius_social_profile_data', 'loginradius_social_invite', 'loginradius_customization', 'loginradius_mailchimp', 'lr_google_analitics'))) {
                    return;
                }
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    LR_Raas_Install:: set_default_options();
                }
                switch_to_blog($old_blog);
                return;
            } else {
                LR_Raas_Install:: set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define('LR_RAAS_DIR', plugin_dir_path(__FILE__));
            define('LR_RAAS_URL', plugin_dir_url(__FILE__));
        }

        public static function enqueue_front_scripts() {
            global $lr_js_in_footer;
            wp_register_script('lr-raas', '//cdn.loginradius.com/hub/prod/js/LoginRadiusRaaS.js', array('jquery', 'lr-social-login'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_register_script('lr-raas-front-script', LR_RAAS_URL . 'assets/js/loginradiusfront.js', array('jquery-ui-datepicker'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_register_style('lr-raas-style', LR_RAAS_URL . 'assets/css/lr-raas-style.css', array(), LR_PLUGIN_VERSION);
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        private function load_dependencies() {
            global $lr_raas_settings, $LR_Raas_Social_Login, $loginradius_api_settings, $accountAPIObject, $userAPIObject;

            // Get LoginRadius commenting settings
            $lr_raas_settings = get_option('LR_Raas_Settings');
            $loginradius_api_settings = get_option('LoginRadius_API_settings');
            require_once( LR_RAAS_DIR . "public/inc/class-lr-raas-social-login.php" );
            $LR_Raas_Social_Login = new LR_Raas_Social_Login;
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? trim($loginradius_api_settings['LoginRadius_apikey']) : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? trim($loginradius_api_settings['LoginRadius_secret']) : '';
            try{
                $accountAPIObject = new \LoginRadiusSDK\CustomerRegistration\AccountAPI($apikey, $secret, array('authentication' => true, 'output_format' => 'json'));
            }  catch (\LoginRadiusSDK\LoginRadiusException $e){
                
            }
            try{
                $userAPIObject = new \LoginRadiusSDK\CustomerRegistration\UserAPI($apikey, $secret, array('authentication' => true, 'output_format' => 'json'));
            }  catch (\LoginRadiusSDK\LoginRadiusException $e){
                
            }            

            // Init ShortCodes
            require_once( LR_RAAS_DIR . "includes/front/class-lr-raas-wp-default-login.php" );
            require_once( LR_RAAS_DIR . "includes/front/class-lr-raas-function.php" );

            // Load required files.
            require_once( LR_RAAS_DIR . "admin/class-lr-raas-admin.php" );
        }

    }

    new LR_Raas();
}

