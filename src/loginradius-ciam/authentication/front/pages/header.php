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
            global $ciam_setting,$ciam_credentials;

            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Social/SocialAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Authentication/AuthenticationAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Account/AccountAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Advanced/MultiFactorAuthenticationAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Account/SottAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Utility/Functions.php');
      
                   
            if(!isset($ciam_credentials['apikey']) || empty($ciam_credentials['apikey']) || !isset($ciam_credentials['secret']) || empty($ciam_credentials['secret'])){
                 return;  
            }
          
            wp_enqueue_script('ciam', '//auth.lrcontent.com/v2/js/LoginRadiusV2.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
            wp_enqueue_script('ciam_fucntions', CIAM_PLUGIN_URL . 'authentication/assets/js/custom.min.js', array('ciam'), CIAM_PLUGIN_VERSION);
            wp_enqueue_style('ciam-style', CIAM_PLUGIN_URL . 'authentication/assets/css/style.min.css', CIAM_PLUGIN_VERSION);
           
        }

    }

    new CIAM_Authentication_Header();
}