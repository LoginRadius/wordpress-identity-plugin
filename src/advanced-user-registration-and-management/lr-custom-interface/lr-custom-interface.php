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
            if(!class_exists('LR_Social_Login')){
                return;
            }
            
            $this->define_constants();
            // Register Activation hook callback.
            $this->install();
            // Declare constants and load dependencies.
            $this->load_dependencies();
            add_action('lr_admin_page', array($this, 'create_loginradius_menu'),7);
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'Custom Interface Settings', 'Custom Interface', 'manage_options', 'loginradius_customization', array('LR_Custom_Interface_Admin', 'options_page'));
        }

        public static function install() {
            
            global $wpdb;
            require_once dirname( __FILE__ ) . '/install.php';

            if (function_exists('is_multisite') && is_multisite()) {
                if (!isset($_GET['page']) || !in_array($_GET['page'], array('LoginRadius', 'SocialLogin', 'User_Registration', 'loginradius_sso', 'loginradius_share', 'loginradius_commenting', 'loginradius_social_profile_data', 'loginradius_social_invite', 'loginradius_customization', 'loginradius_mailchimp', 'lr_google_analitics'))) {
                    return;
                }
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blogids as $blog_id ) {
                    switch_to_blog($blog_id);
                    LR_Custom_Interface_Install::set_default_options();
                }
                switch_to_blog( $old_blog );
                return;
            } else {
                LR_Custom_Interface_Install::set_default_options();
            }
        }

        /**
         *  Define constants needed across the plug-in
         */
        private function define_constants() {
            define('LR_CUSTOM_INTERFACE_DIR', plugin_dir_path(__FILE__));
            define('LR_CUSTOM_INTERFACE_URL', plugin_dir_url(__FILE__));
        }

        private function load_dependencies() {
            global $lr_custom_interface_settings;

            // Get LoginRadius commenting settings
            $lr_custom_interface_settings = get_option('LR_Custom_Interface_Settings');

            // Load required files.
            require_once( LR_CUSTOM_INTERFACE_DIR.'admin/class-loginradius-custom-interface-admin.php' );
        }
    }
new LR_Custom_Interface();
}


