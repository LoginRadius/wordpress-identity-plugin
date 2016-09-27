<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Google_Analytics_Admin' ) ) {

    class LR_Google_Analytics_Admin {
        
        /*
         * Constructor
         */
        public function __construct() {
            add_action( 'admin_init', array( $this, 'admin_init' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
        }

        /**
         * 
         * @param type $hook
         */
        public function load_scripts( $hook) {
            global $lr_js_in_footer;
            if ( $hook == 'loginradius_page_lr_google_analitics' ) {
                wp_enqueue_script( 'lr_google_analytics_admin_script', LR_GOOGLE_ANALYTICS_URL . 'assets/js/lr_google_analytics.js', array( 'jquery' ), LR_PLUGIN_VERSION, $lr_js_in_footer);
            }
        }

        /**
         * Register LR_Google_Analytics_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {

            register_setting( 'lr_google_analytics_settings', 'LR_Google_Analytics_Settings' );

            // Replicate LoginRadius Google Analytics configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site() ) {
                add_action( 'wpmu_new_blog', array( $this, 'replicate_settings_to_new_blog' ) );
                add_action( 'update_option_LR_Google_Analytics_Settings', array( $this, 'update_old_blogs' ) );
            }
        }

        /**
         * Replicate the LoginRadius Google Analytics config to the new blog created in the multisite network
         * 
         * @global type $lr_google_analytics_settings
         * @param type $blogId
         */
        public function replicate_settings_to_new_blog( $blogId) {
            global $lr_google_analytics_settings;
            add_blog_option( $blogId, 'LR_Google_Analytics_Settings', $lr_google_analytics_settings);
        }

        /**
         * Update the LoginRadius Google Analytics options in all the old blogs
         * 
         * @global type $loginradius_api_settings
         * @param type $oldConfig
         */
        public function update_old_blogs( $oldConfig) {
            global $loginradius_api_settings;
            if (isset( $loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1' ) {
                $settings = get_option( 'LR_Google_Analytics_Settings' );
                $blogs = wp_get_sites();
                foreach ( $blogs as $blog) {
                    update_blog_option( $blog['blog_id'], 'LR_Google_Analytics_Settings', $settings);
                }
            }
        }

        /**
         * generate Admin UI
         */
        public static function options_page() {
            include_once LR_GOOGLE_ANALYTICS_DIR . "admin/views/settings.php";
            LR_Google_Analytics_Admin_Settings::render_options_page();
        }

    }
    new LR_Google_Analytics_Admin();
}
