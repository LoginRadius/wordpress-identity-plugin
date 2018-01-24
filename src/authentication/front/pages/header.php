<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('CIAM_Authentication_Header')) {

    class CIAM_Authentication_Header {
        /*
         * class constructor function
         */

        public function __construct() {

            add_action('init', array($this, 'init'));
        }

        /*
         * load required dependencies
         */

        public function init() {
            global $ciam_setting,$ciam_credencials;
            
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Utility/SOTT.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Social/ProvidersAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Social/SocialLoginAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Social/AdvanceSocialLoginAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Authentication/AuthCustomObjectAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Authentication/UserAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/AccountAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/RoleAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/CustomObjectAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/SchemaAPI.php');
             
            if(!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])){
                 return;  
            }
            
            wp_enqueue_script('ciam', '//auth.lrcontent.com/v2/js/LoginRadiusV2.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
            // switching the minified version of js and css file 
            if (isset($ciam_setting['disable_minified_version']) && ($ciam_setting['disable_minified_version'] == '1')) {
                wp_enqueue_script('ciam_fucntions', CIAM_PLUGIN_URL . 'authentication/assets/js/custom.min.js', array('ciam'), CIAM_PLUGIN_VERSION);
                wp_enqueue_style('ciam-style', CIAM_PLUGIN_URL . 'authentication/assets/css/style.min.css', CIAM_PLUGIN_VERSION);
            } else {
                wp_enqueue_script('ciam_fucntions', CIAM_PLUGIN_URL . 'authentication/assets/js/custom.js', array('ciam'), CIAM_PLUGIN_VERSION);
                wp_enqueue_style('ciam-style', CIAM_PLUGIN_URL . 'authentication/assets/css/style.css', CIAM_PLUGIN_VERSION);
            }
        }

    }

    new CIAM_Authentication_Header();
}