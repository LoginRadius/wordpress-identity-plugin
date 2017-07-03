<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Authentication_Helper')) {

    class CIAM_Authentication_Helper {

        public function __construct() {
            global $ciam_credencials;

            if (!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])) {
                return;
            }
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Utility/Functions.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/LoginRadiusException.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Clients/IHttpClient.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Clients/DefaultHttpClient.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Utility/SOTT.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Social/ProvidersAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Social/SocialLoginAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Social/AdvanceSocialLoginAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Authentication/AuthCustomObjectAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Authentication/UserAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/AccountAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/RoleAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/CustomObjectAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/CustomerRegistration/Management/SchemaAPI.php');
            require_once ( CIAM_PLUGIN_DIR . 'authentication/lib/LoginRadiusSDK/Advance/RestHooksAPI.php');
            add_filter('get_avatar', array($this, 'get_user_avatar'), 10, 5);

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        public function get_user_avatar($avatar, $user_id, $size, $default, $alt) {
            if (!empty($user_id)) {
                if (function_exists('get_avatar_url')) {
                    $defaultAvatar = get_avatar_url($user_id);
                } else {
                    $defaultAvatar = "";
                }
                if (!empty($user_id) && !is_super_admin()) {
                    $getProfileImageUrl = get_user_meta($user_id, 'user_avatar_image', true);
                    if (!empty($getProfileImageUrl)) {

                        $img = '<img alt="' . esc_attr($alt) . '" src="' . $getProfileImageUrl . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '" />';
                        /* action for debug mode */
                        do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $img);

                        return '<img alt="' . esc_attr($alt) . '" src="' . $getProfileImageUrl . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '" />';
                    } else if (empty($getProfileImageUrl)) {
                        $img = '<img alt="' . esc_attr($alt) . '" src="' . $defaultAvatar . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '"/>';
                        /* action for debug mode */
                        do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $img);

                        return '<img alt="' . esc_attr($alt) . '" src="' . $defaultAvatar . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '"/>';
                    }
                } else {
                    $img = '<img alt="' . esc_attr($alt) . '" src="' . $defaultAvatar . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '" />';
                    /* action for debug mode */
                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $img);

                    return '<img alt="' . esc_attr($alt) . '" src="' . $defaultAvatar . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '" />';
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
            return;
        }

        public static function set_cookies($userId = 0, $remember = true) {
            wp_clear_auth_cookie();
            wp_set_auth_cookie($userId, $remember);
            wp_set_current_user($userId);
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), true);
            return true;
        }

        public static function ciam_error_msg() {
            global $ciam_message;
            $output = '<style>.hostedservicemessages {position: fixed;top: 0;text-align: center;background: #29f;width: 100%;z-index: 9999;padding: 15px;left: 0;color: #fff;}</style>';
            $output .= "<script>";
            $output .= "setTimeout(function(){jQuery('.hostedservicemessages').hide();},10000);";
            $output .= "jQuery(document).ready(function(){";
            $output .= "if (window.location.href.indexOf('?') > -1) {";
            $output .= "history.pushState('', document.title, window.location.pathname);";
            $output .= " }";
            $output .= "});";
            $output .= "</script>";
            $output .= '<div class="hostedservicemessages">' . $ciam_message . '</div>';
            do_action('ciam_sso_logout');

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), $output);
            return $output;
        }

        /**
         * Adding index to username if username already exists in WordPress
         */
        public function create_another_username_if_exists($name) {
            $isUserNameExists = true;
            $index = 0;
            $userName = $name;
            while ($isUserNameExists == true) {
                if (username_exists($userName) != 0) {
                    $index++;
                    $userName = $name . $index;
                } else {
                    $isUserNameExists = false;
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $userName);
            return $userName;
        }

        public function register($email, $userProfileData) {
            $usernameFirstnameLastname = explode('|LR|', $this->create_user_name($userProfileData));
            
            $userName = isset($usernameFirstnameLastname[0]) && !empty($usernameFirstnameLastname[0]) ? trim($usernameFirstnameLastname[0]) : '';
            $firstName = isset($usernameFirstnameLastname[1]) && !empty($usernameFirstnameLastname[1]) ? trim($usernameFirstnameLastname[1]) : '';
            $lastName = isset($usernameFirstnameLastname[2]) && !empty($usernameFirstnameLastname[2]) ? trim($usernameFirstnameLastname[2]) : '';
            $profileImageUrl = isset($userProfileData->Identities[0]->ImageUrl) && !empty($userProfileData->Identities[0]->ImageUrl) ? trim($userProfileData->Identities[0]->ImageUrl) : ' ';

            $output = array(
                'user_login' => $userName,
                'user_pass' => wp_generate_password(12, true),
                'user_nicename' => $firstName,
                'user_email' => $email,
                'display_name' => $firstName,
                'nickname' => $firstName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_url' => $profileImageUrl,
                'role' => get_option('default_role')
            );
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $output);

            return array(
                'user_login' => $userName,
                'user_pass' => wp_generate_password(12, true),
                'user_nicename' => $firstName,
                'user_email' => $email,
                'display_name' => $firstName,
                'nickname' => $firstName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_url' => $profileImageUrl,
                'role' => get_option('default_role')
            );
        }

        /**
         * Create username, firstname and lastname with profile fetched from Social Networks
         */
        public function create_user_name($profileData) {
            $username = $firstName = $lastName = '';
            if (!empty($profileData->FirstName) && !empty($profileData->LastName)) {
                $username = $profileData->FirstName . ' ' . $profileData->LastName;
                $firstName = $profileData->FirstName;
                $lastName = $profileData->LastName;
            } elseif (!empty($profileData->FullName)) {
                $username = $profileData->FullName;
                $firstName = $profileData->FullName;
            } elseif (!empty($profileData->ProfileName)) {
                $username = $profileData->ProfileName;
                $firstName = $profileData->ProfileName;
            } elseif (!empty($profileData->NickName)) {
                $username = $profileData->NickName;
                $firstName = $profileData->NickName;
            } elseif (isset($profileData->Email[0]->Value) && !empty($profileData->Email[0]->Value)) {
                $user_name = explode('@', $profileData->Email[0]->Value);
                $username = $user_name[0];
                $firstName = str_replace('_', ' ', $user_name[0]);
            } else {
                $username = $profileData->ID;
                $firstName = $profileData->ID;
            }

            $output = $username . '|LR|' . $firstName . '|LR|' . $lastName;
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $output);

            return $username . '|LR|' . $firstName . '|LR|' . $lastName;
        }

        public function get_protocol() {
            if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
                /* action for debug mode */
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), 'https://');
                return 'https://';
            } else {
                /* action for debug mode */
                do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), 'http://');
                return 'http://';
            }
        }

        public function linking($user_id, $userProfileData, $isUpdate = false) {
            $profileImageUrl = isset($userProfileData->Identities[0]->ImageUrl) && !empty($userProfileData->Identities[0]->ImageUrl) ? trim($userProfileData->Identities[0]->ImageUrl) : '';
            if ($isUpdate) {
                update_user_meta($user_id, 'user_avatar_image', $profileImageUrl);
                update_user_meta($user_id, 'ciam_id', $userProfileData->ID);
                update_user_meta($user_id, 'ciam_uid', $userProfileData->Uid);
            } else {
                add_user_meta($user_id, 'user_avatar_image', $profileImageUrl);
                add_user_meta($user_id, 'ciam_id', $userProfileData->ID);
                add_user_meta($user_id, 'ciam_uid', $userProfileData->Uid);
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, "credencials", get_class($this), "");
        }

        /**
         * 
         * @param type $user_id
         * @param type $register
         */
        public function allow_login($user_id, $userProfileData, $register = false) {
            // saving access token for normal login case....       
            if (isset($_POST) && !empty($_POST)) {

                add_user_meta($user_id, 'accesstoken', $_POST['token']);
            }
            // saving data for hosted page login case....
            if (isset($_GET['token']) && !empty($_GET['token'])) {

                add_user_meta($user_id, 'accesstoken', $_GET['token']);
            }
            
            
                  // inserting the current social media connected provider to db
           
            if(isset($userProfileData->Identities) && !empty($userProfileData->Identities)){
                
              add_user_meta($user_id, 'ciam_current_account_linked', $userProfileData->Identities[0]->Provider);
                      }
            
            
            // saving lr data to wordpress on login....
            $metas = array(
                'nickname' => $userProfileData->NickName,
                'user_nicename' => $userProfileData->FirstName,
                'display_name' => $userProfileData->FirstName,
                'first_name' => $userProfileData->FirstName,
                'last_name' => $userProfileData->LastName,
                'user_url' => $userProfileData->ImageUrl,
                'role' => get_option('default_role')
            );


            // checking and saving only those values which are not empty.....            
            foreach ($metas as $key => $value) {
                if (!empty($value)) {
                    update_user_meta($user_id, $key, $value);
                }
            }




            $_user = get_user_by('id', $user_id);
            do_action('ciam_profile_data', $user_id, $userProfileData);
            $this->set_cookies($_user->ID);
            do_action('wp_login', $_user->data->user_login, $_user);
            $this->redirect($_user->ID, $register);

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
        }

        /**
         * Get redirection URL based on Social Login settings.
         */
        public function get_redirect_url() {
            global $ciam_setting;

            $loginRedirect = '';
            if (isset($ciam_setting['after_login_redirect']) && $ciam_setting['after_login_redirect'] == "samepage") {
                if (isset($_GET['redirect_to']) && !empty($_GET['redirect_to'])) {
                    $loginRedirect = $_GET['redirect_to'];
                    /* action for debug mode */
                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $loginRedirect);
                    return $loginRedirect;
                }
            }else{
                $loginRedirect = isset($ciam_setting['after_login_redirect']) ? $ciam_setting['after_login_redirect'] : '';
            }


            $redirectionUrl = site_url();


            if (isset($loginRedirect)) {
                switch (strtolower($loginRedirect)) {
                    case 'homepage':
                        $redirectionUrl = site_url() . '/';
                        break;
                    case 'dashboard':
                        $redirectionUrl = admin_url();
                        break;
                    case 'custom':

                        $customRedirectUrlOther = isset($ciam_setting['custom_redirect_other']) ? trim($ciam_setting['custom_redirect_other']) : '';
                        if (isset($loginRedirect) && strlen($customRedirectUrlOther) > 0) {
                            $redirectionUrl = trim($customRedirectUrlOther);
                            if (strpos($redirectionUrl, 'http') === false) {
                                $redirectionUrl = 'http://' . $redirectionUrl;
                            }
                        } else {
                            $redirectionUrl = site_url() . '/';
                        }

                        break;
                    case 'samepage':
                    default:
                        $redirectionUrl = self:: get_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                        $parsed = parse_url($redirectionUrl);
                        $query = isset($parsed['query']) ? $parsed['query'] : '';
                        parse_str($query, $params);
                        if (isset($params['token'])) {
                            unset($params['token']);
                        }
                        $string = http_build_query($params);
                        $redirectionUrl = self:: get_protocol() . $_SERVER['HTTP_HOST'] . $parsed['path'] . $string;
                        break;
                }
            }

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), $redirectionUrl);

            return $redirectionUrl;
        }

        /**
         * Redirect users after login and register according to plugin settings.
         */
        public function redirect($user_id, $register) {
            $redirectionUrl = self::get_redirect_url($user_id, $register);
            wp_redirect($redirectionUrl);
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), "");
            exit();
        }

    }

    new CIAM_Authentication_Helper();
}