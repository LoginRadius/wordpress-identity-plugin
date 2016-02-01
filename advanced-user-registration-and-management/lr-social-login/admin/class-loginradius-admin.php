<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Social_Login_Admin') ) {

    class LR_Social_Login_Admin {

        /**
         * LR_Social_Login_Admin class instance
         *
         * @var string
         */
        private static $instance;

        /**
         * Get singleton object for class LR_Social_Login_Admin
         *
         * @return object LR_Social_Login_Admin
         */
        public static function get_instance() {
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof LR_Social_Login_Admin ) ) {
                self::$instance = new LR_Social_Login_Admin();
            }
            return self::$instance;
        }

        /*
         * Constructor for class LR_Social_Login_Admin
         */

        public function __construct() {
            if ( ! class_exists( 'Admin_Helper' ) ) {
                require_once "helpers/class-admin-helper.php";
            }
            // Registering hooks callback for admin section.
            $this->register_hook_callbacks();
        }

        public static function load_head_scripts() {
            LR_Common::load_login_script( true );
        }

        /*
         * Register admin hook callbacks
         */

        public function register_hook_callbacks() {
            global $loginRadiusSettings;

            //add_filter( 'plugin_action_links', array($this, 'plugin_action_links'), 10, 2 );
            add_action( 'admin_init', array( $this, 'admin_init' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 1);
            add_action( 'admin_enqueue_scripts', array( $this, 'load_styles' ), 10);
            if ( isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && ( $loginRadiusSettings['LoginRadius_socialLinking'] == '1' ) ) {
                add_action('admin_notices', array( $this, 'account_linking_info_on_profile_page' ) );
            }
        }

        /**
         * Callback for admin_menu hook,
         * Register LoginRadius_settings and its sanitization callback. Add Login Radius meta box to pages and posts.
         */
        public function admin_init() {
            global $pagenow, $loginRadiusSettings;

            register_setting('LoginRadius_setting_options', 'LoginRadius_settings', array( $this, 'validate_options') );

            if ( $pagenow == 'profile.php' && isset( $_REQUEST['token'] ) && ! class_exists( 'LR_Raas_Install' ) ) {
                LR_Common::perform_linking_operation();
            }

            if ( ( isset( $loginRadiusSettings['LoginRadius_noProvider'] ) && $loginRadiusSettings['LoginRadius_noProvider'] == '1' ) || ( isset( $loginRadiusSettings['LoginRadius_enableUserActivation'] ) && $loginRadiusSettings['LoginRadius_enableUserActivation'] == '1' ) ) {
                add_filter( 'manage_users_columns', array( 'Admin_Helper', 'add_provider_column_in_users_list' ) );
                add_action( 'manage_users_custom_column', array( 'Admin_Helper', 'login_radius_show_provider' ), 15, 3 );
                if ( isset( $loginRadiusSettings['LoginRadius_enableUserActivation'] ) && $loginRadiusSettings['LoginRadius_enableUserActivation'] == '1' ) {
                    add_filter( 'admin_head', array( 'Admin_Helper', 'add_script_for_users_page' ), 10 );
                }
            }
            // Replicate Social Login configuration to the subblogs in the multisite network
            if ( is_multisite() && is_main_site() ) {
                add_action( 'wpmu_new_blog', array( $this, 'replicate_settings_to_new_blog' ) );
                add_action( 'update_option_LoginRadius_settings', array( $this, 'login_radius_update_old_blogs' ) );
            }
        }

        /*
         * Adding Javascript/Jquery for admin settings page
         */

        public function load_scripts( $hook ) {
            global $loginRadiusSettings, $lr_custom_interface_settings, $lr_js_in_footer;

            if ( $hook == 'loginradius_page_SocialLogin' || $hook == 'users.php' ) {
                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'jquery-ui-tabs' );
                wp_enqueue_script( 'thickbox' );
                wp_enqueue_script( 'jquery-ui-sortable' );
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'LoginRadius_options_page_script', LOGINRADIUS_PLUGIN_URL . 'assets/js/loginradius-options-page.js', array( 'jquery' ), LR_PLUGIN_VERSION, $lr_js_in_footer );
                wp_enqueue_script( 'LoginRadius_options_page_script2', LOGINRADIUS_PLUGIN_URL . 'assets/js/lr-social-login-admin.js', array( 'jquery', 'wp-color-picker' ), LR_PLUGIN_VERSION, $lr_js_in_footer );
            }

            if ( $hook == 'profile.php' && isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && $loginRadiusSettings['LoginRadius_socialLinking'] == '1' ) {
                // Enqueue Login Scripts must be called in head as footer call loads script to late
                add_action( 'admin_enqueue_scripts', array( 'LR_Common', 'enqueue_login_scripts' ), 10);

                if ( $lr_js_in_footer ) {
                    // Load head scripts after required scripts are loaded.
                    add_action( 'admin_footer', array( __CLASS__, 'load_head_scripts' ), 9999 );
                } else {
                    // Load head scripts after required scripts are loaded.
                    add_action( 'admin_head', array( __CLASS__, 'load_head_scripts' ), 9999 );
                }
            }
        }

        /*
         * adding style to plugin setting page
         */

        public function load_styles() {
            wp_enqueue_style( 'thickbox' );
        }

        // Replicate the social login config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog( $blogId ) {
            global $loginRadiusSettings;
            add_blog_option($blogId, 'LoginRadius_settings', $loginRadiusSettings );
        }

        // Update the social login options in all the old blogs
        public function login_radius_update_old_blogs( $oldConfig ) {
            global $loginradius_api_settings;

            if ( isset( $loginradius_api_settings['multisite_config'] ) && $loginradius_api_settings['multisite_config'] == '1' ) {
                $settings = get_option( 'LoginRadius_settings' );
                $blogs = wp_get_sites();
                foreach ( $blogs as $blog ) {
                    update_blog_option( $blog['blog_id'], 'LoginRadius_settings', $settings );
                }
            }
        }

        /*
         * Callback for add_menu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once LOGINRADIUS_PLUGIN_DIR."admin/views/settings.php";
            LR_Social_Login_Admin_Settings::render_options_page();
        }

        /**
         * Add a settings link to the Plugins page, so people can go straight from the plugin page to
         * settings page.
         */
        public function plugin_action_links( $links, $file ) {
            $settings_link = '<a href="admin.php?page=LoginRadius">' . esc_html__( 'Settings', 'lr-plugin-slug' ) . '</a>';
            if ( $file == 'loginradius-for-wordpress/LoginRadius.php' )
                array_unshift( $links, $settings_link );

            return $links;
        }

        /**
         * Validate plugin options,
         * Function to be called when settings save button is clicked on plugin settings page
         */
        public static function validate_options( $loginRadiusSettings ) {
            require_once LOGINRADIUS_PLUGIN_DIR . 'admin/helpers/class-admin-helper.php';

            $loginRadiusSettings['LoginRadius_socialavatar'] = ( ( isset( $loginRadiusSettings['LoginRadius_socialavatar'] ) && in_array( $loginRadiusSettings['LoginRadius_socialavatar'], array( 'socialavatar', 'largeavatar', 'defaultavatar' ) ) ) ? $loginRadiusSettings['LoginRadius_socialavatar'] : 'socialavatar' );
            $loginRadiusSettings['LoginRadius_dummyemail'] = ( isset( $loginRadiusSettings['LoginRadius_dummyemail'] ) && $loginRadiusSettings['LoginRadius_dummyemail'] == 'notdummyemail' ) ? 'notdummyemail' : 'dummyemail';

            $loginRadiusSettings['LoginRadius_redirect'] = ( ( isset( $loginRadiusSettings['LoginRadius_redirect'] ) && in_array( $loginRadiusSettings['LoginRadius_redirect'], array('samepage', 'homepage', 'dashboard', 'bp', 'custom') ) ) ? $loginRadiusSettings['LoginRadius_redirect'] : 'samepage' );
            $loginRadiusSettings['LoginRadius_loutRedirect'] = ( ( isset( $loginRadiusSettings['LoginRadius_loutRedirect'] ) && in_array( $loginRadiusSettings['LoginRadius_loutRedirect'], array('homepage', 'custom') ) ) ? $loginRadiusSettings['LoginRadius_loutRedirect'] : 'homepage' );
            $loginRadiusSettings['LoginRadius_loginformPosition'] = ( ( isset( $loginRadiusSettings['LoginRadius_loginformPosition'] )  && in_array( $loginRadiusSettings['LoginRadius_loginformPosition'], array( 'embed', 'beside') ) ) ? $loginRadiusSettings['LoginRadius_loginformPosition'] : 'embed' );
            $loginRadiusSettings['LoginRadius_regformPosition'] = ( ( isset( $loginRadiusSettings['LoginRadius_regformPosition'] ) && in_array( $loginRadiusSettings['LoginRadius_regformPosition'], array( 'embed', 'beside') ) ) ? $loginRadiusSettings['LoginRadius_regformPosition'] : 'embed' );
            $loginRadiusSettings['LoginRadius_commentform'] = ( ( isset( $loginRadiusSettings['LoginRadius_commentform'] ) && in_array( $loginRadiusSettings['LoginRadius_commentform'], array('old', 'new') ) ) ? $loginRadiusSettings['LoginRadius_commentform'] : 'new' );
            $loginRadiusSettings['LoginRadius_numColumns'] = ( isset( $loginRadiusSettings['LoginRadius_numColumns'] ) && is_numeric( $loginRadiusSettings['LoginRadius_numColumns'] ) ) ? $loginRadiusSettings['LoginRadius_numColumns'] : '';

            return $loginRadiusSettings;
        }

        /**
         * Displaying account linking on profile page
         */
        public static function account_linking_info_on_profile_page() {
            global $pagenow, $lr_custom_interface_settings;

            $user_Id = get_current_user_id();
            if ( ( isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && $loginRadiusSettings['LoginRadius_socialLinking'] == '1' ) || ! $user_Id == '1' ) {
                return;
            }

            $custom = false;
            if ( isset( $lr_custom_interface_settings['custom_interface'] ) && $lr_custom_interface_settings['custom_interface'] == '1' ) {
                $custom = true;
            }

            if ( $pagenow == 'profile.php' ) {
                if ( ! class_exists( 'LR_Raas_Install' ) ) {
                    echo LR_Common::check_linking_status_parameters();

                    // If remove button clicked
                    if ( isset( $_GET['loginRadiusMap'] ) && ! empty( $_GET['loginRadiusMap'] ) && isset( $_GET['loginRadiusMappingProvider'] ) && ! empty( $_GET['loginRadiusMappingProvider'] ) ) {
                        Login_Helper::unlink_provider();
                    }
                    LR_Common::link_account_if_possible();
                    ?>
                    <div class="metabox-holder columns-2" id="post-body">
                        <div class="stuffbox wrap" style="padding-bottom:10px">
                            <h3><label><?php _e( 'Link your account', 'lr-plugin-slug' ); ?></label></h3>
                            <div class="inside" style='padding:0'>
                                <table  class="form-table editcomment">
                                    <tr>
                                        <td colspan="2"><?php _e( 'By adding another account, you can log in with the new account as well!', 'lr-plugin-slug' ) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <?php
                                            if ( ! class_exists( "Login_Helper" ) ) {
                                                require_once LOGINRADIUS_PLUGIN_DIR . 'public/inc/login/class-login-helper.php';
                                            }
                                            Login_Helper::get_loginradius_interface_container();
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    echo LR_Common::get_connected_providers_list();
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    do_action( 'lr_raas_social_linking' );
                }
            }
        }
    }
    LR_Social_Login_Admin:: get_instance();
}
