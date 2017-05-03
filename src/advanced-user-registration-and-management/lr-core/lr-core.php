<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Core')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Core {

        /**
         * Constructor
         */
        public function __construct() {
            // Declare constants and register files.
            add_action('wp_enqueue_scripts', array($this, 'register_scripts_styles'));
            add_action('admin_enqueue_scripts', array($this, 'register_admin_files'));
            add_action('login_enqueue_scripts', array($this, 'register_scripts_styles'));
            add_action('admin_menu', array($this, 'create_loginradius_menu'));
            add_action('lr_reset_admin_ui', array($this, 'reset_settings'));
            $this->load_dependencies();
        }

        /**
         * Create menu.
         */
        function create_loginradius_menu() {
            global $loginradius_api_settings;
            // Create Menu.
            if (class_exists('LR_Social_Login')) {
                add_menu_page('LoginRadius', 'LoginRadius', 'manage_options', 'LoginRadius', array('LR_Activation_Admin', 'options_page'), LR_ROOT_URL . 'lr-core/assets/images/favicon.ico');
                add_submenu_page('LoginRadius', 'Activation Settings', 'Activation', 'manage_options', 'LoginRadius', array('LR_Activation_Admin', 'options_page'));
            }
            // Customize Menu based on do_action order
            if ((!class_exists('LR_Social_Login')) ||
                    (isset($loginradius_api_settings['LoginRadius_apikey']) && !empty($loginradius_api_settings['LoginRadius_apikey']) && isset($loginradius_api_settings['LoginRadius_secret']) && !empty($loginradius_api_settings['LoginRadius_secret']))) {
                do_action('lr_admin_page');
            }
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginRadiusSettings, loginRadiusObject
         */
        private function load_dependencies() {
            update_option('LR_PLUGIN_VERSION', LR_PLUGIN_VERSION);
            update_option('LR_PLUGIN_PKG', LR_PLUGIN_PKG);

            require_once( LR_ROOT_DIR . "lr-core/lr-core-action.php" );
            /**
             * Advanced Functions
             *
             */
            require_once( LR_ROOT_DIR . "lr-core/includes/helpers/class-advanced-functions.php" );

            // Activation settings class.
            require_once( LR_ROOT_DIR . "lr-core/includes/helpers/class-activation-helper.php" );
            require_once( LR_ROOT_DIR . "lr-core/admin/class-activation-admin.php" );
            require_once( LR_ROOT_DIR . "lr-core/admin/views/class-activation-settings-view.php" );
        }

        /**
         * Registers Scripts and Styles needed in all sections, is called from all sections
         *
         */
        public static function register_scripts_styles() {
            global $lr_js_in_footer;

            //LoginRadius Form Styling
            wp_register_style('lr-form-style', LR_ROOT_URL . 'lr-core/assets/css/lr-form-style.min.css', array(), LR_PLUGIN_VERSION);

            // LR Raas popup css used in LR Raas Popups and LiveFyre Modules
            wp_register_style('lr-raas-popup-style', LR_ROOT_URL . 'lr-core/assets/css/lr-raas-popup-style.min.css', array(), LR_PLUGIN_VERSION);

            // LoginRadius js sdk must be loaded in header.
            wp_register_script('lr-sdk', LR_ROOT_URL . 'lr-social-login/assets/js/LoginRadiusSDK.2.0.1.js', array(), '2.0.1', $lr_js_in_footer);

            // Custom Interface must be loaded in header.
            //wp_register_script('lr-custom-interface', '//cdn.loginradius.com/hub/prod/js/lr-custom-interface.3.js', array(), '3.0.0', $lr_js_in_footer);
            wp_register_script('lr-social-login', '//hub.loginradius.com/include/js/LoginRadius.js', array(), LR_PLUGIN_VERSION, $lr_js_in_footer);

            // Social Sharing js must be loaded in head.
            wp_register_style('jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), LR_PLUGIN_VERSION);
        }

        /**
         * Registers Scripts and Styles needed throughout front end of plugin
         *
         */
        public function register_admin_files() {
            self::register_scripts_styles();

            wp_register_style('lr-admin-style', LR_ROOT_URL . 'lr-core/assets/css/lr-admin-style.css', array(), LR_PLUGIN_VERSION);
            wp_enqueue_style('lr-admin-style');
        }

        /**
         * 
         * @param type $option_name
         */
        public function reset_settings($option_name) {
            ?>
            <div class="lr_options_container">	
                <div class="lr-row lr-reset-body">
                    <h5><?php _e('Reset all the ' . $option_name . ' options to the default recommended settings.', 'lr-plugin-slug'); ?>
                        <span class="lr-tooltip" data-title="<?php _e('This option will reset all the settings to the default ' . $option_name . ' plugin settings.', 'lr-plugin-slug'); ?>">
                            <span class="dashicons dashicons-editor-help"></span>
                        </span>
                    </h5>
                    <div>
                        <form method="post" action="" class="lr-reset">
                            <?php submit_button('Reset All Options', 'secondary', 'reset', false); ?>
                        </form>
                    </div>
                </div>
            </div>
            <?php
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
