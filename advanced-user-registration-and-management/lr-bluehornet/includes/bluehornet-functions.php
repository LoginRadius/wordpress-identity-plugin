<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_BlueHornet_function' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_BlueHornet_function {

        function __construct() {
            add_action('lr_create_social_profile_data', array($this,'get_data_for_bluehornet'));
            if (is_admin()) {
                // ajax for connection handler detection
                add_action('wp_ajax_login_radius_bluehornet_segment', array($this,'login_radius_bluehornet_segment'));
            }
        }

        /*
         * Get Bluehoenet Custom fields
         * getCustomFields   getStaticSegments
         *
         */
        function getBluehornet( $getAction, $api = '', $secret = '' ) {
            $lr_bluehornet_settings = get_option('LR_BlueHornet_Settings');
            $object = new LoginRadius();
            $api = !empty($api) ? trim($api) : trim($lr_bluehornet_settings['bluehornet_key']);
            $secret = !empty($secret) ? trim($secret) : trim($lr_bluehornet_settings['bluehornet_secret']);
            $url = 'https://echo3.bluehornet.com/api/xmlrpc/index.php?' . http_build_query(array('data' => '<api><authentication><api_key>' . $api . '</api_key><shared_secret>' . $secret . '</shared_secret><response_type>xml</response_type></authentication><data><methodCall><methodName>account.' . $getAction . '</methodName></methodCall></data></api>'));
            $xmlObject = $object->loginradius_api_client($url);
            $xml = $this->loginRadiusxmltoobject($xmlObject);
            
            if ($getAction == 'getStaticSegments') {
                $segments = $xml->item->responseData->static_segments->item;
            } elseif ($getAction == 'getCustomFields') {
                $segments = $xml->item->responseData->custom_fields->item;
            }
            $result = array();
            if (!empty($segments)) {
                foreach ($segments as $segment) {
                    $result[(string) $segment->id] = (string) $segment->name;
                }
            }
            return $result;
        }

        /*
         * Subscriber user on Bluehornet
         */
        function subscriberBluehornetmanage($merge_vars, $email) {
            if (!isset($email) || empty($email)) {
                return false;
            }
            $lr_bluehornet_settings = get_option('LR_BlueHornet_Settings');
            $object = new LoginRadius();
            $url = 'https://echo3.bluehornet.com/api/xmlrpc/index.php?' . http_build_query(array('data' => '<api><authentication><api_key>' . trim($lr_bluehornet_settings['bluehornet_key']) . '</api_key><shared_secret>' . trim($lr_bluehornet_settings['bluehornet_secret']) . '</shared_secret><response_type>xml</response_type></authentication><data><methodCall><methodName>legacy.manage_subscriber</methodName><email>' . $email . '</email>' . $merge_vars . '</methodCall></data></api>'));
            $object->loginradius_api_client($url);
        }

        /*
         * Convert xml to object
         */
        function loginRadiusxmltoobject( $xml ) {
            return simplexml_load_string($xml);
        }

        /**
         * verify API connection method
         */
        function login_radius_bluehornet_segment() {
            $key = isset($_POST['key']) ? trim($_POST['key']) : '';
            $secret = isset($_POST['secret']) ? trim($_POST['secret']) : '';
            $result = array();
            $result['Segments'] = $this->getBluehornet('getStaticSegments', $key, $secret);
            $result['CustomFields'] = $this->getBluehornet('getCustomFields', $key, $secret);
            die(json_encode($result));
        }

        /**
         * get_data_for_bluehornet
         * @param  string $user_id
         */
        function get_data_for_bluehornet( $user_id ) {
            $lr_bluehornet_settings = get_option('LR_BlueHornet_Settings');
            global $wpdb;
            if ( isset( $lr_bluehornet_settings['bluehornet_subscribe'] ) && ! empty( $lr_bluehornet_settings['bluehornet_subscribe']) && $lr_bluehornet_settings['bluehornet_subscribe'] == '1') {
                if ( isset( $lr_bluehornet_settings['bluehornet_key'] ) && ! empty( $lr_bluehornet_settings['bluehornet_key']) && isset($lr_bluehornet_settings['bluehornet_secret']) && !empty($lr_bluehornet_settings['bluehornet_secret'])) {
                    $merge_vars = '';
                    // fetch user info
                    $userInfo = get_userdata($user_id);
                    foreach ($lr_bluehornet_settings['bluehornet_fields_titles'] as $lrkey => $lrvalue) {
                        // if value exists for this merge var
                        if (isset($lr_bluehornet_settings['bluehornet_mapping_fields'][$lrvalue])) {
                            $tempParts = explode('|', $lr_bluehornet_settings['bluehornet_mapping_fields'][$lrvalue]);
                            $value = "";
                            // if field is from any separate profile data table
                            if (count($tempParts) > 1) {
                                // execute query according to the prefix
                                switch ($tempParts[0]) {
                                // basic_profile_data table
                                    case 'basic':
                                        $value = $wpdb->get_var("SELECT " . $tempParts[1] . " FROM " . $wpdb->base_prefix . "lr_basic_profile_data WHERE wp_users_id = " . $user_id);
                                        break;
                                        // extended_location_data table
                                    case 'exloc':
                                        $value = $wpdb->get_var("SELECT " . $tempParts[1] . " FROM " . $wpdb->base_prefix . "lr_extended_location_data WHERE wp_users_id = " . $user_id);
                                        break;
                                        // extended_profile_data table
                                    case 'exprofile':
                                        $value = $wpdb->get_var("SELECT " . $tempParts[1] . " FROM " . $wpdb->base_prefix . "lr_extended_profile_data WHERE wp_users_id = " . $user_id);
                                        break;
                                }
                            } else {
                                /* native wordpress profile fields */

                                // get data according to the value
                                switch ($tempParts[0]) {
                                    case 'User ID':
                                        $value = $user_id;
                                        break;
                                    case 'Username':
                                        $value = $userInfo->user_nicename;
                                        break;
                                    case 'First Name':
                                    case 'Display Name':
                                        $value = $userInfo->first_name;
                                        break;
                                    case 'Last Name':
                                        $value = $userInfo->last_name;
                                        break;
                                    case 'Nice Name':
                                        $value = sanitize_title($userInfo->first_name);
                                        break;
                                    case 'Email':
                                        $value = $userInfo->user_email;
                                        break;
                                    case 'Profile Url':
                                        $value = $userInfo->user_url;
                                        break;
                                    case 'Registration Date':
                                        $value = $userInfo->data->user_registered;
                                        break;
                                    case 'Bio':
                                        $value = $userInfo->description;
                                        break;
                                    case 'Phone':
                                        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_phone_numbers'") == $wpdb->base_prefix . "lr_phone_numbers") {
                                            $value = $wpdb->get_var("SELECT phone_number from " . $wpdb->base_prefix . "lr_phone_numbers WHERE phone_number != '' and user_id = " . $user_id . " limit 1");
                                        }
                                        break;
                                    case 'Postal Code':
                                        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_addresses'") == $wpdb->base_prefix . "lr_addresses") {
                                            $value = $wpdb->get_var("SELECT postal_code from " . $wpdb->base_prefix . "lr_addresses WHERE postal_code != '' and user_id = " . $user_id . " limit 1");
                                        }
                                        break;
                                }
                            }
                        } else /* value for this merge var does not exist in database */ {
                            $value = "";
                        }
                        if ($value != '') {
                            $merge_vars .= '<' . $lrvalue . '>' . $value . '</' . $lrvalue . '>';
                        }
                    }
                    if (isset($lr_bluehornet_settings['bluehornet_static_segments']) && !empty($lr_bluehornet_settings['bluehornet_static_segments'])) {
                        $merge_vars .= '<grp>' . implode(', ', $lr_bluehornet_settings['bluehornet_static_segments']) . '</grp>';
                    }

                    $this->subscriberBluehornetmanage($merge_vars, $userInfo->user_email);
                }
            }
        }

    }
new LR_BlueHornet_function();
}