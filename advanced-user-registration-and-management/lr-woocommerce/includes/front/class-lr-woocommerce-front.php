<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The front function class of LoginRadius Woocommerce.
 */
if (!class_exists('LR_Woocommerce_Front')) {

    class LR_Woocommerce_Front {

        function __construct() {
            global $lr_woocommerce_settings;
            
            if (isset($lr_woocommerce_settings['woocommerce_enable']) && $lr_woocommerce_settings['woocommerce_enable'] == '1') {
                add_action('woocommerce_before_customer_login_form', array($this, 'wc_myaccount_url'));
                add_action('woocommerce_thankyou', array($this, 'lr_wc_order_status_completed'), 10, 1);
                add_action('woocommerce_save_account_details', array($this, 'lr_wc_update_user_profile'));
                add_action('woocommerce_created_customer', array($this, 'lr_wc_create_user_on_checkout'),10,3);
                add_filter('wc_get_template',array($this,'lr_wc_user_login_checkout'),10,5);
                add_action('woocommerce_edit_account_form',array($this,'lr_wc_edit_account_form'));
            }
        }
        function lr_wc_edit_account_form(){
            ?>
            <style>
                #main .woocommerce form fieldset{
                display: none !important;
            }
            </style>
            <?php
        }
        /**
         * login user on checkout page
         * 
         * @param type $located
         * @param type $template_name
         * @param type $args
         * @param type $template_path
         * @param type $default_path
         * @return type
         */
        function lr_wc_user_login_checkout($located, $template_name, $args, $template_path, $default_path){
            if($template_name == 'global/form-login.php'){
                return LR_WOOCOMMERCE_DIR.'includes/template/login.php';
            }
            return $located;
        }
        /**
         * register user on raas from woocommerce checkout page
         * 
         * @param type $user_id
         */
        function lr_wc_create_user_on_checkout($user_id, $new_customer_data, $password_generated) {
            extract($_POST);
            $countries = WC()->countries->get_countries();
            $country_code = isset($_POST['billing_country'])?$_POST['billing_country']:'';
            $shipping_country_name = isset($countries[$country_code])?$countries[$country_code]:'';
            $state_code = isset($_POST['billing_state']) ? $_POST['billing_state'] : '';
            $all_states = WC()->countries->get_states($country_code);
            $params = array(
                'EmailId' => isset($_POST['billing_email']) ? $_POST['billing_email'] : '',
                'firstname' => isset($_POST['billing_first_name']) ? $_POST['billing_first_name'] : '',
                'lastname' => isset($_POST['billing_last_name']) ? $_POST['billing_last_name'] : '',
                'company' => isset($_POST['billing_company']) ? $_POST['billing_company'] : '',
                'address1' => isset($_POST['billing_address_1']) ? $_POST['billing_address_1'] : '',
                'address2' => isset($_POST['billing_address_2']) ? $_POST['billing_address_2'] : '',
                'city' => isset($_POST['billing_city']) ? $_POST['billing_city'] : '',
                'state' => isset($all_states[$state_code])?$all_states[$state_code]:'',
                'postalcode' => isset($_POST['billing_postcode']) ? $_POST['billing_postcode'] : '',
                'country' => html_entity_decode($shipping_country_name),
                'phonenumber' => isset($_POST['billing_phone']) ? $_POST['billing_phone'] : '',
                'password' => isset($_POST['account_password']) ? $_POST['account_password'] : $new_customer_data['user_pass']
            );
            $response = json_decode(raas_create_user($params));
            if (isset($response->Uid) && !empty($response->Uid)) {
                update_user_meta($user_id, 'lr_city', $params['city']);
                update_user_meta($user_id, 'lr_state', $params['state']);
                update_user_meta($user_id, 'lr_country', $params['country']);
                update_user_meta($user_id, 'lr_phone', $params['phonenumber']);
                update_user_meta($user_id, 'lr_raas_accountid', $response->ID);
                update_user_meta($user_id, 'lr_raas_uid', $response->Uid);                   

            }
        }
        /**
         * update user profile from woocommerce profile page
         * 
         * @param type $user_id
         */
        function lr_wc_update_user_profile($user_id) {
            $params['firstname'] = !empty($_POST['account_first_name']) ? wc_clean($_POST['account_first_name']) : '';
            $params['lastname'] = !empty($_POST['account_last_name']) ? wc_clean($_POST['account_last_name']) : '';
            $params['EmailId'] = !empty($_POST['account_email']) ? $_POST['account_email'] : '';
            $accountId = get_user_meta($user_id, 'lr_raas_accountid', true);
            if (!empty($accountId)) {
                raas_update_user($params, $accountId);
                if (isset($_POST['password_1']) && !empty($_POST['password_1'])) {
                    $uid = get_user_meta($user_id, 'lr_raas_uid', true);
                    $data['newpassword'] = $_POST['password_1'];
                    $data = array('accountid' => $uid, 'emailid' => $params['EmailId'], 'password' => $data['newpassword']);
                    raas_set_password(http_build_query($data));
                }
            }
        }

        /**
         * update custom fields on checkoutpage
         * 
         * @param type $order_id
         */
        function lr_wc_order_status_completed($order_id) {
            global $lr_woocommerce_settings;
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                $accountId = get_user_meta($user_id, 'lr_raas_accountid', true);
                if (!empty($accountId)) {            
                    if (isset($lr_woocommerce_settings['woocommerce_update_checkout']) && $lr_woocommerce_settings['woocommerce_update_checkout'] == '1') {
                        $countries = WC()->countries->get_countries();
                        $country_code = get_post_meta($order_id, '_billing_country', true);
                        $shipping_country_name = isset($countries[$country_code])?$countries[$country_code]:'';
                        $state_code = get_post_meta($order_id, '_billing_state', true);
                        $all_states = WC()->countries->get_states($country_code);
                        $params = array(
                            'firstname' => get_post_meta($order_id, '_billing_first_name', true),
                            'lastname' => get_post_meta($order_id, '_billing_last_name', true),
                            'company' => get_post_meta($order_id, '_billing_company', true),
                            'address1' => get_post_meta($order_id, '_billing_address_1', true),
                            'address2' => get_post_meta($order_id, '_billing_address_1', true),
                            'city' => get_post_meta($order_id, '_billing_address_2', true),
                            'state' => isset($all_states[$state_code])?$all_states[$state_code]:'',
                            'postalcode' => get_post_meta($order_id, '_billing_postcode', true),
                            'country' => html_entity_decode($shipping_country_name),
                            'phonenumber' => get_post_meta($order_id, '_billing_phone', true)
                        );
                    }
                    $params['CustomFields'] = array(
                        'date_of_payment' => get_the_date(),
                        'payment_gateway' => get_post_meta($order_id, '_payment_method_title', true)
                    );
                    raas_update_user($params, $accountId);
                }
            }
        }

        /**
         * myaccount page redirect on raas login page id user not loggedin
         * 
         * @param type $endpoint
         * @param type $value
         * @param type $permalink
         */
        function wc_myaccount_url() {
            if (!is_user_logged_in()) {
                wp_redirect(LR_Raas_Social_Login::lr_login_url());
                exit();
            }
        }

    }

    new LR_Woocommerce_Front();
}
