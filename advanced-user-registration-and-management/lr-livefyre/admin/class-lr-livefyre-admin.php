<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_LiveFyre_Admin' ) ) {

	class LR_LiveFyre_Admin {

		/*
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init') );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Register LR_LiveFyre_Settings and its sanitization callback. Replicate loginradius settings on multisites.
		 */
		public function admin_init() {
			global $lr_livefyre_settings;

			register_setting( 'lr_livefyre_settings', 'LR_LiveFyre_Settings' );

			// Replicate livefyre configuration to the subblogs in the multisite network
			if ( is_multisite() && is_main_site() ) {
				add_action( 'wpmu_new_blog', array( $this, 'replicate_settings_to_new_blog' ) );
				add_action( 'update_option_LR_LiveFyre_Settings', array( $this, 'update_old_blogs' ) );
			}
		}

		static function admin_enqueue_scripts( $hook ) {
			if( $hook !== 'loginradius_page_lr_livefyre' ){
				return;
			}
			wp_enqueue_script( 'lr-livefyre-admin.js', LR_LIVEFYRE_URL . 'assets/js/lr-livefyre-admin.js' );
		}

		// Replicate the livefyre config to the new blog created in the multisite network
		public function replicate_settings_to_new_blog( $blogId ) {
			global $lr_livefyre_settings;
			add_blog_option( $blogId, 'LR_LiveFyre_Settings', $lr_livefyre_settings );
		}

		// Update the livefyre options in all the old blogs
		public function update_old_blogs( $oldConfig ) {
			global $loginradius_api_settings;
			if ( isset( $loginradius_api_settings['multisite_config'] ) && $loginradius_api_settings['multisite_config'] == '1' ) {
				$settings = get_option( 'LR_LiveFyre_Settings' );
				$blogs = wp_get_sites();
				foreach ( $blogs as $blog ) {
					update_blog_option( $blog['blog_id'], 'LR_LiveFyre_Settings', $settings );
				}
			}
		}

		/*
		 * Callback for add_submenu_page,
		 * This is the first function which is called while plugin admin page is requested
		 */
		public static function options_page() {
			include_once "views/settings.php";
			LR_LiveFyre_Admin_Settings:: render_options_page();
		}
	}
	new LR_LiveFyre_Admin();
}
