<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius Raas.
 */
if (!class_exists('LR_Hosted_Redirect')) {

    class LR_Hosted_Redirect {

        function __construct() {
            add_action('init', array($this, 'init'));
            add_action('wp_footer', array($this, 'hosted_page_notice'));
        }

        public function init() {
            global $lr_raas_settings, $lr_sso_settings;
            if (isset($lr_raas_settings['hosted_enable']) && $lr_raas_settings['hosted_enable'] == '1') {
                
                if (!is_user_logged_in()) {
                    add_filter('login_url', array($this, 'login_url'), 100);
                    add_filter('register_url', array($this, 'registration_url'), 100);
                    add_filter('lostpassword_url', array($this, 'lostpassword_url'), 100);
                } else {
                    add_action('admin_init', array($this, 'hosted_profile_page'));
                }
                //which can also remove 
                add_action('wp_enqueue_scripts', array($this, 'enqueue_front_deregister_scripts'));
                add_action('init', array($this, 'remove_raas_shortcodes'), 20);
                if (isset($lr_sso_settings['sso_enable']) && $lr_sso_settings['sso_enable'] == '1') {
                add_action('wp_print_footer_scripts', array($this, 'ssoInit'));
                }
            }
        }
        
        

        function enqueue_front_deregister_scripts() {
            global $lr_js_in_footer;
            wp_deregister_script('lr-raas-front-script');
            wp_register_script('lr-hosted-front-script', LR_ROOT_URL . 'lr-hosted/assets/js/front.js', array('jquery-ui-datepicker'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_enqueue_script('lr-hosted-front-script');
        }

        public function hosted_profile_page() {
            global $pagenow;
            if ($pagenow == 'profile.php' && !is_super_admin()) {
                wp_redirect($this->profile_url());
                exit();
            }
        }

        public function ssoInit() {
            global $loginradius_api_settings;
            ?>
            <script>
                jQuery(document).ready(function () {

                    var options = {};
                    options.appName = "<?php echo $loginradius_api_settings['sitename']; ?>";
                    options.apikey = "<?php echo $loginradius_api_settings['LoginRadius_apikey']; ?>";
                    LoginRadiusRaaS.init(options, 'sociallogin', function (response) {
                        if (response.isPosted) {
                            handleResponse(true, "");

                        } else {
                            handleResponse(true, "", true);
                            redirect(response);
                        }
                    }, function (response) {
                        if (response[0].description != null) {

                            handleResponse(false, response[0].description);
                        }
                    }, "social-registration-container");
                });
            </script>
            <?php
        }

        function hosted_page_notice() {
            $message = '';
            if (isset($_GET['action_completed']) && $_GET['action_completed'] == "forgotpassword") {
                $message = 'Email has been sent successfully.';
            } elseif (isset($_GET['action_completed']) && $_GET['action_completed'] == "register") {
                $message = 'Account created successfully. Please verify your email.';
            }
            if (!empty($message)) {
                echo '<style>.hostedservicemessages {position: fixed;top: 0;text-align: center;background: #29f;width: 100%;z-index: 9999;padding: 15px;left: 0;color: #fff;}</style>';
                echo '<script>setTimeout(function(){jQuery(".hostedservicemessages").hide();},5000);</script>';
                echo '<div class="hostedservicemessages">' . $message . '</div>';
            }
        }

        function remove_raas_shortcodes() {
            remove_shortcode('raas_login_form');
            remove_shortcode('raas_registration_form');
            remove_shortcode('raas_forgotten_form');
            remove_shortcode('raas_password_form');
        }

        public function profile_url() {
            global $loginradius_api_settings;
            $redirect = home_url('/');

            $appName = isset($loginradius_api_settings['sitename']) ? $loginradius_api_settings['sitename'] : '';
            if (!empty($appName)) {

                return 'https://' . $appName . '.hub.loginradius.com/profile.aspx?action=profile&return_url=' . $redirect;
            }
            return false;
        }

        public function lostpassword_url() {
            return $this->hosted_page_urls('forgotpassword');
        }

        public function registration_url() {
            return $this->hosted_page_urls('register');
        }

        public function login_url() {
            
            return $this->hosted_page_urls('login');
        }

        public function logout_url($url, $redirect) {
           
            return $this->hosted_page_urls('logout', $redirect);
        }

        private function hosted_page_urls($action, $redirect = '') {
            global $loginradius_api_settings, $current;
            
            $redirect = empty($redirect) ? home_url('/') : $redirect;
           
            if(is_single() || is_page() ){
                 $redirect .= '?redirect_to='.get_permalink();
            } else {
                $redirect = empty($redirect) ? home_url('/') : $redirect;
            }
           
            
            $appName = isset($loginradius_api_settings['sitename']) ? $loginradius_api_settings['sitename'] : '';
            if (!empty($appName)) {
                if (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])) {
                    return 'https://' . $appName . '.hub.loginradius.com/auth.aspx?action=' . $action . '&return_url=' . urlencode($redirect . '?' . $_SERVER["QUERY_STRING"]);
                } else {
                    return 'https://' . $appName . '.hub.loginradius.com/auth.aspx?action=' . $action . '&return_url=' . urlencode($redirect);
                }
            }
            return false;
        }

    }

    new LR_Hosted_Redirect();
}