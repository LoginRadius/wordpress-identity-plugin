<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Salesforce')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Salesforce {

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
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'),12);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Salesforce Settings', 'Salesforce', 'manage_options', 'loginradius_salesforce', array('LR_Salesforce_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install() {
            global $wpdb;
            require_once ( dirname(__FILE__) . '/install.php' );
            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    LR_Salesforce_Install::set_default_options();
                }
                switch_to_blog($old_blog);
                return;
            } else {
                LR_Salesforce_Install::set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define('LR_SALESFORCE_PLUGIN_DIR', plugin_dir_path(__FILE__));
            define('LR_SALESFORCE_PLUGIN_URL', plugin_dir_url(__FILE__));
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginradius_commenting_settings
         */
        private function load_dependencies() {
            // Load LoginRadius files.
            require_once( 'admin/loginradius_salesforce_admin_options.php' );
            require_once( 'includes/salesforce-functions.php' );
            require_once( 'lib/salesforce.php' );
        }

    }

    new LR_Salesforce();
}

