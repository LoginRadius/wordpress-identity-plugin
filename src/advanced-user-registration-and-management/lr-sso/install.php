<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_SSO_Install')) {

    /**
     * class responsible for setting default settings for loginradius sso.
     */
    class LR_SSO_Install {

        /**
         * Default $sso_options array
         * @var array
         */
        static $sso_options = array(
            'sso_enable' => '0'
        );

        /**
         * Function to add default SSO settings at activation.
         */
        public static function set_default_options($blog_id) {
            global $lr_sso_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LR_SSO_Settings')) {
                    update_blog_option($blog_id, 'LR_SSO_Settings', self::$sso_options);
                }
                $lr_sso_settings = get_blog_option($blog_id, 'LR_SSO_Settings');
            } else {
                if (!get_option('LR_SSO_Settings')) {
                    update_option('LR_SSO_Settings', self::$sso_options);
                }
                $lr_sso_settings = get_option('LR_SSO_Settings');
            }
        }

    }

    new LR_SSO_Install();
}
