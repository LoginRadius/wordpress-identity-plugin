<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Social_Profile_Data')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Social_Profile_Data {

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
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 5);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Social Profile Data Settings', 'Social Profile Data', 'manage_options', 'loginradius_social_profile_data', array('LR_Social_Profile_Data_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plugin is activating.
         */
        public static function install($blog_id) {
            require_once ( LR_ROOT_DIR . 'lr-social-profile-data/install.php' );
            LR_Social_Profile_Data_Install::set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                global $wpdb;
                delete_blog_option($blog_id, 'LoginRadius_Social_Profile_Data_settings');
                $tables = array(
                    'basic_profile_data',
                    'emails',
                    'extended_location_data',
                    'extended_profile_data',
                    'positions',
                    'companies',
                    'education',
                    'phone_numbers',
                    'imaccounts',
                    'addresses',
                    'sports',
                    'inspirational_people',
                    'skills',
                    'current_status',
                    'certifications',
                    'courses',
                    'volunteer',
                    'recommendations_received',
                    'languages',
                    'patents',
                    'favorites',
                    'facebook_likes',
                    'facebook_events',
                    'facebook_posts',
                    'albums',
                    'contacts',
                    'groups',
                    'status',
                    'twitter_mentions',
                    'linkedin_companies',
                    'popup_custom_fields_map',
                    'popup_custom_fields_dropdown',
                    'popup_custom_fields_data');
                foreach ($tables as $table) {
                    $wpdb->query('DROP TABLE IF EXISTS `' . $wpdb->base_prefix . 'lr_' . $table . '`');
                }
            } else {
                delete_option('LoginRadius_Social_Profile_Data_settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Social Profile Data settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginradius_commenting_settings
         */
        private function load_dependencies() {
            global $lr_social_profile_data_settings, $social_profile_display;

            // Get LoginRadius commenting settings
            $lr_social_profile_data_settings = get_option('LoginRadius_Social_Profile_Data_settings');

            require_once( LR_ROOT_DIR . "lr-social-profile-data/admin/class-lr-social-profile-data-admin.php" );
            require_once( LR_ROOT_DIR . "lr-social-profile-data/includes/helpers/class-lr-social-profile-data-function.php" );
            require_once( LR_ROOT_DIR . "lr-social-profile-data/includes/display/class-lr-display-social-profile-data.php" );
            $social_profile_display = new LR_Display_Social_Profile_Data();
        }

    }

    new LR_Social_Profile_Data();
}
