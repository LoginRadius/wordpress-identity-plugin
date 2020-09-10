<?php

if (!class_exists('CIAM_Hosted_Page')) {

    class CIAM_Hosted_Page {
        /*
         * Constructor
         */

        function __construct() {
            add_action('init', array($this, 'init'), 12);
        }

        /*
         * Function will get initialised with wordpress init function will get initialised.
         */

        public function init() {
            global $ciam_setting;
            $configAPI = new \LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI();
            try {
                $config = $configAPI->getConfigurations();
            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                $currentErrorResponse = "Something went wrong: " . $e->getErrorResponse()->description;
                add_settings_error('ciam_authentication_settings', esc_attr('settings_updated'), $currentErrorResponse, 'error');
            }
            if (isset($config) && isset($config->ProductPlan) && ($config->ProductPlan == 'developer' || $config->ProductPlan == 'business' || $config->ProductPlan == '')) {
              
                if (!empty($ciam_setting)) {
                    if (isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == 1) {
                        add_action('init', array($this, 'remove_shortcodes'), 29);
                  
                        add_filter('lostpassword_url', array($this, 'lostpassword_url'), 101);
                        add_filter('register_url', array($this, 'registration_url'), 101);
                    }
                }
                add_action('wp_footer', array($this, 'ciam_page_notice'));
                if (is_user_logged_in()) {
                    add_action('admin_init', array($this, 'profile_page'), 101);
                } elseif (isset($ciam_setting) && (isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == 1)) {
                    add_filter('login_url', array($this, 'login_url'), 101);                    
                }

                $server = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
                $actual_link = "$server://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                // custom redirection to hosted page if enabled

                if (isset($ciam_setting)) {
                    if (isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == 1) {
                        if ($actual_link == get_permalink($ciam_setting['login_page_id'])) {
                            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), wp_login_url());
                            wp_redirect(wp_login_url());

                            exit();
                        } elseif ($actual_link == get_permalink($ciam_setting['registration_page_id'])) {
                            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), wp_registration_url());
                            wp_redirect(wp_registration_url());

                            exit();
                        } elseif ($actual_link == get_permalink($ciam_setting['lost_password_page_id'])) {
                            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), wp_lostpassword_url());
                            wp_redirect(wp_lostpassword_url());
                            exit();
                        }
                    }
                }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * this will remove the short code on enabling the hosted page from the admin....
         */

        public function remove_shortcodes() {

            if (!isset($_GET['vtype']) && !isset($_GET['vtoken'])) {
                remove_shortcode('ciam_login_form');
            }
            remove_shortcode('ciam_password_form');
            remove_shortcode('ciam_forgot_form');
            remove_shortcode('ciam_registration_form');

            if (!isset($_GET['vtype']) && !isset($_GET['vtoken'])) {
                add_shortcode('ciam_login_form', array($this, 'blank_ciam_shortcode'), 8999999);
            }
            add_shortcode('ciam_password_form', array($this, 'blank_ciam_shortcode'), 8999999);
            add_shortcode('ciam_forgot_form', array($this, 'blank_ciam_shortcode'), 8999999);
            add_shortcode('ciam_registration_form', array($this, 'blank_ciam_shortcode'), 8999999);

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * Blank shortcodes 
         */

        public function blank_ciam_shortcode() {
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
            return '';
        }

        /*
         * manage profile according to the hosted page condition.
         */

        public function profile_page() {
            global $pagenow, $ciam_setting;
          
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * manage hosted page url
         */

        private function hosted_page_urls($action, $redirect = '') {
            global $ciam_credentials, $ciam_setting;
           

            $redirect = empty($redirect) ? home_url('/') : $redirect;        
            if (!isset($_GET['redirect_to'])) {
                if (is_single() || is_page()) {
                    if (get_permalink()) {
                        if (strpos($redirect, "?") > 0) {
                            $redirect .= '&';
                        } else {
                            $redirect .= '?';
                        }
                        $redirect .= 'redirect_to=' . get_permalink();
                    }
                }
            }

         
                $appName = isset($ciam_credentials['sitename']) ? $ciam_credentials['sitename'] : '';
                if (!empty($appName)) {
                    if (isset($ciam_setting['custom_hub_domain']) && $ciam_setting['custom_hub_domain'] !== '') {
                        $url = $ciam_setting['custom_hub_domain'].'/auth.aspx?action=' . $action . '&return_url=' . urlencode($redirect);
                    } else {
                        $url = 'https://' . $appName . '.hub.loginradius.com/auth.aspx?action=' . $action . '&return_url=' . urlencode($redirect);
                    }
                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $url);
                    return $url;
                }
  

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), false);
            return false;
        }

        /*
         * Show error message
         */

        public function ciam_page_notice() {
            $message = $output = '';
            if (isset($_GET['action_completed']) && $_GET['action_completed'] == "forgotpassword") {     
                $message = 'Email has been sent successfully.';
            } elseif (isset($_GET['action_completed']) && $_GET['action_completed'] == "register") {     
                $message = 'Account created successfully. Please verify your email.';
            }
            if (!empty($message)) {
                $output .= '<style>.hostedservicemessages {position: fixed;top: 0;text-align: center;background: #29f;width: 100%;z-index: 9999;padding: 15px;left: 0;color: #fff;}</style>';
                $output .= "<script>if(ciamautohidetime>0){setTimeout(function(){jQuery('.hostedservicemessages').hide();},(ciamautohidetime*1000));}";
                $output .= "jQuery(document).ready(function(){";
                $output .= "if (window.location.href.indexOf('?') > -1) {";
                $output .= "history.pushState('', document.title, window.location.pathname);";
                $output .= " }";
                $output .= "});";
                $output .= "</script>";
                $output .= '<div class="hostedservicemessages">' . $message . '</div>';
            }
            echo $output;

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * Manage lost password url
         */

        public function lostpassword_url() {
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $this->hosted_page_urls('forgotpassword'));

            return $this->hosted_page_urls('forgotpassword');
        }

        /*
         * Manage registration page url
         */

        public function registration_url() {
            /* action for debug mode */
            
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $this->hosted_page_urls('register'));
            return $this->hosted_page_urls('register');
        }

        /*
         * Manage login page url
         */

        public function login_url() {
            /* action for debug mode */         
        
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $this->hosted_page_urls('login'));
                return $this->hosted_page_urls('login');
            
        }

        /*
         * Manage logut url
         */

        public function logout_url($redirect) {

            $actual_link = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (strpos($actual_link, 'lrlogout') === false) {
                return str_replace(array('%26amp%3B', 'auth.aspx'), array('%26', 'profile.aspx'), $this->hosted_page_urls('logout', $redirect . '&lrlogout'));
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $redirect);

            return $redirect;
        }       

    }

    new CIAM_Hosted_Page();
}