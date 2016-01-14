<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Social_Profile_Data' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Social_Profile_Data {

        /**
         * Constructor
         */
        public function __construct() {
            if( ! class_exists( 'LR_Social_Login' ) ) {
                return;
            }
            // Register Activation hook callback.
            $this->install();

            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 5 );
        }

        function create_loginradius_menu() {
            add_submenu_page( 'LoginRadius', 'Social Profile Data Settings', 'Social Profile Data', 'manage_options', 'loginradius_social_profile_data', array( 'LR_Social_Profile_Data_Admin', 'options_page') );
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
                    LR_Social_Profile_Data_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                LR_Social_Profile_Data_Install::set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_SOCIAL_PROFILE_DATA_DIR', plugin_dir_path(__FILE__) );
            define( 'LR_SOCIAL_PROFILE_DATA_URL', plugin_dir_url(__FILE__) );
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginradius_commenting_settings
         */
        private function load_dependencies() {
            global $lr_social_profile_data_settings, $social_profile_display;

            // Get LoginRadius commenting settings
            $lr_social_profile_data_settings = get_option( 'LoginRadius_Social_Profile_Data_settings' );

            require_once( LR_SOCIAL_PROFILE_DATA_DIR . "admin/class-lr-social-profile-data-admin.php" );
            require_once( LR_SOCIAL_PROFILE_DATA_DIR . "includes/helpers/class-lr-social-profile-data-function.php" );
            require_once( LR_SOCIAL_PROFILE_DATA_DIR . "includes/display/class-lr-display-social-profile-data.php" );
            $social_profile_display = new LR_Display_Social_Profile_Data();
        }

    }

    new LR_Social_Profile_Data();
}
