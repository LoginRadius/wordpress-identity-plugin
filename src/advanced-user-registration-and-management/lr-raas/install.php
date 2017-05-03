<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('LR_Raas_Install')) {

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
            'enable_username' => '0',
            'form_validation' => '1'
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
         * Function for adding default raas settings at activation.
         * 
         * @global type $wpdb
         * @global type $loginRadiusSettings
         * @global type $lr_raas_settings
         */
        public static function set_default_options($blog_id) {
            global $wpdb, $loginRadiusSettings, $lr_raas_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LR_Raas_Settings')) {
                    update_blog_option($blog_id, 'LR_Raas_Settings', self::$raas_options);
                    $lr_raas_settings = get_blog_option($blog_id, 'LR_Raas_Settings');
                }
                if (!get_blog_option($blog_id, 'LoginRadius_settings')) {
                    update_blog_option($blog_id, 'LoginRadius_settings', self::$sociallogin_option);
                    $loginRadiusSettings = get_blog_option($blog_id, 'LoginRadius_settings');
                }
            } else {
                if (!get_option('LR_Raas_Settings')) {
                    update_option('LR_Raas_Settings', self::$raas_options);
                    $lr_raas_settings = get_option('LR_Raas_Settings');
                }
                if (!get_option('LoginRadius_settings')) {
                    update_option('LoginRadius_settings', self::$sociallogin_option);
                    $loginRadiusSettings = get_option('LoginRadius_settings');
                }
            }

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_custom_fields_data` (
    			`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    			`wp_users_id` int( 11 ) NOT NULL,
    			`field_title` varchar( 100 ) DEFAULT NULL,
    			`field_value` varchar( 100 ) DEFAULT NULL
    		 )');
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