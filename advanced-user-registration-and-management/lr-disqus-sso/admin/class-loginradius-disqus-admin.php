<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Disqus_Admin' ) ) {

	class LR_Disqus_Admin {

		/*
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init') );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Register LR_Disqus_Settings and its sanitization callback. Replicate loginradius settings on multisites.
		 */
		public function admin_init() {
			global $lr_disqus_settings;

			register_setting( 'lr_disqus_settings', 'LR_Disqus_Settings', array( $this, 'validate_options' ) );

			// Replicate disqus configuration to the subblogs in the multisite network
			if ( is_multisite() && is_main_site() ) {
				add_action( 'wpmu_new_blog', array( $this, 'replicate_settings_to_new_blog' ) );
				add_action( 'update_option_LR_Disqus_Settings', array( $this, 'update_old_blogs' ) );
			}
		}

		static function admin_enqueue_scripts( $hook ) {

			if( $hook !== 'loginradius_page_lr_disqus' ){
				return;
			}
			wp_enqueue_script( 'lr-disqus-custom-interface', LR_DISQUS_URL . 'assets/js/lr-disqus-sso-custom.js' );
		}

		// Replicate the disqus config to the new blog created in the multisite network
		public function replicate_settings_to_new_blog( $blogId ) {
			global $lr_disqus_settings;
			add_blog_option( $blogId, 'LR_Disqus_Settings', $lr_disqus_settings );
		}

		// Update the disqus options in all the old blogs
		public function update_old_blogs( $oldConfig ) {
			global $loginradius_api_settings;
			if ( isset( $loginradius_api_settings['multisite_config'] ) && $loginradius_api_settings['multisite_config'] == '1' ) {
				$settings = get_option( 'LR_Disqus_Settings' );
				$blogs = wp_get_sites();
				foreach ( $blogs as $blog ) {
					update_blog_option( $blog['blog_id'], 'LR_Disqus_Settings', $settings );
				}
			}
		}

		/**
		 * validate_options description
		 * @param  array $settings [description]
		 * @return array           [description]
		 */
        public static function validate_options( $settings ) {
            global $loginRadiusSettings;
            if( isset( $settings['lr_disqus_sso_page_id'] ) && ! empty( $settings['lr_disqus_sso_page_id'] ) ) {
            	update_post_meta( $settings['lr_disqus_sso_page_id'], '_wp_page_template', 'lr_disqus_sso_template.php' );
            }
            return $settings;
        }

		/*
		 * Callback for add_submenu_page,
		 * This is the first function which is called while plugin admin page is requested
		 */
		public static function options_page() {
			include_once "views/settings.php";
			LR_Disqus_Admin_Settings:: render_options_page();
		}

	}

}

new LR_Disqus_Admin();
