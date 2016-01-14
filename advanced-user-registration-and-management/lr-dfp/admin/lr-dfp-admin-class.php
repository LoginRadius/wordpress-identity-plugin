<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_DFP_Admin' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_DFP_Admin {

        /**
         * Constructor
         */
        public function __construct() {
            if( ! class_exists( 'LR_Social_Login' ) ){
                return;
            }
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }

        /**
         * Register LR_DFP_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {
            global $lr_dfp_settings;

            register_setting( 'lr_dfp_settings', 'LR_DFP_Settings' );

            // Replicate disqus configuration to the subblogs in the multisite network
            if ( is_multisite() && is_main_site() ) {
                add_action( 'wpmu_new_blog', array( $this, 'replicate_settings_to_new_blog' ) );
                add_action( 'update_option_LR_DFP_Settings', array( $this, 'update_old_blogs' ) );
            }
        }

        // Replicate the disqus config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog( $blogId ) {
            global $lr_disqus_settings;
            add_blog_option( $blogId, 'LR_DFP_Settings', $lr_disqus_settings );
        }

        // Update the disqus options in all the old blogs
        public function update_old_blogs( $oldConfig ) {
            global $loginradius_api_settings;
            if ( isset( $loginradius_api_settings['multisite_config'] ) && $loginradius_api_settings['multisite_config'] == '1' ) {
                $settings = get_option( 'LR_DFP_Settings' );
                $blogs = wp_get_sites();
                foreach ( $blogs as $blog ) {
                    update_blog_option( $blog['blog_id'], 'LR_DFP_Settings', $settings );
                }
            }
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */
        public static function options_page() {
            include_once LR_DFP_DIR . "admin/views/settings.php";
            $options_page = new LR_DFP_Settings();
            $options_page->render_options_page();
        }
    }
    new LR_DFP_Admin();
}