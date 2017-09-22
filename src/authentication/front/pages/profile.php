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

            if (isset($ciam_setting) && !empty($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] !== "disable") {
                add_action('admin_head', array($this, 'ciam_hook_accountLinking'));
                add_action('admin_head', array($this, 'ciam_hook_accountunlinking'));
                add_action('admin_head', array($this, 'accountlinking_custom_script'));
                add_action('show_user_profile', array($this, 'accountlinking_custom_div'));
            }

            if (isset($ciam_setting['2fa']) && $ciam_setting['2fa'] == 1 && (!isset($ciam_setting['2fa']) || $ciam_setting['2fa'] != "phone")) {
                if (isset($ciam_setting['authenticationtype']) && !empty($ciam_setting['authenticationtype'])) {
                    add_action('show_user_profile', array($this, 'profiletwofactorauthentication'));
                    add_action('edit_user_profile', array($this, 'profiletwofactorauthentication'));
                    add_action('admin_head', array($this, 'TwoFAonprofile'));
                }
            }

            if (isset($ciam_setting['phonelogin']) && $ciam_setting['phonelogin'] == "phone") {
                add_action('show_user_profile', array($this, 'profilephoneuupdate'));
                add_action('admin_head', array($this, 'profilephoneupdatejs'));
                add_action('admin_head', array($this, 'ciam_hook_accountLinking'));
                add_action('admin_head', array($this, 'ciam_hook_accountunlinking'));
                add_action('admin_head', array($this, 'accountlinking_custom_script'));
                add_action('show_user_profile', array($this, 'accountlinking_custom_div'));
            }

            add_action('edit_user_profile', array($this, 'accountlinking_custom_div'));
            if (!isset($ciam_setting['phonelogin']) || $ciam_setting['phonelogin'] != "phone") {
                add_action('admin_head', array($this, 'extra_email_fields'));
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
                    accountlinking();
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
                accountunlinking();
                });</script>

            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * Two Factor Authentication
         */

        public function profiletwofactorauthentication() {?>
            <div style="clear:both;"><h2>Two Factor Authentication</h2><div id="authentication-container"></div></div>
            <?php
        }

        /*
         * update phone on profile if phone verification if enable
         */

        public function profilephoneuupdate() {?>
            <div style="clear:both;"><h2>Update Phone Number</h2><div id="updatephone-container"></div> </div>
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
                <# }else{ #>
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

        function accountlinking_custom_div() {?>
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
                    $lr_profile = $accoutObj->getProfileByEmail($current_user->user_email);
                    add_user_meta($user_id, 'ciam_current_user_uid', $lr_profile->Uid);
                } else {
                    $lr_profile = $accoutObj->getProfileByUid($ciam_uid);
                    $lr_array = array();

                    foreach ($lr_profile->Email as $key => $value) {
                        $lr_array[$key] = $value->Value;
                    }

                    if (!in_array($current_user->user_email, $lr_array)) {
                        wp_update_user(array('ID' => $user_id, 'user_email' => esc_attr($lr_profile->Email[0]->Value)));                // updating email to cms db..
                        $current_user = wp_get_current_user();
                        $current_user->user_email = $lr_profile->Email[0]->Value;
                    }
                }
                if (!empty($_COOKIE['addemail'])) {?>
                    <div class="updated notice is-dismissible">
                        <p><strong><?php echo $_COOKIE['addemail']; ?></strong></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>
                    <?php
                    unset($_COOKIE['addemail']);
                    $_COOKIE['addemail'] = "";
                }
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                         additionalemailform('<?php echo $current_user->user_email ?>',<?php echo json_encode($lr_profile->Email) ?>, '<?php echo count($lr_profile->Email) ?>', '<?php echo CIAM_PLUGIN_URL . 'authentication/assets/images/fancy_close.png'; ?>');
                    });
                </script>
                <?php
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

        /*
         * 2FA on Profile page 
         */

        public function TwoFAonprofile() {?>
            <script type="text/javascript">
                jQuery(document).ready(function(){ // it will call the optional 2 fa f                           unction
                 optionalTwoFA();
                });
            </script>
            <?php
        }

        /*
         * Update phone on profile section. 
         */

        public function profilephoneupdatejs() {?>
                <script type="text/javascript">
                    jQuery(document).ready(function(){ // it will call the optional 2 fa f                           unction
                         updatephoneonprofile();
                    });
                </script>
                <?php
        }

    }

    new CIAM_Authentication_Profile();
}

