<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}
if ( ! class_exists( 'LR_Raas_Install' ) ) {

    /**
     * class responsible for setting default settings for mailchimp.
     */
    class LR_Raas_Install {

        private static $raas_options = array(
            'raas_autopage' => '0',
            'login_page_id' => '',
            'registration_page_id' => '',
            'change_password_page_id' => '',
            'lost_password_page_id' => '',
            'popup_forms_enable' => '',
            'v2captcha_site_key' => '',
            'email_verify_option' => 'enabled',
            'enable_username' => '0'
        );
        private static $sociallogin_option = array(
            'LoginRadius_redirect' => 'samepage',
            'LoginRadius_regRedirect' => 'samepage',
            'LoginRadius_loutRedirect' => 'homepage',
            'LoginRadius_socialavatar' => 'socialavatar',
            'LoginRadius_title' => 'Log in via a social account',
            'enable_degugging' => '0'
        );
        /**
         * Constructor
         */
        public function __construct() {
            $this->set_default_options();
        }

        /**
         * Function for adding default raas settings at activation.
         * 
         * @global type $wpdb
         * @global type $loginRadiusSettings
         * @global type $lr_raas_settings
         */
        public static function set_default_options() {
            global $wpdb, $loginRadiusSettings, $lr_raas_settings, $lr_social_profile_data_settings;

            if ( ! get_option('LR_Raas_Settings') ) {
                update_option('LR_Raas_Settings', self::$raas_options);
                $lr_raas_settings = get_option('LR_Raas_Settings');
            }
            if ( ! get_option('LoginRadius_settings') ) {
                update_option('LoginRadius_settings', self::$sociallogin_option);
                $loginRadiusSettings = get_option('LoginRadius_settings');
            }

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_custom_fields_data` (
    			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    			`wp_users_id` int( 11 ) NOT NULL,
    			`field_title` varchar( 100 ) DEFAULT NULL,
    			`field_value` varchar( 100 ) DEFAULT NULL
    		 )');

            // Disable custom popup if settings remain for Social Login
            $lr_social_profile_data_settings['enable_custom_popup'] = '';

            $lr_raas_settings = get_option('LR_Raas_Settings');
        }

        /**
         * Function to reset raas options to default.
         * 
         * @global type $lr_raas_settings
         */
        public static function reset_options() {
            global $lr_raas_settings, $loginRadiusSettings;
            do_action('lr_reset_admin_action','LR_Raas_Settings', self::$raas_options);
            apply_filters('lr_raas_reset_setting', '');
            do_action('lr_reset_admin_action','LoginRadius_settings', self::$sociallogin_option);
            // Get raas settings
            $lr_raas_settings = get_option('LR_Raas_Settings');
            $loginRadiusSettings = get_option('LoginRadius_settings');
        }

        /**
         * create RaaS custom pages
         * 
         * @param type $settings
         * @return type
         */
        public static function create_pages($settings) {

            // Create Login Page.
            if (isset($settings['login_page_id']) && $settings['login_page_id'] == '') {
                $loginPage = array(
                    'post_title' => 'Login',
                    'post_content' => '[raas_login_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $loginPageId = wp_insert_post($loginPage);
            } else {
                $loginPageId = $settings['login_page_id'];
            }

            // Create Registration Page.
            if (isset($settings['registration_page_id']) && $settings['registration_page_id'] == '') {
                $registrationPage = array(
                    'post_title' => 'Registration',
                    'post_content' => '[raas_registration_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $registrationPageId = wp_insert_post($registrationPage);
            } else {
                $registrationPageId = $settings['registration_page_id'];
            }

            // Create Change Password Page.
            if (isset($settings['change_password_page_id']) && $settings['change_password_page_id'] == '') {
                $changePasswordPage = array(
                    'post_title' => 'Change Password',
                    'post_content' => '[raas_password_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $changePasswordPageId = wp_insert_post($changePasswordPage);
            } else {
                $changePasswordPageId = $settings['change_password_page_id'];
            }

            // Create Lost Password Page.
            if (isset($settings['lost_password_page_id']) && $settings['lost_password_page_id'] == '') {
                $lostPasswordPage = array(
                    'post_title' => 'Lost Password',
                    'post_content' => '[raas_forgotten_form]',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => get_current_user_id(),
                    'comment_status' => 'closed'
                );
                $lostPasswordPageId = wp_insert_post($lostPasswordPage);
            } else {
                $lostPasswordPageId = $settings['lost_password_page_id'];
            }

            return array(
                'login_page_id' => trim($loginPageId),
                'registration_page_id' => trim($registrationPageId),
                'change_password_page_id' => trim($changePasswordPageId),
                'lost_password_page_id' => trim($lostPasswordPageId)
            );
        }

        /**
         * Set Default options when plugin is activated first time.
         * 
         * @param type $settings
         * @return type
         */
        public static function activation($settings) {

            return self::create_pages($settings);
        }

        /**
         * delete post on raas deactive
         * 
         * @param type $settings
         */
        public function deactivation($settings) {

            wp_delete_post($settings['login_page_id'], true);
            wp_delete_post($settings['registration_page_id'], true);
            wp_delete_post($settings['change_password_page_id'], true);
            wp_delete_post($settings['lost_password_page_id'], true);
        }

    }

    new LR_Raas_Install();
}