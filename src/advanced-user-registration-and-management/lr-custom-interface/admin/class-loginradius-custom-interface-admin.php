<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('LR_Custom_Interface_Admin')) {

    class LR_Custom_Interface_Admin {
        /*
         * Constructor for class LR_Commenting_Admin
         */

        public function __construct() {
            add_action('admin_init', array($this, 'admin_init'));
            add_action('wp_ajax_upload_custom_interface_image', array($this, 'upload_custom_interface_image'));
        }

        /**
         * Register LR_Custom_Interface_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {
            register_setting('lr_custom_interface_settings', 'LR_Custom_Interface_Settings', array($this, 'validation'));
            // Load the need scripts
            add_action('admin_enqueue_scripts', array($this, 'load_scripts'));

            //replicate Custom Interface configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_settings_to_new_blog'));
            }
        }

        // replicate the social custom interface config to the new blog created in the multisite network
        public function replicate_settings_to_new_blog($blogid) {
            global $lr_custom_interface_settings;
            add_blog_option($blogid, 'LR_Custom_Interface_Settings', $lr_custom_interface_settings);
        }

        static function validation($settings) {
            $settings['selected_providers'] = self::get_providers();
            return $settings;
        }

        public static function get_providers() {
            global $loginradius_api_settings;
            $listedProviders = null;
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? $loginradius_api_settings['LoginRadius_apikey'] : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? $loginradius_api_settings['LoginRadius_secret'] : '';

            if (!empty($apikey) && !empty($secret)) {
                $getProviderObject = new \LoginRadiusSDK\SocialLogin\GetProvidersAPI($apikey, $secret, array('authentication' => false, 'output_format' => 'json'));
                try {
                    $providers = json_decode($getProviderObject->getProvidersList(), true);
                    $providersName = isset($providers['Providers']) ? $providers['Providers'] : array();
                    foreach ($providersName as $key => $value) {
                        $listedProviders[] = $value['Name'];
                    }
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    return null;
                }
            }
            return $listedProviders;
        }

        public static function get_selected_providers() {
            global $loginradius_api_settings;
            $apikey = isset($loginradius_api_settings['LoginRadius_apikey']) ? $loginradius_api_settings['LoginRadius_apikey'] : '';
            $secret = isset($loginradius_api_settings['LoginRadius_secret']) ? $loginradius_api_settings['LoginRadius_secret'] : '';
            $provider_list = array('amazon', 'aol', 'disqus', 'facebook', 'foursquare', 'github', 'google', 'hyves', 'instagram', 'kaixin', 'linkedin', 'live', 'livejournal', 'mailru', 'mixi', 'myspace', 'odnoklassniki', 'openid', 'orange', 'paypal', 'persona', 'qq', 'renren', 'salesforce', 'sinaweibo', 'stackexchange', 'steam', 'steamcommunity', 'tumblr', 'twitter', 'verisign', 'virgilio', 'vkontakte', 'wordpress', 'xing', 'yahoo');
            if (!empty($apikey) && !empty($secret)) {
                $lrproviders = self::get_providers();
                if (is_array($lrproviders) && count($lrproviders) > 0) {
                    $provider_list = $lrproviders;
                }
            }
            return $provider_list;
        }

        public static function load_scripts($hook) {
            global $loginradius_api_settings, $lr_custom_interface_settings, $lr_js_in_footer;

            // Return if not viewing custom interface admin.
            if ($hook != 'loginradius_page_loginradius_customization') {
                return;
            }
            
            // Custom Interface must be loaded in header.
            wp_register_script('lr-custom-interface', '//cdn.loginradius.com/hub/prod/js/lr-custom-interface.3.js', array(), '3.0.0', $lr_js_in_footer);

            // Load Stylesheets.
            wp_enqueue_style('lr_custom_interface.css', LR_ROOT_URL . 'lr-custom-interface/assets/css/lr_ci_style.css');

            // Load Admin Scripts.
            wp_enqueue_script('jquery');

            // Load jQuery UI Sortable to select providers.
            wp_enqueue_script('jquery-ui-sortable');
            $provider_list = self::get_selected_providers();
            $args = array(
                'siteName' => isset($loginradius_api_settings['sitename']) ? $loginradius_api_settings['sitename'] : '',
                'apiKey' => isset($loginradius_api_settings['LoginRadius_apikey']) ? trim($loginradius_api_settings['LoginRadius_apikey']) : '',
                'providers' => isset($lr_custom_interface_settings['providers']) && !empty($lr_custom_interface_settings['providers']) ? $lr_custom_interface_settings['providers'] : $provider_list
            );
            wp_localize_script('lr-custom-interface', "phpvar", $args);

            //Load custom interface.
            wp_enqueue_script('lr-custom-interface');

            // Load custom scripts after lr-custom-interface loads.
            add_filter('admin_head', array(__CLASS__, 'load_custom_scripts'));
        }

        public static function load_custom_scripts() {
            global $lr_js_in_footer;

            $upload_asset_url = LR_ROOT_URL . 'lr-custom-interface/assets/images/custom_interface/';
            if (is_multisite()) {
                $upload_asset_url .= get_current_blog_id() . '/';
            }
            ?>

            <script type="text/html" id="loginradiuscustom_tmpl">
                <li title="<%=Name %>" id="ci-<%=Name.toLowerCase() %>">
                    <span class="lr-block-frame">
                        <img class="custom_preview_img" src="<?php echo $upload_asset_url; ?><%=Name.toLowerCase() %>.png?ts=<?php echo strtotime(date('Y-m-d H:i:s')); ?>" alt="<%=Name%>">
                    </span>
                </li>
            </script>

            <?php
            wp_enqueue_script('lr-custom-template', LR_ROOT_URL . 'lr-custom-interface/assets/js/template.js', array('lr-custom-interface'), LR_PLUGIN_VERSION, $lr_js_in_footer);
        }

        public function upload_custom_interface_image() {
            require_once( LR_ROOT_DIR . 'lr-custom-interface/includes/helper/ajax_helper.php' );
            $ajax = new LR_CI_Ajax_Helper();
            if ($ajax->check_max_upload()) {
                echo $ajax->upload_handler();
            } else {
                _e('php.ini does not allow file uploading.', 'lr-plugin-slug');
            }
            die();
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            require_once LR_ROOT_DIR . "lr-custom-interface/admin/views/settings.php";
            LR_Custom_Interface_Admin_Settings:: render_options_page();
        }
    }

    new LR_Custom_Interface_Admin();
}
