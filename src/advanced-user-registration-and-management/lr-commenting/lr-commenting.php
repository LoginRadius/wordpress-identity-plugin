<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Commenting')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Commenting {

        /**
         * Constructor
         */
        public function __construct() {
            if (!class_exists('LR_Social_Login')) {
                return;
            }
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(),'install'),10,1);
            add_action('lr_plugin_deactivate', array(get_class(),'uninstall'),10,1);

            // Declare constants and load dependencies.
            $this->load_dependencies();

            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 4);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Social Commenting Settings', 'Social Commenting', 'manage_options', 'loginradius_commenting', array('LR_Commenting_Admin', 'options_page'));
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install($blog_id) {
            require_once (dirname(__FILE__) . '/install.php');
            LR_Commenting_Install::set_default_options($blog_id);
        }
        public static function uninstall($blog_id) {
            if($blog_id){
                delete_blog_option($blog_id, 'LR_Commenting_Settings');
            }else{
                delete_option( 'LR_Commenting_Settings');
            }
        }
        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">'.__('Commenting settings have been reset and default values loaded').'</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }
        

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global lr_commenting_settings
         */
        private function load_dependencies() {
            global $lr_commenting_settings;

            // Get LoginRadius commenting settings
            $lr_commenting_settings = get_option('LR_Commenting_Settings');

            // Load required files.
            require_once( 'includes/helpers/ajax.php' );
            require_once( 'includes/display/loginradius_display_class.php' );
            require_once( 'admin/class-lr-commenting-admin.php' );

            new Ajax_Helper();
            new LoginRadius_Display();
        }

    }

    new LR_Commenting();
}

