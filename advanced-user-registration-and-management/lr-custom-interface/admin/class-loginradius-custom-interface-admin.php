<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Custom_Interface_Admin') ) {

    class LR_Custom_Interface_Admin {
        /*
         * Constructor for class LR_Commenting_Admin
         */

        public function __construct() {
            add_action('admin_init', array( $this, 'admin_init') );
        }

        /**
         * Register LR_Custom_Interface_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {
            register_setting( 'lr_custom_interface_settings', 'LR_Custom_Interface_Settings', array( $this, 'validation' ) );
            // Load the need scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

            //replicate Custom Interface configuration to the subblogs in the multisite network
            if ( is_multisite() && is_main_site() ) {
                add_action( 'wpmu_new_blog', array( $this, 'replicate_settings_to_new_blog' ) );
                add_action( 'update_option_LR_Custom_Interface_Settings', array( $this, 'login_radius_update_old_blogs' ) );
            }
        }

        // replicate the social custom interface config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog( $blogid ) {
            global $lr_custom_interface_settings;
            add_blog_option( $blogid, 'LR_Custom_Interface_Settings', $lr_custom_interface_settings );
        }

        // update the social custom interface options in all the old blogs
        public function login_radius_update_old_blogs( $oldConfig ) {
            global $loginradius_api_settings;
            if ( isset( $loginradius_api_settings['multisite_config'] ) && $loginradius_api_settings['multisite_config'] == '1' ) {
                $settings = get_option( 'LR_Custom_Interface_Settings' );
                $blogs = wp_get_sites();
                foreach ( $blogs as $blog ) {
                    update_blog_option( $blog['blog_id'], 'LR_Custom_Interface_Settings', $settings );
                }
            }
        }

        static function validation( $settings ) {
            $settings['selected_providers'] = LR_Custom_Interface_Install::get_providers();
            return $settings;
        }

        public static function load_scripts( $hook ) {
            global $loginradius_api_settings, $lr_custom_interface_settings;

            // Return if not viewing custom interface admin.
            if ( $hook != 'loginradius_page_loginradius_customization' ) {
                return;
            }

            // Load Stylesheets.
            wp_enqueue_style( 'lr_custom_interface.css', LR_ROOT_URL . 'lr-custom-interface/assets/css/lr_ci_style.css' );

            // Load Admin Scripts.
            wp_enqueue_script( 'jquery' );

            // Load jQuery UI Sortable to select providers.
            wp_enqueue_script( 'jquery-ui-sortable' );
            $provider_list = array( 'amazon', 'aol', 'disqus', 'facebook', 'foursquare', 'github', 'google' );
                array_push( $provider_list, 'hyves', 'instagram', 'kaixin', 'linkedin', 'live', 'livejournal', 'mailru' );
                array_push( $provider_list, 'mixi', 'myspace', 'odnoklassniki', 'openid', 'orange', 'paypal', 'persona' );
                array_push( $provider_list, 'qq', 'renren', 'salesforce', 'sinaweibo', 'stackexchange', 'steam', 'steamcommunity' );
                array_push( $provider_list, 'tumblr', 'twitter', 'verisign', 'virgilio', 'vkontakte', 'wordpress', 'xing', 'yahoo' );
            $args = array(
                'siteName' => isset( $loginradius_api_settings['sitename'] ) ? $loginradius_api_settings['sitename'] : '',
                'apiKey' => isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim( $loginradius_api_settings['LoginRadius_apikey'] ) : '',
                'providers' => isset( $lr_custom_interface_settings['providers'] ) && ! empty( $lr_custom_interface_settings['providers'] ) ? $lr_custom_interface_settings['providers'] : $provider_list
            );

            wp_localize_script( 'lr-custom-interface', "phpvar", $args );

            //Load custom interface.
            wp_enqueue_script( 'lr-custom-interface' );

            // Load custom scripts after lr-custom-interface loads.
            add_filter( 'admin_head', array( __CLASS__, 'load_custom_scripts' ) );
        }

        public static function load_custom_scripts() {
            global $lr_js_in_footer;

            $upload_asset_url = LR_CUSTOM_INTERFACE_URL . 'assets/images/custom_interface/';
            if( is_multisite() ){
                $upload_asset_url .= get_current_blog_id() . '/';
            }
            ?>

            <script type="text/html" id="loginradiuscustom_tmpl">
                <li title="<%=Name %>" id="ci-<%=Name.toLowerCase() %>">
                    <span class="lr-block-frame">
                        <img class="custom_preview_img" src="<?php echo $upload_asset_url; ?><%=Name.toLowerCase() %>.png#"+ new Date().getTime()" alt="<%=Name%>">
                    </span>
                </li>
            </script>

            <?php
            wp_enqueue_script( 'lr-custom-template', LR_CUSTOM_INTERFACE_URL . 'assets/js/template.js', array( 'lr-custom-interface' ), LR_PLUGIN_VERSION, $lr_js_in_footer );
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */
        public static function options_page() {
            include_once LR_CUSTOM_INTERFACE_DIR."admin/views/settings.php";
            LR_Custom_Interface_Admin_Settings:: render_options_page();
        }
    }
new LR_Custom_Interface_Admin();
}
