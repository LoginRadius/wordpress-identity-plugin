<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Custom_Interface_Install')) {

    /**
     * class responsible for setting default settings for custom interface.
     */
    class LR_Custom_Interface_Install {

        /**
         * Loads global custom interface options used for init and reset.
         *
         * @global custom interface_options
         */
        private static $lr_custom_interface_options;

        /**
         * Constructor
         */
        public function __construct() {

            add_action('wp_ajax_upload_custom_interface_image', array($this, 'upload_custom_interface_image'));
        }

        /**
         * Function for adding default custom interface settings at activation.
         */
        public static function set_default_options() {
            global $lr_custom_interface_settings;
            if (!get_option('LR_Custom_Interface_Settings')) {
                //Set custom interface options
                self::$lr_custom_interface_options = array(
                    'custom_interface' => '1',
                    'providers' => array('amazon', 'aol', 'disqus', 'facebook', 'foursquare', 'github', 'google', 'hyves', 'instagram', 'kaixin', 'linkedin', 'live', 'livejournal', 'mailru', 'mixi', 'myspace', 'odnoklassniki', 'openid', 'orange', 'paypal', 'persona', 'qq', 'renren', 'salesforce', 'sinaweibo', 'stackexchange', 'steam', 'steamcommunity', 'tumblr', 'twitter', 'verisign', 'virgilio', 'vkontakte', 'wordpress', 'xing', 'yahoo'),
                    'selected_providers' => self::get_providers()
                );
                update_option('LR_Custom_Interface_Settings', self::$lr_custom_interface_options);
            }
            $lr_custom_interface_settings = get_option('LR_Custom_Interface_Settings');
            self::create_ci_image();
        }

        public static function create_ci_image() {
            $custom_interface_dir = LR_CUSTOM_INTERFACE_DIR . 'assets/images/custom_interface';

            require_once( LR_CUSTOM_INTERFACE_DIR . 'includes/helper/ajax_helper.php' );
            $ajax = new LR_CI_Ajax_Helper();

            $create_images = false;
            if (!file_exists($custom_interface_dir)) {
                wp_mkdir_p($custom_interface_dir);
                $create_images = true;
            }

            if (is_writable($custom_interface_dir)) {
                if (is_multisite()) {
                    if (is_main_site()) {
                        global $loginradius_api_settings;
                        if (isset($loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1') {
                            $blogs = wp_get_sites();
                            foreach ($blogs as $blog) {
                                if (!file_exists($custom_interface_dir . '/' . $blog['blog_id'])) {
                                    wp_mkdir_p($custom_interface_dir . '/' . $blog['blog_id']);
                                    $ajax->move_default_files($custom_interface_dir . '/' . $blog['blog_id']);
                                }
                            }
                        }
                    } else {
                        if (!file_exists($custom_interface_dir . '/' . get_current_blog_id())) {
                            wp_mkdir_p($custom_interface_dir . '/' . get_current_blog_id());
                            LR_CI_Ajax_Helper::move_default_files($custom_interface_dir . '/' . get_current_blog_id(), $default_interface_dir);
                        }
                    }
                } else {
                    if ($create_images) {
                        $ajax->move_default_files($custom_interface_dir);
                    }
                }
            }
        }

        public static function get_providers() {
            global $loginradius_api_settings;
            $listedProviders = null;
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? $loginradius_api_settings['LoginRadius_apikey'] : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? $loginradius_api_settings['LoginRadius_secret'] : '';
            
            if (!empty($apikey) && !empty($secret)) {             
                $getProviderObject = new \LoginRadiusSDK\SocialLogin\GetProvidersAPI($apikey, $secret, array('authentication' => false, 'output_format' => 'json'));
                try {
                    $providers = json_decode($getProviderObject->getProvidersList(), true);
                    $providersName = isset($providers['Providers']) ? $providers['Providers'] : array();
                    foreach ($providersName as $key => $value) {
                        $listedProviders[] = $value['Name'];
                    }
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    return null;
                }
            }
            return $listedProviders;
        }

        public static function get_selected_providers() {
            global $loginradius_api_settings;
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? $loginradius_api_settings['LoginRadius_apikey'] : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? $loginradius_api_settings['LoginRadius_secret'] : '';
            $provider_list = array('amazon', 'aol', 'disqus', 'facebook', 'foursquare', 'github', 'google', 'hyves', 'instagram', 'kaixin', 'linkedin', 'live', 'livejournal', 'mailru', 'mixi', 'myspace', 'odnoklassniki', 'openid', 'orange', 'paypal', 'persona', 'qq', 'renren', 'salesforce', 'sinaweibo', 'stackexchange', 'steam', 'steamcommunity', 'tumblr', 'twitter', 'verisign', 'virgilio', 'vkontakte', 'wordpress', 'xing', 'yahoo');
            if (!empty($apikey) && !empty($secret)) {
                $lrproviders = self::get_providers();
                if(is_array($lrproviders) && count($lrproviders)>0){
                    $provider_list = $lrproviders;
                }
            }
            return $provider_list;
        }

        /**
         * Function to reset custom interface options to default.
         */
        public static function reset_lr_custom_interface_options() {
            global $lr_custom_interface_settings;

            require_once( LR_CUSTOM_INTERFACE_DIR . 'includes/helper/ajax_helper.php' );
            $ajax = new LR_CI_Ajax_Helper();
            $response = $ajax->reset_ci_folder();
            do_action('lr_reset_admin_action', 'LR_Custom_Interface_Settings', self::$lr_custom_interface_options);

            // Get custom interface settings
            $lr_custom_interface_settings = get_option('LR_Custom_Interface_Settings');
            return $response;
        }

        public function upload_custom_interface_image() {
            require_once( LR_CUSTOM_INTERFACE_DIR . 'includes/helper/ajax_helper.php' );
            $ajax = new LR_CI_Ajax_Helper();
            if ($ajax->check_max_upload()) {
                echo $ajax->upload_handler();
            } else {
                _e('php.ini does not allow file uploading.', 'lr-plugin-slug');
            }
            die();
        }

    }

    new LR_Custom_Interface_Install();
}