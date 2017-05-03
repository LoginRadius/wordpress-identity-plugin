<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('LR_Mailchimp_Install')) {

    /**
     * class responsible for setting default settings for mailchimp.
     */
    class LR_Mailchimp_Install {

        private static $options = array(
            'mailchimp_subscribe' => '0',
            'mailchimp_lists' => '',
            'mailchimp_apikey' => ''
        );

        /**
         * Function for adding default mailchimp settings at activation.
         */
        public static function set_default_options($blog_id) {
            global $lr_mailchimp_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LR_Mailchimp_Settings')) {
                    update_blog_option($blog_id, 'LR_Mailchimp_Settings', self::$options);
                }
                $lr_mailchimp_settings = get_blog_option($blog_id, 'LR_Mailchimp_Settings');
            } else {
                if (!get_option('LR_Mailchimp_Settings')) {
                    update_option('LR_Mailchimp_Settings', self::$options);
                }
                $lr_mailchimp_settings = get_option('LR_Mailchimp_Settings');
            }
        }

    }

    new LR_Mailchimp_Install();
}