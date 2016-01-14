<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * class responsible for setting default settings for social invite.
 */
class LR_BlueHornet_Install {

    /**
     * Constructor
     */
    public function __construct() {
        $this->bluehornet_options();
        $this->set_default_options();
        
    }

    /**
     * Loads global social_invite options used for init and reset.
     *
     * @global social_invite_options
     */
    private function bluehornet_options() {
        global $bluehornet_options;

        $bluehornet_options = array(
            'bluehornet_subscribe' => ''
        );
    }

    /**
     * Function for adding default social_invite settings at activation.
     * 
     * @global type $bluehornet_options
     */
    public static function set_default_options() {
        global $bluehornet_options;

        if ( ! get_option( 'LR_BlueHornet_Settings' ) ) {
            update_option( 'LR_BlueHornet_Settings', $bluehornet_options );
        }
    }

    /**
     * Function to reset Social_Invite options to default.
     * 
     * @global type $lr_bluehornet_settings
     * @global type $bluehornet_options
     */
    public static function reset_loginradius_bluehornet_options() {
        global $lr_bluehornet_settings, $bluehornet_options;

        update_option('LR_BlueHornet_Settings', $bluehornet_options);
        // Get bluehornet settings
        $lr_bluehornet_settings = get_option('LR_BlueHornet_Settings');
    }

}

new LR_BlueHornet_Install();
