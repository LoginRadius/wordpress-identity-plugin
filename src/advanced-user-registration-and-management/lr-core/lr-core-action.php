<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Core_Action')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Core_Action {

        /**
         * Constructor
         */
        public function __construct() {
            add_action('activate_' . LR_ROOT_SETTING_LINK, array($this, 'activate'));
            add_action('deactivate_' . LR_ROOT_SETTING_LINK, array($this, 'deactivate'));
            add_filter('plugin_action_links', array($this, 'plugin_setting_links'), 10, 2);
        }

        function activate() {
            if (is_multisite() && is_network_admin()) {
                $blogs = get_sites();
                foreach ($blogs as $blog) {
                    $blog_id = get_object_vars($blog)["blog_id"];
                    if (!get_blog_option($blog_id,'LoginRadius_API_settings')) {
                        $api_options = array(
                            'LoginRadius_apikey' => '',
                            'LoginRadius_secret' => '',
                            'scripts_in_footer' => '1',
                            'delete_options' => '0',
                            'sitename' => '',
                            'raas_enable' => ''
                        );
                        update_blog_option($blog_id, 'LoginRadius_API_settings', $api_options);
                    }
                    do_action('lr_plugin_activate', $blog_id);
                }
            } else {
                if (!get_option('LoginRadius_API_settings')) {
                    $api_options = array(
                        'LoginRadius_apikey' => '',
                        'LoginRadius_secret' => '',
                        'scripts_in_footer' => '1',
                        'delete_options' => '0',
                        'sitename' => '',
                        'raas_enable' => ''
                    );
                    update_option('LoginRadius_API_settings', $api_options);
                }
                do_action('lr_plugin_activate', false);
            }
        }

        function deactivate() {
            $loginradius_api_settings = get_option('LoginRadius_API_settings');
            if (is_multisite() && is_network_admin()) {
                $blogs = get_sites();
                foreach ($blogs as $blog) {
                    $blog_id = get_object_vars($blog)["blog_id"];
                    if (isset($loginradius_api_settings['delete_options']) && $loginradius_api_settings['delete_options'] == '1') {
                        delete_blog_option($blog_id, 'LoginRadius_API_settings');
                        do_action('lr_plugin_deactivate', $blog_id);
                    }
                }
            } else {
                if (isset($loginradius_api_settings['delete_options']) && $loginradius_api_settings['delete_options'] == '1') {
                    delete_option('LoginRadius_API_settings');
                    do_action('lr_plugin_deactivate', false);
                }
            }
        }

        /**
         * Add a settings link to the Plugins page,
         * so people can go straight from the plugin page to the settings page.
         */
        function plugin_setting_links($links, $file) {
            static $thisPlugin = '';
            if (empty($thisPlugin)) {
                $thisPlugin = LR_ROOT_SETTING_LINK;
            }
            if ($file == $thisPlugin) {
                $settingsLink = '<a href="admin.php?page=';
                if (!class_exists('LR_Social_Login') && !class_exists('LR_Raas_Install')) {
                    $settingsLink .= 'loginradius_share';
                } else {
                    $settingsLink .= 'LoginRadius';
                }
                $settingsLink .= '">' . __('Settings', 'lr-plugin-slug') . '</a>';

                array_unshift($links, $settingsLink);
            }
            return $links;
        }

    }

    new LR_Core_Action();
}