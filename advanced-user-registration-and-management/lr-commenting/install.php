<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}
if ( ! class_exists( 'LR_Commenting_Install' ) ) {

    /**
     * class responsible for setting default settings for social commenting.
     */
    class LR_Commenting_Install {

        /**
         * Constructor
         */
        public function __construct() {
            $this->commenting_options();
            $this->set_default_options();
        }

        /**
         * Loads global commenting options used for init and reset.
         *
         * @global commenting_options
         */
        private function commenting_options() {
            global $commenting_options;

            $commenting_options = array(
                'commenting_enable' => '',
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
        }

        /**
         * Function for adding default commenting settings at activation.
         */
        public static function set_default_options() {
            global $lr_commenting_settings, $commenting_options;

            if ( ! get_option( 'LR_Commenting_Settings' ) ) {
                update_option( 'LR_Commenting_Settings', $commenting_options );
                $lr_commenting_settings = get_option( 'LR_Commenting_Settings' );
            }
        }

        /**
         * Function to reset Social Commenting options to default.
         */
        public static function reset_loginradius_commenting_options() {
            global $lr_commenting_settings, $commenting_options;

            update_option('LR_Commenting_Settings', $commenting_options);
            // Get commenting settings
            $lr_commenting_settings = get_option('LR_Commenting_Settings');
        }

    }

    new LR_Commenting_Install();
}
