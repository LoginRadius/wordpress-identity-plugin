<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('Sso_Admin')) {

    class Sso_Admin {
        /*
         * Constructor for class CIAM_Social_Login_Admin
         */

        public function __construct() {
            global $ciam_credencials;
           
            if(!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])){ 
                 return;   
             }
            add_action('admin_init', array($this, 'admin_init'));
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function admin_init() {

            register_setting('Ciam_Sso_Page_settings', 'Ciam_Sso_Page_settings', array($this,'ciam_sso_page_validation'));
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        function ciam_sso_page_validation($settings) {
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $settings);
            return $settings;
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once CIAM_PLUGIN_DIR . "sso/admin/views/settings.php";
            
            CIAM_SSO_Settings::render_options_page();
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

    }

    new Sso_Admin();
}
