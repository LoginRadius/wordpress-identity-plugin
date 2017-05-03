<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Social_Invite')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Social_Invite {

        /**
         * Constructor
         */
        public function __construct() {
            global $loginradius_api_settings;
            if (isset($loginradius_api_settings['raas_enable']) && $loginradius_api_settings['raas_enable'] == 1) {
                return;
            }
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);
            //load dependencies.
            $this->load_dependencies();
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 6);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Social Invite Settings', 'Social Invite', 'manage_options', 'loginradius_social_invite', array('LR_Social_Invite_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install($blog_id) {
            require_once ( dirname(__FILE__) . '/install.php' );
            LR_Social_Invite_Install:: set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                global $wpdb;
                delete_blog_option($blog_id, 'LR_Social_Invite_Settings');
                $wpdb->query('DROP TABLE IF EXISTS `' . $wpdb->base_prefix . 'lr_social_invite_contacts`');
                $wpdb->query('DROP TABLE IF EXISTS `' . $wpdb->base_prefix . 'lr_social_invite_tokens`');
            } else {

                delete_option('LR_Social_Invite_Settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Social Invite settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginradius_commenting_settings
         */
        private function load_dependencies() {
            global $lr_social_invite_settings;

            // Get LoginRadius commenting settings
            $lr_social_invite_settings = get_option('LR_Social_Invite_Settings');

            require_once( LR_ROOT_DIR . "lr-social-invite/includes/helpers/ajax.php" );
            new Ajax_Social_Invite_Helper();

            // Load required files.
            require_once("includes/display/loginradius-display-class.php");
            require_once("admin/class-loginradius-social-invite-admin.php");

            new LR_Social_Invite_Display();
        }

    }

    new LR_Social_Invite();
}
