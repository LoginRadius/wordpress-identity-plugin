<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

use LoginRadiusSDK\CustomerRegistration\Management\AccountAPI;

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
                    var lrObjectInterval1 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval1);
                    LRObject.$hooks.register('afterFormRender', function (actionName) {
                        if (actionName === "registration") {
                            show_birthdate_date_block();
                        }
                    });
                }
                    }, 1);
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

            $verificationurl = (isset($ciam_setting['login_page_id'])) ? get_permalink($ciam_setting['login_page_id']) : '';
            $forgoturl = (isset($ciam_setting['change_password_page_id'])) ? get_permalink($ciam_setting['change_password_page_id']) : '';
            if ((!isset($ciam_credencials['apikey']) && empty($ciam_credencials['apikey'])) || (!isset($ciam_credencials['secret']) && empty($ciam_credencials['secret']))) {
                return;
            }
            ?>
            <script>
                var commonOptions = {};
                commonOptions.apiKey = "<?php echo $ciam_credencials['apikey']; ?>";
                commonOptions.appName = '<?php echo $ciam_credencials['sitename']; ?>';
                commonOptions.formValidationMessage = true;
                commonOptions.hashTemplate = true;
                commonOptions.forgotPasswordUrl = '<?php echo $forgoturl; ?>';
                commonOptions.resetPasswordUrl = '<?php echo $forgoturl; ?>';
               
                <?php
            
                if (isset($ciam_setting['prompt_password']) && $ciam_setting['prompt_password'] == 1) {
                    ?>
                        commonOptions.promptPasswordOnSocialLogin = true;
                    <?php
                }
                else{
                      ?>
                        commonOptions.promptPasswordOnSocialLogin = false;
                    <?php
                }
                if (isset($ciam_setting['login_type']) && $ciam_setting['login_type'] == 1) {
                    ?>
                        commonOptions.usernameLogin = true;
                    <?php
                }
                else{
                    ?>
                        commonOptions.usernameLogin = false;
                    <?php
                }
                if (isset($ciam_setting['onclicksignin']) && $ciam_setting['onclicksignin'] == 1) {
                ?>
                    commonOptions.instantLinkLogin = true;
                <?php if (isset($ciam_setting['instantLinkLoginEmailTemplate']) && !empty($ciam_setting['instantLinkLoginEmailTemplate'])) { ?>
                        commonOptions.instantLinkLoginEmailTemplate = '<?php echo $ciam_setting['instantLinkLoginEmailTemplate'] ?>';
                    <?php
                }
            }
            else{
                ?>
                commonOptions.instantLinkLogin = false;
                <?php
            }
            if (isset($ciam_setting['instantotplogin']) && $ciam_setting['instantotplogin'] == 1) { ?>
                        commonOptions.instantOTPLogin = true;
                          <?php if (isset($ciam_setting['instantOTPLoginEmailTemplate']) && !empty($ciam_setting['instantOTPLoginEmailTemplate'])) { ?>
                        commonOptions.instantOTPLoginEmailTemplate = '<?php echo $ciam_setting['instantOTPLoginEmailTemplate'] ?>';
                    <?php
                }
                }
                else{
                    ?>
                         commonOptions.instantOTPLogin = false;
                         <?php
                }
                if (isset($ciam_setting['askEmailForUnverifiedProfileAlways']) && $ciam_setting['askEmailForUnverifiedProfileAlways'] == 1) {
                    ?>
                        commonOptions.askEmailForUnverifiedProfileAlways = true;
                    <?php
                }
                else{
                     ?>
                        commonOptions.askEmailForUnverifiedProfileAlways = false;
                    <?php
                }
                if (isset($ciam_setting['AskRequiredFieldsOnTraditionalLogin']) && $ciam_setting['AskRequiredFieldsOnTraditionalLogin'] == 1) {
                    ?>
                        commonOptions.askRequiredFieldForTraditionalLogin = true;
                    <?php
                }
                else{
                    ?>
                        commonOptions.askRequiredFieldForTraditionalLogin = false;
                    <?php
                }
                if (isset($ciam_setting['existPhoneNumber']) && $ciam_setting['existPhoneNumber'] == 1) {
                    ?>
                        commonOptions.existPhoneNumber = true;
                    <?php
                }
                else{
                   ?>
                        commonOptions.existPhoneNumber = false;
                    <?php 
                }
                if (isset($ciam_setting['welcome-template']) && $ciam_setting['welcome-template'] != '' && $ciam_setting['welcome-template'] != 'default') {
                    ?>
                        commonOptions.welcomeEmailTemplate = '<?php echo $ciam_setting['welcome-template']?>';
                    <?php
                }
                 if (isset($ciam_setting['reset-template']) && $ciam_setting['reset-template'] != '' && $ciam_setting['reset-template'] != 'default') {
                    ?>
                        commonOptions.resetPasswordEmailTemplate = '<?php echo $ciam_setting['reset-template']?>';
                    <?php
                }
               if (isset($ciam_setting['account-verification-template']) && $ciam_setting['account-verification-template'] != '' && $ciam_setting['account-verification-template'] != 'default') {
                    ?>
                        commonOptions.verificationEmailTemplate = '<?php echo $ciam_setting['account-verification-template']?>';
                    <?php
                }
               if (isset($ciam_setting['smsTemplatePhoneVerification']) && $ciam_setting['smsTemplatePhoneVerification'] != '' && $ciam_setting['smsTemplatePhoneVerification'] != 'default') {
                    ?>
                        commonOptions.smsTemplatePhoneVerification = '<?php echo $ciam_setting['smsTemplatePhoneVerification']?>';
                    <?php
                }
                if (isset($ciam_setting['smsTemplateWelcome']) && $ciam_setting['smsTemplateWelcome'] != '' && $ciam_setting['smsTemplateWelcome'] != 'default') {
                    ?>
                        commonOptions.smsTemplateWelcome = '<?php echo $ciam_setting['smsTemplateWelcome']?>';
                    <?php
                }
                if (isset($ciam_setting['instantLinkLoginEmailTemplate']) && $ciam_setting['instantLinkLoginEmailTemplate'] != '' && $ciam_setting['instantLinkLoginEmailTemplate'] != 'default') {
                    ?>
                        commonOptions.instantLinkLoginEmailTemplate = '<?php echo $ciam_setting['instantLinkLoginEmailTemplate']?>';
                    <?php
                }
                if (isset($ciam_setting['smsTemplate2FA']) && $ciam_setting['smsTemplate2FA'] != '' && $ciam_setting['smsTemplate2FA'] != 'default') {
                    ?>
                        commonOptions.smsTemplate2FA = '<?php echo $ciam_setting['smsTemplate2FA']?>';
                    <?php
                }
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
            
            if (isset($ciam_setting['terms_conditions']) && !empty($ciam_setting['terms_conditions'])) {
                $string = $ciam_setting['terms_conditions'];
                $string = str_replace(array('<script>', '</script>'), '', $string);
                $string = trim(str_replace('"', "'", $string));
                $terms = str_replace(array("\r\n", "\r", "\n"), " ", $string);
                ?>
                    commonOptions.termsAndConditionHtml = '<?php echo $terms ?>';
                <?php
            }
           
                try {
                    
                    $sottApi = new \LoginRadiusSDK\Utility\SOTT($ciam_credencials['apikey'], $ciam_credencials['secret'],array('output_format' => 'json'));
                    $sott = $sottApi->encrypt(20)->Sott;
                    ?>
                        commonOptions.sott = '<?php echo $sott?>';
                <?php } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    ?>
                        console.log('Internal Error Occured to get SOTT!!');
                    <?php
                }
            
            ?>
                commonOptions.verificationUrl = '<?php echo $verificationurl; ?>';
                <?php
              
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
                 if (typeof LoginRadiusV2 === 'undefined') {
    	         var e = document.createElement('script');
    	         e.src = '//auth.lrcontent2.com/v2/js/LoginRadiusV2.js';
    	         e.type = 'text/javascript';
                 document.getElementsByTagName("head")[0].appendChild(e);
	         }
	        var lrloadInterval = setInterval(function () {
    	        if (typeof LoginRadiusV2 != 'undefined') {
        	clearInterval(lrloadInterval);
                 LRObject = new LoginRadiusV2(commonOptions);
    	        }
	        }, 1);
                var lrObjectInterval = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval);
                    LRObject.$hooks.register('endProcess', function (name) {
                    jQuery("#ciam_loading_gif").hide();
                });
                }
                }, 1);
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

