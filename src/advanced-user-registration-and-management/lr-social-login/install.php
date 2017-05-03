<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
/**
 * class responsible for setting default settings for LoginRadius Social Login and Share plugin
 */
if (!class_exists('LR_Social_Login_Install')) {

    class LR_Social_Login_Install {

        private static $login_options = array(
            'LoginRadius_loginform' => '1',
            'LoginRadius_loginformPosition' => 'embed',
            'LoginRadius_regform' => '1',
            'LoginRadius_regformPosition' => 'embed',
            'LoginRadius_commentEnable' => '0',
            'LoginRadius_numColumns' => '4',
            'LoginRadius_noProvider' => '1',
            'LoginRadius_enableUserActivation' => '0',
            'delete_options' => '1',
            'username_separator' => 'dash',
            'LoginRadius_redirect' => 'samepage',
            'LoginRadius_regRedirect' => 'samepage',
            'LoginRadius_loutRedirect' => 'homepage',
            'LoginRadius_socialavatar' => 'socialavatar',
            'LoginRadius_title' => 'Log in via a social account',
            'enable_degugging' => '0',
            'LoginRadius_sendemail' => 'notsendemail',
            'LoginRadius_dummyemail' => 'notdummyemail',
            'msg_email' => 'Unfortunately we could not retrieve your email from your @provider account. Please enter it in the form below in order to continue.',
            'msg_existemail' => 'This email is already registered. Please log in with this email and link any additional ID providers via account linking on your profile page.'
        );

        /**
         * Function for adding default plugin settings at activation
         */
        public static function set_default_options($blog_id) {

            if (version_compare(get_bloginfo('version'), LR_MIN_WP_VERSION, '<')) {
                $message = "Plugin could not be activated because ";
                $message .= "WordPress version is lower than ";
                $message .= LR_MIN_WP_VERSION;
                die($message);
            }
            if ($blog_id) {
                if (!get_blog_option($blog_id,'LoginRadius_settings')) {
                    // If plugin loginradius_db_version option not exist, it means plugin is not latest and update options.
                    update_blog_option($blog_id,'LoginRadius_settings', self::$login_options);
                }

                if (!get_blog_option($blog_id,'LoginRadius_API_settings')) {
                    $api_options = array(
                        'LoginRadius_apikey' => '',
                        'LoginRadius_secret' => '',
                        'scripts_in_footer' => '1',
                        'sitename' => ''
                    );

                    if (get_blog_option($blog_id,'LoginRadius_sharing_settings')) {
                        $loginradius_existing_settings = get_blog_option($blog_id,'LoginRadius_sharing_settings');
                        if (isset($loginradius_existing_settings['LoginRadius_apikey']) && !empty($loginradius_existing_settings['LoginRadius_apikey'])) {
                            $api_options['LoginRadius_apikey'] = $loginradius_existing_settings['LoginRadius_apikey'];
                        }
                    }

                    // Get Existing API key for update.
                    if (get_blog_option($blog_id,'LoginRadius_settings')) {
                        $loginradius_existing_settings = get_blog_option($blog_id,'LoginRadius_settings');
                        if (isset($loginradius_existing_settings['LoginRadius_apikey']) && !empty($loginradius_existing_settings['LoginRadius_apikey'])) {
                            $api_options['LoginRadius_apikey'] = $loginradius_existing_settings['LoginRadius_apikey'];
                        }
                        if (isset($loginradius_existing_settings['LoginRadius_secret']) && !empty($loginradius_existing_settings['LoginRadius_secret'])) {
                            $api_options['LoginRadius_secret'] = $loginradius_existing_settings['LoginRadius_secret'];
                        }
                    }
                    update_blog_option($blog_id,'LoginRadius_API_settings', $api_options);
                }
            } else {
                if (!get_option('LoginRadius_settings')) {
                    // If plugin loginradius_db_version option not exist, it means plugin is not latest and update options.
                    update_option('LoginRadius_settings', self::$login_options);
                }

                if (!get_option('LoginRadius_API_settings')) {
                    $api_options = array(
                        'LoginRadius_apikey' => '',
                        'LoginRadius_secret' => '',
                        'scripts_in_footer' => '1',
                        'sitename' => ''
                    );

                    if (get_option('LoginRadius_sharing_settings')) {
                        $loginradius_existing_settings = get_option('LoginRadius_sharing_settings');
                        if (isset($loginradius_existing_settings['LoginRadius_apikey']) && !empty($loginradius_existing_settings['LoginRadius_apikey'])) {
                            $api_options['LoginRadius_apikey'] = $loginradius_existing_settings['LoginRadius_apikey'];
                        }
                    }

                    // Get Existing API key for update.
                    if (get_option('LoginRadius_settings')) {
                        $loginradius_existing_settings = get_option('LoginRadius_settings');
                        if (isset($loginradius_existing_settings['LoginRadius_apikey']) && !empty($loginradius_existing_settings['LoginRadius_apikey'])) {
                            $api_options['LoginRadius_apikey'] = $loginradius_existing_settings['LoginRadius_apikey'];
                        }
                        if (isset($loginradius_existing_settings['LoginRadius_secret']) && !empty($loginradius_existing_settings['LoginRadius_secret'])) {
                            $api_options['LoginRadius_secret'] = $loginradius_existing_settings['LoginRadius_secret'];
                        }
                    }
                    update_option('LoginRadius_API_settings', $api_options);
                }
            }
        }

    }

    new LR_Social_Login_Install();
}
