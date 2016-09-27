<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

function get_data_for_mailchimp( $user_id ) {
    // Mailchimp integration in user registration when module included.
    global $wpdb, $lr_mailchimp_settings, $loginRadiusMailchimp;
    
    if ( isset( $lr_mailchimp_settings['mailchimp_subscribe']) && $lr_mailchimp_settings['mailchimp_subscribe'] == '1' && isset($lr_mailchimp_settings['mailchimp_apikey']) && $lr_mailchimp_settings['mailchimp_apikey'] != '' && isset($lr_mailchimp_settings['mailchimp_lists']) && $lr_mailchimp_settings['mailchimp_lists'] != '') {
        $tempMergeVars = LR_Mailchimp_Ajax_Helper::login_radius_get_mailchimp_merger_vars(trim($lr_mailchimp_settings['mailchimp_apikey']), trim($lr_mailchimp_settings['mailchimp_lists']));
        if(!is_array($tempMergeVars)){
            return;
        }
        $merge_vars = array();

        // fetch user info
        $userInfo = get_userdata( $user_id );

        $username = isset($username) && ! empty($username) ? $username : $userInfo->user_login;
        $firstName = isset($firstName) && ! empty($firstName) ? $firstName : $userInfo->first_name;
        $lastName = isset($lastName) && ! empty($lastName) ? $lastName : $userInfo->last_name;
        $email = isset($email) && ! empty($email) ? $email : $userInfo->user_email;
        $profileUrl = isset($profileUrl) && ! empty($profileUrl) ? $profileUrl : $userInfo->user_url;
        $bio = isset($bio) && !empty($bio) ? $bio : '';

        foreach ($tempMergeVars as $tempMergeVar) {
            // if value exists for this merge var
            if (isset($lr_mailchimp_settings['mailchimp_merge_var_' . $tempMergeVar['tag']])) {
                $tempParts = explode('|', $lr_mailchimp_settings['mailchimp_merge_var_' . $tempMergeVar['tag']]);
                $value = '';

                // if field is from any separate profile data table
                if (count($tempParts) > 1) {
                    // execute query according to the prefix
                    switch ($tempParts[0]) {
                        // basic_profile_data table
                        case 'basic':
                            $value = $wpdb->get_var('SELECT ' . $tempParts[1] . ' FROM ' . $wpdb->base_prefix . 'lr_basic_profile_data WHERE wp_users_id = ' . $user_id);
                            break;
                        // extended_location_data table
                        case 'exloc':
                            $value = $wpdb->get_var('SELECT ' . $tempParts[1] . ' FROM ' . $wpdb->base_prefix . 'lr_extended_location_data WHERE wp_users_id = ' . $user_id);
                            break;
                        // extended_profile_data table
                        case 'exprofile':
                            $value = $wpdb->get_var('SELECT ' . $tempParts[1] . ' FROM ' . $wpdb->base_prefix . 'lr_extended_profile_data WHERE wp_users_id = ' . $user_id);
                            break;
                    }
                } else /* native wordpress profile fields */ {
                    include_once( ABSPATH . 'wp-includes/pluggable.php');

                    // Get data according to the value.
                    switch ($tempParts[0]) {
                        case 'User ID':
                            $value = $user_id;
                            break;
                        case 'Username':
                            $value = $username;
                            break;
                        case 'First Name':
                        case 'Display Name':
                            $value = $firstName;
                            break;
                        case 'Last Name':
                            $value = $lastName;
                            break;
                        case 'Nicename':
                            $value = sanitize_title($firstName);
                            break;
                        case 'Email':
                            $value = $email;
                            break;
                        case 'Profile Url':
                            $value = $profileUrl;
                            break;
                        case 'Registration Date':
                            $value = $userInfo->data->user_registered;
                            break;
                        case 'Bio':
                            $value = $bio;
                            break;
                    }
                }
            } else /* value for this merge var does not exist in database */ {
                $value = '';
            }
            $merge_vars[$tempMergeVar['tag']] = $value;
        }

        // Double Optin param sends email to confirm add to mailchimp list
        // Sets to false if admin option is turned on
       // $double_optin = false;
        
        //if(  isset($lr_mailchimp_settings['enable_email_confirm']) && $lr_mailchimp_settings['enable_email_confirm'] == '1'  ) {
            $double_optin = true;
            $loginRadiusMailchimp->listSubscribe( trim( $lr_mailchimp_settings['mailchimp_lists'] ), $email, $merge_vars, 'html', $double_optin );
        //}

        
        
    }
// Mailchimp integration ends.
}

add_action( 'lr_create_social_profile_data', 'get_data_for_mailchimp' );
