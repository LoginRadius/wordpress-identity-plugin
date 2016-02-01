<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('SalesforceClient')) {

    class SalesforceClient {

        static protected $url = 'https://login.salesforce.com/services/oauth2/token';

        /**
         * 
         * @param type $params
         * @return boolean
         */
        static function get_access_token($params) {
            $args = array('headers' => array("Content-Type" => "application/x-www-form-urlencoded"), 'body' => $params);
            return self::wp_remote_error_handler(wp_remote_post(self::$url, $args));
        }
        
        /**
         * 
         * @param type $instance
         * @param type $accessToken
         * @param type $type
         * @return type
         */
        static function get_salesforce_object_fields($instance, $accessToken, $type) {
            $url = $instance . '/services/data/v29.0/sobjects/' . $type . '/describe/';
            $args = array('headers' => array("Authorization" => "OAuth $accessToken", "Content-type" => "json/xml"));
            return self::wp_remote_error_handler(wp_remote_get($url, $args));
        }
/**
         * 
         * @param type $instance
         * @param type $accessToken
         * @param type $data
         * @param type $id
         * @param type $type
         * @return type
         */
        static function update_account_at_salesforce($instance, $accessToken, $data, $id, $type) {
            $url = $instance . '/services/data/v29.0/sobjects/' . $type . '/' . $id;
            $args = array('method'=>'PATCH','headers' => array("Authorization" => "OAuth $accessToken", "Content-type" => "application/json"),'body' => $data);
            return self::wp_remote_error_handler(wp_remote_post($url, $args));
        }
        /**
         * 
         * @param type $instance
         * @param type $accessToken
         * @param type $data
         * @param type $type
         * @return type
         */
        static function create_salesforce_object($instance, $accessToken, $data, $type = 'Lead') {
            $url = $instance . '/services/data/v29.0/sobjects/' . $type . '/';
            $args = array('headers' => array("Authorization" => "OAuth $accessToken", "Content-type" => "application/json"),'body' => $data);
            return self::wp_remote_error_handler(wp_remote_post($url, $args));
        }
        /**
         * 
         * @param type $response
         * @return type
         */
        static function wp_remote_error_handler($response){
            if (is_wp_error($response)) {
                return "Something went wrong: " . $response->get_error_message();
            } else {
                return json_decode($response['body']);
            }
        }
    }

}
