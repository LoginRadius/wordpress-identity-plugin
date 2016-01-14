<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * class responsible for setting default settings for disqus sso.
 */
class LR_Disqus_Install {

	static $disqus_options = array(
		'disqus_sso_enable' => '',
		'popup_title' => 'Enter Title Here',
		'lr_disqus_sso_page_id' => ''
	);
	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->set_default_options();
	}

	/**
	 * Function for adding default disqus settings at activation.
	 */
	public static function set_default_options() {
		global $lr_disqus_settings;

		if( ! get_option( 'LR_Disqus_Settings' ) ) {
			update_option( 'LR_Disqus_Settings', self::$disqus_options );
			$lr_disqus_settings = get_option( 'LR_Disqus_Settings' );
		}
	}

	/**
	 * Function to reset Disqus options to default.
	 */
	public static function reset_options() {
		global $lr_disqus_settings;

		update_option( 'LR_Disqus_Settings', self::$disqus_options );
		// Get disqus settings
		$lr_disqus_settings = get_option( 'LR_Disqus_Settings' );
	}
}

new LR_Disqus_Install();