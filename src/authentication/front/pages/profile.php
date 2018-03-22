<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Authentication_Profile')) {

    class CIAM_Authentication_Profile {
        /*
         * Class constructor function
         */

        public function __construct() {

            add_action('init', array($this, 'init'));
        }

        /*
         * Load required dependencies
         */

        public function init() {
            global $ciam_setting;

        
                add_action('admin_head', array($this, 'ciam_hook_accountLinking'));
                add_action('admin_head', array($this, 'ciam_hook_accountunlinking'));
                add_action('admin_head', array($this, 'accountlinking_custom_script'));
                add_action('show_user_profile', array($this, 'accountlinking_custom_div'));
           
                add_action('show_user_profile', array($this, 'profiletwofactorauthentication'));
                add_action('edit_user_profile', array($this, 'profiletwofactorauthentication'));
                add_action('admin_head', array($this, 'TwoFAonprofile'));
                    
               add_action('admin_head', array($this, 'profilephonedisplay'));
               add_action('admin_head', array($this, 'profilephoneupdatejs'));
               
               add_action('admin_head', array($this, 'extra_email_fields'));
               add_action('show_user_profile', array($this, 'profilephoneuupdate'));
               //add_action('admin_head', array($this, 'extra_email_fields'));
                
        
               add_action('edit_user_profile', array($this, 'accountlinking_custom_div'));
            
               
          
        }
        
        
        public function profilephonedisplay()
        {
             $user_id = get_current_user_id();
            global $ciam_credencials,$pagenow;
            $accesstoken = get_user_meta($user_id, 'accesstoken', true);
           
            if (!empty($accesstoken && $pagenow === "profile.php")) {
            $phoneid = '--';
                $userAPI = new \LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($ciam_credencials['apikey'], $ciam_credencials['secret'], array('output_format' => 'json'));
                try{
                      $userpro = $userAPI->getProfile($accesstoken);
                }catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        error_log($e->getErrorResponse()->Description);
                    }
                if(isset($userpro))
                {
                $phoneid = isset($userpro->PhoneId) && $userpro->PhoneId != '' ? $userpro->PhoneId : '--';
                $phone_html = '<tr class="phoneid_table" style="display: none"><th>Phone Number</th><td>'. $phoneid.'</td></tr>';
                ?>
                <script>
                    var phoneid = "<?php echo $phoneid?>";
                    jQuery(document).ready(function() {
            field = '<?php echo $phone_html;?>';
            jQuery(field).insertBefore('.user-url-wrap');
        });
                    </script>
                    <?php
                }
                
            }
        }

        /*
         * Account Linking interface
         */

        public function ciam_hook_accountLinking() {
            $user_id = get_current_user_id();
            $accesstoken = get_user_meta($user_id, 'accesstoken', true);
            ?><script type='text/javascript'><?php
            if (!empty($accesstoken)) {
                ?>
                        // to set localstorage for token to show linking interface in case of hosted page enable .....
                        localStorage.setItem('LRTokenKey', "<?php echo $accesstoken; ?>");
            <?php } ?>
                    jQuery(document).ready(function () {
                        var lrObjectInterval23 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
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

        public function ciam_hook_accountunlinking() {
            ?>
            <script>
                jQuery(document).ready(function () {
                var lrObjectInterval24 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
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

        public function profiletwofactorauthentication() {
            $user_id = get_current_user_id();
            global $ciam_credencials;
            $accesstoken = get_user_meta($user_id, 'accesstoken', true);
           
            if (!empty($accesstoken)) {
                $socialAPI = new \LoginRadiusSDK\CustomerRegistration\Social\SocialLoginAPI($ciam_credencials['apikey'], $ciam_credencials['secret'], array('output_format' => 'json'));
                try{
                      $socialpro = $socialAPI->getUserProfiledata($accesstoken);
                }catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        error_log($e->error_response->description);
                    }
                if(isset($socialpro) && $socialpro->Provider == 'RAAS')
                {
            ?>
          <div style="clear:both;"><h2 class="profiletwofactorauthentication" style="display: none">Two Factor Authentication</h2><div id="authentication-container"></div></div>   
                   
            <?php
                }
            }
        }

        /*
         * update phone on profile if phone verification if enable
         */

        public function profilephoneuupdate() {
           
            
            ?>
                <div style="clear:both;"><h2 class="profilephoneuupdate" style="display: none">Update Phone Number</h2><div id="updatephone-container"></div> </div>
            <?php
            
        }
        
       

        /*
         * Account Linking code starts....
         */

        public function accountlinking_custom_script() {
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

        function accountlinking_custom_div() {
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

        public function extra_email_fields() {
            global $ciam_credencials, $pagenow;
            $user_id = get_current_user_id();
            if ($pagenow === "profile.php") {
                $accoutObj = new \LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($ciam_credencials['apikey'], $ciam_credencials['secret'], array('output_format' => 'json'));
                $current_user = wp_get_current_user(); // getting the current user info....
                $ciam_uid = get_user_meta($user_id, 'ciam_current_user_uid', true);

                if (empty($ciam_uid)) {
                    try {
                        $lr_profile = $accoutObj->getProfileByEmail($current_user->user_email);
                        if(isset($lr_profile->Description)){
                            error_log($lr_profile->Description);
                        }else{
                            add_user_meta($user_id, 'ciam_current_user_uid', $lr_profile->Uid);
                        }
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        error_log($e->getErrorResponse()->Description);
                    }
                } else {
                    $lr_array = array();
                    try {
                        $lr_profile = $accoutObj->getProfileByUid($ciam_uid);
                        if(!empty($lr_profile->Email))
                        {
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
                if(isset($lr_profile->Email))
                {
                ?>
                            <script type="text/javascript">
                            jQuery(document).ready(function () {
                            additionalemailform('<?php echo $current_user->user_email ?>',<?php echo json_encode($lr_profile->Email) ?>, '<?php echo count($lr_profile->Email) ?>', '<?php echo CIAM_PLUGIN_URL . 'authentication/assets/images/fancy_close.png'; ?>');
                                });
                </script>
                <?php
                }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * 2FA on Profile page 
         */

        public function TwoFAonprofile() {
            ?>
                            <script type="text/javascript">
                            jQuery(document).ready(function(){ // it will call the optional 2 fa f                           unction
                       var lrObjectInterval25 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval25);
                        optionalTwoFA();
                        }
                        }, 1);
                            });
                            </script    >
            <?php
        }

        /*
         * Update phone on profile section. 
         */

        public function profilephoneupdatejs() {
            ?>
                            <script type="text/javascript">
                        jQuery(document).ready(function(){ // it will call the optional 2 fa function
                       var lrObjectInterval26 = setInterval(function () {
                if(typeof LRObject !== 'undefined')
                {
                    clearInterval(lrObjectInterval26);
                        updatephoneonprofile();
                        }
                        }, 1);
                            });
                            </script>
                    <?php
                }

            }

            new CIAM_Authentication_Profile();
        }

