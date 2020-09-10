<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'CIAM_WP_Default_Login' ) ) {

	/**
	 * Class CIAM_WP_Default_Login
	 */
    class CIAM_WP_Default_Login {

        /**
         * Constructor
         */
        public function __construct() {
            global $ciam_credentials;
            
            add_shortcode( 'ciam_wp_default_login', array( $this, 'ciam_wp_default_login' ) );
        	// Allows the use of email logins
            add_action( 'wp_authenticate', array( $this, 'optional_email_address_login' ), 1, 2 );

            // Run login_user before headers and cookies are sent
          
            add_action( 'after_setup_theme', array( $this, 'login_user' ) );
            
        }

        /**
         * ciam_wp_default_login displays a default wordpress
         * login form as an optional back door option for wordpress users
         */
        function ciam_wp_default_login() {
            
            if ( ! is_user_logged_in() ) {
                $args = array(
                    'echo'           => true,
                    'redirect' => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                    'form_id'        => 'loginform',
                    'label_username' => __( 'Username or Email' ),
                    'label_password' => __( 'Password' ),
                    'label_remember' => __( 'Remember Me' ),
                    'label_log_in'   => __( 'Log In' ),
                    'id_username'    => 'user_login',
                    'id_password'    => 'user_pass',
                    'id_remember'    => 'rememberme',
                    'id_submit'      => 'wp-submit',
                    'remember'       => true,
                    'value_username' => '',
                    'value_remember' => true
                );
                wp_login_form( $args );
            }
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /**
         * optional_email_address_login allows the user
         * to log in with a email address as well as a username
         * @param  string &$username username or email
         * @param  string &$password password
         */
        function optional_email_address_login( &$username, &$password ) {   
           
            $user = get_user_by( 'email', $username );
            if ( ! empty( $user->user_login ) )
            {
                $username = $user->user_login;
            }
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, "Credencials", get_class(), "");
        }

        /**
         * login_user handles the $_POST array and logs in users
         */
        function login_user() {
           
            if ( ! is_user_logged_in() && ! empty( $_POST['log'] ) && ! empty( $_POST['pwd'] ) ) {
                $creds = array();
                $creds['user_login'] = isset( $_POST['log'] ) ? $_POST['log'] : '';
                $creds['user_password'] = isset( $_POST['pwd'] ) ? $_POST['pwd'] : '';
                $creds['remember'] = isset( $_POST['rememberme'] ) ? true : false;
                $user = wp_signon( $creds, false );
                
                if ( is_wp_error( $user ) ) {
                    error_log( 'CIAM WP DEFAULT LOGIN ERROR' . $user->get_error_message() );
                }
            }
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        } 
    }
     new CIAM_WP_Default_Login();
}
