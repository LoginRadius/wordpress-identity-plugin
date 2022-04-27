<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Authentication_Profile')) {
    class CIAM_Authentication_Profile
    {
        /*
         * Class constructor function
         */

        public function __construct()
        {
            add_action('init', array($this, 'init'));
        }

        /*
         * Load required dependencies
         */

        public function init()
        {
            global $ciam_setting,$ciam_credentials;
            $user_id = get_current_user_id();
            $accesstoken = get_user_meta($user_id, 'accesstoken', true);

            
     
            if (!empty($accesstoken)) {
                $configAPI = new \LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI();
                try {
                    $config = $configAPI->getConfigurations();
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    error_log($e->getErrorResponse()->Description);
                }
                if (isset($config) && isset($config->ProductPlan) && ($config->ProductPlan == 'developer' || $config->ProductPlan == 'business' || $config->ProductPlan == '')) {
                    if (isset($config) && isset($config->EmailVerificationFlow) && $config->EmailVerificationFlow != 'disabled') {
                        add_action('admin_head', array($this, 'ciam_hook_accountLinking'));
                        add_action('admin_head', array($this, 'ciam_hook_accountunlinking'));
                        add_action('admin_head', array($this, 'accountlinking_custom_script'));
                        add_action('show_user_profile', array($this, 'accountlinking_custom_div'));
                        add_action('edit_user_profile', array($this, 'accountlinking_custom_div'));
                    }
                }

                add_action('show_user_profile', array($this, 'profiletwofactorauthentication'));
                add_action('edit_user_profile', array($this, 'profiletwofactorauthentication'));
                add_action('admin_head', array($this, 'TwoFAonprofile'));

                if (isset($config) && isset($config->ProductPlan) && ($config->ProductPlan == 'business' || $config->ProductPlan == '')) {
                    add_action('admin_head', array($this, 'profilePhoneDisplay'));
                    add_action('admin_head', array($this, 'profilePhoneUpdateJS'));
                    add_action('show_user_profile', array($this, 'profilePhoneUpdate'));
                }

                if (isset($config) && isset($config->ProductPlan) && $config->ProductPlan == '') {
                    add_action('admin_head', array($this, 'userProfileUpdateJS'));
                    add_action('show_user_profile', array($this, 'userProfileUpdate'));
                    
                    if (isset($config) && isset($config->EmailVerificationFlow) && $config->EmailVerificationFlow != 'disabled') {
                        add_action('admin_head', array($this, 'extra_email_fields'));
                    }
                }

                add_action('admin_head', array($this, 'profile_password'));
            }
        }

        public function profilePhoneDisplay()
        {
            $user_id = get_current_user_id();
            global $ciam_credentials, $pagenow;
            $accesstoken = get_user_meta($user_id, 'accesstoken', true);

            if (!empty($accesstoken && $pagenow === "profile.php")) {
                $phoneid = '--';
                $authAPI = new \LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI();
                try {
                    $userpro = $authAPI->getProfileByAccessToken($accesstoken);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    error_log($e->getErrorResponse()->Description);
                }
                if (isset($userpro)) {
                    $phoneid = isset($userpro->PhoneId) && $userpro->PhoneId != '' ? $userpro->PhoneId : '';
                    if ($phoneid != '') {
                        $phone_html = '<tr class="phoneid_table" style="display: none"><th>Phone Number</th><td>' . $phoneid . '</td></tr>'; ?>
                    <script>
                        var phoneid = "<?php echo $phoneid ?>";
                        jQuery(document).ready(function() {
                        field = '<?php echo $phone_html; ?>';
                        jQuery(field).insertBefore('.user-url-wrap');
                        });</script>
                    <?php
                    }
                }
            }
        }

        /*
         * Account Linking interface
         */

        public function ciam_hook_accountLinking()
        {
            $user_id = get_current_user_id();
            $accesstoken = get_user_meta($user_id, 'accesstoken', true); ?><script type='text/javascript'>
                jQuery(document).ready(function () {
                var lrObjectInterval23 = setInterval(function () {
                if (typeof LRObject !== 'undefined')
                {                 
                clearInterval(lrObjectInterval23);
                accountlinking();
                }
                }, 1);
                });</script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Account unlinking interface
         */

        public function ciam_hook_accountunlinking()
        {
            ?>
            <script>
                jQuery(document).ready(function () {
                var lrObjectInterval24 = setInterval(function () {
                if (typeof LRObject !== 'undefined')
                {
                clearInterval(lrObjectInterval24);
                accountunlinking();
                }
                }, 1);
                });</script>

            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Two Factor Authentication
         */

        public function profiletwofactorauthentication()
        {
            $user_id = get_current_user_id();
            global $ciam_credentials;
            $accesstoken = get_user_meta($user_id, 'accesstoken', true);
    
            if (!empty($accesstoken)) {
                $authObject = new \LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI();
                try {
                    $userprofile = $authObject->getProfileByAccessToken($accesstoken);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    error_log($e->error_response->description);
                }
          
                if (isset($userprofile->Provider) && $userprofile->Provider == 'Email') {
                    ?>
                    <div style="clear:both;"><h3 class="profiletwofactorauthentication" style="display: none;">Two Factor Authentication</h3><div id="authentication-container"></div></div>   

                    <?php
                }
            }
        }

        /*
         * update phone on profile if phone verification if enable
         */

        public function profilePhoneUpdate()
        {
            ?>
            <div style="clear:both;">
                <h3 class="profilephoneuupdate" style="display: none">Update Phone Number</h3>
                <div id="updatephone-container"></div> 
            </div>
            <?php
        }

        /*
        * update user profile
        */

        public function userProfileUpdate()
        {
            ?>
            <div style="clear:both;">
            <h3 class="userProfileUpdate" style="display: none">Update User Profile</h3>
            <div id="profileeditor-container"></div> 
            </div>
            <?php
        }

        /*
         * Account Linking code starts....
         */

        public function accountlinking_custom_script()
        {
            ?>
            <script type="text/html" id="loginradiuscustom_tmpl_link">
                <# if(isLinked) { #>
                <div class="ciam-linked">
                    <div class="ciam-provider-label ciam-icon-<#=Name.toLowerCase()#>"></div>Connected
                    <a onclick='return <#=ObjectName#>.util.unLinkAccount("<#= Name.toLowerCase() #>","<#= providerId #>")'>delete</a>
                                </div>
                            <# }else { #>
                                <div class="ciam-unlinked">
                                    <a class="ciam-provider-label ciam-icon-<#=Name.toLowerCase()#>" href="javascript:void(0)" onclick="return  <#=ObjectName#>.util.openWindow('<#= Endpoint #>');" title="<#=Name#>" alt="Sign in with <#=Name#>"></a>
                                </div>
                            <# } #>
                        </script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Add linking interface custom div
         */

        public function accountlinking_custom_div()
        {
            ?>
                   <div>
                               <span id="social-msg"></span>
                               <div id="interfacecontainerdiv" class="interfacecontainerdiv"></div>
                               <div style="clear:both;"></div>
                           </div>
                   <?php
                   /* action for debug mode */
                   do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Add More email to CMS ....
         */

        public function extra_email_fields()
        {
            global $ciam_credentials, $pagenow;
            $user_id = get_current_user_id();
            if ($pagenow === "profile.php") {
                $accountObj = new \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI();
                $current_user = wp_get_current_user(); // getting the current user info
                $ciam_uid = get_user_meta($user_id, 'ciam_current_user_uid', true);

                if (empty($ciam_uid)) {
                    try {
                        $lr_profile = $accountObj->getAccountProfileByEmail($current_user->user_email);
                        if (isset($lr_profile->Description)) {
                            error_log($lr_profile->Description);
                        } else {
                            add_user_meta($user_id, 'ciam_current_user_uid', $lr_profile->Uid);
                        }
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        error_log($e->getErrorResponse()->Description);
                    }
                } else {
                    $lr_array = array();
                    try {
                        $lr_profile = $accountObj->getAccountProfileByUid($ciam_uid);
                        if (!empty($lr_profile->Email)) {
                            foreach ($lr_profile->Email as $key => $value) {
                                $lr_array[$key] = $value->Value;
                            }
                            if (!in_array($current_user->user_email, $lr_array)) {
                                wp_update_user(array('ID' => $user_id, 'user_email' => esc_attr($lr_profile->Email[0]->Value)));                // updating email to cms db..
                                $current_user = wp_get_current_user();
                                $current_user->user_email = $lr_profile->Email[0]->Value;
                            }
                        }
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        error_log($e->getErrorResponse()->Description);
                    }
                }
                if (!empty($_COOKIE['addemail'])) {
                    ?>
                                   <div class="updated notice is-dismissible">
                                       <p><strong><?php echo $_COOKIE['addemail']; ?></strong></p>
                                       <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                                   </div>
                                   <?php
                                   unset($_COOKIE['addemail']);
                    $_COOKIE['addemail'] = "";
                }
                if (isset($lr_profile->Email)) {
                    ?>
                                            <script type="text/javascript">
                                            jQuery(document).ready(function () {
                                                        additionalemailform('<?php echo $current_user->user_email ?>',<?php echo json_encode($lr_profile->Email) ?>, <?php echo json_encode($lr_profile->EmailVerified) ?>,'<?php echo count($lr_profile->Email) ?>', '<?php echo CIAM_PLUGIN_URL . 'authentication/assets/images/fancy_close.png'; ?>');
                                    });
                                    </script>
                                   <?php
                }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }
                       
        public function profile_password()
        {
            global $ciam_credentials;
            $uri = $_SERVER['REQUEST_URI']; // getting the current page url
            $pagename = explode('?', basename($uri)); // checking for the query string
            $user_id = get_current_user_id();
            $access_token = get_user_meta($user_id, 'accesstoken', true);
            
            $accountObj = new \LoginRadiusSDK\CustomerRegistration\Account\AccountAPI();
            $authObj = new \LoginRadiusSDK\CustomerRegistration\Authentication\AuthenticationAPI();
            $current_user = wp_get_current_user(); // getting the current user info
            $ciam_uid = get_user_meta($user_id, 'ciam_current_user_uid', true);
            
            if (empty($ciam_uid)) {
                try {
                    $userprofile = $authObj->getProfileByAccessToken($access_token);
                    if (isset($userprofile->Description)) {
                        error_log($userprofile->Description);
                    } else {
                        add_user_meta($user_id, 'ciam_current_user_uid', $userprofile->Uid);
                    }
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    error_log($e->getErrorResponse()->Description);
                }
            } else {
                try {
                    $userprofile = $accountObj->getAccountProfileByUid($ciam_uid);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                }
            }
                   
            if (isset($_POST) && isset($_POST['loginradius-setnewpassword-hidden']) && $_POST['loginradius-setnewpassword-hidden'] == 'setpassword' && isset($_POST['setnewpassword']) && isset($_POST['setconfirmpassword']) && $_POST['setnewpassword'] == $_POST['setconfirmpassword']) {
                try {
                    $ciam_uid = get_user_meta($user_id, 'ciam_current_user_uid', true);
                    $result = $accountObj->setAccountPasswordByUid($_POST['setnewpassword'], $ciam_uid);
                    if (isset($result) && $result) {
                        add_action('admin_notices', array($this, 'admin_notice__success' ));
                    }
                } catch (LoginRadiusException $e) {
                    error_log($e->getErrorResponse()->Description);
                    add_action('admin_notices', array($this, 'admin_notice__error' ));
                }
            }
      
            if ($pagename[0] != "user-new.php" && $pagename[0] != "user-edit.php") { // condition to check the default and edit page
               
                ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
            <?php
            
  
    if (isset($userprofile->Password) && $userprofile->Password != '') {
        ?>
                        var lrObjectInterval22 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval22); 
                    setTimeout(function(){ changepasswordform(); }, 100);
                    jQuery('#pass1-text').css('visibility','hidden');
                    LRObject.$hooks.register('afterFormRender', function (name) {
                    if (name === "changepassword") {
                    jQuery('#changepassword-container').append('<span class="show-password"></span>');
                    }
                    if(name === 'otp')
                    {
                        jQuery("#updatephone-container").after("<span id='authdiv_success'></span>");
                         ciamfunctions.message("An OTP has been sent.", "#authdiv_success", "success");
                    }});
                    }
                    }, 1);
                    jQuery("#password th,#password td").html('');
                    jQuery("#password th").html('<span>Change Password</span>');
                    var content = '<a id="open_password_popup" class="button open ciam-password-button" href="javascript:void(0);">Change Password</a>';
                    content += '<div class="popup-outer-password" style="display:none;">';
                    content += '<span id="close_password_popup">';
                    content += '<img src="<?php echo CIAM_PLUGIN_URL . 'authentication/assets/images/fancy_close.png'; ?>" alt="close" />';
                    content += '</span>';
                    content += '<div class="popup-inner-password">';
                    content += '<span class="popup-txt">';
                    content += '<h1>';
                    content += '<strong>Please Enter New Password</strong>';
                    content += '</h1>';
                    content += '</span>';
                    content += '<div id="ciam_change_password_notification"></div>';
                    content += '<div id="changepassword-container"></div>';
                    content += '</div>';
                    content += '</div>';
                    content += '<span class="password-input-wrapper show-password">';
                    content += '<input style="display:hidden;" type="password" name="pass1" id="pass1" class="regular-text strong" value="" autocomplete="off" data-pw="Z4G%PbRnMl)krYm)vrCiNV!C" aria-describedby="pass-strength-result">';
                    content += '</span>';
                    jQuery(".user-pass1-wrap td").append(content);
            <?php
    } else { //in case of social login and password set to null
                ?>
               
                    setTimeout(function(){ setpasswordform();jQuery('#pass1-text').css('visibility','hidden'); }, 500);
                    jQuery("#password th,#password td").html('');
                    jQuery("#password th").html('<span>Set Password</span>');
                    var content = '<a id="open_password_popup" class="button open ciam-password-button" href="javascript:void(0);">Set Password</a>';
                    content += '<div class="popup-outer-password" style="display:none;">';
                    content += '<span id="close_password_popup">';
                    content += '<img src="<?php echo CIAM_PLUGIN_URL . 'authentication/assets/images/fancy_close.png'; ?>" alt="close" />';
                    content += '</span>';
                    content += '<div class="popup-inner-password">';
                    content += '<span class="popup-txt">';
                    content += '<h1>';
                    content += '<strong>Please Enter New Password</strong>';
                    content += '</h1>';
                    content += '</span>';
                    content += '<div id="ciam_change_password_notification"></div>';
                    content += '<div id="setpassword-container"><form name="loginradius-setnewpassword" method="POST"><div class="loginradius--form-element-content content-loginradius-newpassword"><label for="loginradius-setpassword-newpassword">Password</label><input type="password" name="setnewpassword" id="loginradius-setpassword-newpassword" class="loginradius-password loginradius-newpassword lr-required"><div id="validation-loginradius-setpassword-newpassword" class="loginradius-validation-message validation-loginradius-newpassword"></div></div><div class="loginradius--form-element-content content-loginradius-confirmnewpassword"><label for="loginradius-setpassword-confirmnewpassword">Confirm Password</label><input type="password" name="setconfirmpassword" id="loginradius-setpassword-confirmnewpassword" class="loginradius-password loginradius-confirmnewpassword lr-required"><input type="hidden" name="loginradius-setnewpassword-hidden" value="setpassword"><div id="validation-loginradius-setpassword-confirmnewpassword" class="loginradius-validation-message validation-loginradius-confirmnewpassword"></div></div><input type="submit" name="setnewpasswordsubmit" value="submit" id="loginradius-newpwd-submit-submit" class="loginradius-submit submit-loginradius-submit"></form></div> ';
                    content += '</div>';
                    content += '</div>';
                    content += '<span class="password-input-wrapper show-password">';
                    content += '<input style="display:hidden;" type="password" name="pass1" id="pass1" class="regular-text strong" value="" autocomplete="off" data-pw="Z4G%PbRnMl)krYm)vrCiNV!C" aria-describedby="pass-strength-result">';
                    content += '</span>';
                    jQuery(".user-pass1-wrap td").append(content);
                    jQuery('input[name="setnewpasswordsubmit"]').click(function(e){
                                e.preventDefault();
                    var newpwd = jQuery('#loginradius-setpassword-newpassword').val();
                         var newconfirmpwd = jQuery('#loginradius-setpassword-confirmnewpassword').val();
                         if(newpwd != '' && newconfirmpwd != '')
                         {
                             if(newpwd == newconfirmpwd){
                                 jQuery('form[name="loginradius-setnewpassword"]').submit();
                             }
                             else{
                                 jQuery('#ciam_change_password_notification').text('The Confirm Password field does not match the Password field.').css('color','#FF0000').show().fadeOut(ciamautohidetime * 1000);
                                 return false;
                             }
                         }
                         else{
                             jQuery('#ciam_change_password_notification').text('The password and confirm password fields are required.').css('color','#FF0000').show().fadeOut(ciamautohidetime * 1000);
                             return false;
                         }
                    });
                   
                   <?php
                    
            }
            } else {
                ?>
                    setTimeout(function(){ jQuery("#pass1-text,#pass1").attr('style', 'visibility:visible !important;'); }, 500);
            <?php
            } ?>
                });
            </script> 
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * 2FA on Profile page
         */

        public function TwoFAonprofile()
        {
            ?>
                            <script type="text/javascript">
                            jQuery(document).ready(function(){ // it will call the optional 2 fa f                           unction
                                                var lrObjectInterval25 = setInterval(function () {
                                                if (typeof LRObject !== 'undefined')
                                                {
                                                clearInterval(lrObjectInterval25);
                                                optionalTwoFA();
                            }
                            }, 1);
                            });
                            </script>
                               <?php
        }

        /*
         * Update phone on profile section.
         */

        public function profilePhoneUpdateJS()
        {
            ?>
                               <script type="text/javascript">
                                jQuery(document).ready(function(){ // it will call the optional 2 fa function
                                                        var lrObjectInterval26 = setInterval(function () {
                                                        if (typeof LRObject !== 'undefined')
                                                        {
                                clearInterval(lrObjectInterval26);                    
                                updatephoneonprofile();
                                }
                                }, 1);
                                });
                                </script>
                            <?php
        }

        /*
        * Update user profile section.
        */

        public function userProfileUpdateJS()
        {
            ?>
                            <script type="text/javascript">
                                jQuery(document).ready(function(){ // it will call the optional 2 fa function
                                                        var lrObjectInterval26 = setInterval(function () {
                                                        if (typeof LRObject !== 'undefined')
                                                        {
                                clearInterval(lrObjectInterval26);                    
                                profileUpdateContainer();
                           }
                           }, 1);
                           });
                                </script>
                                            <?php
        }

        public function admin_notice__success()
        {
            ?>
                                        <div class="notice notice-success is-dismissible">
                                            <p><?php _e('Password set successfully', 'sample-text-domain'); ?></p>
                                        </div>
                                        <?php
        }

        public function admin_notice__error()
        {
            ?>
                                        <div class="notice notice-error is-dismissible">
                                            <p><?php _e('An error has occured!', 'sample-text-domain'); ?></p>
                                        </div>
                                        <?php
        }
    }

    new CIAM_Authentication_Profile();
}
