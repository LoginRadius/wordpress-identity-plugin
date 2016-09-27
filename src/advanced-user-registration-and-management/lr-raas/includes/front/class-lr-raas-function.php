<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The front function class of LoginRadius Raas.
 */
if ( ! class_exists( 'LR_Raas_Front' ) ) {

    class LR_Raas_Front {

        /**
         * Constructor
         */
        public function __construct() {
            add_action( 'init', array( 'LR_Raas_Social_Login', 'init' ) );
            add_action( 'init', array( $this, 'init' ) );
            add_action( 'init', array( 'LR_Raas_Social_Login', 'custom_page_redirection' ) );

            /* Display Profile field on RaaS */
            add_action( 'show_user_profile', array( 'LR_Raas_Social_Login', 'display_extra_profile_fields' ) );
            add_action( 'edit_user_profile', array( 'LR_Raas_Social_Login', 'display_extra_profile_fields' ) );
            /* Display Profile field on RaaS */
            /* Start add custom field */
            add_action( 'user_new_form', array('LR_Raas_Social_Login', 'custom_fields'));
            /* End add custom field */
            /* Save Profile field on RaaS */
            add_action( 'personal_options_update', array( 'LR_Raas_Social_Login', 'save_extra_profile_fields') );
            add_action( 'edit_user_profile_update', array( 'LR_Raas_Social_Login', 'save_extra_profile_fields') );
            /* Save Profile field on RaaS */
            /* Modify field on Profile page */
            add_action( 'admin_footer-profile.php', array( 'LR_Raas_Social_Login', 'remove_extra_profile_fields' ) );
            /* Modify field on Profile page */
            /* Save RaaS profile data on wp db */
            add_action( 'user_register', array( 'LR_Raas_Social_Login', 'raas_uid_updation' ), 10, 1 );
            /* Save RaaS profile data on wp db */
            add_filter( 'bp_get_canonical_url',array( get_class(), 'bp_registration_url'), 20, 2 );
            add_action( 'lr_save_profile_data', array( 'LR_Raas_Social_Login', 'lr_save_raas_profile_data'), 10, 2 );
        }

        /**
         * 
         * @param type $redirect_to
         * @param type $args
         * @return type
         */
        public static function bp_registration_url( $redirect_to, $args ){
            if( function_exists( 'bp_is_register_page' ) && bp_is_register_page() ){
                return LR_Raas_Social_Login::lr_registration_url();
            }
            return $redirect_to;
        }

        public static function init() {
            if ( current_user_can( 'manage_options' ) ) {
                add_filter('user_profile_update_errors', array( 'LR_Raas_Social_Login', 'raas_user_updation' ), 10, 3);
            }
            /* Start RaaS form shortcode */
            add_shortcode( 'raas_login_form', array(get_class(), 'raas_login_form' ) );
            add_shortcode( 'raas_registration_form', array(get_class(), 'raas_registration_form' ) );
            add_shortcode( 'raas_forgotten_form', array(get_class(), 'raas_forgotten_form' ) );
            add_shortcode( 'raas_password_form', array(get_class(), 'raas_password_form' ) );
            /* End RaaS form shortcode */
        }

        // Create short codes
        //[raas_login_form]
        public static function raas_login_form( $atts ) {
            global $LR_Raas_Social_Login;
            if ( ! is_user_logged_in() ) {
                $message = '<div id="messageinfo" class="messageinfo"></div>';
                ob_start();
                $html = '<div class="lr-user-reg-container">' . $message . $LR_Raas_Social_Login->login_script() . '<div id="custom-object-container" class="lr-input-style lr-input-frame"></div>' . $LR_Raas_Social_Login->get_interface(). $LR_Raas_Social_Login->raas_forms('login') . '<div id="resetpassword-container" class="lr-input-style"></div><div id="login-container" class="lr-input-style"></div><div class="various-grid accout-login" id="reset_from" ></div><span class="lr-link"><a href = "' . wp_registration_url() . '">Register</a></span><span class="lr-link"><a href = "' . wp_lostpassword_url() . '">Lost Password</a></span></div>';
                return $html . ob_get_clean();
            }
        }

        //[raas_registration_form]
        public static function raas_registration_form( $atts ) {
            global $LR_Raas_Social_Login;
            if ( ! is_user_logged_in() ) {
                $message = '<div id="messageinfo" class="messageinfo"></div>';
                ob_start();
                $html = '<div class="lr-user-reg-container">' . $message;
                $html .= $LR_Raas_Social_Login->login_script();
                $html .= $LR_Raas_Social_Login->get_interface();
                $html .= $LR_Raas_Social_Login->raas_forms('registration');
                $html .= '<div id="registration-container" class="lr-input-style"></div>';
                $html .= '<span class="lr-link"><a href="' . wp_login_url() . '">Login</a></span>';
                $html .= '<span class="lr-link"><a href="' . wp_lostpassword_url() . '">Forgot Password</a></span></div>';
                
                return $html . ob_get_clean();
            }
        }

        //[raas_forgotpassword_form]
        public static function raas_forgotten_form( $atts ) {
            global $LR_Raas_Social_Login;
            if ( ! is_user_logged_in() ) {
                $message = '<div id="messageinfo" class="messageinfo"></div>';
                ob_start();
                $html = '<div class="lr-user-reg-container">' . $message. $LR_Raas_Social_Login->login_script() . $LR_Raas_Social_Login->raas_forms('forgotpassword') . '<div id="forgotpassword-container" class="lr-input-style"></div><span class="lr-link"><a href = "' . wp_login_url() . '">Login</a></span><span class="lr-link"><a href = "' . wp_registration_url() . '">Register</a></span></div>';
                return $html . ob_get_clean();
            }
        }

        //[raas_password_form]
        public static function raas_password_form( $atts ) {
            global $LR_Raas_Social_Login;
            if ( is_user_logged_in() ) {
                $db_message = get_user_meta(get_current_user_id(), 'lr_message_text', true );
                if ( ! empty( $db_message ) ) {
                    delete_user_meta(get_current_user_id(), 'lr_message_text');
                }

                $message = '<div id="messageinfo" class="messageinfo">' . $db_message . '</div>';
                ob_start();
                 
                $html = '<div class="lr-user-reg-container">' . $message . $LR_Raas_Social_Login->login_script() . '<div id="changepasswordbox" class="lr-input-style" style="display:none;"></div><div id="setpasswordbox" class="lr-input-style" style="display:none;"></div></div><script>jQuery(document).ready(function(){passwordChange();});</script>';
                return $html . ob_get_clean();
            }
        }
        
        
    }
new LR_Raas_Front();
}