<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The activation settings class.
 */
if ( ! class_exists( 'LR_Activation_Helper' ) ) {

    class LR_Activation_Helper {
        public function __construct() {
            add_action( 'admin_notices', array( $this, 'display_notice_to_insert_api_and_secret' ) );
        }

        /*
         * Display notice on plugin page, if LR API Key and Secret are empty
         */
        public static function display_notice_to_insert_api_and_secret() {
            $loginradius_api_settings = get_option('LoginRadius_API_settings' );
            if ( class_exists('LR_Social_Login') ) {
                if ( ! isset( $loginradius_api_settings['LoginRadius_apikey'] ) || ! isset( $loginradius_api_settings['LoginRadius_secret'] ) || trim( $loginradius_api_settings['LoginRadius_apikey'] ) == '' || trim( $loginradius_api_settings['LoginRadius_secret'] ) == '' ) {
                    ?>
                    <div id="loginRadiusKeySecretNotification" class="wrap" style="background-color: #FFFFE0; border:1px solid #E6DB55; padding:5px;">
                        <?php _e( 'To activate the <strong>Social Login</strong>, insert LoginRadius API Key and Secret in the <strong>API Settings</strong> section below. <strong>Social Sharing does not require API Key and Secret</strong>.', 'lr-plugin-slug' ); ?>
                    </div>
                    <?php
                }
            }
        }

    }
    new LR_Activation_Helper();

}