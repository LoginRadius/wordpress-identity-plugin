<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Custom_Obj_Install' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Custom_Obj_Install {

        /**
         * Constructor
         */
        public function __construct() {
            add_filter( 'lr_raas_reset_setting', array( $this, 'reset_options' ) );
        }
        
        /**
         * 
         * @global type $lr_Custom_Obj_Fields
         * @return string
         */
        public static function custom_obj_options() {
            global $lr_Custom_Obj_Fields;
            $custom_object_settings = array( 'enable_custom_obj' => '', 'custom_obj_id' => '' );
            foreach ($lr_Custom_Obj_Fields as $field) {
                $custom_object_settings['show_custom_' . $field] = '';
                $custom_object_settings['custom_' . $field . '_required'] = '';
                $custom_object_settings['custom_' . $field . '_title'] = '';
            }
            return $custom_object_settings;
        }

        public function reset_options() {
            update_option('LR_Raas_Custom_Obj_Settings', self::custom_obj_options());
        }

        /**
         * Function for adding default Custom Object settings at activation.
         * 
         * @global type $wpdb
         * @global type $lr_raas_custom_obj_settings
         */
        public static function set_default_options() {
            global $wpdb, $lr_raas_custom_obj_settings;

            if ( ! get_option('LR_Raas_Custom_Obj_Settings') ) {
                update_option( 'LR_Raas_Custom_Obj_Settings', self::custom_obj_options() );
            }
            $lr_raas_custom_obj_settings = get_option( 'LR_Raas_Custom_Obj_Settings' );

            $wpdb->query( 'CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_custom_object_data` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `field_title` varchar( 100 ) DEFAULT NULL,
                `field_value` varchar( 100 ) DEFAULT NULL
			 )');
        }
    }
}