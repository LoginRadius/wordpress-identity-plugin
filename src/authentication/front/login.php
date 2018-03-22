<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('CIAM_Authentication_Login')) {

    class CIAM_Authentication_Login {

        /*
         * Class constructor function.
         */
        function __construct() {
           
            add_action('template_redirect', array($this, 'token_handler'), 9, 2);
            add_action('wp_logout', array($this, 'home_redirection'));

        }

        /*
         * Mange site home redirection
         */
        public function home_redirection() {
            $user_id = get_current_user_id();
            delete_user_meta($user_id, 'accesstoken'); // deleting the logged out user access token from db.
            delete_user_meta($user_id, 'ciam_current_user_uid'); // deleting the current user uid.
             wp_redirect( home_url() );

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
            exit();
        }
        
        /*
         * function to generate random email id
         */
        public function random_id_generation($email_name)
        {
           $base_root = site_url();
    $email_name = str_replace(array("+"," "), "", $email_name);
    $email_domain = str_replace(array("http://","https://"), "", $base_root);
    $email = $email_name . '@' . $email_domain.'.com';
    return $email;
        }
        
        /*
         * handle token when user tries to login
         */

        public function token_handler() {
            global $ciam_credencials, $ciam_message;

            $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';

            if (!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])) {
                return;
            }
            if (!empty($token)) {
                $apikey = isset($ciam_credencials['apikey']) ? $ciam_credencials['apikey'] : '';
                $secret = isset($ciam_credencials['secret']) ? $ciam_credencials['secret'] : '';
                if (!empty($apikey) && !empty($secret)) {
                    $userProfileApi = new \LoginRadiusSDK\CustomerRegistration\Authentication\UserAPI($apikey, $secret, array('output_format' => 'json'));
                    try {

                        $accesstoken = $token;
                            try {
                                $userProfileData = $userProfileApi->getProfile($accesstoken);

                                if (isset($userProfileData->Uid) && !empty($userProfileData->Uid)) {//check uid get or not 
                                    $checkUidExists = get_users(array(
                                        "meta_key" => "ciam_uid",
                                        "meta_value" => $userProfileData->Uid,
                                        "fields" => "ID"
                                    ));
                                    $loginHelper = new CIAM_Authentication_Helper();

                                    if (isset($checkUidExists[0]) && !empty($checkUidExists[0])) {//check uid exist or not in usermeta
                                        $loginHelper->linking($checkUidExists[0], $userProfileData, true);
                                        //allow Login
                                        $loginHelper->allow_login($checkUidExists[0], $userProfileData);
                                    } else {
                                        //Uid not exist in so check email
                                        $email = isset($userProfileData->Email[0]->Value) ? $userProfileData->Email[0]->Value : '';

                                        if (!empty($email)) {
                                            if (email_exists($email)) {
                                                //link user in user meta
                                                $user = get_user_by('email', $email);
                                                $loginHelper->linking($user->ID, $userProfileData);
                                                //allow Login
                                                $loginHelper->allow_login($user->ID, $userProfileData);
                                            } else {
                                                /* Register New User */

                                                $user_id = wp_insert_user($loginHelper->register($email, $userProfileData));
                                                
                                              // checking if username is exist than create dynamic username.
                                                if (isset($user_id->errors['existing_user_login'][0]) && $user_id->errors['existing_user_login'][0] == "Sorry, that username already exists!") { 
                                                    $userarr = $loginHelper->register($email, $userProfileData);

                                                    $userarr['user_login'] = $loginHelper->register($email, $userProfileData)['user_login'] . rand(10, 100);
                                                   
                                                    $user_id = wp_insert_user($userarr);
                                                    

                                                }

                                                if (!is_wp_error($user_id)) {
                                                    $loginHelper->linking($user_id, $userProfileData);
                                                    //allow Login
                                                    $loginHelper->allow_login($user_id, $userProfileData, true);
                                                } else {
                                                    do_action('ciam_sso_logout');
                                                    add_action('wp_footer', array('CIAM_Authentication_Helper', 'ciam_error_msg'));
                                                }
                                            }
                                        } else {
                                            $email = $this->random_id_generation($userProfileData->PhoneId);
                                             $user_id = wp_insert_user($loginHelper->register($email, $userProfileData));
                                             if (isset($user_id->errors['existing_user_login'][0]) && $user_id->errors['existing_user_login'][0] == "Sorry, that username already exists!") { 
                                                    $userarr = $loginHelper->register($email, $userProfileData);

                                                    $userarr['user_login'] = $loginHelper->register($email, $userProfileData)['user_login'] . rand(10, 100);
                                                   
                                                    $user_id = wp_insert_user($userarr);
                                                    

                                                }

                                                if (!is_wp_error($user_id)) {
                                                    $loginHelper->linking($user_id, $userProfileData);
                                                    //allow Login
                                                    $loginHelper->allow_login($user_id, $userProfileData, true);
                                                } else {
                                                    do_action('ciam_sso_logout');
                                                    add_action('wp_footer', array('CIAM_Authentication_Helper', 'ciam_error_msg'));
                                                }
                                        }
                                    }
                                }
                            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                                //User Profile not fetch
                                $ciam_message = $e->getMessage();
                                do_action('ciam_sso_logout');
                                add_action('wp_footer', array('CIAM_Authentication_Helper', 'ciam_error_msg'));
                            }
                       
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        //access Token is invalid
                        $ciam_message = $e->getMessage();
                        do_action('ciam_sso_logout');
                        add_action('wp_footer', array('CIAM_Authentication_Helper', 'ciam_error_msg'));
                    }

                    do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
                    return;
                }
            }


            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }

    }

    new CIAM_Authentication_Login();
}