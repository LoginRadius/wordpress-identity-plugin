<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius Ciam.
 */
if (!class_exists('CIAM_Front')) {

    class CIAM_Front {

        /**
         * Constructor
         */
        public function __construct() { 
            global $ciam_credencials,$ciamloading_gif;
            
            
            if(!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])){ 
                 return;   
             }
            
            add_action('init', array('CIAM_Social_Login', 'init'));
            add_action('init', array($this, 'init'));
            add_action('init', array('CIAM_Social_Login', 'custom_page_redirection'));

            $ciamloading_gif = CIAM_PLUGIN_URL . 'authentication/assets/images/loading_icon.gif';

           
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        public function init() { 
           
            /* Start Ciam form shortcode and actions */
            add_shortcode('ciam_login_form', array($this, 'ciam_login_form'));
            add_shortcode('ciam_registration_form', array($this, 'ciam_registration_form'));
            add_shortcode('ciam_forgotten_form', array(get_class(), 'ciam_forgotten_form'));
            add_shortcode('ciam_password_form', array(get_class(), 'ciam_password_form'));
            add_shortcode('ciam_email_verification', array(get_class(), 'ciam_email_verification'));

            add_action('wp_head', array($this, 'ciam_hook_socialLogin_custom_script'));
            add_action('admin_head', array($this, 'ciam_hook_passwordform'));
            add_action('admin_head', array($this, 'extra_email_fields'));
            add_action('admin_head', array($this, 'ciam_hook_accountLinking'));
            add_action('admin_head', array($this, 'ciam_hook_accountunlinking'));
            add_action('admin_head', array($this, 'accountlinking_custom_script'));
            add_action('wp_head', array($this, 'ciam_hook_changepassword'));
            add_action('wp_head', array($this, 'ciam_hook_loader'));
            add_action('show_user_profile', array($this, 'accountlinking_custom_div'));
            add_action('edit_user_profile', array($this, 'accountlinking_custom_div'));
            
            /* End Ciam form shortcode */
            
             
           
            
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }
        
       
        
        /*
         * custom ciam form loader....
         */

        public function ciam_hook_loader() {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function () {  
                    jQuery("#ciam_loading_gif").hide();

                });
               
                loadingimg();

            </script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        // Create short codes
        //[ciam_login_form]
        public function ciam_login_form() { 
            global $ciam_setting, $ciamloading_gif;
               
            $url = get_permalink($ciam_setting['login_page_id']);
            //checking for hosted page....
                
                if(isset($ciam_setting) && false){
                    if(isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == 1){ 
                       
                        wp_redirect(wp_login_url());
                        
                     
                    }
                }
               
              
            if (!is_user_logged_in()) { ?>
                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        login_hook('<?php echo $url ?>');
                        social('<?php echo $url ?>');
                    });
                </script> 
                <?php
                if(!empty($_GET) && !empty($_GET['vtype']) && !empty($_GET['vtoken'])){
                  ?>  
                <script type="text/javascript">
                    jQuery(document).ready(function(){ 
                        emailverification('<?php echo $url ?>');
                    });
                </script>
               <?php }
                
                $message = '<div id="" class="messageinfo"></div>';
                ob_start();
                
                $html = '<div class="ciam-user-reg-container">' . $message . '<span id="verificationmessage"></span>';
                $html .= '<div class="ciam-user-reg-container">' . $message . '<span id="loginmessage"></span><div id="sociallogin-container"></div><div id="interfacecontainerdiv" class="interfacecontainerdiv"></div><div id="login-container" class="ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="ciam-loading-img"><img  src="' . $ciamloading_gif . '" alt="loding image" class="loading_circle ciam_loading_gif_align" /></div></div><div class="various-grid accout-login" id="reset_from" ></div><span class="ciam-link"><a href = "' . wp_registration_url() . '">Register</a></span><span class="ciam-link btn"><a href = "' . wp_lostpassword_url() . '">Forgot Password</a></span></div>';

                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $html);
                return $html . ob_get_clean();
            }elseif(is_user_logged_in() && (!empty($_GET) && !empty($_GET['vtype']) && !empty($_GET['vtoken']))){
               
                $profile_url = get_edit_user_link( get_current_user_id() ); 
               
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function(){ 
                        emailverification('<?php echo $profile_url ?>');
                    });
                </script>
                <?php
                $message = '<div id="" class="messageinfo"></div>';
                ob_start();
                $html = '<div class="ciam-user-reg-container">' . $message . '<span id="verificationmessage"></span>';
                $html .= '<div class="ciam-user-reg-container">' . $message . '<span id="loginmessage"></span></div>';

                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $html);
                return $html . ob_get_clean();
                
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        //[ciam_email_verification]
        public static function ciam_email_verification() {
            
            if (!is_user_logged_in()) {
                
                $message = '<div id="" class="messageinfo"></div>';
                ob_start();
                $html = '<div class="ciam-user-reg-container">' . $message . '<span id="verificationmessage"></span>';
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), $html);
                return $html . ob_get_clean();
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

        //[ciam_registration_form]
        public function ciam_registration_form() {
            global $ciam_setting, $ciamloading_gif;
            
            $url = get_permalink($ciam_setting['login_page_id']);
            if (!is_user_logged_in()) {
                ?>

                <script type="text/javascript">
                    jQuery(document).ready(function () {
                        registration_hook('<?php echo $url ?>');
                        social('<?php echo $url ?>');
                    });
                </script> 
                
                <?php
                $message = '<div id="messageinfo" class="messageinfo"></div>';
                ob_start();
                $html = '<div class="ciam-user-reg-container">' . $message;
                $html .= '<span id="registration_message"></span><div id="interfacecontainerdiv" class="interfacecontainerdiv"></div><div id="registration-container" class="ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="ciam-loading-img"><img class="loading_circle ciam_loading_gif_align" src="' . $ciamloading_gif . '" alt="loding image" /></div></div>';
                $html .= '<span class="ciam-link"><a href="' . wp_login_url(). '">Login</a></span>';
                $html .= '<span class="ciam-link btn"><a href="' . wp_lostpassword_url() . '">Forgot Password</a></span></div>';
                add_action('wp_footer', array($this,'datepickerscript'));
                
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), $html);
                return $html . ob_get_clean();
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

        public function datepickerscript(){ 
            
            wp_enqueue_style('ciam-style-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
            wp_enqueue_script('ciam-js-datepicker', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js', array('jquery'), CIAM_PLUGIN_VERSION, false);
            
                  /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }
        
        //[ciam_forgotpassword_form]
        public static function ciam_forgotten_form() {
            global $ciam_setting, $ciamloading_gif;
            
            $redirect_url = get_permalink($ciam_setting['login_page_id']);
            if (!is_user_logged_in()) {
                ?>
                <script>
                    jQuery(document).ready(function () {
                        forgotpass_hook('<?php echo $redirect_url ?>');
                    });
                </script>
                <?php
                $message = '<div  class="messageinfo"></div>';
                ob_start();
                $html = '<div class="ciam-user-reg-container">' . $message . '<span id="forgotpasswordmessage"></span><div id="forgotpassword-container" class="forgotpassword-container ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="ciam-loading-img"><img class="loading_circle ciam_loading_gif_align ciam_forgot"  src="' . $ciamloading_gif . '" alt="loding image" /></div></div><span class="ciam-link"><a href = "' . wp_login_url() . '">Login</a></span><span class="ciam-link btn"><a href = "' . wp_registration_url() . '">Register</a></span></div>';
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), $html);
                return $html . ob_get_clean();
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

        public function change_password_handler() { 
            global $ciam_credencials, $message;
            
            if(!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])){ 
                 return;   
             }
            $ciam_message = false;
            $user_id = get_current_user_id();
            
           
            
            $UserAPI = new \LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($ciam_credencials['apikey'], $ciam_credencials['secret']);
            $passform = isset($_POST['passform']) ? $_POST['passform'] : '';
            $oldpassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : '';
            $newpassword = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
            
            if (isset($passform) && ($passform == 1)) {
                if (!empty($oldpassword) && !empty($newpassword)) {

                    $accessToken = get_user_meta($user_id);
                    try {
                        $UserAPI->changeAccountPassword($accessToken['accesstoken'][0], $_POST['oldpassword'], $_POST['newpassword']);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $message = isset($e->getErrorResponse()->Description) ? $e->getErrorResponse()->Description : _e("Opps Something Went Wrong !");
                        add_user_meta($user_id, 'ciam_pass_error', $message);
                        $ciam_message = true;
                    }
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
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        //[ciam_password_form]
        public static function ciam_password_form() {
            global $ciamloading_gif;
           
            if (!is_user_logged_in()) {
                $db_message = get_user_meta(get_current_user_id(), 'ciam_message_text', true);

                if (!empty($db_message)) {
                    delete_user_meta(get_current_user_id(), 'ciam_message_text');
                }

                $message = '<div id="resetpassword" class="messageinfo">' . $db_message . '</div>';
                ob_start();
                add_action('admin_init', array(get_called_class(), 'change_password_handler'));
                 if(isset($_GET) && isset($_GET['vtype']) && !empty($_GET['vtype'])){ // condition to check if vtype and vtoken is present or not....
                
                $html = '<div class="ciam-user-reg-container">' . $message . '<div id="resetpassword-container" class="ciam-input-style"></div><div id="ciam_loading_gif" class="overlay" style="display:none;"><div class="ciam-loading-img"><img class="loading_circle ciam_loading_gif_align ciam_forgot" src="' . $ciamloading_gif . '" alt="loding image" /></div></div><span class="ciam-link"><a href = "' . wp_login_url() . '">Login</a></span><span class="ciam-link btn"><a href = "' . wp_registration_url() . '">Register</a></span></div>';
                 
                
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), $html);
               
                return $html . ob_get_clean();
                
                }else{ ?>
                    <div id="error" ></div>
                    
                    <script type="text/javascript">
                       jQuery(document).ready(function(){
                           
                           jQuery("#error").text('You are not allowed to access this page !').css('color','red');
                           setTimeout(function(){
                               window.location.href = '<?php echo wp_login_url() ?>';
                           },2000)
                       
                       });
                
                    </script>
                
                <?php }
            }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

        /*
         * Hook for change password section.
         */

        public function ciam_hook_changepassword() {
            global $ciam_setting;
            if(isset($ciam_setting) && !empty($ciam_setting['login_page_id'])){
            $redirect_url = get_permalink($ciam_setting['login_page_id']);
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                   changepassword('<?php echo $redirect_url ?>');
                });
            </script>

            <?php
            
           }
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
            
        }

        public function ciam_hook_passwordform() {
            ?>

            <script type="text/javascript">
                jQuery(document).ready(function () {
                    changepasswordform();

                });
            </script>


            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        
        /*
         * Add More email to CMS ....
         */

        public function extra_email_fields() {
            global $ciam_credencials,$pagenow;;
           
            if($pagenow === "profile.php"){
            $accoutObj = new \LoginRadiusSDK\CustomerRegistration\Management\AccountAPI($ciam_credencials['apikey'], $ciam_credencials['secret'], array('output_format' => 'json'));

            $current_user = wp_get_current_user(); // getting the current user info....
            
             $ciam_uid = get_user_meta(get_current_user_id(), 'ciam_current_user_uid', true);
             if(empty($ciam_uid)){ 
                 
            $lr_profile = $accoutObj->getProfileByEmail($current_user->user_email);
            add_user_meta(get_current_user_id(), 'ciam_current_user_uid', $lr_profile->Uid);
             }else{
             $lr_profile = $accoutObj->getProfileByUid($ciam_uid);
             $lr_array = array();
             foreach($lr_profile->Email as $key => $value){
                 $lr_array[$key] = $value->Value;
             }
            
                if(!in_array($current_user->user_email,$lr_array)){
                   
                  wp_update_user(array('ID' => get_current_user_id(), 'user_email' => esc_attr($lr_profile->Email[0]->Value))); // updating email to cms db..
                  $current_user = wp_get_current_user();
                  $current_user->user_email = $lr_profile->Email[0]->Value;
                }
                
                
             }
              if(!empty($_COOKIE['addemail'])){
                add_user_meta(get_current_user_id(), 'ciam_email_msg', $_COOKIE['addemail']);
                    ?>
                    <div class="updated notice is-dismissible">
                        <p><strong><?php echo get_user_meta(get_current_user_id(), 'ciam_email_msg', true); ?></strong></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
                    </div>
                    <?php
                    delete_user_meta(get_current_user_id(), 'ciam_email_msg');
                    unset($_COOKIE['addemail']);
                    $_COOKIE['addemail'] = "";
                    
                    
            }
            ?>

            <script type="text/javascript">
                jQuery(document).ready(function () {
                    var lr_profile_email = <?php echo json_encode($lr_profile->Email) ?>;
                    additionalemailform('<?php echo $current_user->user_email ?>',lr_profile_email, '<?php echo count($lr_profile->Email) ?>');

                });
            </script>
            <?php
          }
        }
        
        
        public function ciam_hook_accountLinking() {
         
            $accesstoken = get_user_meta(get_current_user_id(), 'accesstoken',true);
            if(!empty($accesstoken)){ ?>
            <script type='text/javascript'>
      // to set localstorage for token to show linking interface in case of hosted page enable .....
      
                localStorage.setItem('LRTokenKey', "<?php echo $accesstoken; ?>"); 
                
            </script>     
           <?php }
           
            ?>
            <script>
                jQuery(document).ready(function () {
                    accountlinking();
                });

            </script>

            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        function accountlinking_custom_div() {
            ?>
            <div><span id="social-msg"></span><div id="interfacecontainerdiv" class="interfacecontainerdiv"></div></div>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        public function ciam_hook_accountunlinking() {
            ?>
            <script>

                jQuery(document).ready(function () {
                    accountunlinking();
                });
            </script>

            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        /*
         * Account Linking code starts....
         */

        public function accountlinking_custom_script() {  ?>
            <script type="text/html" id="loginradiuscustom_tmpl_link">

                <# if(isLinked) { #>
               
                <div class="ciam-linked">
                    <div class="ciam-provider-label ciam-icon-<#=Name.toLowerCase()#>">                          
                    </div>
                    
                    Connected
                    <a  onclick='return  <#=ObjectName#>.util.unLinkAccount("<#= Name.toLowerCase() #>","<#= providerId #>")'>delete</a>
                    

                </div>
                <# }else{ #>
                <div class="ciam-unlinked">
                    <a class="ciam-provider-label ciam-icon-<#=Name.toLowerCase()#>" href="javascript:void(0)" onclick="return  <#=ObjectName#>.util.openWindow('<#= Endpoint #>');" title="<#=Name#>" alt="Sign in with <#=Name#>"></a>    
                </div>
                <# } #>
            </script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        /*
         * Social Login Form starts....
         */

        public function ciam_hook_socialLogin_custom_script() {
            ?>
            <script type="text/html" id="loginradiuscustom_tmpl">
                <a class="ciam-provider-label ciam-icon-<#=Name.toLowerCase()#>" href="javascript:void(0)" onclick="return <#=ObjectName#>.util.openWindow('<#= Endpoint #>');" title="<#= Name #>" alt="Sign in with <#=Name#>">
                </a>
            </script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

    }

    new CIAM_Front();
}