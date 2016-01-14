<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Class responsible for setting default settings for DFP.
 */
class LR_DFP_Install {

	private static $options = array(
		'enable' => '',
		'target' => array()
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
		global $lr_dfp_settings;

		if( ! get_option( 'LR_DFP_Settings' ) ) {
			update_option( 'LR_DFP_Settings', self::$options );
			$lr_dfp_settings = get_option( 'LR_DFP_Settings' );
		}
	}

	/**
	 * Function to reset DFP options to default.
	 */
	public static function reset_options() {
		global $lr_dfp_settings;

		update_option( 'LR_DFP_Settings', self::$options );
		// Get disqus settings
		$lr_dfp_settings = get_option( 'LR_DFP_Settings' );
	}
}
new LR_DFP_Install();