<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Google_Analytics')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Google_Analytics {

        /**
         * Constructor
         */
        public function __construct() {
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);

            // load dependencies.
            $this->load_dependencies();
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 14);
        }

        /**
         * Add Submenu in LoginRadius Menu
         */
        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Google Analytics', 'Google Analytics', 'manage_options', 'lr_google_analytics', array('LR_Google_Analytics_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         * 
         * @global type $wpdb
         * @return type
         */
        public static function install($blog_id) {
            require_once( dirname(__FILE__) . '/install.php' );
            LR_Google_Analytics_Install::set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                delete_blog_option($blog_id, 'LR_Google_Analytics_Settings');
            } else {
                delete_option('LR_Google_Analytics_Settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">' . __('Google Analytics settings have been reset and default values loaded', 'lr-plugin-slug') . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         * 
         * @global type $lr_google_analytics_settings
         */
        public static function load_dependencies() {
            global $lr_google_analytics_settings;

            // Get LoginRadius commenting settings
            $lr_google_analytics_settings = get_option('LR_Google_Analytics_Settings');

            // Load required files.
            require_once( LR_ROOT_DIR . "lr-google-analysis/admin/class-lr-google-analytics-admin.php" );
            require_once( LR_ROOT_DIR . "lr-google-analysis/includes/front/class-lr-google-analytics-front.php" );
        }

    }

    new LR_Google_Analytics();
}
