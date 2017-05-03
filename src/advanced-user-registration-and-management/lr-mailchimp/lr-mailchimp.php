<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Mailchimp')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Mailchimp {

        /**
         * Constructor
         */
        public function __construct() {
            if (!class_exists('LR_Social_Login')) {
                return;
            }
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);

            // load dependencies.
            $this->load_dependencies();
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 8);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Mailchimp Settings', 'MailChimp', 'manage_options', 'loginradius_mailchimp', array('LR_Mailchimp_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install($blog_id) {
            require_once ( dirname(__FILE__) . '/install.php' );
            LR_Mailchimp_Install:: set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                delete_blog_option($blog_id, 'LR_Mailchimp_Settings');
            } else {
                delete_option('LR_Mailchimp_Settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Mailchimp settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global lr_mailchimp_settings
         */
        private function load_dependencies() {
            global $lr_mailchimp_settings;

            // Get MailChimp settings
            $lr_mailchimp_settings = get_option('LR_Mailchimp_Settings');

            // Mailchimp API
            if (!class_exists('MCAPI')) {
                require_once( 'includes/helpers/MCAPI.class.php' );
            }

            require_once( LR_ROOT_DIR . 'lr-mailchimp/includes/helpers/ajax.php' );
            new LR_Mailchimp_Ajax_Helper();

            // Load required files.
            require_once( LR_ROOT_DIR . 'lr-mailchimp/admin/class-loginradius-mailchimp-admin.php' );
            require_once( LR_ROOT_DIR . 'lr-mailchimp/includes/display/mailchimp.php' );
        }

    }

    new LR_Mailchimp();
}
