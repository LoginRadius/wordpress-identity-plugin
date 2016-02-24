<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the mailchimp plugin admin.
 */
if ( ! class_exists( 'LR_Raas_Custom_Obj_Admin' ) ) {

    class LR_Raas_Custom_Obj_Admin {
        
        /*
         * Constructor
         */
        public function __construct() {
            add_filter( 'lr_raas_save_setting', array( $this, 'save_setting' ) );
        }

        /**
         * save_setting
         * Saves custom object settings from filter lr_raas_save_setting
         * @global array $lr_raas_custom_obj_settings
         * @global array $lr_Custom_Obj_Fields
         * @param array $settings
         * @return array $settings
         */
        public function save_setting( $settings ) {
            global $lr_raas_custom_obj_settings, $lr_Custom_Obj_Fields;
            
            $custom_object = get_option( 'LR_Raas_Custom_Obj_Settings' );
            $custom_object_settings = array( 'enable_custom_obj', 'custom_obj_id' );
            
            foreach ( $lr_Custom_Obj_Fields as $field ) {
                $custom_object_settings[] = 'show_custom_' . $field;
                $custom_object_settings[] = 'custom_' . $field . '_required';
                $custom_object_settings[] = 'custom_' . $field . '_title';
            }

            // Create custom object settings array and unset from main settings
            foreach ( $custom_object_settings as $custom_object_setting ) {
                $custom_object[ $custom_object_setting ] = isset( $settings[ $custom_object_setting ] ) ? $settings[ $custom_object_setting ] : '';
                unset( $settings[ $custom_object_setting ] );
            }

            // Update custom object settings
            update_option( 'LR_Raas_Custom_Obj_Settings', $custom_object );

            // Reload custom object settings after update
            $lr_raas_custom_obj_settings = get_option( 'LR_Raas_Custom_Obj_Settings' );

            // Return master settings for continued operations
            return $settings;
        }
    }

    new LR_Raas_Custom_Obj_Admin();
}
