<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Sailthru')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Sailthru {

        /**
         * Constructor
         */
        public function __construct() {
            // Register Activation hook callback.
            $this->install();

            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 12);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Sailthru', 'Sailthru', 'manage_options', 'lr-sailthru', array('LR_Sailthru_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install() {
            global $wpdb;
            require_once( dirname(__FILE__) . '/install.php' );
            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    LR_Sailthru_Install::set_default_options();
                }
                switch_to_blog($old_blog);
                return;
            } else {
                LR_Sailthru_Install::set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define('LR_SAILTHRU_DIR', plugin_dir_path(__FILE__));
            define('LR_SAILTHRU_URL', plugin_dir_url(__FILE__));
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public static function load_dependencies() {
            global $lr_sailthru_settings;

            // Get LoginRadius commenting settings
            $lr_sailthru_settings = get_option('LR_Sailthru_Settings');
            
            require_once(LR_SAILTHRU_DIR . "lib/Sailthru_Client.php");
            require_once(LR_SAILTHRU_DIR . "lib/Sailthru_Client_Exception.php");
            require_once(LR_SAILTHRU_DIR . "lib/Sailthru_Util.php");
            require_once(LR_SAILTHRU_DIR . "lib/class-wp-sailthru-client.php");
            
            // Load required files.
            require_once(LR_SAILTHRU_DIR . "admin/class-lr-sailthru-admin.php");
            require_once(LR_SAILTHRU_DIR . "includes/front/class-lr-sailthru-front.php");
        }

    }

    new LR_Sailthru();
}
