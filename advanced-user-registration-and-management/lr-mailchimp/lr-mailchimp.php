<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Mailchimp' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Mailchimp {

        /**
         * Constructor
         */
        public function __construct() {
            if( ! class_exists( 'LR_Social_Login' ) ){
                return;
            }
            // Register Activation hook callback.
            $this->install();

            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 8 );
        }

        function create_loginradius_menu() {
            add_submenu_page( 'LoginRadius', 'Mailchimp Settings', 'MailChimp', 'manage_options', 'loginradius_mailchimp', array( 'LR_Mailchimp_Admin', 'options_page' ) );
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
                    LR_Mailchimp_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                LR_Mailchimp_Install:: set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_MAILCHIMP_DIR', plugin_dir_path(__FILE__) );
            define( 'LR_MAILCHIMP_URL', plugin_dir_url(__FILE__) );
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global lr_mailchimp_settings
         */
        private function load_dependencies() {
            global $lr_mailchimp_settings;

            // Get MailChimp settings
            $lr_mailchimp_settings = get_option( 'LR_Mailchimp_Settings' );

            // Mailchimp API
            if ( ! class_exists( 'MCAPI' ) ) {
    		  require_once( 'includes/helpers/MCAPI.class.php' );
    	    }

            require_once( LR_MAILCHIMP_DIR . 'includes/helpers/ajax.php' );
            new LR_Mailchimp_Ajax_Helper();

            // Load required files.
            require_once( 'admin/class-loginradius-mailchimp-admin.php' );
            require_once( 'includes/display/mailchimp.php' );
        }

    }

    new LR_Mailchimp();
}
