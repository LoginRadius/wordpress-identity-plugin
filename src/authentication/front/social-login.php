<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius Ciam.
 */
if (!class_exists('CIAM_Social_Login')) {

    class CIAM_Social_Login {


        /*
         * class constructor fucntion
         */
        
        public function __construct(){
            
            add_action('init', array($this, 'init'));
        }
        
        
        /**
         * Load necessary scripts and CSS.
         * 
         * @global type $wpdb
         */
        
        public function init() {
           
            
            global $loginradius_api_settings;
          
            $loginradius_api_settings = get_option('LoginRadius_API_settings');
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
          
        }

    }
    
    new CIAM_Social_Login();

}