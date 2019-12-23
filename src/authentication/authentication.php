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



            global $ciam_credentials;

            

            if(!isset($ciam_credentials['apikey']) || empty($ciam_credentials['apikey']) || !isset($ciam_credentials['secret']) || empty($ciam_credentials['secret'])){ 

                 return;   

             }

            $this->load_dependencies();

            add_action('ciam_admin_menu', array($this, 'menu'));

            /* action for debug mode */

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');

           

        }



        /*

         * Function will create the menu.

         */

        

        

        public function menu() { 

            add_submenu_page('ciam-activation', 'Authentication Settings', 'Authentication', 'manage_options', 'ciam-authentication', array('CIAM_Authentication_Admin', 'options_page'));

            /* action for debug mode */

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');

        }



        /**

         * Loads PHP files that required by the plug-in

         */

        public function load_dependencies() { 



            // Load required files.

            

            require_once(CIAM_PLUGIN_DIR . "authentication/front/pages/header.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/pages/login.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/pages/common.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/pages/registration.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/pages/passwordhandler.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/pages/backupcode.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/pages/profile.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/admin/class-authentication.php");

            

            require_once(CIAM_PLUGIN_DIR . "authentication/front/helper.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/login.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/wp-default-login.php");

            require_once(CIAM_PLUGIN_DIR . "authentication/front/social-login.php");



              /* action for debug mode */

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');

        }



    }



    new CIAM_Authentication();

}

