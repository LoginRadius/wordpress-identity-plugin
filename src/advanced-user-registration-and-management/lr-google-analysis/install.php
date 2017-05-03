<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Google_Analytics_Install')) {

    /**
     * class responsible for setting default settings for loginradius Google Analytics.
     */
    class LR_Google_Analytics_Install {

        /**
         * $ga_options
         * @var type 
         */
        private static $options = array(
            'ga_enable' => '0',
            'ga_tracking_id' => ''
        );

        /**
         * Function to add default Google Analytics settings at activation.
         * 
         * @global type $lr_google_analytics_settings
         */
        public static function set_default_options($blog_id) {
            global $lr_google_analytics_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LR_Google_Analytics_Settings')) {
                    update_blog_option($blog_id, 'LR_Google_Analytics_Settings', self::$options);
                    $lr_google_analytics_settings = get_blog_option($blog_id, 'LR_Google_Analytics_Settings');
                }
            } else {
                if (!get_option('LR_Google_Analytics_Settings')) {
                    update_option('LR_Google_Analytics_Settings', self::$options);
                    $lr_google_analytics_settings = get_option('LR_Google_Analytics_Settings');
                }
            }
        }

    }

    new LR_Google_Analytics_Install();
}
