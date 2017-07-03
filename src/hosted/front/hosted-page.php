<?php

if (!class_exists('CIAM_Hosted_Page')) {

    class CIAM_Hosted_Page {

        function __construct() {
            global $ciam_credencials, $ciam_setting;

            if (!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])) {
                return;
            }

            if (!empty($ciam_setting)) { 
                if (isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == 1) {
                    add_action( 'init', array($this,'remove_ciam_shortcodes') ,29 );
                   
                    add_filter('lostpassword_url', array($this, 'lostpassword_url'), 101);
                    add_filter('register_url', array($this, 'registration_url'),101);
                }
            }
            $ciam_credencials = get_option('Ciam_API_settings');
            add_action('init', array($this, 'init'));

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function init() {
            global $ciam_setting;

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
                        wp_redirect(wp_login_url());

                        exit();
                    } elseif ($actual_link == get_permalink($ciam_setting['registration_page_id'])) {

                        wp_redirect(wp_registration_url());
                        exit();
                    } elseif ($actual_link == get_permalink($ciam_setting['lost_password_page_id'])) {
                        wp_redirect(wp_lostpassword_url());
                        exit();
                    }
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }
        /*
         * this will remove the short code on enabling the hosted page from the admin....
         */
         public function remove_ciam_shortcodes(){
         
if(!isset($_GET) && !isset($_GET['vtype']) && !isset($_GET['vtoken'])){
            remove_shortcode( 'ciam_login_form' );
}
            remove_shortcode( 'ciam_password_form' );
            remove_shortcode( 'ciam_forgotten_form' );
            remove_shortcode( 'ciam_registration_form' );
            
             if(!isset($_GET) && !isset($_GET['vtype']) && !isset($_GET['vtoken'])){
             add_shortcode( 'ciam_login_form', array($this,'blank_ciam_shortcode'), 8999999 );
             }
            add_shortcode( 'ciam_password_form', array($this,'blank_ciam_shortcode'), 8999999 );
            add_shortcode( 'ciam_forgotten_form', array($this,'blank_ciam_shortcode'), 8999999 );
            add_shortcode( 'ciam_registration_form', array($this,'blank_ciam_shortcode'), 8999999 );
            
        }
        public function blank_ciam_shortcode(){
            return '';
        }
        public function profile_page() { 
            global $pagenow, $ciam_setting;
            if (true && isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == 1){ 
                
                if ($pagenow == 'profile.php' && !is_super_admin()) { 
                    wp_redirect($this->profile_url());
                    exit();
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

       private function hosted_page_urls($action, $redirect = '') {
            global $ciam_credencials;
            
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
            
            $appName = isset($ciam_credencials['sitename']) ? $ciam_credencials['sitename'] : '';
            if (!empty($appName)) {
                if (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])) {
                        /* action for debug mode */
                       do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
                    return 'https://' . $appName . '.hub.loginradius.com/auth.aspx?action=' . $action . '&return_url=' . urlencode($redirect . '?' . $_SERVER["QUERY_STRING"]);
                } else {
                    /* action for debug mode */
                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
                  
                    return 'https://' . $appName . '.hub.loginradius.com/auth.aspx?action=' . $action . '&return_url=' . urlencode($redirect);
                }
            }
            return false;
                        
        }

        public function ciam_page_notice() {
            $message = $output = '';
            if (isset($_GET['action_completed']) && $_GET['action_completed'] == "forgotpassword") {
                $message = 'Email has been sent successfully.';
            } elseif (isset($_GET['action_completed']) && $_GET['action_completed'] == "register") {
                $message = 'Account created successfully. Please verify your email.';
            }
            if (!empty($message)) {
                $output .= '<style>.hostedservicemessages {position: fixed;top: 0;text-align: center;background: #29f;width: 100%;z-index: 9999;padding: 15px;left: 0;color: #fff;}</style>';
                $output .= "<script>setTimeout(function(){jQuery('.hostedservicemessages').hide();},5000);";
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
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function lostpassword_url() {
            return $this->hosted_page_urls('forgotpassword');

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function registration_url() { 
            return $this->hosted_page_urls('register');

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function login_url() { 
            return $this->hosted_page_urls('login');

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public function logout_url($redirect) {

            $actual_link = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (strpos($actual_link, 'lrlogout') === false) {
                return str_replace(array('%26amp%3B', 'auth.aspx'), array('%26', 'profile.aspx'), $this->hosted_page_urls('logout', $redirect . '&lrlogout'));
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');

            return $redirect;
        }

        public function profile_url() {
            global $ciam_credencials;

            $appName = isset($ciam_credencials['sitename']) ? $ciam_credencials['sitename'] : '';
            if (!empty($appName)) {
                return 'https://' . $appName . '.hub.loginradius.com/profile.aspx?action=profile';
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');

            return false;
        }

    }

    new CIAM_Hosted_Page();
}