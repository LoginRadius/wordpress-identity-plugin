<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

require_once( 'lib/Livefyre.php' );
use LRLivefyre\Livefyre;

if ( ! class_exists( 'LR_Livefyre' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Livefyre {

        /**
         * Constructor
         */
        public function __construct() {
        	            
            // Register Activation hook callback.
            $this->install();

        	$this->define_constants();
            $this->load_dependencies();

            add_action( 'wp_ajax_lr_livefyre_login', array( $this, 'livefyre_login' ) );
            add_action( 'wp_ajax_nopriv_lr_livefyre_login', array( $this, 'livefyre_login' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'test_scripts' ) );
            add_action( 'lr_admin_page', array( $this, 'create_loginradius_menu' ), 10 );  
        }

        function create_loginradius_menu() {
            add_submenu_page('LoginRadius', 'LiveFyre', 'LiveFyre', 'manage_options', 'lr_livefyre', array( 'LR_LiveFyre_Admin', 'options_page' ) );
        }

        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_LIVEFYRE_DIR', plugin_dir_path( __FILE__ ) );
            define( 'LR_LIVEFYRE_URL', plugin_dir_url( __FILE__ ) );
        }

        private function load_dependencies() {
            require_once( 'admin/class-lr-livefyre-admin.php' );
        }

        static function enqueue_scripts(  ) {
        	wp_enqueue_script( 'jquery' );
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install() {
            global $wpdb;
            require_once ( dirname(__FILE__) . '/install.php' );
            if ( function_exists('is_multisite') && is_multisite() ) {
                // check if it is a network activation - if so, run the activation function for each blog id
                $old_blog = $wpdb->blogid;
                // Get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    LR_Livefyre_Install::set_default_options();
                }
                switch_to_blog($old_blog);
                return;
            } else {
                LR_Livefyre_Install::set_default_options();
            }
        }

        static function test_scripts() {
            global $lr_livefyre_settings, $loginradius_api_settings, $lr_custom_interface_settings;

            if( empty( $lr_livefyre_settings['enable_livefyre'] ) ) {
                return;
            }
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'lr-sdk' );
            wp_enqueue_script( 'lr-custom-interface' );
            wp_enqueue_script( 'lr-social-login' );

            if( is_user_logged_in() ) {
                $user_id = get_current_user_id();
                $current_user = wp_get_current_user();
                $logged_in = true;
                $provider_id = get_user_meta( $user_id, 'loginradius_provider_id', TRUE );
                $provider_id = ! empty( $provider_id ) ? $provider_id : $user_id;
            }

            wp_register_script( 'lr-livefyre', LR_LIVEFYRE_URL . '/assets/js/lr-livefyre.js' );
            $args = array(
                'siteName'  => isset( $loginradius_api_settings['sitename'] ) ? $loginradius_api_settings['sitename'] : '',
                'apiKey'    => isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim( $loginradius_api_settings['LoginRadius_apikey'] ) : '',
                'providers' => isset( $lr_custom_interface_settings['selected_providers'] ) ? $lr_custom_interface_settings['selected_providers'] : 'Providers Not Configured',
                'url' => get_admin_url() . 'admin-ajax.php',
                'logout_url' => urldecode( wp_logout_url( get_permalink() ) ),
                'profile_url' => urldecode( get_edit_profile_url() ),
                'profile_name' => ! empty( $current_user->display_name ) ? $current_user->display_name : '',
                'logged_in' => isset( $logged_in ) ? true : false,
                'provider_id' => ! empty( $provider_id ) ? $provider_id : '',
                'enable_login' => ! empty( $lr_livefyre_settings['enable_login'] ) ? true : false
            );

            $callback = LR_Common::get_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $link = '';
            if( $callback != NULL || ! empty( $callback ) ) {
                $link = $callback;
            }

            ?>
                <script type="text/html" id="lr_livefyre_template">
                    <a class="lr_custom_provider" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&is_access_token=true&callback=<?php echo urlencode( $link ); ?>')" ><img src="<?php echo LR_CUSTOM_INTERFACE_URL; ?>assets/images/custom_interface/<%=Name.toLowerCase() %>.png" /></a>
                </script>
            <?php

            wp_localize_script( 'lr-livefyre', "phpvar", $args);
            wp_enqueue_style( 'lr-raas-popup-style' );
            wp_enqueue_style( 'lr-livefyre-style', LR_LIVEFYRE_URL . '/assets/css/lr-livefyre.css' );
            wp_enqueue_script( 'lr-livefyre' );
        }

        public static function livefyre_login() {
            $domain_name = get_option( 'livefyre_apps-livefyre_domain_name' );
            $domain_key = get_option( 'livefyre_apps-livefyre_domain_key' );

            $site_id = get_option( 'livefyre_apps-livefyre_site_id' );
            $site_key = get_option( 'livefyre_apps-livefyre_site_key' );

            $user_id = ! empty( $_POST['ID'] ) ? $_POST['ID'] : '';
            $display_name = ! empty( $_POST['name'] ) ? $_POST['name'] : '';

            if( ! empty( $domain_name ) && ! empty( $domain_key ) && ! empty( $user_id ) ) {
                $network = Livefyre::getNetwork( $domain_name, $domain_key );
                $userAuthToken = $network->buildUserAuthToken( $user_id, $display_name, 86400 );

                echo json_encode( array('token' => $userAuthToken ) );
            }else{
                echo json_encode( array('error' => 'Required Parameter Missing' ) );
            }
            die();
        }

    }
    new LR_Livefyre();
}