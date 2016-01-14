<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Sailthru_Install')) {

    /**
     * class responsible for setting default settings for loginradius sailthru.
     */
    class LR_Sailthru_Install {

        /**
         * Default $sailthru_options array
         * @var array
         */
        static $sailthru_options = array(
            'sailthru_enable' => '0',
            'sailthru_api_key' => '',
            'sailthru_api_secret' => '',
            'sailthru_subscriber_lists' => '',
            'sailthru_label_fields' => '',
            'sailthru_value_fields' => '',
            'sailthru_userprofile_update' => ''
        );

        /**
         * Constructor
         */
        public function __construct() {
            $this->set_default_options();
        }

        /**
         * Function to add default Sailthru settings at activation.
         * 
         * @global type $lr_sailthru_settings
         */
        public static function set_default_options() {
            global $lr_sailthru_settings;

            if (!get_option('LR_Sailthru_Settings')) {
                update_option('LR_Sailthru_Settings', self::$sailthru_options);
                $lr_sailthru_settings = get_option('LR_Sailthru_Settings');
            }
        }

        /**
         * Function to reset Sailthru options to default.
         */
        public static function reset_sailthru_options() {
            global $lr_sailthru_settings;
            do_action('lr_reset_admin_action','LR_Sailthru_Settings', self::$sailthru_options);
            // Get Sailthru settings
            $lr_sailthru_settings = get_option('LR_Sailthru_Settings');
        }

    }

    new LR_Sailthru_Install();
}
