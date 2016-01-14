<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Disqus' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Disqus {

        /**
         * Constructor
         */
        public function __construct() {
            if( ! class_exists( 'LR_Social_Login' ) ){
                return;
            }
            // Register Activation hook callback.
            $this->install();

            // Declare constants and load dependencies.
            $this->define_constants();
            $this->load_dependencies();

            add_action( 'wp_footer', array( $this, 'embed_scripts' ) );
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 9 );
        }

        static function embed_scripts(  ) {
            global $post, $wp_version, $lr_disqus_settings;

            //Used for is_plugin_active check
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if( ! isset( $lr_disqus_settings['disqus_sso_enable'] ) || $lr_disqus_settings['disqus_sso_enable'] != '1' || ! is_plugin_active( 'disqus-comment-system/disqus.php' ) ) {
                return;
            }
            
            $embed_vars = array(
                'disqusConfig' => array(
                    'platform' => 'wordpress@'.$wp_version,
                    'language' => apply_filters( 'disqus_language_filter', '' ),
                ),
                'disqusIdentifier' => dsq_identifier_for_post( $post ),
                'disqusShortname' => strtolower( get_option( 'disqus_forum_url' ) ),
                'disqusTitle' => dsq_title_for_post( $post ),
                'disqusUrl' => get_permalink(),
                'options' => array(
                    'manualSync' => get_option('disqus_manual_sync'),
                ),
                'postId' => $post->ID,
            );
            // Add SSO vars if enabled
            $sso = dsq_sso();
            if ($sso) {
                global $current_site;

                foreach ($sso as $k=>$v) {
                    $embed_vars['disqusConfig'][$k] = $v;
                }
                $sitename = get_bloginfo('name');
                $embed_vars['disqusConfig']['sso'] = array(
                    'name' => wp_specialchars_decode( $sitename, ENT_QUOTES ),
                    'button' => get_option( 'disqus_sso_button' ),
                    'url' => get_permalink( $lr_disqus_settings['lr_disqus_sso_page_id'] ),
                    'logout' => html_entity_decode( wp_logout_url( get_permalink() ) ),
                    'width' => '300',
                    'height' => '300',
                );
            }
            
            wp_localize_script( 'dsq_embed_script', 'embedVars', $embed_vars );
            wp_enqueue_script( 'dsq_embed_script' );
        }

        function create_loginradius_menu() {
            add_submenu_page( 'LoginRadius', 'Disqus SSO Settings', 'Disqus SSO', 'manage_options', 'lr_disqus', array('LR_Disqus_Admin', 'options_page') );
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install() {
            global $wpdb;
            
            require_once (dirname(__FILE__) . '/install.php');
            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    LR_Disqus_Install::set_default_options();
                }
                switch_to_blog($old_blog);
                return;
            } else {
                LR_Disqus_Install::set_default_options();
            }
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_DISQUS_DIR', plugin_dir_path(__FILE__) );
            define( 'LR_DISQUS_URL', plugin_dir_url(__FILE__) );
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginradius_commenting_settings
         */
        private function load_dependencies() {
            global $lr_disqus_settings;

            // Get LoginRadius commenting settings
            $lr_disqus_settings = get_option('LR_Disqus_Settings');

            require_once( LR_DISQUS_DIR . 'includes/templates/lr-disqus-sso-page-template.php' );
            require_once( LR_DISQUS_DIR . 'includes/helpers/ajax.php' );
            new Ajax_Login_Helper();

            // Load required files.
            require_once("admin/class-loginradius-disqus-admin.php");
        }

    }

    new LR_Disqus();
}
