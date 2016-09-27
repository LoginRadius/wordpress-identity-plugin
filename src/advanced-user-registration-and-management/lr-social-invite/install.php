<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * class responsible for setting default settings for social invite.
 */
class LR_Social_Invite_Install {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->social_invite_options();
		$this->set_default_options();
	}

	/**
	 * Loads global social_invite options used for init and reset.
	 *
	 * @global social_invite_options
	 */
	private function social_invite_options() {
		global $social_invite_options;

		$social_invite_options = array(
			'social_invite_enable' => '',
			'sort_by' => 'name',
			'sort_direction' => 'asc',
			'subject' => 'Enter social invite subject here',
			'message' => 'Enter social invite message here',
			'enable_editable' => '1',
			'enable_custom_email' => '',
			'email_name' => 'Email name',
			'email_address' => 'someone@someone.com',
			'fb_id' => ''
		);
	}

	/**
	 * Function for adding default social_invite settings at activation.
	 */
	public static function set_default_options() {
		global $wpdb, $lr_social_invite_settings, $social_invite_options;

		if( ! get_option( 'LR_Social_Invite_Settings' ) ) {
			update_option( 'LR_Social_Invite_Settings', $social_invite_options );	
		}
		$lr_social_invite_settings = get_option( 'LR_Social_Invite_Settings' );

		if( isset( $lr_social_invite_settings['social_invite_enable'] ) && $lr_social_invite_settings['social_invite_enable'] == '1' ) {
			$wpdb->query( 'CREATE TABLE IF NOT EXISTS `'. $wpdb->base_prefix .'lr_social_invite_contacts`(
				`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`user_id` int( 11 ) NOT NULL,
				`provider` varchar( 20 ) NOT NULL,
				`name` varchar( 100 ) DEFAULT NULL,
				`email` varchar( 100 ) DEFAULT NULL,
				`phone_number` varchar( 30 ) DEFAULT NULL,
				`social_id` varchar( 255 ) DEFAULT NULL,
				`profile_url` varchar( 1000 ) DEFAULT NULL,
				`image_url` varchar( 1000 ) DEFAULT NULL,
				`status` text DEFAULT NULL,
				`industry` varchar( 50 ) DEFAULT NULL,
				`country` varchar( 20 ) DEFAULT NULL,
				`location` varchar( 255 ) DEFAULT NULL,
				`gender` varchar( 10 ) DEFAULT NULL,
				`dob` date DEFAULT NULL,
				`registered` int( 1 ) DEFAULT 0,
				`reg_user_id` int( 11 ) NULL
			)' );

			$wpdb->query( 'CREATE TABLE IF NOT EXISTS `'. $wpdb->base_prefix .'lr_social_invite_tokens`(
				`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`user_id` int( 11 ) NOT NULL,
				`provider` varchar( 20 ) NOT NULL,
				`token` longtext NOT NULL,
				`creationdatetime` datetime NOT NULL
			)' );
		}
	}

	/**
	 * Function to reset Social_Invite options to default.
	 */
	public static function reset_loginradius_social_invite_options() {
		global $lr_social_invite_settings, $social_invite_options;

		update_option( 'LR_Social_Invite_Settings', $social_invite_options );
		// Get social_invite settings
		$lr_social_invite_settings = get_option( 'LR_Social_Invite_Settings' );
	}
}

new LR_Social_Invite_Install();