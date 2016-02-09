<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Core' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Core {

        /**
         * Constructor
         */
        public function __construct() {
            // Declare constants and register files.
            add_action('lr_reset_admin_action',array($this,'reset_settings_action'),10,2);
            add_action('wp_enqueue_scripts', array($this, 'register_scripts_styles'));
            add_action('admin_enqueue_scripts', array($this, 'register_admin_files'));
            add_action('login_enqueue_scripts', array($this, 'register_scripts_styles'));
            add_action('admin_menu', array($this, 'create_loginradius_menu'));
            add_filter('plugin_action_links', array($this, 'loginradius_login_setting_links'), 10, 2);
            add_action('lr_reset_admin_ui',array($this,'reset_settings'));
            $this->define_constants();
            $this->load_dependencies();
        }

        /**
         * Add a settings link to the Plugins page,
         * so people can go straight from the plugin page to the settings page.
         */
        function loginradius_login_setting_links($links, $file) {
            static $thisPlugin = '';
            if (empty($thisPlugin)) {
                $thisPlugin = LR_ROOT_SETTING_LINK;
            }
            if ($file == $thisPlugin) {
                $settingsLink = '<a href="admin.php?page=';
                if ( ! class_exists( 'LR_Social_Login' ) && ! class_exists( 'LR_Raas_Install' ) ) {
                    $settingsLink .= 'loginradius_share';
                } else {
                    $settingsLink .= 'LoginRadius';
                }
                $settingsLink .= '">' . __( 'Settings', 'lr-plugin-slug' ) . '</a>';

                array_unshift($links, $settingsLink);
            }
            return $links;
        }

        /**
         * Create menu.
         */
        function create_loginradius_menu() {
            // Create Menu.
            if ( class_exists( 'LR_Social_Login' ) ) {		
                add_menu_page( 'LoginRadius', 'LoginRadius', 'manage_options', 'LoginRadius', array('LR_Activation_Admin', 'options_page'), LR_CORE_URL . 'assets/images/favicon.ico' );
                add_submenu_page( 'LoginRadius', 'Activation Settings', 'Activation', 'manage_options', 'LoginRadius', array('LR_Activation_Admin', 'options_page'));
            }
            // Customize Menu based on do_action order
            do_action('lr_admin_page');
        }

        /**
         * Define constants needed across the plug-in.
         */
        public function define_constants() {

            define( 'LR_CORE_DIR', plugin_dir_path(__FILE__) );
            define( 'LR_CORE_URL', plugin_dir_url(__FILE__) );

            define( 'LR_VALIDATION_API_URL', 'https://api.loginradius.com/api/v2/app/validate' );
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginRadiusSettings, loginRadiusObject
         */
        private function load_dependencies() {
            global $loginRadiusObject;
            
            update_option( 'LR_PLUGIN_VERSION', LR_PLUGIN_VERSION );
            update_option( 'LR_PLUGIN_PKG', LR_PLUGIN_PKG );
            
            //Load required files.
            require_once ( LR_CORE_DIR . 'lib/LoginRadiusSDK.php' );
            // Get object for LoginRadius SDK.
            $loginRadiusObject = new LoginRadius();

            /**
             * Advanced Functions
             *
             */
            require_once( LR_CORE_DIR . "includes/helpers/class-advanced-functions.php" );

            // Activation settings class.
            require_once( LR_CORE_DIR . 'includes/helpers/class-activation-helper.php' );
            require_once( LR_CORE_DIR . 'admin/class-activation-admin.php' );
            require_once( LR_CORE_DIR . 'admin/views/class-activation-settings-view.php' );
        }

        /**
         * Registers Scripts and Styles needed in all sections, is called from all sections
         *
         */
        public static function register_scripts_styles() {
            global $lr_js_in_footer;

            //LoginRadius Form Styling
            wp_register_style( 'lr-form-style', LR_CORE_URL . 'assets/css/lr-form-style.min.css', array(), LR_PLUGIN_VERSION );

            // LR Raas popup css used in LR Raas Popups and LiveFyre Modules
            wp_register_style( 'lr-raas-popup-style', LR_CORE_URL . 'assets/css/lr-raas-popup-style.min.css', array(), LR_PLUGIN_VERSION );

            // LoginRadius js sdk must be loaded in header.
            wp_register_script( 'lr-sdk', LR_CORE_URL . 'js/LoginRadiusSDK.2.0.0.js', array(), '2.0.0', $lr_js_in_footer );

            // Custom Interface must be loaded in header.
            wp_register_script( 'lr-custom-interface', '//cdn.loginradius.com/hub/prod/js/lr-custom-interface.3.js', array(), '3.0.0', $lr_js_in_footer);
            wp_register_script( 'lr-social-login', '//hub.loginradius.com/include/js/LoginRadius.js', array(), LR_PLUGIN_VERSION, $lr_js_in_footer);

            // Social Sharing js must be loaded in head.
            wp_register_script( 'lr-social-sharing', '//share.lrcontent.com/prod/v1/loginradius.js', array(), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_register_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), LR_PLUGIN_VERSION);
        }

        /**
         * Registers Scripts and Styles needed throughout front end of plugin
         *
         */
        public function register_admin_files() {
            self::register_scripts_styles();

            wp_register_style('lr-admin-style', LR_CORE_URL . 'assets/css/lr-admin-style.css', array(), LR_PLUGIN_VERSION);
            wp_enqueue_style('lr-admin-style');
        }
        /**
         * 
         * @param type $option_name
         */
        public function reset_settings( $option_name ) {
            ?>
            <div class="lr_options_container">	
                <div class="lr-row lr-reset-body">
                    <h5><?php _e( 'Reset all the ' . $option_name . ' options to the default recommended settings.', 'lr-plugin-slug' ); ?>
                        <span class="lr-tooltip" data-title="<?php _e('This option will reset all the settings to the default ' . $option_name . ' plugin settings', 'lr-plugin-slug' ); ?>">
                            <span class="dashicons dashicons-editor-help"></span>
                        </span>
                    </h5>
                    <div>
                        <form method="post" action="" class="lr-reset">
                            <?php submit_button('Reset All Options', 'secondary', 'reset', false ); ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }
        
        /**
         * 
         * @global type $loginradius_api_settings
         * @param type $option
         * @param type $settings
         */
        public static function reset_settings_action($option, $settings){
            if (is_multisite() && is_main_site()) {
                global $loginradius_api_settings;
                if (isset($loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1') {
                    $blogs = wp_get_sites();
                    foreach ($blogs as $blog) {
                        update_blog_option($blog['blog_id'], $option, $settings);
                    }
                }
            }
            update_option($option, $settings);
        }

        public static function get_spinner() {
            return '<div class="lr_fade">' . 
                        '<div style="margin-left: 40%;width: 338px;height: 338px;text-align: center;margin-top: 12%;font-size: 4em;color: #fff;">' .
                            '<div class="lr-ur-spinner">' .
                                '<div class="spinner-frame">' .
                                    '<div class="spinner-cover"></div>' .
                                    '<div class="spinner-bar"></div>' .
                                '</div>' .
                            '</div>' .
                        '</div>' .
                    '</div>';
        }
    }
new LR_Core();
}
