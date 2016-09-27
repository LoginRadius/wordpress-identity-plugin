<?php

// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

if ( !class_exists( 'Social_Commenting' ) ) {

    /**
     * Class responsible for implementing Social Commenting functionality
     */
    class Social_Commenting {

        /**
         * Social_Commenting calss instance
         *
         * @var string
         */
        private static $instance;

        /**
         * Get singleton object for class Social_Commenting
         *
         * @return object Social_Commenting
         */
        public static function get_instance() {

            if ( !isset( self::$instance ) && !( self::$instance instanceof Social_Commenting ) ) {
                self::$instance = new Social_Commenting();
            }
            return self::$instance;
        }

        /**
         * Constructor for class Social_Commenting
         */
        public function __construct() {

            $this->register_hook_callback();
        }

        /**
         * callbacks for commenting hooks
         */
        public function register_hook_callback() {
            global $loginRadiusSettings, $user_ID;

            // show social login interface on comment form
            if ( isset( $loginRadiusSettings['LoginRadius_commentEnable'] ) && $loginRadiusSettings['LoginRadius_commentEnable'] == '1' ) {
                if ( get_option( 'comment_registration' ) && intval( $user_ID ) == 0 && $loginRadiusSettings['LoginRadius_commentform'] != '' ) {
                    add_action( 'comment_form_must_log_in_after', array('Login_Helper', 'display_social_login_interface') );
                } elseif ( isset( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) ) {

                    switch ( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) {
                        case 'very_top':
                            $commentHook = 'comment_form_before';
                            break;
                        case 'before_fields':
                            $commentHook = 'comment_form_before_fields';
                            break;
                        case 'after_fields':
                            $commentHook = 'comment_form_after_fields';
                            break;
                        case 'very_bottom':
                            $commentHook = 'comment_form_after';
                            break;
                        case 'after_leave_reply':
                            $commentHook = 'comment_form_top';
                            break;
                        default:
                            $commentHook = 'after_leave_reply';
                    }
                    add_action( $commentHook, array('Login_Helper', 'display_social_login_interface') );
                }
            }
        }

    }

}