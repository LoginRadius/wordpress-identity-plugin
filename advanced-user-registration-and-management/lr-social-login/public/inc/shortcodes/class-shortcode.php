<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('Login_Radius_Shortcode')) {

    /**
     * This class is responsible for adding plugin shortcodes
     */
    class Login_Radius_Shortcode {

        /**
         * Login_Radius_Shortcode calss instance
         *
         * @var string
         */
        private static $instance;

        /**
         * Get singleton object for class Login_Radius_Shortcode
         *
         * @return object Login_Radius_Shortcode
         */
        public static function get_instance() {

            if (!isset(self::$instance) && !( self::$instance instanceof Login_Radius_Shortcode )) {
                self::$instance = new Login_Radius_Shortcode();
            }
            return self::$instance;
        }

        /*
         * Constructor for class Login_Radius_Shortcode
         */

        public function __construct() {

            $this->register_shortcodes();
        }

        /*
         * Register all LoginRadius plugin shortcodes.
         */

        public function register_shortcodes() {

            add_shortcode( 'LoginRadius_Login', array( $this, 'login_shortcode' ) );
            add_shortcode( 'LoginRadius_Linking', array( $this, 'linking_widget_shortcode' ) );
        }

        /**
         * Callback for social login shortcode.
         */
        public static function login_shortcode($params) {
            if (is_user_logged_in()) {
                return '';
            }
            $return = '';
            $tempArray = array(
                'style' => '',
            );
            extract(shortcode_atts($tempArray, $params));
            if ($style != '') {
                $return .= '<div style="' . $style . '">';
            }
            $return .= Login_Helper::get_loginradius_interface_container(true);

            if ($style != '') {
                $return .= '</div>';
            }
            return $return;
        }

        /**
         * Callback for Social Linking widget shortcode
         */
        public static function linking_widget_shortcode() {
            global $loginRadiusObject, $loginradius_api_settings, $lr_custom_interface_settings;

            if ( ! is_user_logged_in() ) {
                return '';
            }

            $custom = false;
            if (isset($lr_custom_interface_settings['custom_interface']) && $lr_custom_interface_settings['custom_interface'] == '1') {
                $custom = true;
            }

            $html = LR_Common:: check_linking_status_parameters();
            if (!( $loginRadiusObject->loginradius_is_valid_guid(trim($loginradius_api_settings['LoginRadius_apikey'])) && $loginRadiusObject->loginradius_is_valid_guid(trim($loginradius_api_settings['LoginRadius_secret'])) )) {
                $html .= '<div style="color:red">' . __('Your LoginRadius API key or secret is not valid, please correct it or contact LoginRadius support at <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b>', 'lr-plugin-slug' ) . '</div>';
            }

            // function call
            LR_Common:: link_account_if_possible();
            if (!( $loginRadiusObject->loginradius_is_valid_guid(trim($loginradius_api_settings['LoginRadius_apikey'])) && $loginRadiusObject->loginradius_is_valid_guid(trim($loginradius_api_settings['LoginRadius_secret'])) )) {
                $html .= '<div style="color:red">' . __('Your LoginRadius API key or secret is not valid, please correct it or contact LoginRadius support at <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b>', 'lr-plugin-slug' ) . '</div>';
            }

            $html .= Login_Helper::get_loginradius_interface_container(true);
            $html .= '<table class="loginRadiusLinking">';
            $html .= LR_Common::get_connected_providers_list();

            $html .= '</table>';

            return $html;
        }

    }

}