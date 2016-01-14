<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Google_Analytics' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Google_Analytics {

        /**
         * Constructor
         */
        public function __construct() {
            // Register Activation hook callback.
            $this->install();

            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 14 );
        }

        /**
         * Add Submenu in LoginRadius Menu
         */
        function create_loginradius_menu() {
            add_submenu_page( 'LoginRadius', 'Google Analytics', 'Google Analytics', 'manage_options', 'lr_google_analitics', array( 'LR_Google_Analytics_Admin', 'options_page' ) );
        }

        /**
         * Function for setting default options while plgin is activating.
         * 
         * @global type $wpdb
         * @return type
         */
        public static function install() {
            global $wpdb;
            require_once( dirname( __FILE__ ) . '/install.php' );
            if ( function_exists( 'is_multisite' ) && is_multisite() ) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    LR_Google_Analytics_Install::set_default_options();
                }
                switch_to_blog($old_blog);
                return;
            } else {
                LR_Google_Analytics_Install::set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_GOOGLE_ANALYTICS_DIR', plugin_dir_path(__FILE__) );
            define( 'LR_GOOGLE_ANALYTICS_URL', plugin_dir_url(__FILE__) );
        }

        /**
         * Loads PHP files that required by the plug-in
         * 
         * @global type $lr_google_analytics_settings
         */
        public static function load_dependencies() {
            global $lr_google_analytics_settings;

            // Get LoginRadius commenting settings
            $lr_google_analytics_settings = get_option( 'LR_Google_Analytics_Settings' );

            // Load required files.
            require_once( LR_GOOGLE_ANALYTICS_DIR . "admin/class-lr-google-analytics-admin.php" );
            require_once( LR_GOOGLE_ANALYTICS_DIR . "includes/front/class-lr-google-analytics-front.php" );
        }

    }

    new LR_Google_Analytics();
}
