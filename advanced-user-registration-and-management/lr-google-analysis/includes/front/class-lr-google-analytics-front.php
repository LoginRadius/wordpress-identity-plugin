<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The front function class of LoginRadius Google Analytics.
 */
if ( ! class_exists( 'LR_Google_Analytics_Front' ) ) {

    class LR_Google_Analytics_Front {

        public function __construct() {
            add_action( 'init', array( $this, 'init' ) );
        }
        /**
         * add user id in google analitics code
         * 
         * @return type
         */
        public function lr_google_callback_script(){
            if( is_user_logged_in() ) {
                $user_id = get_current_user_id();
                $id = get_user_meta( $user_id, 'lr_raas_uid', true);
                $id = isset( $id) && ! empty( $id) ? $id : get_user_meta( $user_id, 'loginradius_current_id', true );
                   
                return "if( typeof ga != 'undefined' ){ 
                            ga( 'set', '&uid', '" . $id . "' ); 
                        }";
            }
        }
        /**
         * add script in heared or footer by init hook
         * 
         * @global type $lr_google_analytics_settings
         * @global type $lr_js_in_footer
         */
        public function init() {
            global $lr_google_analytics_settings, $lr_js_in_footer;
            if (isset( $lr_google_analytics_settings['ga_enable']) && $lr_google_analytics_settings['ga_enable'] == '1' ) {
                if ( $lr_js_in_footer) {
                    add_action( 'wp_footer', array( $this, 'load_script' ), 999 );
                } else {
                    add_action( 'wp_head', array( $this, 'load_script' ), 999 );
                }
                wp_register_script( 'lr-google-analyitcs-script', '//cdn.loginradius.com/hub/prod/js/lr-google-analyitcs.js', array( 'jquery' ), LR_PLUGIN_VERSION, $lr_js_in_footer );
            }
        }

        /**
         * google analytics script
         * 
         * @global type $lr_google_analytics_settings
         */
        public function load_script() {
            global $lr_google_analytics_settings;?>
                <script>
                    jQuery(document).ready(function () {
                        <?php if ( isset( $lr_google_analytics_settings['ga_tracking_id']) && ! empty( $lr_google_analytics_settings['ga_tracking_id'] ) ) {?>
                            if ( typeof ga == 'undefined' && typeof _gaq == 'undefined' ) {
                                (function (i, s, o, g, r, a, m) {
                                    i['GoogleAnalyticsObject'] = r;
                                    i[r] = i[r] || function () {
                                        (i[r].q = i[r].q || []).push(arguments)
                                    }, i[r].l = 1 * new Date();
                                    a = s.createElement(o),
                                            m = s.getElementsByTagName(o)[0];
                                    a.async = 1;
                                    a.src = g;
                                    m.parentNode.insertBefore(a, m)
                                })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga' );

                                ga( 'create', '<?php echo $lr_google_analytics_settings['ga_tracking_id']; ?>', 'auto' );
                                ga( 'send', 'pageview' );
                            }
                        <?php }
                        echo $this->lr_google_callback_script();?>
                    });
                </script>
                <?php
            }
        }
    new LR_Google_Analytics_Front();
}
