<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Social_Sharing' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Social_Sharing {

        /**
         * Constructor
         */
        public function __construct() {

            // Register Activation hook callback.
            $this->install();

            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();

            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ), 5 );
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 3 );
        }

        function create_loginradius_menu() {

            if ( ! class_exists( 'LR_Social_Login' ) ) {
                // Create Menu.		
                add_menu_page( 'LoginRadius', 'Social Sharing', 'manage_options', 'loginradius_share', array( 'LR_Social_Share_Admin', 'options_page' ), LR_CORE_URL . 'assets/images/favicon.ico' );
            } else {
                // Add Social Sharing menu.
                add_submenu_page( 'LoginRadius', 'Social Sharing Settings', 'Social Sharing', 'manage_options', 'loginradius_share', array( 'LR_Social_Share_Admin', 'options_page' ) );
            }
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install() {
            global $wpdb;
            require_once ( dirname(__FILE__) . '/install.php' );
            if ( function_exists('is_multisite') && is_multisite() ) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    LR_Sharing_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                LR_Sharing_Install::set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_SHARE_PLUGIN_DIR', plugin_dir_path(__FILE__) );
            define( 'LR_SHARE_PLUGIN_URL', plugin_dir_url(__FILE__) );
        }

        public static function enqueue_front_scripts() {
            wp_enqueue_style( 'lr-social-sharing-front', LR_SHARE_PLUGIN_URL . 'assets/css/lr-social-sharing-front.css', array(), '1.0' );
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginradius_commenting_settings
         */
        private function load_dependencies() {
            global $loginradius_share_settings;

            $loginradius_share_settings = get_option( 'LoginRadius_share_settings' );
            // Load LoginRadius files.
            require_once( LR_SHARE_PLUGIN_DIR . 'admin/lr-social-share-admin.php' );
            if ( ( isset( $loginradius_share_settings['horizontal_enable'] ) && $loginradius_share_settings['horizontal_enable'] == 1 ) || ( isset( $loginradius_share_settings['vertical_enable'] ) && $loginradius_share_settings['vertical_enable'] == 1 ) ) {
                require_once( LR_SHARE_PLUGIN_DIR . 'includes/common/sharing.php' );
                require_once( LR_SHARE_PLUGIN_DIR . 'includes/shortcode/shortcode.php' );
            }
            if( isset( $loginradius_share_settings['horizontal_enable'] ) && $loginradius_share_settings['horizontal_enable'] == 1 ) {
                require_once( LR_SHARE_PLUGIN_DIR . 'includes/horizontal/lr-simplified-social-share-horizontal.php' );
                require_once( LR_SHARE_PLUGIN_DIR . 'includes/widgets/lr-horizontal-share-widget.php' );
            }
            if( isset( $loginradius_share_settings['vertical_enable'] ) && $loginradius_share_settings['vertical_enable'] == 1 ) {
                require_once( LR_SHARE_PLUGIN_DIR . 'includes/vertical/lr-simplified-social-share-vertical.php' );
                require_once( LR_SHARE_PLUGIN_DIR . 'includes/widgets/lr-vertical-share-widget.php' );
            }
            
        }

    }

    new LR_Social_Sharing();
}
