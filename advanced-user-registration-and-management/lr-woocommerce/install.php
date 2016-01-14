<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Woocommerce_Install')) {

    /**
     * class responsible for setting default settings for loginradius woocommerce.
     */
    class LR_Woocommerce_Install {

        /**
         * Default $woocommerce_options array
         * @var array
         */
        static $woocommerce_options = array(
            'woocommerce_enable' => '',
            'woocommerce_update_checkout' => ''
        );
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->set_default_options();
        }

        /**
         * Function to add default Woocommerce settings at activation.
         */
        public static function set_default_options() {
            global $lr_woocommerce_settings;

            if (!get_option('LR_Woocommerce_Settings')) {
                update_option('LR_Woocommerce_Settings', self::$woocommerce_options);
                $lr_woocommerce_settings = get_option('LR_Woocommerce_Settings');
            }
        }

        /**
         * Function to reset Woocommerce options to default.
         */
        public static function reset_woocommerce_options() {
            global $lr_woocommerce_settings;

            update_option('LR_Woocommerce_Settings', self::$woocommerce_options);
            // Get Woocommerce settings
            $lr_woocommerce_settings = get_option('LR_Woocommerce_Settings');
        }

    }

    new LR_Woocommerce_Install();
}
