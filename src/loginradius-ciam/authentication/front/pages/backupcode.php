<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

use LoginRadiusSDK\CustomerRegistration\Advanced\MultiFactorAuthenticationAPI;

if (!class_exists('CIAM_Authentication_Backupcode')) {

    class CIAM_Authentication_Backupcode {
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
            add_action('show_user_profile', array($this, 'ciam_backupcode'));
        }

        /*
         * Replace old password section in the wp admin
         */

        public function ciam_backupcode() {
            
            global $ciam_setting, $ciam_credentials;
                $user_id = get_current_user_id();
                if ($user_id > 0) {
                    $accessToken = get_user_meta($user_id, 'accesstoken', true);
                    if (!empty($accessToken)) {
                        $mfaObject = new MultiFactorAuthenticationAPI();
                        try {
                            $authpermission = $mfaObject->mfaConfigureByAccessToken($accessToken);
                            if ((isset($authpermission->IsGoogleAuthenticatorVerified) && $authpermission->IsGoogleAuthenticatorVerified) || (isset($authpermission->IsOTPAuthenticatorVerified) && $authpermission->IsOTPAuthenticatorVerified)) {
                                ?>
                                <script type="text/javascript">
                                    jQuery(document).ready(function () {
                                        generatebackupcodebutton('<?php echo $accessToken ?>');
                                    });
                                </script>
                                <?php }
                        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                            $message = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : _e("Opps Something Went Wrong !");
                            add_user_meta($user_id, 'ciam_pass_error', $message);
                        }
                    }
                }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

    }

    new CIAM_Authentication_Backupcode();
}
