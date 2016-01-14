<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('LR_Sailthru_Admin')) {

    class LR_Sailthru_Admin {

        /**
         * Constructor
         */
        public function __construct() {
            add_action('admin_init', array($this, 'admin_init'));
        }

        /**
         * Register LR_Sailthru_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {

            register_setting('lr_sailthru_settings', 'LR_Sailthru_Settings');
            add_action('wp_ajax_get_sailthru_subscriber_list', array(get_class(), 'get_sailthru_subscriber_list'));
            // Replicate LoginRadius Sailthru configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_settings_to_new_blog'));
                add_action('update_option_LR_Sailthru_Settings', array($this, 'update_old_blogs'));
            }
        }

        public static function get_sailthru_subscriber_list() {
            global $lr_sailthru_settings;
            $output['status'] = 'error';
            $sailthru_api_key = isset($_POST['sailthru_api_key']) ? $_POST['sailthru_api_key'] : '';
            $sailthru_api_secret = isset($_POST['sailthru_api_secret']) ? $_POST['sailthru_api_secret'] : '';
            if (empty($sailthru_api_key) || empty($sailthru_api_secret)) {
                $output['message'] = __('Please Enter Sailthru Api and Secret.', 'LoginRadius');
            } else {
                $obj_sailthru = new WP_LR_Sailthru_Client($sailthru_api_key, $sailthru_api_secret);
                try {
                    $lists = $obj_sailthru->getLists();
                }catch (Exception $e) {
                    $lists['errormsg'] = $e->getMessage();
                }

                if (isset($lists['errormsg']) && !empty($lists['errormsg'])) {
                    $output['message'] = $lists['errormsg'];
                } else {
                    $seledtedSailthruSubscriberLists = isset($lr_sailthru_settings['sailthru_subscriber_lists']) ? $lr_sailthru_settings['sailthru_subscriber_lists'] : array();
                    $output['message'] = __('Please Try again', 'LoginRadius');
                    $output['html'] = '';
                    if (count($lists['lists']) > 0) {
                        foreach ($lists['lists'] as $list) {
                            $output['html'] .= '<option value="' . $list['name'] . '"';
                            if (in_array($list['name'], $seledtedSailthruSubscriberLists)) {
                                $output['html'] .= ' selected="selected"';
                            }
                            $output['html'] .= '>' . $list['name'] . '</option>';
                        }
                        $output['status'] = 'success';
                        $output['message'] = __('Please select sailthru subscriber lists and mapping fields.', 'LoginRadius');
                    }
                }
                echo json_encode($output);
                exit();
            }
        }

        /**
         * Replicate the LoginRadius Sailthru config to the new blog created in the multisite network
         * 
         * @global type $lr_sailthru_settings
         * @param type $blogId
         */
        public function replicate_settings_to_new_blog($blogId) {
            global $lr_sailthru_settings;
            add_blog_option($blogId, 'LR_Sailthru_Settings', $lr_sailthru_settings);
        }

        /**
         * Update the LoginRadius Sailthru options in all the old blogs
         * 
         * @global type $loginradius_api_settings
         * @param type $oldConfig
         */
        public function update_old_blogs($oldConfig) {
            global $loginradius_api_settings;
            if (isset($loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1') {
                $settings = get_option('LR_Sailthru_Settings');
                $blogs = wp_get_sites();
                foreach ($blogs as $blog) {
                    update_blog_option($blog['blog_id'], 'LR_Sailthru_Settings', $settings);
                }
            }
        }

        /**
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */
        public static function options_page() {
            include_once LR_SAILTHRU_DIR . "admin/views/settings.php";
            LR_Sailthru_Admin_Settings::render_options_page();
        }

    }

    new LR_Sailthru_Admin();
}
