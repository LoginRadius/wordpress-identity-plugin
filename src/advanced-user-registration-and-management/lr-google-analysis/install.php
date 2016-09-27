<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Google_Analytics_Install' ) ) {

    /**
     * class responsible for setting default settings for loginradius Google Analytics.
     */
    class LR_Google_Analytics_Install {

        /**
         * $ga_options
         * @var type 
         */
        private static $ga_options = array(
            'ga_enable' => '',
            'ga_tracking_id' => ''
        );

        /**
         * Constructor
         */
        public function __construct() {
            $this->set_default_options();
        }

        /**
         * Function to add default Google Analytics settings at activation.
         * 
         * @global type $lr_google_analytics_settings
         */
        public static function set_default_options() {
            global $lr_google_analytics_settings;

            if ( !get_option( 'LR_Google_Analytics_Settings' ) ) {
                update_option( 'LR_Google_Analytics_Settings', self::$ga_options);
                $lr_google_analytics_settings = get_option( 'LR_Google_Analytics_Settings' );
            }
        }

        /**
         * Function to reset Google Analytics options to default.
         * 
         * @global type $lr_google_analytics_settings
         */
        public static function reset_google_analytics_options() {
            global $lr_google_analytics_settings;
            do_action( 'lr_reset_admin_action','LR_Google_Analytics_Settings', self::$ga_options);
            // Get GA settings
            $lr_google_analytics_settings = get_option( 'LR_Google_Analytics_Settings' );
        }

    }
    new LR_Google_Analytics_Install();
}
