<?php

/**
 * class responsible for setting default settings for LoginRadius Social Login and Share plugin
 */
class Activation_Install {

    private static $login_options = array(        
        'delete_options' => '1' 
    );

    private static $api_options = array(        
        'apikey' => '' 
    );

    /**
     * Function for adding default plugin settings at activation
     */
    public static function set_default_options() {

        if ( ! get_option( 'ciam_uninstall_settings' ) ) {
            // If plugin loginradius_db_version option not exist, it means plugin is not latest and update options.
            update_option( 'ciam_uninstall_settings', self::$login_options );
        }
        if ( ! get_option( 'ciam_api_settings' ) ) {
            // If plugin loginradius_db_version option not exist, it means plugin is not latest and update options.
            update_option( 'ciam_api_settings', self::$api_options );
        }
    }

}
