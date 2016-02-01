<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


if ( ! class_exists( 'LR_Social_Login' ) ) {

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

            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof LR_Social_Login ) ) {
                self::$instance = new LR_Social_Login();
            }
            return self::$instance;
        }

        /**
         * Construct and start plug-in's other functionalities
         */
        public function __construct() {

            if ( ! $this->is_requirements_met() ) {
                //Return if requirements are not met.
                return;
            }

            //Declare constants and load dependencies
            $this->define_constants();
            $this->load_dependencies();
            // Register Activation hook callback.
            $this->install();
            
            add_action( 'lr_admin_page', array('LR_Social_Login', 'create_loginradius_menu'),2 );
        }

        public static function create_loginradius_menu() {
                add_submenu_page( 'LoginRadius', 'Social Login Settings', 'Social Login', 'manage_options', 'SocialLogin', array( 'LR_Social_Login_Admin', 'options_page'));
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

            if ( ! version_compare( $wp_version, self:: $wp_min_version, '>=' ) ) {
                add_action( 'admin_notices', array( $this, 'notify_admin' ) );
                return false;
            }
            return true;
        }

        /**
         * Display admin notice if requirements are not made
         */
        public static function notify_admin() {
            echo '<div id="message" class="error"><p><strong>';
            echo __( 'Sorry, This LoginRadius plugin requires WordPress ' . self::$wp_min_version . ' or higher. Please upgrade your WordPress setup', 'lr-plugin-slug' );
            echo '</strong></p></div>';
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LOGINRADIUS_PLUGIN_DIR', plugin_dir_path(__FILE__) );
            define( 'LOGINRADIUS_PLUGIN_URL', plugin_dir_url(__FILE__) );
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginRadiusSettings, loginRadiusObject
         */
        private function load_dependencies() {
            global $loginRadiusSettings, $loginradius_api_settings, $loginRadiusLoginIsBpActive;

            //Load required files.
            require_once( LOGINRADIUS_PLUGIN_DIR.'common/class-loginradius-common.php' );
            require_once( LOGINRADIUS_PLUGIN_DIR.'common/loginradius-ajax.php' );
            require_once( LOGINRADIUS_PLUGIN_DIR.'widgets/lr-social-login-widget.php' );
            require_once( LOGINRADIUS_PLUGIN_DIR.'widgets/lr-social-linking-widget.php' );
            require_once( LOGINRADIUS_PLUGIN_DIR.'public/inc/login/class-login-helper.php' );

            // Get LoginRadius plugin options.
            $loginRadiusSettings = get_option( 'LoginRadius_settings' );

            $loginRadiusLoginIsBpActive = false;

            add_action('bp_include', array( 'Login_Helper', 'set_budddy_press_status_variable' ) );

            // Admin Panel

                // load admin functionality
                require_once( LOGINRADIUS_PLUGIN_DIR.'admin/class-loginradius-admin.php' );

                // Load public functionality
                require_once( LOGINRADIUS_PLUGIN_DIR.'public/class-loginradius-front.php' );

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
                    LR_Social_Login_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                LR_Social_Login_Install::set_default_options();
            }
        }

    }
    new LR_Social_Login();
}
