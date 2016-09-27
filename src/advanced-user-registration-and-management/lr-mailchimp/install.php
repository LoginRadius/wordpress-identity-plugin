<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * class responsible for setting default settings for mailchimp.
 */
class LR_Mailchimp_Install {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->mailchimp_options();
		$this->set_default_options();
	}

	/**
	 * Loads global mailchimp options used for init and reset.
	 *
	 * @global mailchimp_options
	 */
	private function mailchimp_options() {
		global $mailchimp_options;

		$mailchimp_options = array(
				'mailchimp_subscribe' => '0',
				'mailchimp_lists'	  => '',
				'mailchimp_apikey'	  => ''
		);
	}

	/**
	 * Function for adding default mailchimp settings at activation.
	 */
	public static function set_default_options() {
		global $lr_mailchimp_settings, $mailchimp_options;

		if( ! get_option( 'LR_Mailchimp_Settings' ) ) {
			update_option( 'LR_Mailchimp_Settings', $mailchimp_options );
			$lr_mailchimp_settings = get_option( 'LR_Mailchimp_Settings' );
		}
	}

	/**
	 * Function to reset mailchimp options to default.
	 */
	public static function reset_loginradius_mailchimp_options() {
		global $lr_mailchimp_settings, $mailchimp_options;

		update_option( 'LR_Mailchimp_Settings', $mailchimp_options );
		// Get mailchimp settings
		$lr_mailchimp_settings = get_option( 'LR_Mailchimp_Settings' );
	}
}

new LR_Mailchimp_Install();