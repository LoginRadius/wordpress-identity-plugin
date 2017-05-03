<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_SSO')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_SSO {

        /**
         * Constructor
         */
        public function __construct() {
            global $loginradius_api_settings;
            if (!isset($loginradius_api_settings['raas_enable']) || $loginradius_api_settings['raas_enable'] != 1) {
                return;
            }

            // load dependencies.
            $this->load_dependencies();
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 10);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'SSO Settings', 'Single sign-on', 'manage_options', 'loginradius_sso', array('LR_SSO_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install($blog_id) {
            require_once( dirname(__FILE__) . '/install.php' );
            LR_SSO_Install::set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                delete_blog_option($blog_id, 'LR_SSO_Settings');
            } else {
                delete_option('LR_SSO_Settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Single Sign On settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public static function load_dependencies() {
            global $lr_sso_settings;

            // Get LoginRadius commenting settings
            $lr_sso_settings = get_option('LR_SSO_Settings');

            // Load required files.
            require_once( LR_ROOT_DIR . 'lr-sso/admin/class-lr-sso-admin.php' );
            require_once( LR_ROOT_DIR . 'lr-sso/includes/front/class-lr-sso-front.php' );
        }

    }

    new LR_SSO();
}
