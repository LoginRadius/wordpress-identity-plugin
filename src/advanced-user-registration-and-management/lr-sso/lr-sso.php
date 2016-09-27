<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_SSO')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_SSO {

        /**
         * Constructor
         */
        public function __construct() {
            global $loginradius_api_settings;
            if(!isset($loginradius_api_settings['raas_enable']) || $loginradius_api_settings['raas_enable'] != 1) {
                return;
            }
            // Register Activation hook callback.
            $this->install();

            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 10 );
        }

        function create_loginradius_menu() {
            add_submenu_page( 'LoginRadius', 'SSO Settings', 'Single sign-on', 'manage_options', 'loginradius_sso', array('LR_SSO_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install() {
            global $wpdb;
            require_once( dirname(__FILE__) . '/install.php' );
            if (function_exists('is_multisite') && is_multisite()) {
                if (!isset($_GET['page']) || !in_array($_GET['page'], array('LoginRadius', 'SocialLogin', 'User_Registration', 'loginradius_sso', 'loginradius_share', 'loginradius_commenting', 'loginradius_social_profile_data', 'loginradius_social_invite', 'loginradius_customization', 'loginradius_mailchimp', 'lr_google_analitics'))) {
                    return;
                }
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    LR_SSO_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                LR_SSO_Install::set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_SSO_DIR', plugin_dir_path(__FILE__) );
            define( 'LR_SSO_URL', plugin_dir_url(__FILE__) );
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public static function load_dependencies() {
            global $lr_sso_settings;

            // Get LoginRadius commenting settings
            $lr_sso_settings = get_option( 'LR_SSO_Settings' );

            // Load required files.
            require_once( LR_SSO_DIR . 'admin/class-lr-sso-admin.php' );
            require_once( LR_SSO_DIR . 'includes/front/class-lr-sso-front.php' );
        }

    }

    new LR_SSO();
}
