<?php

// Exit if called directly

if (!defined('ABSPATH')) {

    exit();

}

/**

 * The activation settings class.

 */

if (!class_exists('ciam_authentication_settings')) {



    class ciam_authentication_settings {



        /**

         * generate ciam page selection option

         * 

         * @param type $pages

         * @param type $settings

         * @param type $name

         * @return string

         */

        private function select_field($pages, $settings, $name) {

            $output = '<select class="ciam-row-field" name="ciam_authentication_settings[' . $name . ']" id="ciam_login_page_id">';

            $output .= '<option value="">' . __(' Select Page ', 'ciam-plugin-slug') . '</option>';

            foreach ($pages as $page) {

                $select_page = '';



                if (isset($settings[$name]) && $page->ID == $settings[$name]) {

                    $select_page = ' selected="selected"';

                }

                $output .= '<option value="' . $page->ID . '" ' . $select_page . '>' . $page->post_title . '</option>';

            }

            $output .= '</select>';

            /* action for debug mode */

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), $output);

            return $output;

        }



        /*

         * This function will render the HTML.....

         */



        public function render_options_page($args) {

            global $ciam_setting;



            $pages = get_pages($args);

            $ciam_setting = get_option('Ciam_Authentication_settings');

            ?>

<div class="wrap active-wrap cf">
  <header>
    <h1 class="logo"><a href="//www.loginradius.com" target="_blank">Authentication Page Configuration</a></h1>
  </header>
  <div class="cf">
    <form action="options.php" method="post">
      <?php

                        settings_fields('ciam_authentication_settings');

                        settings_errors();

                        ?>
      <ul class="ciam-options-tab-btns">
        <li class="nav-tab ciam-active" data-tab="ciam_options_tab-1">
          <?php _e('User Registration', 'ciam-plugin-slug') ?>
        </li>
        <li class="nav-tab" data-tab="ciam_options_tab-2">
          <?php _e('Authentication', 'ciam-plugin-slug') ?>
        </li>
        <li class="nav-tab" data-tab="ciam_options_tab-3">
          <?php _e('2FA Settings', 'ciam-plugin-slug') ?>
        </li>
        <li class="nav-tab" data-tab="ciam_options_tab-4">
          <?php _e('Advanced Settings', 'ciam-plugin-slug') ?>
        </li>
        <?php do_action("ciam_auth_tab_title"); ?>
        <li class="nav-tab" data-tab="ciam_options_tab-9">
          <?php _e('Short Codes', 'ciam-plugin-slug') ?>
        </li>
      </ul>
      <div id="ciam_options_tab-1" class="ciam-tab-frame ciam-active">
        <div class="ciam_options_container">
          <div class="ciam-row">
            <h3>
              <?php _e('User Registration integration', 'ciam-plugin-slug'); ?>
            </h3>
            <div>
              <?php

                                        /* action for hosted page */

                                        do_action("hosted_page");

                                        ?>
              <div id="autopage-generate">
                <input type="checkbox" class="ciam-toggle" id="ciam-autopage" name="ciam_authentication_settings[ciam_autopage]" value='1' <?php echo ( isset($ciam_setting['ciam_autopage']) && $ciam_setting['ciam_autopage'] == '1' ) ? 'checked' : '' ?> />
                <label class="ciam-show-toggle" for="ciam-autopage">
                  <?php _e('Auto Generate Authentication Page'); ?>
                  <span class="ciam-tooltip" data-title="<?php _e('This feature when enabled, automatically generates Authentication Pages.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
                <div class="ciam-row ciam-custom-page-settings">
                  <div>
                    <label> <span class="ciam_property_title">
                      <?php _e('Login page', 'ciam-plugin-slug'); ?>
                      <span class="ciam-tooltip" data-title="<?php _e('Add login page short code from Short Code tab in selected page.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </span> <?php echo $this->select_field($pages, $ciam_setting, 'login_page_id'); ?> </label>
                  </div>
                  <div>
                    <label> <span class="ciam_property_title">
                      <?php _e('Registration page', 'ciam-plugin-slug'); ?>
                      <span class="ciam-tooltip" data-title="<?php _e('Add registration page short code from Short Code tab in selected page.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </span> <?php echo $this->select_field($pages, $ciam_setting, 'registration_page_id'); ?> </label>
                  </div>
                  <div>
                    <label> <span class="ciam_property_title">
                      <?php _e('Forgot Password Page', 'ciam-plugin-slug'); ?>
                      <span class="ciam-tooltip" data-title="<?php _e('Add forgot password page short code from Short Code tab in selected page.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </span> <?php echo $this->select_field($pages, $ciam_setting, 'lost_password_page_id'); ?> </label>
                  </div>
                  <div>
                    <label> <span class="ciam_property_title">
                      <?php _e('Reset password page', 'ciam-plugin-slug'); ?>
                      <span class="ciam-tooltip" data-title="<?php _e('Add reset password page short code from Short Code tab in selected page.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </span> <?php echo $this->select_field($pages, $ciam_setting, 'change_password_page_id'); ?> </label>
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <h3>
              <?php _e('Redirection settings after login ', 'ciam-plugin-slug'); ?>
              <span class="active-tooltip" data-title="<?php _e('This feature sets the redirection to the page where user will get redirected to post login.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h3>
            <div class="custom-radio">
              <input id="radio0" type="radio" class="loginRedirectionRadio" name="ciam_authentication_settings[after_login_redirect]" value="samepage" <?php echo (!isset($ciam_setting['after_login_redirect']) || $ciam_setting['after_login_redirect'] == 'samepage' ) ? 'checked' : ''; ?>/>
              <label for="radio0"> <?php _e('Redirect to the same page where the user logged in', 'ciam-plugin-slug'); ?> </label>
            </div>
            <div class="custom-radio">
              <input id="radio2" type="radio" class="loginRedirectionRadio" name="ciam_authentication_settings[after_login_redirect]" value="homepage" <?php echo ( isset($ciam_setting['after_login_redirect']) && $ciam_setting['after_login_redirect'] == 'homepage' ) ? 'checked' : ''; ?>/>
              <label for="radio2">
              <?php _e('Redirect to the home page of your WordPress site', 'ciam-plugin-slug'); ?>
              </label> </div>
             <div class="custom-radio">
              <input id="radio3" type="radio" class="loginRedirectionRadio" name="ciam_authentication_settings[after_login_redirect]" value="dashboard" <?php echo ( isset($ciam_setting['after_login_redirect']) && $ciam_setting['after_login_redirect'] == 'dashboard' ) ? 'checked' : ''; ?> />
              <label for="radio3">
              <?php _e('Redirect to the user\'s account dashboard', 'ciam-plugin-slug'); ?>
              </label></div>
            <div class="custom-radio">
            <input id="radio4" type="radio" class="loginRedirectionRadio custom" id="customUrl" name="ciam_authentication_settings[after_login_redirect]" value="custom"  <?php echo ( isset($ciam_setting['after_login_redirect']) && $ciam_setting['after_login_redirect'] == 'custom' ) ? 'checked' : ''; ?>/>
            <label for="radio4">
            <?php _e('Redirect to a custom URL'); ?>
            </label>
            <div class="ciam-row" id="customRedirectUrlField">
             
                
                <!--<span class="ciam_property_title"><?php _e('Redirect to a custom URL'); ?></span>-->
                
                <input type="text" class="ciam-row-field" id="customRedirectOther" name="ciam_authentication_settings[custom_redirect_other]" value="<?php echo (isset($ciam_setting['custom_redirect_other'])) ? $ciam_setting['custom_redirect_other'] : ''; ?>" autofill='off' autocomplete='off' >
              
            </div>
            </div>
          </div>
        </div>
      </div>
      <div id="ciam_options_tab-2" class="ciam-tab-frame"> 
        
        <!-- Authentication Flow Type -->
        
        <div class="ciam_options_container">
          <div class="ciam-row ciam-ur-shortcodes loginoptions">
            <h3>
              <?php _e('Select authentication flow', 'ciam-plugin-slug'); ?>
            </h3>
            <div class="custom-radio">
              <input id="radio5"  type="radio" id="emaillogin" name="ciam_authentication_settings[phonelogin]" value="email" <?php echo (!isset($ciam_setting['phonelogin']) || $ciam_setting['phonelogin'] == 'email' ) ? 'checked' : ''; ?> />
              <label for="radio5">
              <?php _e('Login with email address', 'ciam-plugin-slug'); ?>
              </label> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, lets the end-user register/login with the email address.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span>
            
            </div>
            <div class="custom-radio">
              <input id="radio6" type="radio" id="phonelogin" name="ciam_authentication_settings[phonelogin]" value="phone" <?php echo ( isset($ciam_setting['phonelogin']) && $ciam_setting['phonelogin'] == 'phone' ) ? 'checked' : ''; ?> />
              <label for="radio6">
              <?php _e('Login with phone number', 'ciam-plugin-slug'); ?>
              </label> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, lets the end-user register/login with the phone number.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </div>
            
            <!-- Phone Login template -->
            
            <div class="ciam-row" id="phonetemplatediv">
              <div id="instantotp">
                <input type="checkbox" class="ciam-toggle active-toggle" id="instant_otp" name="ciam_authentication_settings[instantotplogin]" value='1' <?php echo ( isset($ciam_setting['instantotplogin']) && $ciam_setting['instantotplogin'] == '1' ) ? 'checked' : '' ?> />
                <label class="ciam-show-toggle" for="instant_otp">
                  <?php _e('Enable instant OTP login'); ?>
                  <span class="ciam-tooltip" data-title="<?php _e('Turn on, if you want to enable istant OTP login', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              </div>
              <div id="phonecheck">
                <input type="checkbox" class="ciam-toggle active-toggle" id="phone_check" name="ciam_authentication_settings[existPhoneNumber]" value='1' <?php echo ( isset($ciam_setting['existPhoneNumber']) && $ciam_setting['existPhoneNumber'] == '1' ) ? 'checked' : '' ?> />
                <label class="ciam-show-toggle" for="phone_check">
                  <?php _e('Check phone number exist or not?'); ?>
                  <span class="ciam-tooltip" data-title="<?php _e('Turn on, if you want to enable Phone Exist functionality', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              </div>
              <label class="custom-label">
                <?php _e('Use custom phone verification template', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip" id="custom-phone-temp" data-title="<?php _e('Enter the name of the custom phone verification template which is created in the LoginRadius Dashboard', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              <div>
                <label class="" for="custom-phone-template">
                  <input placeholder="Template name" type="text" name="ciam_authentication_settings[smsTemplatePhoneVerification]" id="custom-phone-template" value="<?php echo (isset($ciam_setting['smsTemplatePhoneVerification']) && !empty($ciam_setting['smsTemplatePhoneVerification'])) ? $ciam_setting['smsTemplatePhoneVerification'] : '' ?>" />
                </label>
              </div>
              <label class="custom-label">
                <?php _e('Use custom phone welcome template', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip" id="custom-phone-temp" data-title="<?php _e('Enter the name of the custom phone welcome template which is created in the LoginRadius Dashboard', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              <div>
                <label class="" for="custom-phone-welcome-template">
                  <input placeholder="Template name" type="text" name="ciam_authentication_settings[smsTemplateWelcome]" id="custom-phone-welcome-template" value="<?php echo (isset($ciam_setting['smsTemplateWelcome']) && !empty($ciam_setting['smsTemplateWelcome'])) ? $ciam_setting['smsTemplateWelcome'] : '' ?>" />
                </label>
              </div>
            </div>
            <div class="ciam-row" id="emailflowdiv">
              <div class="custom-radio">
                <input id="radio7" type="radio" class="loginRedirectionRadio authentication_flow_type" name="ciam_authentication_settings[authentication_flow_type]" value="required" id="required" <?php echo (!isset($ciam_setting['authentication_flow_type']) || $ciam_setting['authentication_flow_type'] == 'required' ) ? 'checked' : ''; ?>/>
                <label for="radio7">
                <?php _e('Required email verification flow for registration', 'ciam-plugin-slug'); ?>
                </label>
                </div>
              <div class="custom-radio">
                <input id="radio8" type="radio" class="loginRedirectionRadio authentication_flow_type" name="ciam_authentication_settings[authentication_flow_type]" value="optional" id="optional" <?php echo ( isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == 'optional' ) ? 'checked' : ''; ?> />
                <label for="radio8">
                <?php _e('Optional email verification flow for registration', 'ciam-plugin-slug'); ?>
                 </label>
                 </div>
                 
              <div class="custom-radio">
                <input id="radio9" type="radio" class="loginRedirectionRadio authentication_flow_type" name="ciam_authentication_settings[authentication_flow_type]" value="disable" id="disable" <?php echo (isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == 'disable' ) ? 'checked' : ''; ?>/>
                <label  for="radio9">
                <?php _e('Disable the email verification for the registering users', 'ciam-plugin-slug'); ?>
                </label> </div>
              <div class="ciam-row" id="requireflow">
                <input type="checkbox" class="ciam-toggle" id="ciam-loginOnEmailVerification" name="ciam_authentication_settings[loginOnEmailVerification]" value='1' <?php echo ( isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == 'required' && isset($ciam_setting['loginOnEmailVerification']) && $ciam_setting['loginOnEmailVerification'] == '1' ) ? 'checked' : '' ?> />
                <label class="ciam-show-toggle" for="ciam-loginOnEmailVerification">
                  <?php _e('Enable login on email verification'); ?>
                  <span class="ciam-tooltip" data-title="<?php _e('This feature when enabled, logs in the user when email is verified. Make sure that this feature is also enabled from the LoginRadius dashboard.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
                <input type="checkbox" class="ciam-toggle" id="prompt_password" name="ciam_authentication_settings[prompt_password]" value='1' <?php echo ( isset($ciam_setting['prompt_password']) && $ciam_setting['prompt_password'] == '1' ) ? 'checked' : '' ?> />
                <label class="ciam-show-toggle" for="prompt_password">
                  <?php _e('Enable prompt password on Social login', 'ciam-plugin-slug'); ?>
                  <span class="ciam-tooltip" data-title="<?php _e('This feature when enabled, will prompt the user to set the password at the time of login for the time from any social provider.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
                <input type="checkbox" class="ciam-toggle" id="allow_username_login" name="ciam_authentication_settings[login_type]" value="username" <?php echo ( isset($ciam_setting['login_type']) && $ciam_setting['login_type'] == 'username' ) ? 'checked' : ''; ?> />
                <label class="ciam-show-toggle" for="allow_username_login">
                  <?php _e('Enable login with username.', 'ciam-plugin-slug'); ?>
                  <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, will let the user to login with username as well as password.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
                <input type="checkbox" class="ciam-toggle" id="ask_email_for_unverified" name="ciam_authentication_settings[askEmailForUnverifiedProfileAlways]" value="1" <?php echo ( isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == 'required' && isset($ciam_setting['askEmailForUnverifiedProfileAlways']) && $ciam_setting['askEmailForUnverifiedProfileAlways'] == '1' ) ? 'checked' : ''; ?> />
                <label class="ciam-show-toggle" for="ask_email_for_unverified">
                  <?php _e('Ask for email from unverified user.', 'ciam-plugin-slug'); ?>
                  <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, will ask for email every time user tries to login if email is not verified.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              </div>
              <div class="ciam-row" id="optionalflow">
                <input type="checkbox" class="ciam-toggle" id="ciam-loginOnEmailVerification-optional" name="ciam_authentication_settings[loginOnEmailVerification]" value='1' <?php echo ( isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == 'optional' && isset($ciam_setting['loginOnEmailVerification']) && $ciam_setting['loginOnEmailVerification'] == '1' ) ? 'checked' : '' ?> />
                <label class="ciam-show-toggle" for="ciam-loginOnEmailVerification-optional">
                  <?php _e('Enable login on email verification'); ?>
                  <span class="ciam-tooltip" data-title="<?php _e('Turn on, if you want to enable login on email verification.Make sure that this option is also enabled from the LoginRadius', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
                <label class="active-toggle custom-top-marign">
                  <input type="checkbox" class="active-toggle" name="ciam_authentication_settings[askEmailForUnverifiedProfileAlways]" value="1" <?php echo ( isset($ciam_setting['authentication_flow_type']) && $ciam_setting['authentication_flow_type'] == 'optional' && isset($ciam_setting['askEmailForUnverifiedProfileAlways']) && $ciam_setting['askEmailForUnverifiedProfileAlways'] == '1' ) ? 'checked' : ''; ?> />
                  <span class="active-toggle-name">
                  <?php _e('Ask for email from unverified user.', 'ciam-plugin-slug'); ?>
                  </span> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, will ask for email every time user tries to login if email is not verified.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              </div>
              <div id="customemailtemplates" class="ciam-row">
                <div> <span class="ciam_property_title first">
                  <?php _e('Welcome email : ', 'ciam-plugin-slug'); ?>
                        <span class="ciam-tooltip" data-title="<?php _e('Enter the name of the custom Welcome Email template which is created in the LoginRadius Dashboard', 'ciam-plugin-slug'); ?>"> 
                            <span class="dashicons dashicons-editor-help"></span>
                        </span>
                  </span>
                  <input type="text" class="ciam-row-field" name="ciam_authentication_settings[welcome-template]" id="welcome_template" value="<?php echo (isset($ciam_setting['welcome-template']) && !empty($ciam_setting['welcome-template'])) ? $ciam_setting['welcome-template'] : '' ?>" />
                </div>
                <div> <span class="ciam_property_title">
                  <?php _e('Reset password email : ', 'ciam-plugin-slug'); ?>
                        <span class="ciam-tooltip" data-title="<?php _e('Enter the name of the custom Reset Password Email template which is created in the LoginRadius Dashboard', 'ciam-plugin-slug'); ?>"> 
                            <span class="dashicons dashicons-editor-help"></span>
                        </span>
                  </span>
                  <input type="text" class="ciam-row-field" name="ciam_authentication_settings[reset-template]" id="reset_template" value="<?php echo (isset($ciam_setting['reset-template']) && !empty($ciam_setting['reset-template'])) ? $ciam_setting['reset-template'] : '' ?>" />
                </div>
                <div> <span class="ciam_property_title">
                  <?php _e('Account verification email : ', 'ciam-plugin-slug'); ?>
                        <span class="ciam-tooltip" data-title="<?php _e('Enter the name of the custom Verification Email template which is created in the LoginRadius Dashboard', 'ciam-plugin-slug'); ?>"> 
                            <span class="dashicons dashicons-editor-help"></span>
                        </span>
                  </span>
                  <input type="text" class="ciam-row-field" name="ciam_authentication_settings[account-verification-template]" id="account-verification-template" value="<?php echo (isset($ciam_setting['account-verification-template']) && !empty($ciam_setting['account-verification-template'])) ? $ciam_setting['account-verification-template'] : '' ?>" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="ciam_options_tab-3" class="ciam-tab-frame"> 
        
        <!-- 2 Factor Authentication -->
        
        <div class="ciam_options_container">
          <div class="ciam-row ciam-ur-shortcodes loginoptions">
            <label class="active-toggle">
              <input id="2fa" type="checkbox" class="active-toggle" name="ciam_authentication_settings[2fa]" value="1" <?php echo ( isset($ciam_setting['2fa']) && $ciam_setting['2fa'] == '1' ) ? 'checked' : ''; ?> />
              <span class="active-toggle-name">
              <?php _e('Enable Two Factor Authentication', 'ciam-plugin-slug'); ?>
              </span> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature enables Two Factor Authentication.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
            
            <!-- select authentication type -->
            
            <div class="ciam-row" id="showauthdiv"> 
              
              <!-- google authenticator -->
              
              <div id="googleauthenticator">
                <input type="checkbox" class="ciam-toggle" id="google_authenticator" name="ciam_authentication_settings[google_authenticator]" value='1' <?php echo ( isset($ciam_setting['google_authenticator']) && $ciam_setting['google_authenticator'] == '1' ) ? 'checked' : '' ?> />
                <label class="ciam-show-toggle" for="google_authenticator">
                  <?php _e('Enable Google Authenticator'); ?>
                  <span class="ciam-tooltip" data-title="<?php _e('This feature enables Google Authenticator as one of the Second factor.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              </div>
              <label class="showauthdiv custom-label">
                <?php _e('Select the Two Factor Authentication Flow', 'ciam-plugin-slug'); ?>
                <?php

                                            if (isset($ciam_setting['authenticationtype']) && !empty($ciam_setting['authenticationtype'])) {

                                                $type = $ciam_setting['authenticationtype'];

                                            } else {

                                                $type = "";

                                            }

                                            ?>
                  <span class="ciam-tooltip" data-title="<?php _e('The feature helps in setting the flow for Two Factor Authentication', 'ciam-plugin-slug'); ?>"> 
                      <span class="dashicons dashicons-editor-help"></span>
                  </span>
              </label>
              <select name="ciam_authentication_settings[authenticationtype]" id="authenticationtype" class="showauthenticationdiv">
                <option value="twoFactorAuthentication" <?php if ($type === "twoFactorAuthentication") { ?> selected ="selected" <?php } ?>>
                <?php _e('Required two factor authentication', 'ciam-plugin-slug'); ?>
                </option>
                <option value="optionalTwoFactorAuthentication" <?php if ($type === "optionalTwoFactorAuthentication") { ?> selected ="selected" <?php } ?>>
                <?php _e('Optional two factor authentication', 'ciam-plugin-slug'); ?>
                </option>
              </select>
              
              <!-- 2FA OTP template -->
              
              <label class="custom-label">
                <?php _e('Use custom OTP template', 'ciam-plugin-slug'); ?>
                  <span class="ciam-tooltip" id="custom-otp-temp" data-title="<?php _e('Enter the name of the custom OTP template which is created in the LoginRadius Dashboard', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span>
              </label>
              <div>
                <label class="" for="custom-otp-template">
                  <input placeholder="Template name" type="text" name="ciam_authentication_settings[smsTemplate2FA]" id="custom-otp-template" value="<?php echo (isset($ciam_setting['smsTemplate2FA']) && !empty($ciam_setting['smsTemplate2FA'])) ? $ciam_setting['smsTemplate2FA'] : '' ?>" />
                   </label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="ciam_options_tab-4" class="ciam-tab-frame">
        <div class="ciam_options_container">
          <div class="ciam-row">
            <h3>
              <?php _e('Advanced options', 'ciam-plugin-slug'); ?>
            </h3>
            <div>
              <input type="checkbox" class="ciam-toggle" id="ciam-oneclicksignin" name="ciam_authentication_settings[onclicksignin]" value='1' <?php echo ( isset($ciam_setting['onclicksignin']) && $ciam_setting['onclicksignin'] == '1' ) ? 'checked' : '' ?> />
              <label class="ciam-show-toggle" for="ciam-oneclicksignin">
                <?php _e('Enable Instant Link Login'); ?>
                <span class="ciam-tooltip oneclick-signin-tooltip" data-title="<?php _e('This feature enables Instant Link Login on the login form. Make sure that this option is also enabled from the LoginRadius.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
            </div>
            <div class="ciam-row" id="hideoneclickdiv">
              <label class="custom-label">
                <?php _e('Instant Link Login custom template', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip" id="custom-oneclick-temp" data-title="<?php _e('Enter the name of the custom template which is created in the LoginRadius Dashboard', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              <div>
                <label class="" for="custom-onclick-template">
                  <input placeholder="Template name" type="text" name="ciam_authentication_settings[instantLinkLoginEmailTemplate]" id="custom-oneclick-template" value="<?php echo (isset($ciam_setting['instantLinkLoginEmailTemplate']) && !empty($ciam_setting['instantLinkLoginEmailTemplate'])) ? $ciam_setting['instantLinkLoginEmailTemplate'] : '' ?>" />
                </label>
              </div>
              <div>
                <label class="custom-label">
                  <?php _e('Instant Link Login button custom name', 'ciam-plugin-slug'); ?>
                  <span class="ciam-tooltip" id="custom-oneclick-temp" data-title="<?php _e('Enter custom name for the one click signin button', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
                <label for="custom-onclick-name">
                  <input placeholder="Button custom name" type="text" name="ciam_authentication_settings[instantLinkLoginButtonLabel]" id="custom-oneclick-customname" value="<?php echo (isset($ciam_setting['instantLinkLoginButtonLabel']) && !empty($ciam_setting['instantLinkLoginButtonLabel'])) ? $ciam_setting['instantLinkLoginButtonLabel'] : '' ?>" />
                </label>
              </div>
            </div>
            <div>
              <label class="active-toggle">
                <input type="checkbox" class="active-toggle" name="ciam_authentication_settings[remember_me]" value="1" <?php echo ( isset($ciam_setting['remember_me']) && $ciam_setting['remember_me'] == '1' ) ? 'checked' : ''; ?> />
                <span class="active-toggle-name">
                <?php _e('Enable Remember Me.', 'ciam-plugin-slug'); ?>
                </span> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, let\'s the user be logged in even after the browser is closed till the time Remember Me token does not expire. Make sure that this option is also enabled from the LoginRadius.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
            </div>
            <div class="ciam-ur-shortcodes loginoptions">
              <label class="active-toggle">
                <input id="captcha" type="checkbox" class="active-toggle" name="ciam_authentication_settings[captcha]" value="1" <?php echo ( isset($ciam_setting['captcha']) && $ciam_setting['captcha'] == '1' ) ? 'checked' : ''; ?> />
                <span class="active-toggle-name">
                <?php _e('Enable google recaptcha', 'ciam-plugin-slug'); ?>
                </span> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature enables Google recaptcha on registration form. Make sure the same recaptcha settings are set in the LoginRadius dashboard.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
              
              <!-- select captcha type -->
              
              <div class="ciam-row" id="showcaptcha">
                <label class="showcaptcha custom-label">
                  <?php

                                                _e('Select google recaptcha type', 'ciam-plugin-slug');

                                                if (isset($ciam_setting['captchatype']) && !empty($ciam_setting['captchatype'])) {

                                                    $type = $ciam_setting['captchatype'];

                                                } else {

                                                    $type = "";

                                                }

                                                ?>
                    <span class="ciam-tooltip" data-title="<?php _e('This feature allows to select type of the Google recaptcha to be used on the registration form.', 'ciam-plugin-slug'); ?>"> 
                      <span class="dashicons dashicons-editor-help"></span>
                  </span>
                </label>
                <select name="ciam_authentication_settings[captchatype]" id="captchatype" class="showcaptcha">
                  <option value="">Select</option>
                  <option value="v2Recaptcha" <?php if ($type === "v2Recaptcha") { ?> selected ="selected" <?php } ?>>V2 Recaptcha</option>
                  <option value="invisibleRecaptcha" <?php if ($type === "invisibleRecaptcha") { ?> selected ="selected" <?php } ?>>Invisible Recaptcha</option>
                </select>
                <label class="custom-label"> <?php _e('Enter google recaptcha public key', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip" data-title="<?php _e('Enter the Google recaptcha key fo the functionality to work. Make sure that the same key is added in the LoginRadius dashboard.', 'ciam-plugin-slug'); ?>"> 
                      <span class="dashicons dashicons-editor-help"></span>
                  </span></label>
                <div class="hidekeytxtbox">
                  <input type="text" name="ciam_authentication_settings[recaptchasitekey]" value="<?php echo (isset($ciam_setting['recaptchasitekey']) && !empty($ciam_setting['recaptchasitekey'])) ? $ciam_setting['recaptchasitekey'] : '' ?>" placeholder="Enter google recaptcha public key" id="recaptchasitekey" />
                </div>
                <p id="captchameassage"></p>
              </div>
            </div>
            <div class="ciam-ur-shortcodes loginoptions">
              <label class="active-toggle">
                <input type="checkbox" class="active-toggle" name="ciam_authentication_settings[password-stength]" value="1" <?php echo ( isset($ciam_setting['password-stength']) && $ciam_setting['password-stength'] == '1' ) ? 'checked' : ''; ?> id="password-setting" />
                <span class="active-toggle-name">
                <?php _e('Enable password strength', 'ciam-plugin-slug'); ?>
                </span> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, shows the strength bar under the password field on registration form, reset password form and change password form.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
            </div>
            <div>
              <label class="active-toggle">
                <input type="checkbox" class="active-toggle" name="ciam_authentication_settings[disable_minified_version]" value="1" <?php echo ( isset($ciam_setting['disable_minified_version']) && $ciam_setting['disable_minified_version'] == '1' ) ? 'checked' : ''; ?> />
                <span class="active-toggle-name">
                <?php _e('Enable minified version of JS/CSS file?', 'ciam-plugin-slug'); ?>
                </span> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature enables minified version of js/css file.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
            </div>
            <div>
              <p class="margin-0">&nbsp;</p>
              <h3>
                <?php _e('Set password limit', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature allows maximum and minimu length of the password to be set.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h3>
              <div class="ciam-row custom-left">
                <div class="ciam-row-6 first"> <span class="ciam_property_title">
                  <?php _e('Min limit', 'ciam-plugin-slug'); ?>
                  </span>
                  <input type="text" class="ciam-row-field" name="ciam_authentication_settings[pass-min-length]" value="<?php echo ( isset($ciam_setting['pass-min-length']) && !empty($ciam_setting['pass-min-length']) ) ? $ciam_setting['pass-min-length'] : ''; ?>" />
                </div>
                <div class="ciam-row-6 last"> <span class="ciam_property_title">
                  <?php _e('Max limit', 'ciam-plugin-slug'); ?>
                  </span>
                  <input type="text" class="ciam-row-field" name="ciam_authentication_settings[pass-max-length]" value="<?php echo ( isset($ciam_setting['pass-max-length']) && !empty($ciam_setting['pass-max-length']) ) ? $ciam_setting['pass-max-length'] : ''; ?>" />
                </div>
              </div>
            </div>
            <div class="ciam-ur-shortcodes loginoptions Notification-timeout-settings-field">
              <p class="margin-0">&nbsp;</p>
              <h3 class="ciam_property_title">
                <?php _e('Notification timeout settings', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip " id="autohidetime-temp" data-title="<?php _e('Enter the duration (in seconds) to hide response message.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h3>
              <label>
                <input placeholder="Time In Seconds" type="number" class="ciam-row-field custom-tooltip" name="ciam_authentication_settings[autohidetime]" id="autohidetime" value="<?php echo (isset($ciam_setting['autohidetime']) && !empty($ciam_setting['autohidetime'])) ? $ciam_setting['autohidetime'] : '' ?>" />
              </label>
            </div>
            <div> <br>
              <h3>
                <?php _e('Terms and condition', 'ciam-plugin-slug'); ?>
                <span class="active-tooltip" data-title="<?php _e('Enter the content which needs to be displayed on the registration form.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h3>
              <label>
                <textarea rows="4" cols="50" name="ciam_authentication_settings[terms_conditions]" id="terms_conditions"><?php echo (isset($ciam_setting['terms_conditions']) && !empty($ciam_setting['terms_conditions']) ? $ciam_setting['terms_conditions'] : ''); ?></textarea>
              </label>
            </div>
            <div>
              <h3>
                <?php _e('Enter custom ciam options for LoginRadius interface.', 'ciam-plugin-slug'); ?>
                <span class="active-tooltip" data-title="<?php _e('This feature allows custom CIAM options to be enabled on the LoginRadius interface.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h3>
              <label>
              <textarea rows="4" cols="50" name="ciam_authentication_settings[custom_field_obj]" id="custom_field_obj"><?php echo (isset($ciam_setting['custom_field_obj']) && !empty($ciam_setting['custom_field_obj']) ? $ciam_setting['custom_field_obj'] : ''); ?></textarea>
              <p><b>
                <?php _e('Custom customer registration options that are added in the LoginRadius js. ', 'ciam-plugin-slug'); ?>
                </b></p>
              </label>
            </div>
          </div>
        </div>
        <div class="ciam_options_container">
          <div class="ciam-row ciam-ur-shortcodes">
            <h3>
              <?php _e('Debug log', 'ciam-plugin-slug'); ?>
            </h3>
            <label class="active-toggle">
              <input type="checkbox" class="active-toggle" name="ciam_authentication_settings[debug_enable]" value="1" <?php echo ( isset($ciam_setting['debug_enable']) && $ciam_setting['debug_enable'] == '1' ) ? 'checked' : ''; ?> />
              <span class="active-toggle-name">
              <?php _e('Enable log ?', 'ciam-plugin-slug'); ?>
              </span> <span class="ciam-tooltip tip-top" data-title="<?php _e('This feature when enabled, automatically generates debug logs.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </label>
          </div>
        </div>
      </div>
      <?php do_action("ciam_auth_tab_page"); ?>
      <div id="ciam_options_tab-9" class="ciam-tab-frame"> 
        
        <!-- Authentication Flow Type -->
        
        <div class="ciam_options_container" id="ciam-shortcodes">
          <div class="ciam-row ciam-ur-shortcodes">
            <h3>
              <?php _e('User registration short codes', 'ciam-plugin-slug'); ?>
            </h3>
            <div class="ciam_shortcode_div">
              <h4>
                <?php _e('Login form', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip tip-top" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the login form', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h4>
              <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly>[ciam_login_form]</textarea>
            </div>
            <div class="ciam_shortcode_div">
              <h4>
                <?php _e('Registration form', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip tip-top" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the registration form', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h4>
              <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly>[ciam_registration_form]</textarea>
            </div>
            <div class="ciam_shortcode_div">
              <h4>
                <?php _e('Forgot password form', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip tip-top" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the forgot password form', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h4>
              <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly>[ciam_forgot_form]</textarea>
            </div>
            <div class="ciam_shortcode_div">
              <h4>
                <?php _e('Reset password form', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip tip-top" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display reset password form', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h4>
              <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly>[ciam_password_form]</textarea>
            </div>
            <div class="ciam_shortcode_div">
              <h4>
                <?php _e('Default WordPress login form', 'ciam-plugin-slug'); ?>
                <span class="ciam-tooltip tip-top" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the default Wordpress login form. This can be used while configuring your site.It is independent from LR forms.', 'ciam-plugin-slug'); ?>"> <span class="dashicons dashicons-editor-help"></span> </span> </h4>
              <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly>[ciam_wp_default_login]</textarea>
            </div>
          </div>
        </div>
      </div>
      <div style="position: relative;">
        <div class="ciam-option-disabled-hr" style="display: none;"></div>
      </div>
      <p class="submit" id="savebtn">
        <?php submit_button('Save settings', 'primary', 'submit', false); ?>
      </p>
    </form>
  </div>
</div>
<?php

            /* action for debug mode */

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");

        }



    }



    new ciam_authentication_settings();

}
