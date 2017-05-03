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
            if (!class_exists('LR_Raas_Install')) {
                return;
            }

            // Register Activation hook callback.
            add_action('lr_plugin_activate', array($this, 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array($this, 'uninstall'), 10, 1);
            add_action('lr_raas_reset_setting', array($this, 'reset_options'));
            $this->load_dependencies();
        }

        /**
         * Function for setting default options while plugin is activating.
         */
        public function install($blog_id) {
            require_once (dirname(__FILE__) . '/install.php');
            LR_Custom_Obj_Install::set_default_options($blog_id);
        }

        public function uninstall($blog_id) {
            if ($blog_id) {
                delete_blog_option($blog_id, 'LR_Raas_Custom_Obj_Settings');
            } else {
                delete_option('LR_Raas_Custom_Obj_Settings');
            }
        }

        public function reset_options() {
            $this->uninstall();
            $this->install();
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        private function load_dependencies() {
            global $loginRadiusCustomObject, $lr_Custom_Obj_Fields, $loginradius_api_settings;
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? $loginradius_api_settings['LoginRadius_apikey'] : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? $loginradius_api_settings['LoginRadius_secret'] : '';
            $lr_Custom_Obj_Fields = array('one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight');

            try {
                $loginRadiusCustomObject = new \LoginRadiusSDK\CustomerRegistration\CustomObjectAPI($apikey, $secret, array('output_format' => 'json'));
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                
            }
            require_once(LR_ROOT_DIR . 'lr-custom-object/admin/views/settings.php');
            require_once(LR_ROOT_DIR . 'lr-custom-object/admin/class-lr-custom-object-admin.php');
            require_once(LR_ROOT_DIR . 'lr-custom-object/includes/front/class-lr-raas-custom-obj-front.php');
        }

    }

    new LR_Custom_Obj();
}





