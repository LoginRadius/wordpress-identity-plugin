<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}



if (!class_exists('CIAM_Authentication')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class CIAM_Authentication {
        
        

        /**
         * Constructor
         */
        public function __construct() { 

            global $ciam_credencials;
            
            if(!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])){ 
                 return;   
             }
            add_action('wp_enqueue_scripts', array($this, 'front_scripts'));
            $this->load_dependencies();
            add_action('ciam_admin_menu', array($this, 'menu'));
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
           
        }

        public function menu() { 
            add_submenu_page('ciam-activation', 'Authentication Settings', 'Authentication', 'manage_options', 'ciam-authentication', array('CIAM_Authentication_Admin', 'options_page'));
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public static function load_dependencies() { 

            // Load required files.
            
            
            require_once(CIAM_PLUGIN_DIR . "authentication/front/class-ciam-hooks.php");
            require_once(CIAM_PLUGIN_DIR . "authentication/admin/class-authentication.php");
            require_once(CIAM_PLUGIN_DIR . "authentication/front/helper.php");
            require_once(CIAM_PLUGIN_DIR . "authentication/front/login.php");
            require_once(CIAM_PLUGIN_DIR . "authentication/front/class-ciam-wp-default-login.php");
            require_once(CIAM_PLUGIN_DIR . "authentication/front/class-ciam-function.php");
            require_once(CIAM_PLUGIN_DIR . "authentication/front/class-ciam-social-login.php");

              /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), '');
        }

        public function front_scripts() {
            wp_enqueue_script('ciam', '//auth.lrcontent.com/v2/js/LoginRadiusV2.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
            wp_enqueue_script('ciam_fucntions', CIAM_PLUGIN_URL . 'authentication/assets/js/custom.min.js', array('jquery'), CIAM_PLUGIN_VERSION);
            wp_enqueue_script('ciam_fucntions', CIAM_PLUGIN_URL . 'authentication/assets/js/custom.js', array('jquery'), CIAM_PLUGIN_VERSION);
            
             wp_enqueue_style('ciam-style', CIAM_PLUGIN_URL . 'authentication/assets/css/style.min.css');
            wp_enqueue_style('ciam-style', CIAM_PLUGIN_URL . 'authentication/assets/css/style.css');
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
              
        }

        


    }

    new CIAM_Authentication();
}
