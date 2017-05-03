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
         * Function for adding default custom interface settings at activation.
         */
        public static function set_default_options($blog_id) {
            global $lr_custom_interface_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LR_Custom_Interface_Settings')) {
                    //Set custom interface options
                    self::$lr_custom_interface_options = array(
                        'custom_interface' => '0',
                        'providers' => array('amazon', 'aol', 'disqus', 'facebook', 'foursquare', 'github', 'google', 'hyves', 'instagram', 'kaixin', 'linkedin', 'live', 'livejournal', 'mailru', 'mixi', 'myspace', 'odnoklassniki', 'openid', 'orange', 'paypal', 'persona', 'qq', 'renren', 'salesforce', 'sinaweibo', 'stackexchange', 'steam', 'steamcommunity', 'tumblr', 'twitter', 'verisign', 'virgilio', 'vkontakte', 'wordpress', 'xing', 'yahoo'),
                        'selected_providers' => LR_Custom_Interface_Admin::get_providers()
                    );
                    update_blog_option($blog_id, 'LR_Custom_Interface_Settings', self::$lr_custom_interface_options);
                }
                $lr_custom_interface_settings = get_blog_option($blog_id, 'LR_Custom_Interface_Settings');
                
            }else{
                if (!get_option('LR_Custom_Interface_Settings')) {
                    //Set custom interface options
                    self::$lr_custom_interface_options = array(
                        'custom_interface' => '0',
                        'providers' => array('amazon', 'aol', 'disqus', 'facebook', 'foursquare', 'github', 'google', 'hyves', 'instagram', 'kaixin', 'linkedin', 'live', 'livejournal', 'mailru', 'mixi', 'myspace', 'odnoklassniki', 'openid', 'orange', 'paypal', 'persona', 'qq', 'renren', 'salesforce', 'sinaweibo', 'stackexchange', 'steam', 'steamcommunity', 'tumblr', 'twitter', 'verisign', 'virgilio', 'vkontakte', 'wordpress', 'xing', 'yahoo'),
                        'selected_providers' => LR_Custom_Interface_Admin::get_providers()
                    );
                    update_option('LR_Custom_Interface_Settings', self::$lr_custom_interface_options);
                }
                $lr_custom_interface_settings = get_option('LR_Custom_Interface_Settings');
            }
            self::create_ci_image($blog_id);
        }

        public static function create_ci_image($blog_id) {
            $default_interface_dir = LR_ROOT_DIR . 'lr-custom-interface/assets/images/default_interface';
            $custom_interface_dir = LR_ROOT_DIR . 'lr-custom-interface/assets/images/custom_interface';

            require_once( LR_ROOT_DIR . 'lr-custom-interface/includes/helper/ajax_helper.php' );
            $ajax = new LR_CI_Ajax_Helper();

            $create_images = false;
            if (!file_exists($custom_interface_dir)) {
                wp_mkdir_p($custom_interface_dir);
                $create_images = true;
            }

            if (is_writable($custom_interface_dir)) {
                if ($blog_id) {
                    if (!file_exists($custom_interface_dir . '/' . $blog_id)) {
                        wp_mkdir_p($custom_interface_dir . '/' . $blog_id);
                        LR_CI_Ajax_Helper::move_default_files($custom_interface_dir . '/' . $blog_id, $default_interface_dir);
                    }
                } else {
                    if ($create_images) {
                        $ajax->move_default_files($custom_interface_dir);
                    }
                }
            }
        }

    }

    new LR_Custom_Interface_Install();
}