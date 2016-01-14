<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * class responsible for setting default settings for Salesforce.
 */
class LR_Salesforce_Install {

    /**
     * Constructor
     */
    public function __construct() {
        $this->salesforce_options();
        $this->set_default_options();
    }

    /**
     * Loads global Salesforce options used for init and reset.
     *
     * @global social_invite_options
     */
    private function salesforce_options() {
        global $salesforce_options;

        $salesforce_options = array(
            'salesforce_subscribe' => '1'
        );
    }

    /**
     * Function for adding default Salesforce settings at activation.
     */
    public static function set_default_options() {
        global $salesforce_options;

        if ( ! get_option( 'LR_Salesforce_Settings' ) ) {
            update_option( 'LR_Salesforce_Settings', $salesforce_options );
        }
    }

    /**
     * Function to reset Social_Invite options to default.
     */
    public static function reset_loginradius_salesforce_options() {
        global $lr_salesforce_settings, $salesforce_options;

        update_option( 'LR_Salesforce_Settings', $salesforce_options);
        // Get Salesforce settings
        $lr_salesforce_settings = get_option( 'LR_Salesforce_Settings' );
    }

}

new LR_Salesforce_Install();
