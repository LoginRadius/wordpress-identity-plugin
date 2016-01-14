<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Salesforce_Admin' ) ) {

    class LR_Salesforce_Admin {

        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'));
            add_action('admin_init', array($this, 'admin_init'));
        }

        /**
         * 
         * @param type $hook
         */
        public function load_scripts($hook) {
            if ($hook == 'loginradius_page_loginradius_salesforce') {
                wp_enqueue_style('lr_salesforce_admin_style', LR_SALESFORCE_PLUGIN_URL . 'assets/css/salesforce.css', array(), '1.0', false);
                wp_enqueue_script('lr_salesforce_admin_script', LR_SALESFORCE_PLUGIN_URL . 'assets/js/salesforce.js', array('jquery'), '1.0', false);
            }
        }

        /**
         * Register LR_Salesforce_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {
            register_setting('lr_salesforce_settings', 'LR_Salesforce_Settings');

            //replicate Social Commenting configuration to the subblogs in the multisite network
            if ( is_multisite() && is_main_site() ) {
                add_action( 'wpmu_new_blog', array($this, 'replicate_loginradius_settings_to_new_blog'));
                add_action( 'update_option_LR_Salesforce_Settings', array( $this, 'login_radius_update_old_blogs' ) );
            }
        }

        /**
         * replicate the social salesforce config to the new blog created in the multisite network
         * 
         * @global type $lr_salesforce_settings
         * @param type $blogId
         */
        public function replicate_loginradius_settings_to_new_blog($blogId) {
            global $lr_salesforce_settings;
            add_blog_option($blogId, 'LR_Salesforce_Settings', $lr_salesforce_settings);
        }

        /**
         * update the social salesforce options in all the old blogs
         * 
         * @param type $oldConfig
         */
        public function login_radius_update_old_blogs($oldConfig) {
            $newConfig = get_option('LR_Salesforce_Settings');
            if (isset($newConfig['multisite_config']) && $newConfig['multisite_config'] == '1') {
                $blogs = wp_get_sites();
                foreach ($blogs as $blog) {
                    update_blog_option($blog['blog_id'], 'LR_Salesforce_Settings', $newConfig);
                }
            }
        }

        /**
         * SalesForce Admin UI
         */
        public static function options_page() {
            include_once "views/settings.php";
            LR_Salesforce_Admin_Settings::render_options_page();
        }

    }

    new LR_Salesforce_Admin();
}
