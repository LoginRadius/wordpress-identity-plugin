<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_SSO')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class CIAM_SSO {

        /**
         * Constructor
         */
        public function __construct() {
            global $ciam_credentials;
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
            if (!isset($ciam_credentials['apikey']) || empty($ciam_credentials['apikey']) || !isset($ciam_credentials['secret']) || empty($ciam_credentials['secret'])) {
                return;
            }

            add_action('init', array($this, 'init'));
        }

        public function init() {
            global $ciam_sso_page_settings;
            $ciam_sso_page_settings = get_option('Ciam_Sso_Page_settings');

            $is_enable = false;
            if (isset($ciam_sso_page_settings['sso_enable']) && $ciam_sso_page_settings['sso_enable'] == '1') {
                if (!is_super_admin()) {
                    $is_enable = true;
                    if(get_current_user_id() != '0'){
                    $accessToken = get_user_meta(get_current_user_id(), 'accesstoken', true);
                    if (empty($accessToken)) {
                        $is_enable = false;
                    }
                    }
                }
            }
            $this->load_dependencies($is_enable);
            add_action('ciam_admin_menu', array($this, 'menu'));
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * Create menu for the module
         */

        public function menu() {
            global $ciam_credentials;
            $configAPI = new \LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI();
          
            try{
                $config = $configAPI->getConfigurations();
            }
            catch (\LoginRadiusSDK\LoginRadiusException $e) { 
                    $currentErrorResponse = "Something went wrong: " . $e->getErrorResponse()->description;
                    add_settings_error('ciam_authentication_settings', esc_attr('settings_updated'), $currentErrorResponse, 'error');
            }
            
            if (!isset($ciam_credentials['apikey']) && empty($ciam_credentials['apikey']) || !isset($ciam_credentials['secret']) && empty($ciam_credentials['secret'])) {
                return;
            }else if(isset($config) && isset($config->ProductPlan) && $config->ProductPlan == 'free') { 
                return;
            }
            add_submenu_page('ciam-activation', 'SSO Page Settings', 'SSO', 'manage_options', 'ciam-sso', array('Sso_Admin', 'options_page'));
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         *  Load LR V2 Script 
         */

        public function sso_enqueue_scripts() {
            wp_register_script('ciam-auth-script', '//auth.lrcontent.com/v2/js/LoginRadiusV2.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
            wp_enqueue_script('ciam-auth-script');
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public function load_dependencies($is_enable) {

            // Load required files.
            require_once(CIAM_PLUGIN_DIR . "sso/admin/class-sso-admin.php");
            if ($is_enable) {
                add_action('wp_enqueue_scripts', array($this, 'sso_enqueue_scripts'));
                require_once(CIAM_PLUGIN_DIR . "sso/front/front-sso.php");
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

    }

    new CIAM_SSO();
}
