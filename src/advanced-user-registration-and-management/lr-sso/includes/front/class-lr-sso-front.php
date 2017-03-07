<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius SSO.
 */
if (!class_exists('LR_SSO_Front')) {

    class LR_SSO_Front {

        public static function on_load() {
            add_action('init', array(get_class(), 'init'));
            add_action('admin_init', array(get_class(), 'init'));
        }

        /**
         * 
         * @global type $lr_sso_settings
         */
        public static function init() {
            global $lr_sso_settings;
            if (isset($lr_sso_settings['sso_enable']) && $lr_sso_settings['sso_enable'] == '1') {
                add_action('wp_enqueue_scripts', array(get_class(), 'load_sso_variables'));
                add_action('admin_enqueue_scripts', array(get_class(), 'load_sso_variables'));
                self::load_sso_scripts_before();
            }
        }

        /**
         * 
         * @global type $loginradius_api_settings
         * @global type $lr_raas_settings
         */
        public static function load_sso_variables() {
            $ssoRootUrl = function_exists('wp_parse_url') ? wp_parse_url(site_url()) : parse_url(site_url());
            $ssoRootUrl['path'] = isset($ssoRootUrl['path']) ? trim(trim($ssoRootUrl['path'], "/")) : '';
            $ssoTempDir = explode("/", $ssoRootUrl['path']);
            $ssoDir = isset($ssoTempDir[0]) ? trim($ssoTempDir[0]) : '';

            global $loginradius_api_settings, $lr_raas_settings;
            ?>
            <script>
                var lrSsoOptions = {};
                lrSsoOptions.sitename = '<?php echo $loginradius_api_settings['sitename']; ?>';
            <?php if (is_user_logged_in() && !is_super_admin()) { ?>
                    lrSsoOptions.logouturl = '<?php echo html_entity_decode(wp_logout_url()); ?>';
            <?php } else { ?>
                    lrSsoOptions.logouturl = false;
            <?php } ?>
                lrSsoOptions.loginurl = '<?php echo get_permalink($lr_raas_settings['login_page_id']); ?>';
            <?php if (isset($loginradius_api_settings['raas_enable']) && $loginradius_api_settings['raas_enable'] == '1') { ?>
                    lrSsoOptions.raasenable = true;
            <?php } else { ?>
                    lrSsoOptions.raasenable = false;
            <?php } ?>
            <?php if (is_user_logged_in()) { ?>
                    lrSsoOptions.islogin = true;
            <?php } else { ?>
                    lrSsoOptions.islogin = false;
            <?php } ?>
            <?php if (isset($_POST['token'])) { ?>
                    lrSsoOptions.istoken = true;
            <?php } else { ?>
                    lrSsoOptions.istoken = false;
            <?php } ?>
            <?php if (apply_filters('lr_sso_force_logout_user', '__return_false') === true) { ?>
                    lrSsoOptions.isforcelogout = '<?php echo html_entity_decode(wp_logout_url()); ?>';
            <?php } else { ?>
                    lrSsoOptions.isforcelogout = false;
            <?php } ?>
            </script>
            <?php
        }

        /**
         * 
         * @global type $lr_js_in_footer
         */
        public static function load_sso_scripts_before() {
            global $lr_js_in_footer;
            LR_Raas::enqueue_front_scripts();
            wp_enqueue_script('lr-sso', '//cdn.loginradius.com/hub/prod/js/LoginRadiusSSO.js', array('jquery', 'lr-raas', 'lr-social-login'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_enqueue_script('lr-sso-script', LR_SSO_URL . 'assets/js/loginradiusssofront.js', array('lr-sso'), LR_PLUGIN_VERSION, $lr_js_in_footer);
        }

    }

    LR_SSO_Front::on_load();
}
