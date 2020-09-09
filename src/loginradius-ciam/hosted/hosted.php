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
            global $ciam_credentials;
           
            if(!isset($ciam_credentials['apikey']) || empty($ciam_credentials['apikey']) || !isset($ciam_credentials['secret']) || empty($ciam_credentials['secret'])){
                 return;   
             }
            
            add_action('init',array($this,'init'));
        }
        
        /*
         * This function will called with the with the wordpress init function.
         */
        public function init(){ 
            
            $this->load_dependencies();
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /**
         * Loads PHP files that required by the plug-in
         */
        public function load_dependencies() {

            // Load required files.
            require_once(CIAM_PLUGIN_DIR . "hosted/front/hosted-page.php");
            require_once(CIAM_PLUGIN_DIR . "hosted/admin/settings.php");
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

    }

    new CIAM_Hosted();
}
