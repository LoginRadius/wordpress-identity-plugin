<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Livefyre_Install' ) ) {

    class LR_Livefyre_Install {

    	private static $options = array(
    			'enable_livefyre' => '',
    			'enable_login' => '1');
    	/**
    	 * Constructor
    	 */
    	public function __construct() {
    	    
    	}

    	public static function set_default_options() {
    	    global $lr_livefyre_settings;
    	    if ( ! get_option( 'LR_LiveFyre_Settings' ) ) {
    	        // Adding LoginRadius plugin options if not available.
    	        update_option('LR_LiveFyre_Settings', self::$options);
    	    }

    	    // Get LoginRadius plugin settings.
    	    $lr_livefyre_settings = get_option('LR_LiveFyre_Settings');
    	}

        public static function reset_options() {
            global $lr_livefyre_settings;

            update_option( 'LR_LiveFyre_Settings', self::$options );
            // Get disqus settings
            $lr_livefyre_settings = get_option( 'LR_LiveFyre_Settings' );
        }
        
    }
}