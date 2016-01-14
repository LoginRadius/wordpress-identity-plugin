<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Woocommerce_Admin')) {

    class LR_Woocommerce_Admin {
        
        public function __construct() {
            add_action('admin_init', array($this, 'admin_init'));
        }
        /**
         * Woocommerce admin page ui
         */
        public static function options_page() {
            include_once LR_WOOCOMMERCE_DIR."admin/views/settings.php";
            LR_Woocommerce_Admin_Settings::render_options_page();
        }
        /**
         * save woocommerce setting
         */
        public function admin_init() {
            register_setting('lr_woocommerce_settings', 'LR_Woocommerce_Settings', array($this, 'validate_options'));
        }
        /**
         * check save setting
         * 
         * @param type $settings
         * @return type
         */
        public static function validate_options($settings) {
            return $settings;
        }
        /**
         * manage woocommerce setting on multisite
         * @global type $loginradius_api_settings
         * @param type $oldConfig
         */
        public function update_old_blogs($oldConfig) {
            global $loginradius_api_settings;
            if (isset($loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1') {
                $settings = get_option('LR_Woocommerce_Settings');
                $blogs = wp_get_sites();
                foreach ($blogs as $blog) {
                    update_blog_option($blog['blog_id'], 'LR_Woocommerce_Settings', $settings);
                }
            }
        }

    }
new LR_Woocommerce_Admin();
}