<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

use LoginRadiusSDK\Utility\SOTT;

if (!class_exists('CIAM_Authentication_Commonmethods')) {

    class CIAM_Authentication_Commonmethods {
        /*
         * class constructor
         */

        public function __construct() {
            add_action('init', array($this, 'init'));
        }

        /*
         * Load all required dependencies
         */

        public function init() {
            add_action('wp_head', array($this, 'ciam_hook_commonoptions'));
            add_action('wp_head', array($this, 'ciam_hook_loader'));
            add_action('admin_head', array($this, 'ciam_hook_commonoptions'));
            add_action('wp_head', array($this, 'birthdateonregistrationtime'));
        }

        /*
         * custom ciam form loader....
         */

        public static function ciam_hook_loader() {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    setTimeout(function () {
                        jQuery("#ciam_loading_gif").hide();
                    }, 3000);
                    loadingimg();
                });
            </script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * show birth date on registration time....
         */

        public function birthdateonregistrationtime() {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {
                    LRObject.$hooks.register('afterFormRender', function (actionName) {
                        if (actionName === "registration") {
                            show_birthdate_date_block();
                        }
                    });

                });



            </script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Ciam Hook for Common Option ....
         */

        public function ciam_hook_commonoptions() {
            global $ciam_credencials, $ciam_setting;

            $verificationurl = get_permalink($ciam_setting['login_page_id']);
            $forgoturl = get_permalink($ciam_setting['change_password_page_id']);
            if ((!isset($ciam_credencials['apikey']) && empty($ciam_credencials['apikey'])) || (!isset($ciam_credencials['secret']) && empty($ciam_credencials['secret']))) {
                return;
            }
            ?>
            <script>
                var commonOptions = {};
                commonOptions.apiKey = "<?php echo $ciam_credencials['apikey']; ?>";
                commonOptions.appName = "<?php echo $ciam_credencials['sitename']; ?>";
                commonOptions.formValidationMessage = true;
                commonOptions.hashTemplate = true;
                commonOptions.askRequiredFieldForTraditionalLogin = true;
                commonOptions.forgotPasswordUrl = '<?php echo $forgoturl; ?>';
                commonOptions.resetPasswordUrl = '<?php echo $forgoturl; ?>';
            <?php
            if (isset($ciam_setting['debug_enable']) && $ciam_setting['debug_enable'] == 1) {
                ?>
                    commonOptions.debugMode = true;
                <?php
            }
            if (isset($ciam_setting['password-stength']) && $ciam_setting['password-stength'] == 1) {
                ?>
                    commonOptions.displayPasswordStrength = true;
                <?php
            }
            if (isset($ciam_setting['pass-max-length']) && isset($ciam_setting['pass-min-length']) && !empty($ciam_setting['pass-max-length']) && !empty($ciam_setting['pass-min-length'])) {
                ?>

                    commonOptions.passwordLength = {min: "<?php echo $ciam_setting['pass-min-length']; ?>", max: "<?php echo $ciam_setting['pass-max-length'] ?>"};
                <?php
            }
            if (isset($ciam_setting['terms_conditions']) && !empty($ciam_setting['terms_conditions'])) {
                ?>
                    commonOptions.termsAndConditionHtml = '<?php echo $ciam_setting['terms_conditions'] ?>';
                <?php
            }
            if (!isset($ciam_setting['captcha'])) { // this will work only if captcha is not enabled.
                try {
                    new LoginRadiusSDK\Utility\Functions($ciam_credencials['apikey'], $ciam_credencials['secret']);
                    $sott = new SOTT();
                    ?>
                        commonOptions.sott = '<?php echo urlencode($sott->encrypt(10, true)); ?>';
                <?php } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    ?>
                        console.log('Internal Error Occured to get SOTT!!');
                    <?php
                }
            }
            ?>
                commonOptions.verificationUrl = '<?php echo $verificationurl; ?>';
            <?php if (isset($ciam_setting['phonelogin']) && $ciam_setting['phonelogin'] == "phone") { ?>
                    commonOptions.phoneLogin = true;
                <?php if (isset($ciam_setting['instantotplogin']) && !empty($ciam_setting['instantotplogin'])) { ?>
                        commonOptions.instantOTPLogin = true;
                    <?php
                }
                if (isset($ciam_setting['existPhoneNumber']) && !empty($ciam_setting['existPhoneNumber'])) {
                    ?>
                        commonOptions.existPhoneNumber = true;
                    <?php
                }
                if (isset($ciam_setting['smsTemplatePhoneVerification']) && !empty($ciam_setting['smsTemplatePhoneVerification'])) {
                    ?>
                        commonOptions.smsTemplatePhoneVerification = "<?php echo $ciam_setting['smsTemplatePhoneVerification']; ?>";
                    <?php
                }
                if (isset($ciam_setting['smsTemplateWelcome']) && !empty($ciam_setting['smsTemplateWelcome'])) {
                    ?>
                        commonOptions.smsTemplateWelcome = "<?php echo $ciam_setting['smsTemplateWelcome'] ?>";
                    <?php
                }
            } elseif (isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == "required") {
                if (isset($ciam_setting['prompt_password']) && $ciam_setting['prompt_password'] == 1) {
                    ?>
                        commonOptions.promptPasswordOnSocialLogin = true;
                    <?php
                }
                if (isset($ciam_setting['login_type']) && $ciam_setting['login_type'] == "username") {
                    ?>
                        commonOptions.usernameLogin = true;
                    <?php
                }
                if (isset($ciam_setting['askEmailForUnverifiedProfileAlways']) && $ciam_setting['askEmailForUnverifiedProfileAlways'] == 1) {
                    ?>
                        commonOptions.askEmailForUnverifiedProfileAlways = true;
                    <?php
                }
                if (isset($ciam_setting['loginOnEmailVerification']) && $ciam_setting['loginOnEmailVerification'] == 1) {
                    ?>
                        commonOptions.loginOnEmailVerification = true;
                    <?php
                }
            } elseif (isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == "optional") {
                ?>
                    commonOptions.optionalEmailVerification = true;
                <?php
                if (isset($ciam_setting['loginOnEmailVerification']) && $ciam_setting['loginOnEmailVerification'] == 1) {
                    ?>
                        commonOptions.loginOnEmailVerification = true;
                    <?php
                }
                if (isset($ciam_setting['askEmailForUnverifiedProfileAlways']) && $ciam_setting['askEmailForUnverifiedProfileAlways'] == 1) {
                    ?>
                        commonOptions.askEmailForUnverifiedProfileAlways = true;
                    <?php
                }
            } elseif (isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == "disable") {
                ?>
                    commonOptions.disabledEmailVerification = true;
                    commonOptions.promptPasswordOnSocialLogin = false;
                    commonOptions.loginOnEmailVerification = false;
                    commonOptions.askEmailForUnverifiedProfileAlways = false;
                    commonOptions.usernameLogin = false;
                <?php
            }
            if (isset($ciam_setting['captcha']) && $ciam_setting['captcha'] == 1) {
                if ($ciam_setting['captchatype'] == "invisibleRecaptcha") {
                    ?>
                        commonOptions.invisibleRecaptcha = true;
                <?php } elseif ($ciam_setting['captchatype'] == "v2Recaptcha") { ?>
                        commonOptions.v2Recaptcha = true;
                <?php } ?>
                    commonOptions.v2RecaptchaSiteKey = '<?php echo (isset($ciam_setting['recaptchasitekey']) && !empty($ciam_setting['recaptchasitekey']) ? $ciam_setting['recaptchasitekey'] : '') ?>';
                <?php
            }
            if (isset($ciam_setting['account-verification-template']) && !empty($ciam_setting['account-verification-template'])) {
                ?>
                    commonOptions.verificationEmailTemplate = '<?php echo $ciam_setting['account-verification-template'] ?>';
                <?php
            }
            if (isset($ciam_setting['welcome-template']) && !empty($ciam_setting['welcome-template'])) {
                ?>
                    commonOptions.welcomeEmailTemplate = '<?php echo $ciam_setting['welcome-template'] ?>';
                <?php
            }
            if (isset($ciam_setting['reset-template']) && !empty($ciam_setting['reset-template'])) {
                ?>
                    commonOptions.resetPasswordEmailTemplate = '<?php echo $ciam_setting['reset-template'] ?>';
                <?php
            }
// 2 factor authentication
            if (isset($ciam_setting['2fa']) && $ciam_setting['2fa'] == 1) {
                if (isset($ciam_setting['authenticationtype']) && $ciam_setting['authenticationtype'] == 'twoFactorAuthentication') {
                    ?>
                        commonOptions.twoFactorAuthentication = true;
                    <?php
                    if (isset($ciam_setting['google_authenticator']) && $ciam_setting['google_authenticator'] == 1) {
                        ?>
                            commonOptions.googleAuthentication = true;
                        <?php
                    }
                } elseif (isset($ciam_setting['authenticationtype']) && $ciam_setting['authenticationtype'] == 'optionalTwoFactorAuthentication') {
                    ?>
                        commonOptions.optionalTwoFactorAuthentication = true;
                        commonOptions.showTwoFactorOnProfile = true;
                        commonOptions.googleAuthentication = true;
                    <?php
                }
                if (isset($ciam_setting['smsTemplate2FA']) && !empty($ciam_setting['smsTemplate2FA'])) {
                    ?>
                        commonOptions.smsTemplate2FA = "<?php echo $ciam_setting['smsTemplate2FA'] ?>";
                    <?php
                }
            }
            if (isset($ciam_setting['onclicksignin']) && $ciam_setting['onclicksignin'] == 1 && (!isset($ciam_setting['phonelogin']) || $ciam_setting['phonelogin'] != 'phone')) {
                ?>
                    commonOptions.instantLinkLogin = true;
                <?php if (isset($ciam_setting['instantLinkLoginEmailTemplate']) && !empty($ciam_setting['instantLinkLoginEmailTemplate'])) { ?>
                        commonOptions.instantLinkLoginEmailTemplate = '<?php echo $ciam_setting['instantLinkLoginEmailTemplate'] ?>';
                    <?php
                }
            }
            if (isset($ciam_setting['remember_me']) && $ciam_setting['remember_me'] == 1) {
                ?>
                    commonOptions.stayLogin = true;
                <?php
            }
            if (isset($ciam_setting['autohidetime']) && !empty($ciam_setting['autohidetime'])) {
                ?>
                    var ciamautohidetime = <?php echo (int)$ciam_setting['autohidetime'];?>;
                <?php
            }else{
                ?>
                    var ciamautohidetime = 0;
                <?php
            }
            $customString = isset($ciam_setting['custom_field_obj']) ? $ciam_setting['custom_field_obj'] : '';


            if (!empty($customString)) {
                $ciamCustomOption = json_decode($customString, true);
                if (!is_array($ciamCustomOption)) {
                    echo htmlentities($customString);
                } else {
                    foreach ($ciamCustomOption as $key => $value) {
                        echo 'commonOptions.' . $key . ' = ' . (is_array($value) ? json_encode($value) : "'" . $value . "'") . ';';
                    }
                }
            }
            ?>
                var LRObject = new LoginRadiusV2(commonOptions);
                LRObject.$hooks.call('setButtonsName', {
                    instantLinkLoginButtonLabel: '<?php echo $ciam_setting["instantLinkLoginButtonLabel"] ?>'
                });
                LRObject.$hooks.register('endProcess', function (name) {
                    jQuery("#ciam_loading_gif").hide();
                });
                jQuery(document).ready(function () {
                <?php 
                if(!is_super_admin()){?>
                    jQuery("#email").attr('readonly', 'readonly');
                    <?php }
                ?>
                        });
            </script>                
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

    }

    new CIAM_Authentication_Commonmethods();
}

