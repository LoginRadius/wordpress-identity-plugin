<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Custom_Obj')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Custom_Obj {

        /**
         * Constructor
         */
        public function __construct() {
            if ( ! class_exists('LR_Raas_Install') ) {
                return;
            }

            // Register Activation hook callback.
            $this->define_constants();
            $this->install();
            $this->load_dependencies();
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define('LR_CUSTOM_OBJECT_DIR', plugin_dir_path(__FILE__));
            define('LR_CUSTOM_OBJECT_URL', plugin_dir_url(__FILE__));
        }
        /**
         * Function for setting default options while plugin is activating.
         */
        public static function install() {
            global $wpdb;
            require_once (dirname(__FILE__) . '/install.php');
            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    LR_Custom_Obj_Install::set_default_options();
                }
                switch_to_blog($old_blog);
                return;
            } else {
                LR_Custom_Obj_Install::set_default_options();
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        private function load_dependencies() {
            global $loginRadiusCustomObject, $lr_Custom_Obj_Fields;
            $lr_Custom_Obj_Fields = array('one','two','three','four','five','six','seven','eight');
            require_once(LR_CUSTOM_OBJECT_DIR.'lib/CustomObject.php');
            $loginRadiusCustomObject = new CustomObject();
            require_once(LR_CUSTOM_OBJECT_DIR.'admin/views/settings.php');
            require_once(LR_CUSTOM_OBJECT_DIR.'admin/class-lr-custom-object-admin.php');
            require_once(LR_CUSTOM_OBJECT_DIR.'includes/front/class-lr-raas-custom-obj-front.php');
        }

    }

    new LR_Custom_Obj();
}





