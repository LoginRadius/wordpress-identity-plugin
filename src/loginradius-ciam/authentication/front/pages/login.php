<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Authentication_Loginfunction')) {
    class CIAM_Authentication_Loginfunction
    {
        /*
         * Class cosntructor function
         */

        public function __construct()
        {
            global $ciam_setting;
            
            add_action('init', array($this, 'init'), 105);
            if (isset($ciam_setting) && !empty($ciam_setting['login_page_id'])) {
                add_action('init', array($this, 'custom_page_redirection'));
            }
        }

        /*
         * Load all dependencies
         */

        public function init()
        {
            global $ciam_setting;

            add_filter('login_url', array($this, 'custom_login_page'), 100);
            add_shortcode('ciam_login_form', array($this, 'ciam_login_form'));
            add_action('wp_head', array($this, 'ciam_hook_socialLogin_custom_script'));
            if (empty($ciam_setting['login_page_id'])) { // calling the default registration page if page is not created.
                add_filter('register_url', array($this, 'default_registration_page'), 100);
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * change authentication link for the login page....
         */

        public function custom_login_page()
        {
            global $ciam_setting;
            if (!empty($ciam_setting['login_page_id'])) {
                $login_page = $this->get_redirect_to_params(get_permalink($ciam_setting['login_page_id']));
            } else {
                $login_page = site_url('wp-login.php');
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $login_page);
            return $login_page;
        }
        
        /*
         * default registration link.
         */

        public function default_registration_page()
        {
            $register_page = site_url('wp-login.php?action=register', 'login');

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $register_page);
            return $register_page;
        }
        /*
         * Add datepicker
         */
        public function datepickerscript()
        {
            wp_enqueue_style('ciam-style-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
            wp_enqueue_script('ciam-js-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
             
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }
        /*
         * manage redirection
         */

        public function get_redirect_to_params($redirectParam)
        {
            global $ciam_setting;

            if (isset($_GET['redirect_to']) && !empty(isset($_GET['redirect_to']))) {
                if (strpos($redirectParam, "?") > 0) {
                    $redirectParam .= '&';
                } else {
                    $redirectParam .= '?';
                }
                $redirectParam .= 'redirect_to=' . urlencode($_GET['redirect_to']);
            } elseif (is_single() || is_page()) {

                //condition to check the url host with the site host....

                $urlhost = parse_url(get_permalink());
                if ($urlhost['host'] == $_SERVER['HTTP_HOST']) {
                    if (get_permalink() && !in_array(get_permalink(), array(get_permalink($ciam_setting['login_page_id']), get_permalink($ciam_setting['registration_page_id']), get_permalink($ciam_setting['change_password_page_id']), get_permalink($ciam_setting['lost_password_page_id'])))) {
                        if (strpos($redirectParam, "?") > 0) {
                            $redirectParam .= '&';
                        } else {
                            $redirectParam .= '?';
                        }
                        $redirectParam .= 'redirect_to=' . urlencode(get_permalink()) . '&referral=true';
                    }
                }
            }

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $redirectParam);
            return $redirectParam;
        }

        /**
         * Redirect the page to ciam shortcode page on directly opening the default wordpress page.
         *
         */
        public function custom_page_redirection()
        {
            global $pagenow, $ciam_setting;
            $login_page_id = !empty($ciam_setting['login_page_id']) ? $ciam_setting['login_page_id'] : '';
            $register_page_id = !empty($ciam_setting['registration_page_id']) ? $ciam_setting['registration_page_id'] : '';
            $lost_pass_page_id = !empty($ciam_setting['lost_password_page_id']) ? $ciam_setting['lost_password_page_id'] : '';

            if ('wp-login.php' == $pagenow && !is_user_logged_in()) {
                $url = get_permalink($login_page_id);

                if (isset($_GET['action']) && 'register' == $_GET['action']) {
                    $url = get_permalink($register_page_id);
                } elseif (isset($_GET['action']) && 'lostpassword' == $_GET['action']) {
                    $url = get_permalink($lost_pass_page_id);
                }

                if ($url) {
                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $url);
                    wp_redirect($url);
                    exit();
                } else {
                    error_log('USER REGISTRATION NOT CONFIGURED CORRECTLY: Login, Registration or Lost Password page(s) are not set');
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Create short codes
         */

        //[ciam_login_form]
        public function ciam_login_form()
        {
            global $ciam_setting,$ciam_sso_page_settings;
           
            $ciam_sso_page_settings = get_option('Ciam_Sso_Page_settings');
            
            if (!empty($ciam_setting['login_page_id'])) {
                $url = get_permalink($ciam_setting['login_page_id']);
                //checking for hosted page....

                if (isset($ciam_setting) && false) {
                    if (isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == 1) {
                        wp_redirect(wp_login_url());
                    }
                }


                if (!is_user_logged_in()) {
                    add_action('wp_footer', array($this,'datepickerscript'));
                    $message = '<div id="" class="messageinfo"></div>';
                    ob_start();
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                          <?php
                    $configAPI = new \LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI();
                    try {
                        $config = $configAPI->getConfigurations();
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $currentErrorResponse = "Something went wrong: " . $e->getErrorResponse()->description;
                        add_settings_error('ciam_authentication_settings', esc_attr('settings_updated'), $currentErrorResponse, 'error');
                    }


                    if (isset($config) && isset($config->ProductPlan) && ($config->ProductPlan == 'developer' || $config->ProductPlan == 'business' || $config->ProductPlan == '')) {
                        if (isset($ciam_sso_page_settings['sso_enable']) && $ciam_sso_page_settings['sso_enable'] == '1') {?>
                                <?php if (!empty($_GET['vtype']) && !empty($_GET['vtoken'])) { ?>
                                        <?php
                                        if ($_GET['vtype'] === 'oneclicksignin' || $_GET['vtype'] === 'emailverification') {
                                            ?>                                              
                                             if(typeof LRObject !== 'undefined')
                                             {                                              
                                                var ssologin_options = {};
                                                LRObject.init("ssoLogin", ssologin_options);
                                             }
                                        <?php
                                        } ?>
                
                                <?php
                                    }
                            }
                    } ?>
                            login_hook('<?php echo $url ?>');
                            social('<?php echo $url ?>');
                    <?php if (!empty($_GET['vtype']) && !empty($_GET['vtoken'])) { ?>
                        <?php
                        if ($_GET['vtype'] === 'oneclicksignin') {
                            ?>
                                    oneclicksignin();
                        <?php
                        } else { ?>
                                    emailverification('<?php echo $url ?>');
                            <?php
                        }
                    } ?>
                        });
                    </script>
                    <?php
                    $html = '<div class="ciam-user-reg-container">' . $message . '<span id="verificationmessage"></span><span id="loginmessage"></span>';
                    $html .= '<div id="sociallogin-container"></div><div id="interfacecontainerdiv" class="interfacecontainerdiv"></div><div id="login-container" class="ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="lr_loading_screen"><div class="lr_loading_screen_center" style="position: fixed;"><img  src="' . CIAM_PLUGIN_URL . 'authentication/assets/images/loading-white.png' . '" alt="loding image" class="loading_circle ciam_loading_gif_align lr_loading_screen_spinner" /></div></div></div><div class="various-grid accout-login" id="reset_from" ></div><span class="ciam-link"><a href = "' . wp_registration_url() . '">Register</a></span><span class="ciam-link btn"><a href = "' . wp_lostpassword_url() . '">Forgot Password</a></span></div>';

                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $html);
                    return $html . ob_get_clean();
                } elseif (is_user_logged_in() && (!empty($_GET) && !empty($_GET['vtype']) && !empty($_GET['vtoken']))) {
                    $profile_url = get_edit_user_link(get_current_user_id()); 
                    $message = '<div id="" class="messageinfo"></div>';
                    ob_start();
                    ?>
                    <script type="text/javascript">
                        jQuery(document).ready(function () {
                            emailverification('<?php echo $profile_url ?>');
                        });
                    </script>
                    <?php
                    $html = '<div class="ciam-user-reg-container">' . $message . '<span id="verificationmessage"></span>';
                    $html .= '<div class="ciam-user-reg-container">' . $message . '<span id="loginmessage"></span></div>';

                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $html);
                    return $html . ob_get_clean();
                }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Social Login Form starts....
         */

        public function ciam_hook_socialLogin_custom_script()
        {
            ?>
            <script type="text/html" id="loginradiuscustom_tmpl"><span class="ciam-provider-label ciam-icon-<#=Name.toLowerCase()#>" onclick="return <#=ObjectName#>.util.openWindow('<#= Endpoint #>');" title="<#= Name #>"></span></script>
            <?php
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }
    }

    new CIAM_Authentication_Loginfunction();
}
