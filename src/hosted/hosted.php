<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Hosted')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class CIAM_Hosted {

        /**
         * Constructor
         */
        public function __construct() {
            global $ciam_credencials;
           
            if(!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])){
                 return;   
             }
            $this->load_dependencies();
          /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public function load_dependencies() {

            // Load required files.
            require_once(CIAM_PLUGIN_DIR . "hosted/front/hosted-page.php");
            require_once(CIAM_PLUGIN_DIR . "hosted/admin/settings.php");
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

    }

    new CIAM_Hosted();
}
