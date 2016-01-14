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
            'sso_enable' => ''
        );

        /**
         * Constructor
         */
        public function __construct() {
            $this->set_default_options();
        }

        /**
         * Function to add default SSO settings at activation.
         */
        public static function set_default_options() {
            global $lr_sso_settings;

            if (!get_option('LR_SSO_Settings')) {
                update_option('LR_SSO_Settings', self::$sso_options);
                $lr_sso_settings = get_option('LR_SSO_Settings');
            }
        }

        /**
         * Function to reset SSO options to default.
         */
        public static function reset_sso_options() {
            global $lr_sso_settings;
            do_action('lr_reset_admin_action','LR_SSO_Settings', self::$sso_options);
            // Get SSO settings
            $lr_sso_settings = get_option('LR_SSO_Settings');
        }

    }

    new LR_SSO_Install();
}
