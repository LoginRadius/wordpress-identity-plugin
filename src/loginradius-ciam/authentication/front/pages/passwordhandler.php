<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Authentication_Passwordhandler')) {

    class CIAM_Authentication_Passwordhandler {
        /*
         * class constructor function
         */

        public function __construct() {
            add_action('init', array($this, 'init'));
        }

        /*
         * load required dependencies
         */

        public function init() {
            global $ciam_credentials;
            add_shortcode('ciam_forgot_form', array($this, 'ciam_forgot_form'));
            add_action('wp_head', array($this, 'ciam_hook_changepassword'));
            add_shortcode('ciam_password_form', array($this, 'ciam_password_form'));
            add_filter('lostpassword_url', array($this, 'custom_forgot_page'), 100);
        }

        /*
         * Forgot password form
         */

        public function ciam_forgot_form() {
            global $ciam_setting;           
     
            if (!empty($ciam_setting['lost_password_page_id'])) {
                $redirect_url = get_permalink($ciam_setting['login_page_id']);
                if (!is_user_logged_in()) {
                    ?>
                    <script>
                        jQuery(document).ready(function () {
                        forgotpass_hook('<?php echo $redirect_url ?>');
                        });</script>
                    <?php
                    $message = '<div  class="messageinfo"></div>';
                    ob_start();
                    $html = '<div class="ciam-user-reg-container">' . $message . '<span id="forgotpasswordmessage"></span><div id="forgotpassword-container" class="forgotpassword-container ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="lr_loading_screen"><div class="lr_loading_screen_center" style="position: fixed;"><img class="loading_circle ciam_loading_gif_align ciam_forgot lr_loading_screen_spinner"  src="' . CIAM_PLUGIN_URL . 'authentication/assets/images/loading-white.png' . '" alt="loding image" /></div></div></div><span class="ciam-link"><a href = "' . wp_login_url() . '">Login</a></span><span class="ciam-link btn"><a href = "' . wp_registration_url() . '">Register</a></span></div>';
                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $html);
                    return $html . ob_get_clean();
                }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Hook for change password section.
         */

        public function ciam_hook_changepassword() {
            global $ciam_setting;
            if (isset($ciam_setting) && !empty($ciam_setting['login_page_id'])) {
                $redirect_url = get_permalink($ciam_setting['login_page_id']);
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                    resetPassword('<?php echo $redirect_url ?>');
                    });</script>

                <?php
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }      

        /*
         * Reset password form
         */

        public function ciam_password_form() {
            $user_id = get_current_user_id();
            if (!is_user_logged_in()) {
                $db_message = get_user_meta($user_id, 'ciam_message_text', true);

                if (!empty($db_message)) {
                    delete_user_meta($user_id, 'ciam_message_text');
                }

                $message = '<div id="resetpassword" class="messageinfo">' . $db_message . '</div>';
                ob_start();
                add_action('admin_init', array($this, 'change_password_handler'));
                if (isset($_GET['vtype']) && !empty($_GET['vtype'])) { // condition to check if vtype and vtoken is present or not....
                    $html = '<div class="ciam-user-reg-container">' . $message . '<div id="resetpassword-container" class="ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="lr_loading_screen"><div class="lr_loading_screen_center" style="position: fixed;"><img class="loading_circle ciam_loading_gif_align ciam_forgot lr_loading_screen_spinner" src="' . CIAM_PLUGIN_URL . 'authentication/assets/images/loading-white.png' . '" alt="loding image" /></div></div></div><span class="ciam-link"><a href = "' . wp_login_url() . '">Login</a></span><span class="ciam-link btn"><a href = "' . wp_registration_url() . '">Register</a></span></div>';
                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $html);
                    return $html . ob_get_clean();
                } else {
                    ?>
                    <div id="error" ></div>
                    <script type="text/javascript">
                        jQuery(document).ready(function(){
                        jQuery("#error").text('You are not allowed to access this page !').css('color', 'red');
                        setTimeout(function(){
                        window.location.href = '<?php echo wp_login_url() ?>';
                        }, 2000);
                        });</script>
                    <?php
                }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Replace old password section in the wp admin
         */

       
        /*
         * Change Password handler
         */

        public function change_password_handler() {
            
            global $ciam_credentials, $message;
            $ciam_message = false;
            $user_id = get_current_user_id();
            $authAPI = new \LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI();
            $passform = isset($_POST['passform']) ? $_POST['passform'] : '';
            $oldpassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '';
            $newpassword = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';

            if (($passform == 1) && !empty($oldpassword) && !empty($newpassword)) {
                    $accessToken = get_user_meta($user_id, 'accesstoken', true);
                    try {
                        $authAPI->changePassword($accessToken, $_POST['newpassword'], $_POST['oldpassword']);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $message = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : _e("Opps Something Went Wrong !");
                        add_user_meta($user_id, 'ciam_pass_error', sanitize_text_field($message));
                        $ciam_message = true;
                    }
            }
            register_setting('ciam_authentication_settings', 'ciam_authentication_settings', array($this, 'validation'));
            if (isset($_GET['updated']) && $ciam_message == false) {
                if (!empty(get_user_meta($user_id, 'ciam_pass_error', true))) {
                    ?>
                    <div class="updated notice is-dismissible">
                        <p><strong><?php echo get_user_meta($user_id, 'ciam_pass_error', true); ?></strong></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>
                    <?php
                    delete_user_meta($user_id, 'ciam_pass_error');
                }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }

        /*
         * change authentication link for the forgotpassword page....
         */

        public function custom_forgot_page() {
            global $ciam_setting;
            if (!empty($ciam_setting['lost_password_page_id'])) {
                $forgot_page = get_permalink($ciam_setting['lost_password_page_id']);
            } else {
                $forgot_page = site_url('wp-login.php?action=lostpassword');
            }
         
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $forgot_page);
            return $forgot_page;
        }

    }

    new CIAM_Authentication_Passwordhandler();
}
