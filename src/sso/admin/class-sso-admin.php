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
            add_action('admin_init', array($this, 'init'));
        }

        /*
         * load all required dependencies
         */

        public function init() {
            register_setting('Ciam_Sso_Page_settings', 'Ciam_Sso_Page_settings');
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once CIAM_PLUGIN_DIR . "sso/admin/views/settings.php";
            $obj_CIAM_SSO_Settings = new CIAM_SSO_Settings;
            $obj_CIAM_SSO_Settings->render_options_page();
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

    }

    new Sso_Admin();
}
