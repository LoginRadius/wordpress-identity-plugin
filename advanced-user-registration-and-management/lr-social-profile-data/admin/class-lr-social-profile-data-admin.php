<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Social_Profile_Data_Admin' ) ) {

    class LR_Social_Profile_Data_Admin {
        /*
         * Constructor
         */
        public function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts') );
            add_action( 'admin_enqueue_scripts', array( $this, 'include_thickbox') );
            add_action( 'admin_enqueue_scripts', array( $this, 'get_profile_data_script') );
            add_action( 'admin_init', array( $this, 'admin_init' ) );
        }

        /*
         * Enqueue Admin Scripts
         */

        public function load_scripts( $hook ) {

            if ( $hook != 'loginradius_page_loginradius_social_profile_data' ) {
                return;
            }
            global $lr_js_in_footer;
            wp_enqueue_script('lr_profile_data_admin_script', LR_SOCIAL_PROFILE_DATA_URL . 'assets/js/social-profile-data-admin.js', array('jquery'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            wp_enqueue_style('lr_profile_data_admin_style', LR_SOCIAL_PROFILE_DATA_URL . 'assets/css/lr-social-profile-data-settings.css');
        }

        /**
         * Register LoginRadius_Social_Profile_Data_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {

            register_setting( 'lr_social_profile_data_settings', 'LoginRadius_Social_Profile_Data_settings', array( $this, 'social_profile_validate' ) );

            //replicate Social Commenting configuration to the subblogs in the multisite network
            if ( is_multisite() && is_main_site() ) {
                add_action( 'wpmu_new_blog', array( $this, 'replicate_settings_to_new_blog' ) );
                add_action( 'update_option_LoginRadius_Social_Profile_Data_settings', array( $this, 'update_old_blogs' ) );
            }
        }

        public function social_profile_validate( $settings ) {
            return $settings;
        }

        // replicate the social Social Profile Data config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog( $blogId ) {
            global $lr_social_profile_data_settings;
            add_blog_option( $blogId, 'LoginRadius_Social_Profile_Data_settings', $lr_social_profile_data_settings );
        }

        // update the social Social Profile Data options in all the old blogs
        public function update_old_blogs( $oldConfig ) {
            global $loginradius_api_settings;
            if ( isset( $loginradius_api_settings['multisite_config'] ) && $loginradius_api_settings['multisite_config'] == '1' ) {
                $settings = get_option('LoginRadius_Social_Profile_Data_settings');
                $blogs = wp_get_sites();
                foreach ( $blogs as $blog ) {
                    update_blog_option( $blog['blog_id'], 'LoginRadius_Social_Profile_Data_settings', $settings );
                }
            }
        }

        /**
         * include thickbox js and css
         */
        public function include_thickbox( $hook ) {
            if ( $hook == 'users.php' ) {
                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
            }
        }

        /**
         * include thickbox js and css
         */
        public function get_profile_data_script( $hook ) {
            if ( $hook == 'users.php' ) {
                ?>
                <script type="text/javascript">
                    function loginRadiusGetProfileData(userId) {
                        tb_show('User Profile Data', 'admin-ajax.php?action=lr_get_profile_data&user_id=' + userId + '&width=1100&height=500');
                    }
                </script>
                <?php
            }
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once LR_SOCIAL_PROFILE_DATA_DIR."admin/views/settings.php";
            LR_Social_Profile_Data_Admin_Settings:: render_options_page();
        }

    }

    new LR_Social_Profile_Data_Admin();
}
