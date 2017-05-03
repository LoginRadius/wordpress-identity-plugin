<?php

class Ajax_Social_Invite_Helper {

    public function __construct() {
        add_action('wp_ajax_social_invite_get_contacts', array($this, 'get_contacts'), 1);
        add_action('wp_ajax_social_invite_search_contacts', array($this, 'search_contacts'), 1);
        add_action('wp_ajax_social_invite_is_token_valid', array($this, 'is_token_valid'), 1);
        add_action('wp_ajax_social_invite_get_provider_token', array($this, 'get_provider_token'), 1);
        add_action('wp_ajax_social_invite_update_provider_token', array($this, 'update_provider_token'), 1);
        add_action('wp_ajax_social_invite_send_message', array($this, 'send_message'), 1);
    }

    public function get_provider_token() {
        global $wpdb;

        $provider = isset($_POST['provider']) ? $_POST['provider'] : '';

        if (!empty($provider)) {
            // If the user is logged in
            $user = wp_get_current_user();
            $results = $wpdb->get_results(
                    $wpdb->prepare("SELECT * FROM " . $wpdb->base_prefix . "lr_social_invite_tokens WHERE user_id = %d AND provider = %s", $user->ID, $_POST['provider'])
            );

            echo json_encode($results);
        }

        die();
    }

    public function update_provider_token() {
        global $wpdb;

        // If the user is logged in
        $user = wp_get_current_user();

        $provider = isset($_POST['provider']) ? $_POST['provider'] : '';
        $token = isset($_POST['token']) ? $_POST['token'] : '';

        if (!empty($provider) && !empty($token)) {

            $date = new DateTime();
            $datestring = $date->format('Y-m-d H:i:s');

            $data = array();

            $data['user_id'] = $user->ID;
            $data['provider'] = $provider;

            $delete_response = $wpdb->delete($wpdb->base_prefix . 'lr_social_invite_tokens', $data);

            $data['token'] = $token;
            $data['creationdatetime'] = $datestring;
            $insert_response = $wpdb->insert($wpdb->base_prefix . 'lr_social_invite_tokens', $data);

            echo json_encode($array = array("deleteResponse" => $delete_response, "insertResponse" => $insert_response));
        }
        die();
    }

    public function is_token_valid() {

        $token = isset($_POST['token']) ? $_POST['token'] : '';

        if (!empty($token)) {
            global $socialLoginObject;
            $Response = $socialLoginObject->exchangeAccessToken($token);
            if (isset($Response->access_token) && $Response->access_token != '') {
                echo json_encode($Response->access_token);
            } else {
                echo json_encode("false");
            }
        }

        die();
    }

    public function get_contacts() {
        global $wpdb, $socialLoginObject;

        $accessToken = $_POST['token'];

        $cursor = '0';

        $contacts = array();

        try {
            $contacts = $socialLoginObject->getContacts($accessToken, $cursor);
        } catch (\LoginRadiusSDK\LoginRadiusException $e) {
            $contacts = null;
            error_log($e->errorResponse->message);
            die(json_encode(array('error' => $e->errorResponse->message)));
        }

        // If the user is logged in
        $user = wp_get_current_user();

        if (!$user->exists() || !isset($contacts)) {
            die(json_encode(array('error' => 'WordPress user is not logged in or no contacts were received')));
        }

        if (!empty($_POST['provider']) && !empty($_POST['token'])) {

            $date = new DateTime();
            $datestring = $date->format('Y-m-d H:i:s');

            $data = array();
            $data['user_id'] = $user->ID;
            $data['provider'] = $_POST['provider'];
            $wpdb->delete($wpdb->base_prefix . 'lr_social_invite_contacts', $data);
            $wpdb->delete($wpdb->base_prefix . 'lr_social_invite_tokens', $data);

            $data['token'] = $_POST['token'];
            $data['creationdatetime'] = $datestring;
            $wpdb->insert($wpdb->base_prefix . 'lr_social_invite_tokens', $data);


            for ($i = 0; $i < count($contacts->Data); $i++) {

                // create array to insert data

                $data = array();
                $data['user_id'] = $user->ID;
                $data['provider'] = $_POST['provider'];
                $data['name'] = $contacts->Data[$i]->Name;
                $data['email'] = $contacts->Data[$i]->EmailID;
                $data['phone_number'] = $contacts->Data[$i]->PhoneNumber;
                $data['social_id'] = $contacts->Data[$i]->ID;
                $data['profile_url'] = $contacts->Data[$i]->ProfileUrl;
                $data['image_url'] = $contacts->Data[$i]->ImageUrl;
                $data['status'] = $contacts->Data[$i]->Status;
                $data['industry'] = $contacts->Data[$i]->Industry;
                $data['country'] = $contacts->Data[$i]->Country;
                $data['location'] = $contacts->Data[$i]->Location;
                $data['gender'] = $contacts->Data[$i]->Gender;


                if ($data['name'] == "") {
                    $emailname = explode('@', $data['email']);
                    $data['name'] = $emailname[0];
                }
                $data['name'] = ucfirst($data['name']);

                // Set date.
                $date = $contacts->Data[$i]->DateOfBirth;
                if ($date != NULL) {
                    $time = strtotime($date);
                    $data['dob'] = date('Y-m-d', $time);
                }

                if ($data['name'] != 'Private private') {
                    $wpdb->insert($wpdb->base_prefix . 'lr_social_invite_contacts', $data);
                }
            }
        }

        die();
    }

    public function search_contacts() {
        global $wpdb, $lr_social_invite_settings;

        $search = $_POST['search'];
        $search_query = "";

        if ($lr_social_invite_settings['sort_direction'] == 'asc') {
            $sort = "ORDER BY " . $lr_social_invite_settings['sort_by'] . " ASC";
        } else {
            $sort = "ORDER BY " . $lr_social_invite_settings['sort_by'] . " DESC";
        }


        // If the user is logged in
        $user = wp_get_current_user();

        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->base_prefix . "lr_social_invite_contacts WHERE user_id = %d " . $sort, $user->ID);

        if (!empty($search)) {
            $search_query = "%" . $search . "%";
            $query = $wpdb->prepare("SELECT * FROM " . $wpdb->base_prefix . "lr_social_invite_contacts WHERE user_id = %d AND name LIKE %s " . $sort, $user->ID, $search_query);
        }

        $results = $wpdb->get_results($query);

        echo json_encode($results);

        die();
    }

    public function send_message() {
        global $socialLoginObject, $lr_social_invite_settings;

        // If the user is logged in
        $user = wp_get_current_user();

        $accessToken = $_POST['token'];
        $provider = $_POST['provider'];
        $subject = $_POST['subject'];
        $contacts = $_POST['contacts'];
        $message = $_POST['message'];
        $output = array();

        if (!empty($accessToken) && !empty($provider)) {

            if ($provider == "twitter" || $provider == "linkedin") {
                for ($i = 0; $i < count($contacts); $i++) {

                    $to = $contacts[$i]['Id'];
                    $to_name = $contacts[$i]['Name'];

                    if ($provider == "twitter") {
                        $message = substr($message, 0, 140);
                    }

                    try {
                        $response = $socialLoginObject->sendMessage($accessToken, $to, $subject, $message);
                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                        $response = null;
                    }

                    if (isset($response->isPosted) && $response->isPosted != '') {
                        $isPosted = true;
                    } else {
                        $isPosted = false;
                    }
                    $output[] = array(
                        "isPosted" => $isPosted,
                        "name" => $to_name,
                        "response" => $response
                    );
                }
            } else {
                for ($i = 0; $i < count($contacts); $i++) {
                    $to = $contacts[$i]['Id'];
                    $to_name = $contacts[$i]['Name'];

                    $from_name = $user->display_name;
                    $from_email = $user->user_email;

                    if (isset($lr_social_invite_settings['enable_custom_email']) && $lr_social_invite_settings['enable_custom_email'] == '1') {

                        $from_name = $lr_social_invite_settings['email_name'];
                        $from_email = $lr_social_invite_settings['email_address'];
                        // Send Mail with Google/Yahoo.
                        $headers = 'From: ' . $from_name . ' <' . $from_email . '>' . "\r\n" .
                                'Reply-To: ' . $from_email . "\r\n" .
                                'X-Mailer: PHP/' . phpversion();
                        $response = wp_mail($to, $subject, $message, $headers);
                        $output[] = array(
                            "isPosted" => $response,
                            "name" => $to_name
                        );
                    } else {
                        $output[] = array(
                            "isPosted" => $response,
                            "name" => $to_name
                        );
                    }
                }
            }
        }

        die(json_encode($output));
    }

}

new Ajax_Social_Invite_Helper();

