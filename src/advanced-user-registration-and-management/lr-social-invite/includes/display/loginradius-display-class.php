<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

class LR_Social_Invite_Display {

    public function __construct() {
        $this->init();
    }

    public function init() {
        global $lr_social_invite_settings;

        if ( isset( $lr_social_invite_settings['social_invite_enable'] ) && $lr_social_invite_settings['social_invite_enable'] == '1' ) {
            
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_glob_scripts' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            add_action( 'wp_footer', array( $this, 'footer_scripts' ), 1000 );
            add_shortcode( 'LoginRadius_Social_Invite', array( $this, 'social_invite_shortcode' ) );
        }
    }

    public static function is_login_page() {
        return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
    }

    public static function enqueue_glob_scripts() {
        
        wp_enqueue_style( 'lr-social-invite-style', LR_SOCIAL_INVITE_URL . 'assets/css/lr-social-invite.min.css' );

        ?>
            <!-- Custom Share Template -->
            <script type="text/html" id="lr_social_invite_template">
                <label>
                    <span id="lr-<%= Name.toLowerCase() %>-social-invite-trigger" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&callback=<?php echo get_permalink(); ?>&is_access_token=true&scope=lr_read_contact,lr_write_message');" ></span>
                    <span class="lr-img-icon-<%= Name.toLowerCase() %> social-invite" title="<?php _e( 'Share with ','lr-plugin-slug' );?>"></span>
                    <input type="checkbox" class="lr-social-invite-switch" name="<%= Name.toLowerCase() %>" id="lr-<%= Name.toLowerCase() %>-social-invite-switch" />
                </label>
            </script>

            <!-- Custom Share Template -->
            <script type="text/html" id="lr_social_invite_login_template">
                <label class="lr-<%= Name.toLowerCase() %>-social-invite-login lr-social-invite-login-provider" style="display: none;">
                    <span id="lr-<%= Name.toLowerCase() %>-social-invite-login-trigger" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&callback=<?php echo get_permalink(); ?>&is_access_token=true&scope=lr_read_contact,lr_write_message');" ></span>
                    <span class="lr-img-icon-<%= Name.toLowerCase() %> social-invite-login" title="<?php _e( 'Share with ','lr-plugin-slug' );?>"></span>
                    <input type="checkbox" class="lr-social-invite-login-switch" name="<%= Name.toLowerCase() %>" id="lr-<%= Name.toLowerCase() %>-social-invite-login-switch" />
                </label>
            </script>

        <?php
    }

    public static function enqueue_scripts() {
        global $loginradius_api_settings, $loginRadiusSettings, $lr_social_invite_settings;
        
        wp_register_script( 'lr-social-invite-lib', LR_SOCIAL_INVITE_URL . 'assets/js/social-invite-library.js', array( 'jquery','lr-custom-interface','lr-sdk' ), '1.0' );

        $args = array( 
            'ajax_url' => get_admin_url() . 'admin-ajax.php',
            'debugging' => isset( $loginRadiusSettings['enable_degugging'] ) ? 'true' : 'false',
            'is_user_logged_in' => ( is_user_logged_in() === '1' ) ? 'true' : 'false',
            'loginRadiusApiKey' => isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim( $loginradius_api_settings['LoginRadius_apikey'] ) : '',
            'mysteryperson' => LR_SOCIAL_INVITE_URL . 'assets/images/mysteryperson.jpeg',
            'facebook_app_id' => isset( $lr_social_invite_settings['fb_id'] ) ? $lr_social_invite_settings['fb_id'] : '',
            'facebook_share_url' => get_permalink()
        );

        wp_localize_script( 'lr-social-invite-lib', "socialInviteDetails", $args );

        wp_enqueue_script( 'fb-connect', '//connect.facebook.net/en_US/all.js' );
        wp_enqueue_script( 'lr-social-invite-lib' );
    }

    static function footer_scripts() {
        global $loginradius_api_settings;
        
        if ( ! self::is_login_page() ) { ?>

            <script type="text/javascript">
                $LRIC.util.ready(function () {
                    var options = {};
                    options.apikey = "<?php echo $loginradius_api_settings['LoginRadius_apikey']; ?>";
                    options.appname = "<?php echo $loginradius_api_settings['sitename']; ?>";
                    options.templatename = "lr_social_invite_template";
                    options.providers = ['Twitter', 'Facebook', 'LinkedIn', 'Google', 'Yahoo'];
                    $LRIC.renderInterface("lr-social-invite-provider", options);
                });
            </script>

            <script type="text/javascript">
                $LRIC.util.ready(function () {
                    var options = {};
                    options.apikey = "<?php echo $loginradius_api_settings['LoginRadius_apikey']; ?>";
                    options.appname = "<?php echo $loginradius_api_settings['sitename']; ?>";
                    options.templatename = "lr_social_invite_login_template";
                    options.providers = ['Twitter', 'LinkedIn', 'Google', 'Yahoo'];
                    $LRIC.renderInterface("lr_si_providerbox", options);
                });
            </script>

        <?php } 
    }

    public static function social_invite_shortcode() {
        global $loginradius_api_settings, $lr_social_invite_settings, $loginRadiusSettings;

        $user = wp_get_current_user();
        if ( ! $user->exists() ) {
            return;
        }
      
$html = '<div class="lr-social-invite-bar" style="background: blue">
            <div class="lr-social-invite-provider" style="float: left;">
            </div>
            <div class="lr_social_invite_search" style="float: right;">
                <input class="lr_social_invite_search_input" type="text" placeholder=" '.__( 'Search','lr-plugin-slug' ).'" />
            </div>
        </div>
        <div class="lr_social_invite_results cf">
        </div>
        <div class="lrcustomfriendinvite_widgetpopup" style="display: none;">
            <div class="lr-popupbox-overlay" id="lr-popupbox-overlay" onclick="javascript:$LRSI.closeWidget();">
            </div>
            <div class="lr-popupbox-inner">
                <div class="lr-wait" id="lr_fri_loading_div" style="z-index: 1060; display: none;">
                    <div class="circle" style="margin-top: 20%">
                        <div class="image">
                        </div>
                        <div class="background">
                            <div class="shadow">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lr-popupbox-heading" id="lr_popupbox_heading">
                    <div class="lr-si-box-bottom-bbtn" id="lr-back-button" onclick="$LRSI.resetWidget();" style="display: block;">&#8678;</div>
                    '. __( 'Invite Your Friends','lr-plugin-slug' ).'
                    <div class="lr-popupbox-close" onclick="javascript:$LRSI.closeWidget();">×</div>
                </div>
                <div class="lr_si_providerbox" id="lr_si_providerbox" style="display: none;">
                </div>

                <div class="lr_si_messagebox" id="lr_si_messagebox" style="display: block;">
                    <div class="lr-si-box invitefriend-list" id="lr-si-autocomplete">
                        <div class="lr-popupbox-namebox" id="lr_divspantag">
                        </div>
                        <input type="text" name="lr-si-txtautocomplete" class="lr-si-txtautocomplete" id="lr-si-txtautocomplete" autocomplete="off" placeholder="'. __( 'Enter a friend ','lr-plugin-slug' ).'">
                        <div class="lr-output-shadow" id="lr-contactlist-shadow" >
                            <div class="lr-output" id="output"></div>
                        </div>
                    </div>
                    <div class="lr-si-box" id="lr-si-subject">
                        <input id="lr_si_subject" type="text" name="usrname" placeholder="'. __( 'Subject','lr-plugin-slug' ).'" '. (empty( $lr_social_invite_settings['enable_editable'] ) ? 'readonly="readonly"' : '').' onchange="$LRSI.validate( this, this )">
                    </div>

                    <div class="lr-si-box-textbox" id="lr-si-message">
                        <textarea id="lr_si_message" name="comment" placeholder="'. __( 'Enter text here...','lr-plugin-slug' ).'" '. (empty( $lr_social_invite_settings['enable_editable'] ) ? 'readonly="readonly"' : '').' onchange="$LRSI.validate( this, this )"></textarea>
                    </div>
                    <div class="lr-si-box-bottom" id="lr-socialbottombox">
                        <div class="lr-si-box-bottom-btn" id="lr_si_box_bottom_btn" onclick="javascript:$LRSI.send_message();">'. __( 'Send Message','lr-plugin-slug' ).'</div>
                    </div>
                </div>
                <div class="lr_si_response" id="lr_si_response">
                </div>
            </div>
        </div>
        <script type="text/javascript">';
                if( ! empty( $lr_social_invite_settings['subject'] ) ) {
                    $html .= 'var si_custom_subject = document.getElementById(\'lr_si_subject\');
                    si_custom_subject.value = "'.$lr_social_invite_settings['subject'].'";';
                }
                if( ! empty( $lr_social_invite_settings['message'] ) ) {
                    $html .= 'var si_custom_message = document.getElementById(\'lr_si_message\');
                    si_custom_message.value = "'.$lr_social_invite_settings['message'].'";';
                } 
        $html .= '</script>';
        return $html;
    }

}
