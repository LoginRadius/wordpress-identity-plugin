<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('LR_Sharing_Install')) {

    /**
     * class responsible for setting default settings for social invite.
     */
    class LR_Sharing_Install {

        private static $options = array(
            'horizontal_enable' => '1',
            'vertical_enable' => '',
            'horizontal_share_interface' => 'responsive',
            'vertical_share_interface' => '32-v',
            'horizontal_sharing_providers' => array(
                'Default' => array(
                    'Facebook' => 'Facebook',
                    'Email' => 'Email',
                    'Print' => 'Print',
                    'GooglePlus' => 'GooglePlus',
                    'LinkedIn' => 'LinkedIn',
                    'Twitter' => 'Twitter',
                    'Pinterest' => 'Pinterest'
                ),
                'Hybrid' => array(
                    'Facebook Like' => 'Facebook Like',
                    'Twitter Tweet' => 'Twitter Tweet',
                    'Google+ Share' => 'Google+ Share',
                    'Pinterest Pin it' => 'Pinterest Pin it',
                    'LinkedIn Share' => 'LinkedIn Share'
                )
            ),
            'vertical_sharing_providers' => array(
                'Default' => array(
                    'Facebook' => 'Facebook',
                    'Email' => 'Email',
                    'Print' => 'Print',
                    'GooglePlus' => 'GooglePlus',
                    'LinkedIn' => 'LinkedIn',
                    'Twitter' => 'Twitter',
                    'Pinterest' => 'Pinterest'
                ),
                'Hybrid' => array(
                    'Facebook Like' => 'Facebook Like',
                    'Twitter Tweet' => 'Twitter Tweet',
                    'Google+ Share' => 'Google+ Share',
                    'Pinterest Pin it' => 'Pinterest Pin it',
                    'LinkedIn Share' => 'LinkedIn Share'
                )
            ),
            'lr-clicker-hr-home' => '1',
            'lr-clicker-hr-post' => '1',
            'lr-clicker-hr-static' => '1',
            'lr-clicker-hr-excerpts' => '1',
            'lr-clicker-hr-custom' => '',
            'horizontal_position' => array(
                'Home' => array(
                    'Top' => 'Top'
                ),
                'Posts' => array(
                    'Top' => 'Top',
                    'Bottom' => 'Bottom'
                ),
                'Pages' => array(
                    'Top' => 'Top'
                ),
                'Excerpts' => array(
                    'Top' => 'Top'
                )
            ),
            'horizontal_rearrange_providers' => array(
                'Facebook',
                'Twitter',
                'LinkedIn',
                'GooglePlus',
                'Pinterest',
                'Email',
                'Print'
            ),
            'vertical_rearrange_providers' => array(
                'Facebook',
                'Twitter',
                'LinkedIn',
                'GooglePlus',
                'Pinterest',
                'Email',
                'Print'
            ),
            'isTotalShare' => 'true',
            'isOpenSingleWindow' => 'false',
            'mobile_enable' => 'false',
            'shortenUrl' => 'true',
            'emailcontent' => 'false',
            'popupHeightWidth' => ''
        );

        /**
         * Function for adding default social_profile_data settings at activation.
         */
        public static function set_default_options($blog_id) {
            global $loginradius_share_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LoginRadius_share_settings')) {
                    // Adding LoginRadius plugin options if not available.
                    update_blog_option($blog_id, 'LoginRadius_share_settings', self::$options);
                }

                // Get LoginRadius plugin settings.
                $loginradius_share_settings = get_blog_option($blog_id, 'LoginRadius_share_settings');
            } else {
                if (!get_option('LoginRadius_share_settings')) {
                    // Adding LoginRadius plugin options if not available.
                    update_option('LoginRadius_share_settings', self::$options);
                }

                // Get LoginRadius plugin settings.
                $loginradius_share_settings = get_option('LoginRadius_share_settings');
            }
        }

    }

    new LR_Sharing_Install();
}
