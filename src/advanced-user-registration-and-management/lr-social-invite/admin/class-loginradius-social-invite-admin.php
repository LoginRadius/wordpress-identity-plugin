<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('LR_Social_Invite_Admin')) {

    class LR_Social_Invite_Admin {
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
            if ($hook != 'loginradius_page_loginradius_social_invite') {
                return;
            }
            wp_enqueue_script('lr_social_invite_admin_script', LR_ROOT_URL . 'lr-social-invite/assets/js/social-invite-admin.js', array('jquery'), '1.0', false);
        }

        /**
         * Register LR_Social_Invite_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {
            register_setting('lr_social_invite_settings', 'LR_Social_Invite_Settings');

            //replicate Social Commenting configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_loginradius_settings_to_new_blog'));
            }
        }

        // Replicate the social social_invite config to the new blog created in the multisite network
        public function replicate_loginradius_settings_to_new_blog($blogId) {
            global $lr_social_invite_settings;
            add_blog_option($blogId, 'LR_Social_Invite_Settings', $lr_social_invite_settings);
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            require_once "views/settings.php";
            LR_Social_Invite_Admin_Settings:: render_options_page();
        }

    }

}

new LR_Social_Invite_Admin();
