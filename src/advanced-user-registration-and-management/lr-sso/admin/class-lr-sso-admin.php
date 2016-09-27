<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('LR_SSO_Admin')) {

    class LR_SSO_Admin {
        /*
         * Constructor
         */

        public function __construct() {
            add_action('admin_init', array($this, 'admin_init'));
        }

        /**
         * Register LR_SSO_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {

            register_setting('lr_sso_settings', 'LR_SSO_Settings');

            // Replicate LoginRadius SSO configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_settings_to_new_blog'));
                add_action('update_option_LR_SSO_Settings', array($this, 'update_old_blogs'));
            }
        }

        // Replicate the LoginRadius SSO config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog($blogId) {
            global $lr_sso_settings;
            add_blog_option($blogId, 'LR_SSO_Settings', $lr_sso_settings);
        }

        // Update the LoginRadius SSO options in all the old blogs
        public function update_old_blogs($oldConfig) {
            global $loginradius_api_settings;
            if (isset($loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1') {
                $settings = get_option('LR_SSO_Settings');
                $blogs = wp_get_sites();
                foreach ($blogs as $blog) {
                    update_blog_option($blog['blog_id'], 'LR_SSO_Settings', $settings);
                }
            }
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once LR_SSO_DIR."admin/views/settings.php";
            LR_SSO_Admin_Settings:: render_options_page();
        }

    }

    new LR_SSO_Admin();
}
