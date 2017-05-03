<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('LR_Commenting_Install')) {

    /**
     * class responsible for setting default settings for social commenting.
     */
    class LR_Commenting_Install {

        private static $options = array(
            'commenting_enable' => '0',
            'image_upload_enable' => '1',
            'sharing_enable' => '1',
            'editor_enable' => '1',
            'enable_moderation_msg' => '1',
            'no_comment_msg' => 'Please type a comment.',
            'moderation_msg' => 'Your comment is awaiting moderation',
            'commenting_title' => 'Leave a Reply',
            'display_comment_type' => 'comment',
            'approve_social_user_comments' => '1',
            'approve_wp_user_comments' => '1'
        );

        /**
         * Function for adding default commenting settings at activation.
         */
        public static function set_default_options($blog_id) {
            global $lr_commenting_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LR_Commenting_Settings')) {
                    update_blog_option($blog_id, 'LR_Commenting_Settings', self::$options);
                }

                $lr_commenting_settings = get_blog_option($blog_id, 'LR_Commenting_Settings');
            } else {
                if (!get_option('LR_Commenting_Settings')) {
                    update_option('LR_Commenting_Settings', self::$options);
                }

                $lr_commenting_settings = get_option('LR_Commenting_Settings');
            }
        }

    }

    new LR_Commenting_Install();
}
