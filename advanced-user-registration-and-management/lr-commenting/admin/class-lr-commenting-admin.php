<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Commenting_Admin' ) ) {

    class LR_Commenting_Admin {
        /*
         * Constructor for class LR_Commenting_Admin
         */

        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'));
            add_action('admin_init', array($this, 'admin_init'));
        }

        /*
         * Enqueue Admin Scripts
         */

        public function load_scripts($hook) {
            if ($hook != 'loginradius_page_loginradius_commenting') {
                return;
            }
            global $lr_js_in_footer;
            wp_enqueue_script('lr_commenting_admin_script', LR_COMMENTS_URL . 'assets/js/lr-commenting-admin.min.js', array('jquery'), '1.0', $lr_js_in_footer);
        }

        /**
         * Callback for admin_menu hook,
         * Register LoginRadius_settings and its sanitization callback. Add Login Radius meta box to pages and posts.
         */
        public function admin_init() {
            register_setting('lr_commenting_settings', 'LR_Commenting_Settings');

            //replicate Social Commenting configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_loginradius_settings_to_new_blog'));
                add_action('update_option_LR_Commenting_Settings', array($this, 'login_radius_update_old_blogs'));
            }
        }

        // Replicate the social commenting config to the new blog created in the multisite network
        public function replicate_loginradius_settings_to_new_blog($blogId) {
            global $lr_commenting_settings;
            add_blog_option($blogId, 'LR_Commenting_Settings', $lr_commenting_settings);
        }

        // Update the social commenting options in all the old blogs
        public function login_radius_update_old_blogs( $oldConfig ) {
            global $loginradius_api_settings;
            if (isset($loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1') {
                $settings = get_option('LR_Commenting_Settings');
                $blogs = wp_get_sites();
                foreach ($blogs as $blog) {
                    update_blog_option($blog['blog_id'], 'LR_Commenting_Settings', $settings);
                }
            }
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once "views/settings.php";
            LR_Commenting_Admin_Settings::render_options_page();
        }

    }
new LR_Commenting_Admin();
}
