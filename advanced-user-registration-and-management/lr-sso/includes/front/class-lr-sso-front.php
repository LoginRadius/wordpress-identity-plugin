<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius SSO.
 */
if (!class_exists('LR_SSO_Front')) {

    class LR_SSO_Front {
        public static function on_load() {
            add_action('init', array(get_class(), 'init'));        
        }

        public static function init() {
            global $lr_sso_settings;
            if ( isset($lr_sso_settings['sso_enable']) && $lr_sso_settings['sso_enable'] == '1' ) {
                add_action('wp_enqueue_scripts', array(get_class(), 'load_sso_scripts_before'));
                add_action('admin_enqueue_scripts', array(get_class(), 'load_sso_scripts_before'));
            }
        }
        
        public static function load_sso_scripts_before() {
            global $lr_js_in_footer;
            LR_Raas::enqueue_front_scripts();
            wp_enqueue_script('lr-sso', '//cdn.loginradius.com/hub/prod/js/LoginRadiusSSO.js', array('jquery','lr-raas', 'lr-social-login'), LR_PLUGIN_VERSION, $lr_js_in_footer);
            add_action( 'wp_print_footer_scripts', array( get_class(), 'load_sso_init_scripts' ) );
            add_action( 'wp_print_footer_scripts', array( get_class(), 'load_sso_login_script' ) );
            add_action( 'wp_print_footer_scripts', array( get_class(), 'load_sso_logout_script' ) );
            add_action( 'admin_print_footer_scripts', array( get_class(), 'load_sso_init_scripts' ) );
            add_action( 'admin_print_footer_scripts', array( get_class(), 'load_sso_login_script' ) ); 
            add_action( 'admin_print_footer_scripts', array( get_class(), 'load_sso_logout_script' ) );
        }

        public static function isnotloginthenlogout_script() {
            ?>
            <script>
                jQuery(document).ready(function () {
                    LoginRadiusSSO.isNotLoginThenLogout(function () {
                        window.location.href = '<?php echo html_entity_decode( wp_logout_url() ); ?>';
                    });
                });
            </script>
            <?php
        }

        /**
         * Load SSO Initalization Script LoginRadiusSSO.init
         */
        public static function load_sso_init_scripts() {
            global $loginradius_api_settings;
            ?>
            <script>
                jQuery(document).ready(function () {
                    LoginRadiusSSO.init("<?php echo $loginradius_api_settings['sitename']; ?>");
                });
            </script>
            <?php
        }

        /**
         * Load SSO LoginRadiusSSO.isNotLoginThenLogout script if logged in
         * Load SSO LoginRadiusSSO.login Script if page has token and not logged in
         */
        public static function load_sso_login_script() {
            if ( is_user_logged_in() && ! is_admin() && ! is_super_admin() ) {
                self::isnotloginthenlogout_script();
            }
            if ( ! isset( $_POST['token'] ) && ! is_user_logged_in() ) {
                ?>
                <script>
                    jQuery(document).ready(function () {
                        LoginRadiusSSO.login('<?php echo the_permalink(); ?>');
                    });
                </script>
                <?php
            }
        }

        /**
         * Load SSO LoginRadiusSSO.logout script on logout button clicks
         */
        public static function load_sso_logout_script() {
                ?>
                <script>
                    jQuery(document).ready(function ($) {
                        var href = $('#wp-admin-bar-logout a').attr('href');
                        $('#wp-admin-bar-logout a').removeAttr('href');

                        function logout(href) {
                            LoginRadiusSSO.logout(href);
                        };
                        $('#wp-admin-bar-logout').click( function (e) {
                            e.preventDefault();
                            logout(href);
                        });

                        if ($('a[href*="logout"]').length > 0) {
                            href = $('a[href*="logout"]').attr('href');
                            $('a[href*="logout"]').attr('data-action', 'lr-sso-logout');
                            $('a[href*="logout"]').removeAttr('href');
                            $('a[data-action="lr-sso-logout"]').click(function () {
                                logout(href);
                            });
                        }
                        return false;
                    });
                </script>
                <?php
        }

    }

    LR_SSO_Front::on_load();
}
