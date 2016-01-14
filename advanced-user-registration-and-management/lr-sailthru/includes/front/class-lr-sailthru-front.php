<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius Sailthru.
 */
if (!class_exists('LR_Sailthru_Front')) {

    class LR_Sailthru_Front {

        public static function on_load() {
            add_action('init', array(get_class(), 'init'));
        }

        /**
         * 
         * @global type $lr_sailthru_settings
         */
        public static function init() {
            global $lr_sailthru_settings;
            if (isset($lr_sailthru_settings['sailthru_enable']) && $lr_sailthru_settings['sailthru_enable'] == '1') {
                //register a user
                add_action('lr_create_social_profile_data', array(get_class(), 'user_profile_sailthruapi'), 10, 1);
                //update a user
                if (isset($lr_sailthru_settings['sailthru_userprofile_update']) && $lr_sailthru_settings['sailthru_userprofile_update'] == '1') {
                    add_action('lr_update_social_profile_data', array(get_class(), 'user_profile_sailthruapi'), 10, 2);
                }
            }
        }

        /**
         * 
         * @global type $lr_sailthru_settings
         * @param type $userId
         */
        public static function user_profile_sailthruapi($userId) {
            global $lr_sailthru_settings;
            $mappingFields = array();
            $sailthru_api_key = isset($lr_sailthru_settings['sailthru_api_key']) ? $lr_sailthru_settings['sailthru_api_key'] : '';
            $sailthru_api_secret = isset($lr_sailthru_settings['sailthru_api_secret']) ? $lr_sailthru_settings['sailthru_api_secret'] : '';
            $subscriberLists = isset($lr_sailthru_settings['sailthru_subscriber_lists']) ? $lr_sailthru_settings['sailthru_subscriber_lists'] : array();
            $sailthruValueFields = isset($lr_sailthru_settings['sailthru_value_fields']) ? $lr_sailthru_settings['sailthru_value_fields'] : array('');
            $sailthruLabelFields = isset($lr_sailthru_settings['sailthru_label_fields']) ? $lr_sailthru_settings['sailthru_label_fields'] : array('email');
            if (count($sailthruLabelFields) > 0) {
                foreach ($sailthruLabelFields as $label => $value) {
                    $mappingFields[$value] = isset($sailthruValueFields[$label]) ? $sailthruValueFields[$label] : '';
                }
            }

            $data = self::sailthru_user_profile_mapping_fields($userId, $mappingFields, $subscriberLists);
            $obj_sailthru = new WP_LR_Sailthru_Client($sailthru_api_key, $sailthru_api_secret);
            $responce = $obj_sailthru->saveUser($data['email'], $data['options']);
        }

        /**
         * 
         * @param type $userId
         * @param type $mappingFields
         * @param type $subscriberLists
         * @return boolean
         */
        public static function sailthru_user_profile_mapping_fields( $userId, $mappingFields, $subscriberLists ) {
            $user_info = get_userdata( $userId );
            $data['options'] = array();
            $data['email'] = isset( $user_info->user_email ) ? $user_info->user_email : '';

            if (is_array( $mappingFields) && count( $mappingFields ) > 0) {
                foreach ( $mappingFields as $label => $value) {
                    $data['options']['vars'][$label] = self::get_user_profile_data( $userId, $value );
                }
            }

            if (is_array( $subscriberLists) && count( $subscriberLists ) > 0) {
                foreach ( $subscriberLists as $k => $subscriberList ) {
                    $data['options']['lists'][$subscriberList] = true;
                }
            }

            return $data;
        }

        /**
         * 
         * @global type $wpdb
         * @param type $userId
         * @param type $field
         * @return type
         */
        static function get_user_profile_data($userId, $field) {
            global $wpdb;
            $tempParts = explode('|', $field);
            $value = '';
            // if field is from any separate profile data table
            if (count($tempParts) > 1) {
                // execute query according to the prefix
                switch ($tempParts[0]) {
                    // basic_profile_data table
                    case 'basic':
                        $value = $wpdb->get_var('SELECT ' . $tempParts[1] . ' FROM ' . $wpdb->base_prefix . 'lr_basic_profile_data WHERE wp_users_id = ' . $userId);
                        break;
                    // extended_location_data table
                    case 'exloc':
                        $value = $wpdb->get_var('SELECT ' . $tempParts[1] . ' FROM ' . $wpdb->base_prefix . 'lr_extended_location_data WHERE wp_users_id = ' . $userId);
                        break;
                    // extended_profile_data table
                    case 'exprofile':
                        $value = $wpdb->get_var('SELECT ' . $tempParts[1] . ' FROM ' . $wpdb->base_prefix . 'lr_extended_profile_data WHERE wp_users_id = ' . $userId);
                        break;
                }
            } else {
                $userInfo = get_userdata($userId);
                // Get data according to the value.
                switch ($tempParts[0]) {
                    case 'User ID':
                        $value = $userId;
                        break;
                    case 'Username':
                        $value = isset( $userInfo->user_login ) ? $userInfo->user_login : '';
                        break;
                    case 'First Name':
                    case 'Display Name':
                    case 'Nicename':
                        $value = isset( $userInfo->first_name ) ? $userInfo->first_name : '';
                        break;
                    case 'Last Name':
                        $value = isset( $userInfo->last_name ) ? $userInfo->last_name : '';
                        break;
                    case 'Email':
                        $value = isset( $userInfo->user_email ) ? $userInfo->user_email : '';
                        break;
                    case 'Profile Url':
                        $value = isset( $userInfo->user_url ) ? $userInfo->user_url : '';
                        break;
                    case 'Registration Date':
                        $value = isset( $userInfo->data->user_registered ) ? $userInfo->data->user_registered : '';
                        break;
                }
            }
            return $value == NULL ? '' : $value;
        }

    }

    LR_Sailthru_Front::on_load();
}
