<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('LR_Social_Profile_Data_Admin')) {

    class LR_Social_Profile_Data_Admin {
        /*
         * Constructor
         */

        public function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'include_thickbox'));
            add_action('admin_enqueue_scripts', array($this, 'get_profile_data_script'));
            add_action('admin_init', array($this, 'admin_init'));
            add_action('deleted_user', array($this, 'delete_social_profile_data'));
        }

        /**
         * Deletes Social Profile Data after deleting a user
         */
        function delete_social_profile_data($id) {
            global $wpdb;
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_basic_profile_data WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_emails WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_extended_location_data WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_extended_profile_data WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_positions WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_companies WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_education WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_phone_numbers WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_imaccounts WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_addresses WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_sports WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_inspirational_people WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_skills WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_current_status WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_certifications WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_courses WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_volunteer WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_recommendations_received WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_languages WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_patents WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_favorites WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_facebook_likes WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_facebook_events WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_facebook_posts WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_albums WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_contacts WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_contacts WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_groups WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_status WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_twitter_mentions WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_linkedin_companies WHERE wp_users_id = %d", $id));
            $wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->base_prefix . "lr_popup_custom_fields_data WHERE wp_users_id = %d", $id));
        }

        /*
         * Enqueue Admin Scripts
         */

        public function load_scripts($hook) {

            if ($hook != 'loginradius_page_loginradius_social_profile_data') {
                return;
            }
            global $lr_js_in_footer;
            wp_enqueue_script('lr_profile_data_admin_script', LR_ROOT_URL . 'lr-social-profile-data/assets/js/social-profile-data-admin.js', array('jquery'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_enqueue_style('lr_profile_data_admin_style', LR_ROOT_URL . 'lr-social-profile-data/assets/css/lr-social-profile-data-settings.css');
        }

        /**
         * Register LoginRadius_Social_Profile_Data_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {

            register_setting('lr_social_profile_data_settings', 'LoginRadius_Social_Profile_Data_settings', array($this, 'social_profile_validate'));

            //replicate Social Commenting configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_settings_to_new_blog'));
            }
        }

        public function social_profile_validate($settings) {
            return $settings;
        }

        // replicate the social Social Profile Data config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog($blogId) {
            global $lr_social_profile_data_settings;
            add_blog_option($blogId, 'LoginRadius_Social_Profile_Data_settings', $lr_social_profile_data_settings);
        }

        /**
         * include thickbox js and css
         */
        public function include_thickbox($hook) {
            if ($hook == 'users.php') {
                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
            }
        }

        /**
         * include thickbox js and css
         */
        public function get_profile_data_script($hook) {
            if ($hook == 'users.php') {
                ?>
                <script type="text/javascript">
                    function loginRadiusGetProfileData(userId) {
                        tb_show('User Profile Data', 'admin-ajax.php?action=lr_get_profile_data&user_id=' + userId + '&width=1100&height=500');
                    }
                </script>
                <?php

            }
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            require_once LR_ROOT_DIR . "lr-social-profile-data/admin/views/settings.php";
            LR_Social_Profile_Data_Admin_Settings:: render_options_page();
        }

    }

    new LR_Social_Profile_Data_Admin();
}
