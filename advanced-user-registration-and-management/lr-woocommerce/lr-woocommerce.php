<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Woocommerce' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Woocommerce {

        /**
         * Constructor
         */
        public function __construct() {
            /**
             * Check if WooCommerce and RaaS is active
             */
            if ( ! class_exists( 'LR_Raas_Install' ) || ! in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins') ) ) ) {
                return;
            }
            $this->install();
            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 13 );
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
                    LR_Woocommerce_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                LR_Woocommerce_Install::set_default_options();
            }
        }
        /**
         * create sub memu in LoginRadius plugin
         */
        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'woocommerce Settings', 'Woocommerce', 'manage_options', 'loginradius_woocommerce', array('LR_Woocommerce_Admin', 'options_page'));
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define('LR_WOOCOMMERCE_DIR', plugin_dir_path(__FILE__));
            define('LR_WOOCOMMERCE_URL', plugin_dir_url(__FILE__));
        }
        /**
         * Loads PHP files that required by the plug-in
         */
        public static function load_dependencies() {
            global $lr_woocommerce_settings;

            // Get LoginRadius commenting settings
            $lr_woocommerce_settings = get_option( 'LR_Woocommerce_Settings' );
            
            // Load required files.
            require_once( LR_WOOCOMMERCE_DIR."admin/class-lr-woocommerce-admin.php" );
            require_once( LR_WOOCOMMERCE_DIR."includes/front/class-lr-woocommerce-front.php" );
        }

    }

    new LR_Woocommerce();
}