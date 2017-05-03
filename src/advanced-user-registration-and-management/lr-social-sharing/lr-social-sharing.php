<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('LR_Social_Sharing')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Social_Sharing {

        /**
         * Constructor
         */
        public function __construct() {
            global $lr_js_in_footer;

            // load dependencies.
            $this->load_dependencies();

            add_action('lr_admin_page', array($this, 'create_loginradius_menu'), 3);
            // Register Activation hook callback.
            add_action('lr_plugin_activate', array(get_class(), 'install'), 10, 1);
            add_action('lr_plugin_deactivate', array(get_class(), 'uninstall'), 10, 1);

            add_action('admin_enqueue_scripts', array($this, 'share_add_stylesheet'));
            if ($lr_js_in_footer) {
                add_action('wp_footer', array($this, 'enqueue_share_scripts'), 1);
                add_action('wp_enqueue_scripts', array($this, 'enqueue_front_style'), 5);
            } else {
                add_action('wp_enqueue_scripts', array($this, 'enqueue_share_scripts'), 20);
                add_action('wp_enqueue_scripts', array($this, 'enqueue_front_style'), 5);
            }
        }

        function create_loginradius_menu() {

            if (!class_exists('LR_Social_Login')) {
                // Create Menu.		
                add_menu_page('LoginRadius', 'Social Sharing', 'manage_options', 'loginradius_share', array('LR_Social_Share_Admin', 'options_page'), LR_ROOT_URL . 'lr-core/assets/images/favicon.ico');
            } else {
                // Add Social Sharing menu.
                add_submenu_page('LoginRadius', 'Social Sharing Settings', 'Social Sharing', 'manage_options', 'loginradius_share', array('LR_Social_Share_Admin', 'options_page'));
            }
        }

        /**
         * Function for setting default options while plgin is activating.
         */
        public static function install($blog_id) {
            require_once ( LR_ROOT_DIR . "lr-social-sharing/install.php" );
            LR_Sharing_Install::set_default_options($blog_id);
        }

        public static function uninstall($blog_id) {
            if ($blog_id) {
                delete_blog_option($blog_id, 'LoginRadius_share_settings');
            } else {
                delete_option('LoginRadius_share_settings');
            }
        }

        public static function reset_options() {
            if (isset($_POST['reset'])) {
                self::uninstall(false);
                self::install(false);
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Sharing settings have been reset and default values have been applied to the plug-in</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
        }

        public static function enqueue_front_style() {
            wp_enqueue_style('lr-social-sharing-front', LR_ROOT_URL . 'lr-social-sharing/assets/css/lr-social-sharing-front.css', array(), '1.0');
            wp_enqueue_style('oss-share-widget-style', 'https://share.lrcontent.com/prod/v2/css/os-share-widget-style.css');
        }

        /**
         * Add stylesheet and JavaScript to client sections
         */
        public function enqueue_share_scripts() {
            wp_enqueue_script('loginradius_javascript_init', plugins_url('/assets/js/loginradius_sharing.js', __FILE__), array('jquery'), '1.0.0');
            wp_enqueue_script('lr-social-sharing');
            wp_enqueue_script('lr_share', 'https://share.lrcontent.com/prod/v2/js/opensocialsharedefaulttheme.js');
            wp_enqueue_script('lr-social-sharing');
            wp_enqueue_script('lr_shares', 'https://share.lrcontent.com/prod/v2/js/opensocialshare.js');
            wp_enqueue_script('lr-social-sharing');
        }

        /**
         * Add stylesheet and JavaScript to admin section.
         */
        public function share_add_stylesheet($hook) {
            global $lr_js_in_footer;
            if ($hook != 'loginradius_page_loginradius_share' && $hook != 'toplevel_page_loginradius_share') {
                return;
            }
            wp_enqueue_style('loginradius_sharing_style', plugins_url('/assets/css/lr-social-sharing-admin.css', __FILE__));
            wp_enqueue_script('loginradius_share_admin_javascript', plugins_url('/assets/js/loginradius_sharing_admin.js', __FILE__), array('jquery', 'jquery-ui-sortable', 'jquery-ui-mouse', 'jquery-touch-punch'), false, $lr_js_in_footer);
        }

        /**
         * Loads PHP files that required by the plug-in
         *
         * @global loginradius_commenting_settings
         */
        private function load_dependencies() {
            global $loginradius_share_settings;

            $loginradius_share_settings = get_option('LoginRadius_share_settings');
            // Load LoginRadius files.
            require_once( LR_ROOT_DIR . "lr-social-sharing/admin/lr-social-share-admin.php" );
            if (( isset($loginradius_share_settings['horizontal_enable']) && $loginradius_share_settings['horizontal_enable'] == 1 ) || ( isset($loginradius_share_settings['vertical_enable']) && $loginradius_share_settings['vertical_enable'] == 1 )) {
                require_once( LR_ROOT_DIR . "lr-social-sharing/includes/common/sharing.php" );
                require_once( LR_ROOT_DIR . "lr-social-sharing/includes/shortcode/shortcode.php" );
            }
            if (isset($loginradius_share_settings['horizontal_enable']) && $loginradius_share_settings['horizontal_enable'] == 1) {
                require_once( LR_ROOT_DIR . "lr-social-sharing/includes/horizontal/lr-simplified-social-share-horizontal.php" );
                require_once( LR_ROOT_DIR . "lr-social-sharing/includes/widgets/lr-horizontal-share-widget.php" );
            }
            if (isset($loginradius_share_settings['vertical_enable']) && $loginradius_share_settings['vertical_enable'] == 1) {
                require_once( LR_ROOT_DIR . "lr-social-sharing/includes/vertical/lr-simplified-social-share-vertical.php");
                require_once( LR_ROOT_DIR . "lr-social-sharing/includes/widgets/lr-vertical-share-widget.php");
            }
        }

    }

    new LR_Social_Sharing();
}
