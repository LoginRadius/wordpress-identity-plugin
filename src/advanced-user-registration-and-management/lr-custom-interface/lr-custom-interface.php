<?php

// Custom Interface
// Set this as 1 when custom interface enabled
//$lr_custom_interface_settings['custom_interface']

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Custom_Interface')) {

    /**
     * The main class and initialization point of the plugin
     */
    class LR_Custom_Interface {

        /**
         *  Construction
         */
        public function __construct() {
            if (!class_exists('LR_Social_Login')) {
                return;
            }
            add_action('wp_enqueue_scripts', array($this, 'register_scripts_styles'));
            add_action('login_enqueue_scripts', array($this, 'register_scripts_styles'));
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);
            // Declare constants and load dependencies.
            $this->load_dependencies();
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 7);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Custom Interface Settings', 'Custom Interface', 'manage_options', 'loginradius_customization', array('LR_Custom_Interface_Admin', 'options_page'));
        }

        /**
         * Registers Scripts and Styles needed in all sections, is called from all sections
         *
         */
        public static function register_scripts_styles() {
            global $lr_js_in_footer;
            // Custom Interface must be loaded in header.
            wp_register_script('lr-custom-interface', '//cdn.loginradius.com/hub/prod/js/lr-custom-interface.3.js', array(), '3.0.0', $lr_js_in_footer);
        }

        public static function install($blog_id) {
            require_once dirname(__FILE__) . '/install.php';
            LR_Custom_Interface_Install::set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                delete_blog_option($blog_id, 'LR_Custom_Interface_Settings');
            } else {
                delete_option('LR_Custom_Interface_Settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                require_once( LR_ROOT_DIR . 'lr-custom-interface/includes/helper/ajax_helper.php' );
                $ajax = new LR_CI_Ajax_Helper();
                $response = $ajax->reset_ci_folder();
                echo '<p style="display:none;" class="lr-' . $response['isValid'] . '-box lr-notif">' . $response['message'] . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        private function load_dependencies() {
            global $lr_custom_interface_settings;
            // Get LoginRadius commenting settings
            $lr_custom_interface_settings = get_option('LR_Custom_Interface_Settings');

            // Load required files.
            require_once( LR_ROOT_DIR . 'lr-custom-interface/admin/class-loginradius-custom-interface-admin.php' );
        }

    }

    new LR_Custom_Interface();
}


