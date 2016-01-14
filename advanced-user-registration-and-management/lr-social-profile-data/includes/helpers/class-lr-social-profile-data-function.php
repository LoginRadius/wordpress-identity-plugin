<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Social_Profile_Data_Function' ) ) {

    class LR_Social_Profile_Data_Function {

        /**
         * Construct the plugin object
         */
        public function __construct() {
            add_action( 'lr_save_profile_data', array( $this, 'save_profile_data' ), 10, 2 );
            add_action( 'lr_update_extented_user_profile', array( $this, 'update_extented_user_profile' ), 10, 3);
        }

        /**
         * Save profile data in database.
         */
        public static function save_profile_data( $userId, $profileData ) {

            global $loginRadiusObject, $wpdb, $loginradius_api_settings, $lr_social_profile_data_settings;

            // Is request token isset
            $loginRadiusSecret = isset( $loginradius_api_settings['LoginRadius_secret'] ) ? $loginradius_api_settings['LoginRadius_secret'] : '';

            $token = ! empty( $profileData['Token'] ) ? $profileData['Token'] : '';

            try {
                $response = $loginRadiusObject->loginradius_exchange_access_token( $loginRadiusSecret, $token );
            } catch ( LoginRadiusException $e ){
                $response = null;
                error_log( $e );
            }

            if ( ! isset( $response->access_token ) ) {
                $response->access_token = ! empty( $_REQUEST['token'] ) ? $_REQUEST['token'] : '';
            }

            if ( ! empty( $response->access_token ) ) {
                try {
                   $userProfileObject = $loginRadiusObject->loginradius_get_user_profiledata( $response->access_token ); 
                } catch ( LoginRadiusException $e ) {
                   $userProfileObject = null;
                   error_log( $e );
                }
                
                if ( isset( $lr_social_profile_data_settings['enable_custom_popup'] ) && $lr_social_profile_data_settings['enable_custom_popup'] == '1') {

                    if ( isset( $lr_social_profile_data_settings['show_email'] ) && $lr_social_profile_data_settings['show_email'] == '1') {
                        $userProfileObject->Email = $profileData['Email'];
                    }

                    if ( isset( $lr_social_profile_data_settings['show_gender'] ) && $lr_social_profile_data_settings['show_gender'] == '1') {
                        $userProfileObject->Gender = $profileData['Gender'];
                    }

                    if ( isset( $lr_social_profile_data_settings['show_birthdate'] ) && $lr_social_profile_data_settings['show_birthdate'] == '1') {
                        $userProfileObject->BirthDate = $profileData['BirthDate'];
                    }

                    if ( isset( $lr_social_profile_data_settings['show_phonenumber'] ) && $lr_social_profile_data_settings['show_phonenumber'] == '1') {
                        $userProfileObject->PhoneNumber = $profileData['PhoneNumber'];
                    }

                    if ( isset( $lr_social_profile_data_settings['show_city'] ) && $lr_social_profile_data_settings['show_city'] == '1') {
                        $userProfileObject->City = $profileData['City'];
                    }

                    if ( isset( $lr_social_profile_data_settings['show_postalcode'] ) && $lr_social_profile_data_settings['show_postalcode'] == '1') {
                        $userProfileObject->PostalCode = $profileData['PostalCode'];
                    }

                    if ( isset( $lr_social_profile_data_settings['show_relationshipstatus'] ) && $lr_social_profile_data_settings['show_relationshipstatus'] == '1') {
                        $userProfileObject->RelationshipStatus = $profileData['RelationshipStatus'];
                    }

                    if ( isset( $lr_social_profile_data_settings['show_custom_one'] ) && $lr_social_profile_data_settings['show_custom_one'] == '1') {
                        $userProfileObject->Field_1 = $profileData['Field_1'];
                        $wpdb->insert($wpdb->base_prefix . 'lr_popup_custom_fields_data', array('wp_users_id' => $userId, 'field_title' => $lr_social_profile_data_settings['custom_one_title'], 'field_value' => $profileData['Field_1']));
                    }

                    if ( isset( $lr_social_profile_data_settings['show_custom_two'] ) && $lr_social_profile_data_settings['show_custom_two'] == '1') {
                        $userProfileObject->Field_2 = $profileData['Field_2'];
                        $wpdb->insert($wpdb->base_prefix . 'lr_popup_custom_fields_data', array('wp_users_id' => $userId, 'field_title' => $lr_social_profile_data_settings['custom_two_title'], 'field_value' => $profileData['Field_2']));
                    }

                    if ( isset( $lr_social_profile_data_settings['show_custom_three'] ) && $lr_social_profile_data_settings['show_custom_three'] == '1') {
                        $userProfileObject->Field_3 = $profileData['Field_3'];
                        $wpdb->insert($wpdb->base_prefix . 'lr_popup_custom_fields_data', array('wp_users_id' => $userId, 'field_title' => $lr_social_profile_data_settings['custom_three_title'], 'field_value' => $profileData['Field_3']));
                    }

                    if ( isset( $lr_social_profile_data_settings['show_custom_four'] ) && $lr_social_profile_data_settings['show_custom_four'] == '1') {
                        $userProfileObject->Field_4 = $profileData['Field_4'];
                        $wpdb->insert($wpdb->base_prefix . 'lr_popup_custom_fields_data', array('wp_users_id' => $userId, 'field_title' => $lr_social_profile_data_settings['custom_four_title'], 'field_value' => $profileData['Field_4']));
                    }

                    if ( isset( $lr_social_profile_data_settings['show_custom_five'] ) && $lr_social_profile_data_settings['show_custom_five'] == '1') {
                        $userProfileObject->Field_5 = $profileData['Field_5'];
                        $wpdb->insert($wpdb->base_prefix . 'lr_popup_custom_fields_data', array('wp_users_id' => $userId, 'field_title' => $lr_social_profile_data_settings['custom_five_title'], 'field_value' => $profileData['Field_5']));
                    }
                }

                $fetchProfileData = self::validate_profiledata( $userProfileObject );
                $profileData = array_merge( $fetchProfileData );
            } else {
                return;
            }

            self::update_extented_user_profile( $userId, $profileData, $response->access_token );
        }

        /**
         * update_extented_user_profile used to save and update Social Profile Data
         * provided by LoginRadius
         * @param  string $userId       WordPress user id
         * @param  array $profileData   User Profile Data
         * @param  string $access_token Token of LoginRadius user
         */
        public static function update_extented_user_profile( $userId, $profileData, $access_token = '' ) {
            global $wpdb, $lr_social_profile_data_settings, $loginRadiusObject;

            if( empty( $access_token ) ) {
                $access_token = isset( $_REQUEST['token'] ) ? $_REQUEST['token'] : '';
            }

            if ( empty( $profileData['ID'] ) || ! isset( $lr_social_profile_data_settings['profiledata'] ) || empty( $access_token ) ) {
                return;
            }
            
            // Insert basic profile data if option is selected 
            if ( in_array( 'basic', $lr_social_profile_data_settings['profiledata'] ) ) {
                $data = array();
                $data['wp_users_id'] = $userId;
                $data['social_id'] = isset($profileData['ID']) ? $profileData['ID'] : '';
                $data['provider'] = isset($profileData['Provider']) ? $profileData['Provider'] : '';
                $data['prefix'] = isset($profileData['Prefix']) ? $profileData['Prefix'] : '';
                $data['first_name'] = isset($profileData['FirstName']) ? $profileData['FirstName'] : '';
                $data['middle_name'] = isset($profileData['MiddleName']) ? $profileData['MiddleName'] : '';
                $data['last_name'] = isset($profileData['LastName']) ? $profileData['LastName'] : '';
                $data['suffix'] = isset($profileData['Suffix']) ? $profileData['Suffix'] : '';
                $data['nick_name'] = isset($profileData['NickName']) ? $profileData['NickName'] : '';
                $data['profile_name'] = isset($profileData['ProfileName']) ? $profileData['ProfileName'] : '';
                $data['profile_url'] = isset($profileData['ProfileUrl']) ? $profileData['ProfileUrl'] : '';
                $data['birth_date'] = ! empty( $profileData['BirthDate'] ) ? $profileData['BirthDate'] : NULL;
                $data['gender'] = isset($profileData['Gender']) && $profileData['Gender'] != '' ? $profileData['Gender'] : 'unknown';
                $data['website'] = isset($profileData['Website']) ? $profileData['Website'] : '';
                $data['thumbnail_image_url'] = isset($profileData['Thumbnail']) ? $profileData['Thumbnail'] : '';
                $data['image_url'] = isset($profileData['ImageUrl']) ? $profileData['ImageUrl'] : '';

                $data = self::check_data($data);
                if ( ! $wpdb->update($wpdb->base_prefix . 'lr_basic_profile_data', $data, array('wp_users_id' => $userId))) {
                    if ( ! $wpdb->get_var($wpdb->prepare('SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_basic_profile_data' . " WHERE wp_users_id = %d", $userId))) {
                        $wpdb->insert($wpdb->base_prefix . 'lr_basic_profile_data', $data);
                    }
                }
                // Emails.
                if (isset($profileData['Emails']) && count($profileData['Emails']) > 0) {
                    foreach ($profileData['Emails'] as $lrEmail) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['email_type'] = $lrEmail->Type;
                        $data['email'] = $lrEmail->Value;
                        $data = self::check_data($data);
                        if ( ! $wpdb->update($wpdb->base_prefix . 'lr_emails', $data, array('wp_users_id' => $userId))) {
                            if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_emails' . " WHERE wp_users_id = %d", $userId))) {
                                $wpdb->insert($wpdb->base_prefix . 'lr_emails', $data);
                            }
                        }
                    }
                }
            }

            // Insert extended location data if option is selected.
            if ( in_array( 'exlocation', $lr_social_profile_data_settings['profiledata'] ) ) {

                $data = array();
                $data['wp_users_id'] = $userId;
                $data['address_line_1'] = isset($profileData['address_line_1']) ? $profileData['address_line_1'] : '';
                $data['address_line_2'] = isset($profileData['address_line_2']) ? $profileData['address_line_2'] : '';
                $data['hometown'] = isset($profileData['HomeTown']) ? $profileData['HomeTown'] : '';
                $data['city'] = isset($profileData['City']) ? $profileData['City'] : '';
                $data['local_city'] = isset($profileData['LocalCity']) ? $profileData['LocalCity'] : '';
                $data['profile_city'] = isset($profileData['ProfileCity']) ? $profileData['ProfileCity'] : '';
                $data['state'] = isset($profileData['State']) ? $profileData['State'] : '';
                $data['postal_code'] = isset($profileData['PostalCode']) ? $profileData['PostalCode'] : '';
                $data['country'] = isset($profileData['Country']) ? $profileData['Country'] : '';
                $data['local_country'] = isset($profileData['LocalCountry']) ? $profileData['LocalCountry'] : '';
                $data['profile_country'] = isset($profileData['ProfileCountry']) ? $profileData['ProfileCountry'] : '';
                $data['region'] = isset($profileData['Region']) ? $profileData['Region'] : '';
                $data['local_language'] = isset($profileData['LocalLanguage']) ? $profileData['LocalLanguage'] : '';
                $data['language'] = isset($profileData['Language']) ? $profileData['Language'] : '';
                $data = self::check_data($data);
                if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_extended_location_data', $data, array('wp_users_id' => $userId))) {
                    if (!$wpdb->get_var($wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_extended_location_data' . " WHERE wp_users_id = %d", $userId))) {
                        $wpdb->insert($wpdb->base_prefix . 'lr_extended_location_data', $data);
                    }
                }
            }

            // Insert extended profile data if option is selected.
            if ( in_array( 'extended', $lr_social_profile_data_settings['profiledata'] ) ) {
                
                $data = array();
                $data['wp_users_id'] = $userId;
                $data['website'] = isset( $profileData['Website'] ) ? $profileData['Website'] : '';
                $data['favicon'] = isset( $profileData['Favicon'] ) ? $profileData['Favicon'] : '';
                $data['industry'] = isset( $profileData['Industry'] ) ? $profileData['Industry'] : '';
                $data['about'] = isset( $profileData['Bio'] ) ? $profileData['Bio'] : '';
                $data['timezone'] = isset( $profileData['TimeZone'] ) ? $profileData['TimeZone'] : '';
                $data['verified'] = isset( $profileData['Verified'] ) ? $profileData['Verified'] : '';
                $data['last_profile_update'] = isset( $profileData['LastProfileUpdate'] ) ? $profileData['LastProfileUpdate'] : NULL;
                $data['created'] = isset( $profileData['Created'] ) ? $profileData['Created'] : '';
                $data['relationship_status'] = isset( $profileData['RelationshipStatus'] ) ? $profileData['RelationshipStatus'] : '';
                $data['quote'] = isset( $profileData['Quote'] ) ? $profileData['Quote'] : '';
                $profileData['InterestedIn'] = isset( $profileData['InterestedIn'] ) ? $profileData['InterestedIn'] : '';
                $data['interested_in'] = is_array( $profileData['InterestedIn'] ) ? implode( ', ', $profileData['InterestedIn'] ) : $profileData['InterestedIn'];
                
                //Age Range - Min-Max
                $data['age_range_min'] = isset( $profileData['Age_Range_Min'] ) ? $profileData['Age_Range_Min'] : '';
                $data['age_range_max'] = isset( $profileData['Age_Range_Max'] ) ? $profileData['Age_Range_Max'] : '';

                if ( isset( $profileData['Interests'] ) && ! is_string( $profileData['Interests'] ) ) {
                    foreach ( $profileData['Interests'] as $key => $value ) {
                        $data['interests'] = $value->InterestedName;
                    }
                } else {
                    $data['interests'] = isset( $profileData['Interests'] ) ? $profileData['Interests'] : '';
                }

                $data['religion'] = isset( $profileData['Religion'] ) ? $profileData['Religion'] : '';
                $data['political_view'] = isset( $profileData['PoliticalView'] ) ? $profileData['PoliticalView'] : '';
                $data['https_image_url'] = isset( $profileData['HttpsImageUrl'] ) ? $profileData['HttpsImageUrl'] : '';
                $data['followers_count'] = isset( $profileData['FollowersCount'] ) ? (int) $profileData['FollowersCount'] : 0;
                $data['friends_count'] = isset( $profileData['FriendsCount'] ) ? (int) $profileData['FriendsCount'] : 0;
                $data['is_geo_enabled'] = isset( $profileData['IsGeoEnabled'] ) && $profileData['IsGeoEnabled'] == 'True' ? '1' : '0';
                $data['total_status_count'] = isset( $profileData['TotalStatusCount'] ) ? (int) $profileData['TotalStatusCount'] : 0;
                $data['number_of_recommenders'] = isset( $profileData['NumberOfRecommenders'] ) ? (int) $profileData['NumberOfRecommenders'] : 0;
                $data['hirable'] = isset( $profileData['Hirable'] ) ? (int) $profileData['Hirable'] : 0;
                $data['repository_url'] = isset( $profileData['RepositoryUrl'] ) ? $profileData['RepositoryUrl'] : '';
                $data['age'] = isset( $profileData['Age'] ) ? (int) $profileData['Age'] : 0;
                $data['professional_headline'] = isset( $profileData['ProfessionalHeadline'] ) ? $profileData['ProfessionalHeadline'] : '';

                $data = self::check_data( $data );
                if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_extended_profile_data', $data, array( 'wp_users_id' => $userId ) ) ) {
                    if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_extended_profile_data' . " WHERE wp_users_id = %d", $userId ) ) ) {
                        $wpdb->insert( $wpdb->base_prefix . 'lr_extended_profile_data', $data);
                    }
                }
                
                // Positions
                if ( is_array( $profileData['Positions'] ) && count( $profileData['Positions'] ) > 0 ) {
                    $companyIds = $wpdb->get_col( 'select company from ' . $wpdb->base_prefix . 'lr_positions where wp_users_id = ' . $userId );
                    if ( is_array( $companyIds ) && count( $companyIds ) > 0 ) {
                        $wpdb->get_results( 'delete from ' . $wpdb->base_prefix . 'lr_companies where id in (' . implode( ",", $companyIds ) . ')' );
                    }

                    $wpdb->delete( $wpdb->base_prefix . 'lr_positions', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['Positions'] as $lrPosition ) {

                        // Companies
                        if ( isset( $lrPosition->Company ) ) {
                            $temp = array();
                            $temp['wp_users_id'] = $userId;
                            $temp['company_name'] = $lrPosition->Company->Name;
                            $temp['company_type'] = $lrPosition->Company->Type;
                            $temp['industry'] = $lrPosition->Company->Industry;
                            $temp = self::check_data( $temp );
                            $wpdb->insert( $wpdb->base_prefix . 'lr_companies', $temp );
                            $tempId = $wpdb->insert_id;
                        }
                        // Positions
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['position'] = $lrPosition->Position;
                        $data['summary'] = $lrPosition->Summary;
                        $data['start_date'] = $lrPosition->StartDate;
                        $data['end_date'] = $lrPosition->EndDate;
                        $data['is_current'] = isset( $lrPosition->IsCurrent ) && ! empty( $lrPosition->IsCurrent ) ? (int)$lrPosition->IsCurrent : 0;
                        $data['company'] = isset( $tempId ) ? (int)$tempId : NULL;
                        $data['location'] = $lrPosition->Location;
                        $data = self::check_data( $data );
                        
                        $response = $wpdb->insert( $wpdb->base_prefix . 'lr_positions', $data );
                    }
                }

                // Education
                if ( is_array( $profileData['Educations'] ) && count( $profileData['Educations'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_education', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['Educations'] as $education) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['school'] = $education->School;
                        $data['year'] = $education->year;
                        $data['type'] = $education->type;
                        $data['notes'] = $education->notes;
                        $data['activities'] = $education->activities;
                        $data['degree'] = $education->degree;
                        $data['field_of_study'] = $education->fieldofstudy;
                        $data['start_date'] = $education->StartDate;
                        $data['end_date'] = $education->EndDate;
                        $data = self::check_data( $data );
                        $wpdb->insert( $wpdb->base_prefix . 'lr_education', $data );
                    }
                }

                // Phone numbers
                if ( is_array( $profileData['PhoneNumbers'] ) && count( $profileData['PhoneNumbers'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_phone_numbers', array( 'wp_users_id' => $userId) );
                    foreach ( $profileData['PhoneNumbers'] as $lrPhoneNumber) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['number_type'] = $lrPhoneNumber->PhoneType;
                        $data['phone_number'] = $lrPhoneNumber->PhoneNumber;
                        $data = self::check_data( $data );
                        $wpdb->insert( $wpdb->base_prefix . 'lr_phone_numbers', $data );
                    }
                }

                // IM Accounts
                if ( is_array( $profileData['IMAccounts'] ) && count( $profileData['IMAccounts'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_IMaccounts', array( 'wp_users_id' => $userId) );
                    foreach ( $profileData['IMAccounts'] as $lrImacc) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['account_type'] = $lrImacc->AccountType;
                        $data['account_username'] = $lrImacc->AccountName;
                        $data = self::check_data( $data);
                        $wpdb->insert( $wpdb->base_prefix . 'lr_IMaccounts', $data);
                    }
                }

                // Addresses
                if ( is_array($profileData['Addresses'] ) && count($profileData['Addresses'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_addresses', array('wp_users_id' => $userId ) );
                    foreach ( $profileData['Addresses'] as $lraddress ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['type'] = $lraddress->Type;
                        $data['address_line1'] = $lraddress->Address1;
                        $data['address_line2'] = $lraddress->Address2;
                        $data['city'] = $lraddress->City;
                        $data['state'] = $lraddress->State;
                        $data['postal_code'] = $lraddress->PostalCode;
                        $data['region'] = $lraddress->Region;
                        $data = self::check_data($data);
                        $wpdb->insert($wpdb->base_prefix . 'lr_addresses', $data);
                    }
                }

                // Sports
                if ( is_array( $profileData['Sports'] ) && count( $profileData['Sports'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_sports', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['Sports'] as $lrSport ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['sport_id'] = $lrSport->Id;
                        $data['sport'] = $lrSport->Name;
                        $data = self::check_data($data);
                        $wpdb->insert($wpdb->base_prefix . 'lr_sports', $data);
                    }
                }

                // Inspirational People
                if ( is_array( $profileData['InspirationalPeople'] ) && count( $profileData['InspirationalPeople'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_inspirational_people', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['InspirationalPeople'] as $lrIP ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['social_id'] = $lrIP->Id;
                        $data['name'] = $lrIP->Name;
                        $data = self::check_data( $data );
                        $wpdb->insert( $wpdb->base_prefix . 'lr_inspirational_people', $data );
                    }
                }

                // Skills
                if (is_array( $profileData['Skills']) && count( $profileData['Skills']) > 0) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_skills', array( 'wp_users_id' => $userId) );
                    foreach ( $profileData['Skills'] as $lrSkill) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['skill_id'] = $lrSkill->Id;
                        $data['name'] = $lrSkill->Name;
                        $data = self::check_data( $data);
                        $wpdb->insert( $wpdb->base_prefix . 'lr_skills', $data);
                    }
                }

                // Current Status
                if (is_array( $profileData['CurrentStatus']) && count( $profileData['CurrentStatus']) > 0) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_current_status', array( 'wp_users_id' => $userId) );
                    foreach ( $profileData['CurrentStatus'] as $lrCurrentStatus) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['status_id'] = $lrCurrentStatus->Id;
                        $data['status'] = $lrCurrentStatus->Text;
                        $data['source'] = $lrCurrentStatus->Source;
                        $data['created_date'] = $lrCurrentStatus->CreatedDate;
                        $data = self::check_data( $data);
                        $wpdb->insert( $wpdb->base_prefix . 'lr_current_status', $data);
                    }
                }

                // Certifications
                if (is_array( $profileData['Certifications']) && count( $profileData['Certifications']) > 0) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_certifications', array( 'wp_users_id' => $userId) );
                    foreach ( $profileData['Certifications'] as $lrCertification) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['certification_id'] = $lrCertification->Id;
                        $data['certification_name'] = $lrCertification->Name;
                        $data['authority'] = $lrCertification->Authority;
                        $data['license_number'] = $lrCertification->Number;
                        $data['start_date'] = $lrCertification->StartDate;
                        $data['end_date'] = $lrCertification->EndDate;
                        $data = self::check_data( $data);
                        $wpdb->insert( $wpdb->base_prefix . 'lr_certifications', $data);
                    }
                }

                // Courses
                if ( is_array( $profileData['Courses'] ) && count( $profileData['Courses'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_courses', array( 'wp_users_id' => $userId) );
                    foreach ( $profileData['Courses'] as $lrCourse) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['course_id'] = $lrCourse->Id;
                        $data['course'] = $lrCourse->Name;
                        $data['course_number'] = $lrCourse->Number;
                        $data = self::check_data( $data );
                        $wpdb->insert( $wpdb->base_prefix . 'lr_courses', $data );
                    }
                }

                // Volunteer
                if ( is_array( $profileData['Volunteer'] ) && count( $profileData['Volunteer'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_volunteer', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['Volunteer'] as $lrVolunteer ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['volunteer_id'] = $lrVolunteer->Id;
                        $data['role'] = $lrVolunteer->Role;
                        $data['organization'] = $lrVolunteer->Organization;
                        $data['cause'] = $lrVolunteer->Cause;
                        $data = self::check_data( $data );
                        $wpdb->insert( $wpdb->base_prefix . 'lr_volunteer', $data );
                    }
                }

                // Recommendations received
                if ( is_array( $profileData['RecommendationsReceived'] ) && count( $profileData['RecommendationsReceived'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_recommendations_received', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['RecommendationsReceived'] as $lrRR ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['recommendation_id'] = $lrRR->Id;
                        $data['recommendation_type'] = $lrRR->RecommendationType;
                        $data['recommendation_text'] = $lrRR->RecommendationText;
                        $data['recommender'] = $lrRR->Recommender;
                        $data = self::check_data( $data );
                        $wpdb->insert( $wpdb->base_prefix . 'lr_recommendations_received', $data );
                    }
                }

                // Languages
                if ( is_array( $profileData['Languages'] ) && count( $profileData['Languages'] ) > 0) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_languages', array('wp_users_id' => $userId ) );
                    foreach ( $profileData['Languages'] as $lrLanguage ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['language_id'] = $lrLanguage->Id;
                        $data['language'] = $lrLanguage->Name;
                        $data = self::check_data( $data );
                        $wpdb->insert($wpdb->base_prefix . 'lr_languages', $data );
                    }
                }

                // Patents
                if ( is_array( $profileData['Patents'] ) && count( $profileData['Patents'] ) > 0) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_patents', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['Patents'] as $lrPatent ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['patent_id'] = $lrPatent->Id;
                        $data['title'] = $lrPatent->Title;
                        $data['date'] = $lrPatent->Date;
                        $data = self::check_data( $data);
                        $wpdb->insert( $wpdb->base_prefix . 'lr_patents', $data );
                    }
                }

                // Favorites
                if ( is_array( $profileData['Favorites'] ) && count( $profileData['Favorites'] ) > 0 ) {
                    $wpdb->delete( $wpdb->base_prefix . 'lr_favorites', array( 'wp_users_id' => $userId ) );
                    foreach ( $profileData['Favorites'] as $lrFavorite ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['social_id'] = $lrFavorite->Id;
                        $data['name'] = $lrFavorite->Name;
                        $data['type'] = $lrFavorite->Type;
                        $data = self::check_data( $data );
                        $wpdb->insert( $wpdb->base_prefix . 'lr_favorites', $data );
                    }
                } //END FAVORITES
            }

            // Insert contacts if option is selected
            if ( in_array( $profileData['Provider'], array( 'twitter', 'facebook', 'linkedin', 'google', 'yahoo', 'foursquare', 'live', 'renren', 'vkontakte' ) ) && in_array( 'contacts', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $contacts = $loginRadiusObject->loginradius_get_contacts( $access_token );
                } catch ( LoginRadiusException $e ) {
                    $contacts = null;
                    error_log( $profileData['Provider'] . ' failed getting (contacts) ' . json_encode( $e->errorResponse ) );
                }

                if ( isset( $contacts ) && is_array( $contacts->Data ) && count( $contacts->Data ) > 0 ) {
                    foreach ( $contacts->Data as $contact ) {
                        // create array to insert data
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['social_id'] = $contact->ID;
                        $data['provider'] = $profileData['Provider'];
                        $data['name'] = $contact->Name;
                        $data['email'] = $contact->EmailID;
                        $data['profile_url'] = $contact->ProfileUrl;
                        $data['image_url'] = $contact->ImageUrl;
                        $data['status'] = $contact->Status;
                        $data['industry'] = $contact->Industry;
                        $data['country'] = $contact->Country;
                        $data['gender'] = $contact->Gender;
                        $data['phone_number'] = $contact->PhoneNumber;
                        $data = self::check_data( $data );
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_contacts', $data, array( 'wp_users_id' => $userId, 'social_id' => $data['social_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_contacts' . " WHERE wp_users_id = %d and social_id = %s", $userId, $data['social_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_contacts', $data);
                            }
                        }
                    }
                }
            }

            // Insert LinkedIn Companies if option is selected
            if ( in_array( $profileData['Provider'], array( 'linkedin' ) ) && in_array( 'linkedin_companies', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $linkedInCompanies = $loginRadiusObject->loginradius_get_followed_companies( $access_token );
                } catch ( LoginRadiusException $e ) {
                    $linkedInCompanies = null;
                    error_log( $profileData['Provider'] . ' failed getting (linkedin_companies) ' . json_encode( $e->errorResponse ) );
                }
                
                if ( isset( $linkedInCompanies ) && is_array( $linkedInCompanies ) && count( $linkedInCompanies ) > 0 ) {
                    foreach ( $linkedInCompanies as $company ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['company_id'] = $company->ID;
                        $data['company_name'] = $company->Name;
                        $data = self::check_data($data);

                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_linkedin_companies', $data, array( 'wp_users_id' => $userId, 'company_id'=>$data['company_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_linkedin_companies' . " WHERE wp_users_id = %d and company_id = %s", $userId, $data['company_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_linkedin_companies', $data );
                            }
                        }
                    }
                }
            }

            // Insert status if option is selected
            if ( in_array( $profileData['Provider'], array( 'twitter', 'facebook', 'linkedin', 'renren', 'vkontakte' ) ) && in_array( 'status', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $status = $loginRadiusObject->loginradius_get_status( $access_token );
                } catch ( LoginRadiusException $e ) {
                    $status = null;
                    error_log( $profileData['Provider'] . ' failed getting (get_status) ' . json_encode( $e->errorResponse ) );
                }

                if ( isset( $status ) && is_array( $status ) && count( $status ) > 0 ) {
                    foreach ($status as $lrStatus) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['provider'] = $profileData['Provider'];
                        $data['status_id'] = $lrStatus->Id;
                        $data['status'] = $lrStatus->Text;
                        $data['date_time'] = $lrStatus->DateTime;
                        $data['likes'] = $lrStatus->Likes;
                        $data['place'] = $lrStatus->Place;
                        $data['source'] = $lrStatus->Source;
                        $data['image_url'] = $lrStatus->ImageUrl;
                        $data['link_url'] = $lrStatus->LinkUrl;
                        $data = self::check_data($data);
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_status', $data, array( 'wp_users_id' => $userId, 'status_id'=>$data['status_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_status' . " WHERE wp_users_id = %d and status_id = %s", $userId, $data['status_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_status', $data);
                            }
                        }
                    }
                }
            }

            // Insert mentions if option is selected
            if ( $profileData['Provider'] == 'twitter' && in_array( 'mentions', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $mentions = $loginRadiusObject->loginradius_get_mentions( $access_token );
                } catch ( LoginRadiusException $e ) {
                    $mentions = null;
                    error_log( $profileData['Provider'] . ' failed getting (get_mentions) ' . json_encode( $e->errorResponse ) );
                }

                if ( isset( $mentions ) && is_array( $mentions ) && count( $mentions ) > 0 ) {
                    foreach ( $mentions as $mention ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['mention_id'] = $mention->Id;
                        $data['tweet'] = $mention->Text;
                        $data['date_time'] = $mention->DateTime;
                        $data['likes'] = $mention->Likes;
                        $data['place'] = $mention->Place;
                        $data['source'] = $mention->Source;
                        $data['image_url'] = $mention->ImageUrl;
                        $data['link_url'] = $mention->LinkUrl;
                        $data['mentioned_by'] = $mention->Name;
                        $data = self::check_data($data);
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_twitter_mentions', $data, array( 'wp_users_id' => $userId, 'mention_id'=>$data['mention_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_twitter_mentions' . " WHERE wp_users_id = %d and mention_id = %s", $userId, $data['mention_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_twitter_mentions', $data );
                            }
                        }
                    }  
                }
            }

            // Insert groups if option is selected
            if ( in_array( $profileData['Provider'], array( 'facebook', 'vkontakte' ) ) && in_array( 'groups', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                   $groups = $loginRadiusObject->loginradius_get_groups( $access_token ); 
                } catch (LoginRadiusException $e) {
                    $groups = null;
                    error_log( $profileData['Provider'] . ' failed getting (groups) ' . json_encode( $e->errorResponse ) );
                }

                if ( isset( $groups ) && is_array( $groups ) && count( $groups ) > 0 ) {
                    foreach ( $groups as $group ) {
                        $data = array();
                        $data['wp_users_id']  = $userId;
                        $data['provider']     = $profileData['Provider'];
                        $data['country']      = $group->Country;
                        $data['description']  = $group->Description;
                        $data['email']        = $group->Email;
                        $data['group_id']     = $group->ID;
                        $data['image']        = $group->Image;
                        $data['logo']         = $group->Logo;
                        $data['member_count'] = $group->MemberCount;
                        $data['name']         = $group->Name;
                        $data['postal_code']  = $group->PostalCode;
                        $data['type']         = $group->Type;
                        
                        $data = self::check_data($data);
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_groups', $data, array( 'wp_users_id' => $userId, 'group_id'=>$data['group_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_groups' . " WHERE wp_users_id = %d and group_id = %s", $userId, $data['group_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_groups', $data);
                            }
                        }
                    }
                }
            }

            // Insert Facebook Likes, if option is selected
            if ( $profileData['Provider'] == 'facebook' && in_array( 'likes', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $fblikes = $loginRadiusObject->loginradius_get_likes( $access_token ); 
                } catch (LoginRadiusException $e) {
                    $fblikes = null;
                    error_log( $profileData['Provider'] . ' failed getting (likes) ' . json_encode( $e->errorResponse ) );
                }

                if ( isset( $fblikes ) && count( $fblikes ) > 0) {
                    foreach ( $fblikes as $like ) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['like_id'] = $like->ID;
                        $data['name'] = $like->Name;
                        $data['category'] = $like->Category;
                        $data['created_date'] = date('Y-m-d H:i:s', strtotime($like->CreatedDate));
                        $data['website'] = $like->Website;
                        $data['description'] = $like->Description;

                        $data = self::check_data($data);
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_facebook_likes', $data, array( 'wp_users_id' => $userId, 'like_id'=>$data['like_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare( 'SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_facebook_likes' . " WHERE wp_users_id = %d and like_id = %s", $userId, $data['like_id']))) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_facebook_likes', $data);
                            }
                        }
                    }
                }
            }

            // Insert facebook events if option is selected
            if ( $profileData['Provider'] == 'facebook' && in_array( 'events', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $events = $loginRadiusObject->loginradius_get_events( $access_token );
                } catch (LoginRadiusException $e) {
                    $events = null;
                    error_log( $profileData['Provider'] . ' failed getting (events) ' . json_encode( $e->errorResponse ) );  
                }

                if ( isset( $events ) && is_array($events) && count($events) > 0 ) {                    
                    foreach ($events as $event) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['event_id']    = $event->ID;
                        $data['description'] = $event->Description;
                        $data['name']        = $event->Name;
                        $data['start_time']  = date( 'Y-m-d H:i:s', strtotime( $event->StartTime ) );
                        $data['end_time']    = date( 'Y-m-d H:i:s', strtotime( $event->EndTime ) );
                        $data['privacy']     = $event->Privacy;
                        $data['rsvp_status'] = $event->RsvpStatus;
                        $data['location']    = $event->Location;
                        $data['owner_id']    = $event->OwnerId;
                        $data['owner_name']  = $event->OwnerName;
                        $data['updated_date']= date( 'Y-m-d H:i:s', strtotime( $event->UpdatedDate ) );
                        $data = self::check_data($data);
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_facebook_events', $data, array( 'wp_users_id' => $userId, 'event_id'=>$data['event_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare('SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_facebook_events' . " WHERE wp_users_id = %d and event_id = %s", $userId, $data['event_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_facebook_events', $data );
                            }
                        }
                    }
                }
            }

            // Insert posts if option is selected
            if ( $profileData['Provider'] == 'facebook' && in_array( 'posts', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $posts = $loginRadiusObject->loginradius_get_posts( $access_token );
                } catch ( LoginRadiusException $e ) {
                    $posts = null;
                    error_log( $profileData['Provider'] . ' failed getting (posts) ' . json_encode( $e->errorResponse ) );
                }
                
                if ( isset( $posts ) && is_array( $posts ) && count( $posts ) > 0 ) {
                    //$wpdb->delete( $wpdb->base_prefix . 'lr_facebook_posts', array( 'wp_users_id' => $userId ) );
                    foreach ($posts as $post) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['post_id'] = $post->ID;
                        $data['name'] = $post->Name;
                        $data['title'] = $post->Title;
                        $data['start_time'] = date( 'Y-m-d H:i:s', strtotime( $post->StartTime ) );
                        $data['update_time'] = date( 'Y-m-d H:i:s', strtotime( $post->UpdateTime ) );
                        $data['message'] = $post->Message;
                        $data['place'] = $post->Place;
                        $data['picture'] = $post->Picture;
                        $data['likes'] = $post->Likes;
                        $data['shares'] = $post->Share;
                        $data['type'] = $post->Type;
                        $data = self::check_data($data);
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_facebook_posts', $data, array( 'wp_users_id' => $userId, 'post_id'=> $data['post_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare('SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_facebook_posts' . " WHERE wp_users_id = %d and post_id = %s", $userId, $data['post_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_facebook_posts', $data );
                            }
                        }
                    }
                }
            }

            // Album API
            if ( $profileData['Provider'] == 'facebook' && in_array( 'albums', $lr_social_profile_data_settings['profiledata'] ) ) {
                try {
                    $albums = $loginRadiusObject->loginradius_get_photo_albums($access_token);
                } catch (LoginRadiusException $e) {
                    $albums = null;
                    error_log( $profileData['Provider'] . ' failed getting (albums) ' . json_encode( $e->errorResponse ) );
                }
                
                if ( isset( $albums ) && is_array( $albums ) && count( $albums ) > 0 ) {
                    foreach ($albums as $album) {
                        $data = array();
                        $data['wp_users_id'] = $userId;
                        $data['album_id'] = $album->ID;
                        $data['owner_id'] = $album->OwnerId;
                        $data['title'] = $album->Title;
                        $data['description'] = $album->Description;
                        $data['location'] = $album->Location;
                        $data['type'] = $album->Type;
                        $data['created_date'] = date('Y-m-d H:i:s', strtotime( $album->CreatedDate) );
                        $data['updated_date'] = date('Y-m-d H:i:s', strtotime( $album->UpdatedDate) );
                        $data['cover_image_url'] = $album->CoverImageUrl;
                        $data['image_count'] = $album->ImageCount;
                        $data['directory_url'] = $album->DirectoryUrl;

                        $data = self::check_data($data);
                        if ( ! $wpdb->update( $wpdb->base_prefix . 'lr_albums', $data, array( 'wp_users_id' => $userId, 'album_id'=>$data['album_id'] ) ) ) {
                            if ( ! $wpdb->get_var( $wpdb->prepare('SELECT wp_users_id FROM ' . $wpdb->base_prefix . 'lr_albums' . " WHERE wp_users_id = %d and album_id = %s", $userId, $data['album_id'] ) ) ) {
                                $wpdb->insert( $wpdb->base_prefix . 'lr_albums', $data );
                            }
                        }
                    }
                }
            }
        }

        /**
         * Filter the data fetched from LoginRadius.
         */
        public static function validate_profiledata( $userProfileObject ) {
            global $lr_social_profile_data_settings;

            if ( isset( $lr_social_profile_data_settings['enable_custom_popup'] ) && $lr_social_profile_data_settings['enable_custom_popup'] == '1' ) {

                if ( isset( $lr_social_profile_data_settings['show_email'] )  && $lr_social_profile_data_settings['show_email'] == '1' ) {
                    $custom_email = true;
                }

                if ( isset( $lr_social_profile_data_settings['show_phonenumber'] ) && $lr_social_profile_data_settings['show_phonenumber'] == '1' ) {
                    $custom_phonenumber = true;
                }

                if ( isset( $lr_social_profile_data_settings['show_city'] ) && $lr_social_profile_data_settings['show_city'] == '1' ) {
                    $custom_city = true;
                }

                if ( isset( $lr_social_profile_data_settings['show_postalcode'] ) && $lr_social_profile_data_settings['show_postalcode'] == '1' ) {
                    $custom_postalcode = true;
                }
            }

            $profileData['ID'] = ! empty( $userProfileObject->ID ) ? $userProfileObject->ID : '';
            $profileData['uid'] = ! empty( $userProfileObject->Uid ) ? $userProfileObject->Uid : '';
            $profileData['UniqueId'] = uniqid('LoginRadius_', true);

            // Set Email as custom or from object
            if ( isset( $custom_email ) ) {
                $profileData['Email'] = $userProfileObject->Email;
            } else {
                if ( isset( $userProfileObject->Email ) && is_array( $userProfileObject->Email ) ) {
                    foreach ( $userProfileObject->Email as $email ) {
                        if ( $email->Value != '' ) {
                            $profileData['Email'] = $email->Value;
                        }
                    }
                } else {
                    $profileData['Email'] = '';
                }
            }

            $profileData['FullName'] = ! empty( $userProfileObject->FullName ) && $userProfileObject->FullName != 'null' ? $userProfileObject->FullName : '';
            $profileData['ProfileName'] = ! empty( $userProfileObject->ProfileName ) && $userProfileObject->ProfileName != 'null' ? $userProfileObject->ProfileName : '';
            $profileData['NickName'] = ! empty( $userProfileObject->NickName ) && $userProfileObject->NickName != 'null' ? $userProfileObject->NickName : '';
            $profileData['FirstName'] = ! empty( $userProfileObject->FirstName ) && $userProfileObject->FirstName != 'null' ? $userProfileObject->FirstName : '';
            $profileData['LastName'] = ! empty( $userProfileObject->LastName ) && $userProfileObject->LastName != 'null' ? $userProfileObject->LastName : '';
            $profileData['Provider'] = ! empty( $userProfileObject->Provider ) && $userProfileObject->Provider != 'null' ? $userProfileObject->Provider : '';
            $profileData['ThumbnailImageUrl'] = isset( $userProfileObject->ThumbnailImageUrl ) && $userProfileObject->ThumbnailImageUrl != 'null' ? trim( $userProfileObject->ThumbnailImageUrl) : '';
            $profileData['Thumbnail'] = ! empty( $userProfileObject->ThumbnailImageUrl ) && $userProfileObject->ThumbnailImageUrl != 'null' ? trim( $userProfileObject->ThumbnailImageUrl) : '';

            if ( empty( $profileData['Thumbnail'] ) && $profileData['Provider'] == 'facebook') {
                $profileData['Thumbnail'] = 'http://graph.facebook.com/' . $profileData['ID'] . '/picture?type=square';
            }

            $profileData['PictureUrl'] = ! empty( $userProfileObject->ImageUrl ) && $userProfileObject->ImageUrl != 'null' ? trim( $userProfileObject->ImageUrl ) : '';
            $profileData['Bio'] = ! empty( $userProfileObject->About ) && $userProfileObject->About != 'null' ? $userProfileObject->About : '';
            $profileData['ProfileUrl'] = ! empty( $userProfileObject->ProfileUrl ) && $userProfileObject->ProfileUrl != 'null' ? $userProfileObject->ProfileUrl : '';

            // Basic profile data
            if ( in_array( 'basic', $lr_social_profile_data_settings['profiledata'] ) ) {
                $profileData['Prefix'] = ! empty( $userProfileObject->Prefix ) && $userProfileObject->Prefix != 'null' ? $userProfileObject->Prefix : '';
                $profileData['MiddleName'] = ! empty( $userProfileObject->MiddleName ) && $userProfileObject->MiddleName != 'null' ? $userProfileObject->MiddleName : '';
                $profileData['Suffix'] = ! empty( $userProfileObject->Suffix ) && $userProfileObject->Suffix != 'null' ? $userProfileObject->Suffix : '';
                $profileData['BirthDate'] = ! empty( $userProfileObject->BirthDate ) && $userProfileObject->BirthDate != 'null' ? $userProfileObject->BirthDate : NULL;

                // Format birthdate according to database
                if ( $profileData['BirthDate'] != '' && $profileData['BirthDate'] != NULL ) {

                    $profileData['BirthDate'] = $profileData['BirthDate'];
                }

                $profileData['Gender'] = ! empty( $userProfileObject->Gender ) && $userProfileObject->Gender != 'null' ? $userProfileObject->Gender : '';
                if ( $profileData['Gender'] == 'M' || $profileData['Gender'] == 'Male') {
                    $profileData['Gender'] = 'male';
                } elseif ( $profileData['Gender'] == 'F' || $profileData['Gender'] == 'Female') {
                    $profileData['Gender'] = 'female';
                } else {
                    $profileData['Gender'] = '';
                }


                // Country name
                if ( isset( $userProfileObject->Country->Name ) && is_string( $userProfileObject->Country->Name ) ) {
                    $profileData['CountryName'] = $userProfileObject->Country->Name != 'unknown' && $userProfileObject->Country->Name != 'null' ? $userProfileObject->Country->Name : '';
                } elseif ( isset( $userProfileObject->Country ) && is_string( $userProfileObject->Country ) ) {
                    $profileData['CountryName'] = $userProfileObject->Country != 'unknown' && $userProfileObject->Country != 'null' ? $userProfileObject->Country : '';
                } else {
                    $profileData['CountryName'] = '';
                }

                // Country Code
                if ( isset( $userProfileObject->Country->Code ) && is_string( $userProfileObject->Country->Code ) ) {
                    $profileData['CountryCode'] = $userProfileObject->Country->Code != 'unknown' && $userProfileObject->Country->Code != 'null' ? $userProfileObject->Country->Code : '';
                } else {
                    $profileData['CountryCode'] = '';
                }

                $profileData['ImageUrl'] = ! empty( $userProfileObject->ImageUrl ) ? $userProfileObject->ImageUrl : '';
                $profileData['Emails'] = $userProfileObject->Email;
            }

            // Extended location data
            if ( in_array( 'exlocation', $lr_social_profile_data_settings['profiledata'] ) ) {

                if ( isset( $profileData['Addresses'][0] ) ) {
                    $profileData['Address_Line_1'] = $profileData['Addresses'][0]->Address1;
                    $profileData['Address_Line_2'] = $profileData['Addresses'][0]->Address2;
                    $profileData['City'] = $profileData['Addresses'][0]->City;
                    $data['state'] = $profileData['Addresses'][0]->State;
                    $data['postal_code'] = $profileData['Addresses'][0]->PostalCode;
                    $data['region'] = $profileData['Addresses'][0]->Region;
                }
                $profileData['Address_Line_1'] = ! empty($userProfileObject->MainAddress) ? $userProfileObject->MainAddress : '';
                $profileData['Address_Line_2'] = ! empty($userProfileObject->MainAddress) ? $userProfileObject->MainAddress : '';
                $profileData['MainAddress'] = ! empty($userProfileObject->MainAddress) ? $userProfileObject->MainAddress : '';
                $profileData['HomeTown'] = ! empty($userProfileObject->HomeTown) ? $userProfileObject->HomeTown : '';
                $profileData['State'] = ! empty($userProfileObject->State) ? $userProfileObject->State : '';
                $profileData['LocalCity'] = ! empty($userProfileObject->LocalCity) && $userProfileObject->LocalCity != 'unknown' ? $userProfileObject->LocalCity : '';
                $profileData['ProfileCity'] = ! empty($userProfileObject->ProfileCity) && $userProfileObject->ProfileCity != 'unknown' ? $userProfileObject->ProfileCity : '';
                if ( isset( $custom_city ) ) {
                    $profileData['City'] = ! empty( $userProfileObject->City ) && $userProfileObject->City != 'unknown' ? $userProfileObject->City : '';
                } else {
                    $profileData['City'] = ! empty( $profileData['LocalCity'] ) ? $profileData['LocalCity'] : $profileData['ProfileCity'];
                }


                $profileData['LocalLanguage'] = ! empty($userProfileObject->LocalLanguage) && $userProfileObject->LocalLanguage != 'unknown' ? $userProfileObject->LocalLanguage : '';
                $profileData['Language'] = ! empty($userProfileObject->Language) && $userProfileObject->Language != 'unknown' ? $userProfileObject->Language : '';
                $profileData['LocalCountry'] = ! empty($userProfileObject->LocalCountry) && $userProfileObject->LocalCountry != 'unknown' ? $userProfileObject->LocalCountry : '';
                $profileData['ProfileCountry'] = ! empty($userProfileObject->ProfileCountry) && $userProfileObject->ProfileCountry != 'unknown' ? $userProfileObject->ProfileCountry : '';
                if (isset($custom_postalcode)) {
                    $profileData['PostalCode'] = ! empty($userProfileObject->PostalCode) && $userProfileObject->PostalCode != 'unknown' ? $userProfileObject->PostalCode : '';
                } else {
                    $profileData['PostalCode'] = '';
                }
            }

            // Extended profile data
            if ( in_array( 'extended', $lr_social_profile_data_settings['profiledata'] ) ) {
                $profileData['Website'] = ! empty($userProfileObject->Website) ? $userProfileObject->Website : '';
                $profileData['Favicon'] = ! empty($userProfileObject->Favicon) ? $userProfileObject->Favicon : '';
                $profileData['Industry'] = ! empty($userProfileObject->Industry) ? $userProfileObject->Industry : '';
                $profileData['TimeZone'] = ! empty($userProfileObject->TimeZone) ? $userProfileObject->TimeZone : '';

                $profileData['LastProfileUpdate'] = ! empty($userProfileObject->UpdatedTime) && $userProfileObject->UpdatedTime != 'null' ? $userProfileObject->UpdatedTime : NULL;

                // Format birthdate according to database
                if ($profileData['LastProfileUpdate'] != '' && $profileData['LastProfileUpdate'] != NULL) {

                    $profileData['LastProfileUpdate'] = date( 'Y-m-d H:i:s', strtotime( $profileData['LastProfileUpdate'] ) );
                }

                $profileData['Created'] = ! empty( $userProfileObject->Created ) ? $userProfileObject->Created : '';
                $profileData['Verified'] = ! empty( $userProfileObject->Verified ) ? $userProfileObject->Verified : '';
                $profileData['RelationshipStatus'] = ! empty( $userProfileObject->RelationshipStatus ) ? $userProfileObject->RelationshipStatus : '';
                $profileData['Quote'] = ! empty( $userProfileObject->Quota ) ? $userProfileObject->Quota : '';
                $profileData['InterestedIn'] = ! empty( $userProfileObject->InterestedIn ) ? $userProfileObject->InterestedIn : '';
                $profileData['Interests'] = ! empty( $userProfileObject->Interests ) ? $userProfileObject->Interests : '';
                $profileData['Religion'] = ! empty( $userProfileObject->Religion ) ? $userProfileObject->Religion : '';
                $profileData['PoliticalView'] = ! empty( $userProfileObject->Political ) ? $userProfileObject->Political : '';
                $profileData['HttpsImageUrl'] = ! empty( $userProfileObject->HttpsImageUrl ) ? $userProfileObject->HttpsImageUrl : '';
                $profileData['FollowersCount'] = ! empty( $userProfileObject->FollowersCount ) ? $userProfileObject->FollowersCount : '';
                $profileData['FriendsCount'] = ! empty( $userProfileObject->FriendsCount ) ? $userProfileObject->FriendsCount : '';
                $profileData['IsGeoEnabled'] = ! empty( $userProfileObject->IsGeoEnabled ) ? $userProfileObject->IsGeoEnabled : '';
                $profileData['TotalStatusCount'] = ! empty( $userProfileObject->TotalStatusesCount ) ? $userProfileObject->TotalStatusesCount : '';
                $profileData['NumberOfRecommenders'] = ! empty( $userProfileObject->NumRecommenders ) ? $userProfileObject->NumRecommenders : '';
                $profileData['Honors'] = ! empty( $userProfileObject->Honors ) ? $userProfileObject->Honors : '';
                $profileData['Associations'] = ! empty( $userProfileObject->Associations ) ? $userProfileObject->Associations : '';
                $profileData['Hirable'] = ! empty( $userProfileObject->Hireable ) ? $userProfileObject->Hireable : '';
                $profileData['RepositoryUrl'] = ! empty($userProfileObject->RepositoryUrl ) ? $userProfileObject->RepositoryUrl : '';
                $profileData['Age'] = ! empty( $userProfileObject->Age ) ? $userProfileObject->Age : '';

                $profileData['ProfessionalHeadline'] = ! empty( $userProfileObject->ProfessionalHeadline ) ? $userProfileObject->ProfessionalHeadline : '';

                if ( isset( $custom_phonenumber ) ) {
                    $profileData['PhoneNumber'] = isset( $userProfileObject->PhoneNumber ) ? $userProfileObject->PhoneNumber : '';
                }

                if( isset( $userProfileObject->AgeRange ) ) {
                    $profileData['Age_Range_Min'] = isset( $userProfileObject->AgeRange->Min ) ? $userProfileObject->AgeRange->Min : '';
                    $profileData['Age_Range_Max'] = isset( $userProfileObject->AgeRange->Max ) ? $userProfileObject->AgeRange->Max : '';
                }

                // Arrays
                $profileData['Positions'] = isset( $userProfileObject->Positions ) ? $userProfileObject->Positions : '';
                $profileData['Educations'] = isset( $userProfileObject->Educations ) ? $userProfileObject->Educations : '';
                $profileData['PhoneNumbers'] = isset( $userProfileObject->PhoneNumbers ) ? $userProfileObject->PhoneNumbers : '';
                if ( is_array( $profileData['PhoneNumbers'] ) ) {
                    foreach ( $profileData['PhoneNumbers'] as $phone ) {
                        if ( $phone->PhoneNumber != '' ) {
                            $profileData['PhoneNumber'] = $phone->PhoneNumber;
                            break;
                        }
                    }
                }
                $profileData['IMAccounts'] = isset( $userProfileObject->IMAccounts ) ? $userProfileObject->IMAccounts : '';
                $profileData['Addresses'] = isset( $userProfileObject->Addresses ) ? $userProfileObject->Addresses : '';
                $profileData['Sports'] = isset( $userProfileObject->Sports ) ? $userProfileObject->Sports : '';
                $profileData['InspirationalPeople'] = isset( $userProfileObject->InspirationalPeople ) ? $userProfileObject->InspirationalPeople : '';
                $profileData['Skills'] = isset( $userProfileObject->Skills ) ? $userProfileObject->Skills : '';
                $profileData['CurrentStatus'] = isset( $userProfileObject->CurrentStatus ) ? $userProfileObject->CurrentStatus : '';
                $profileData['Certifications'] = isset( $userProfileObject->Certifications ) ? $userProfileObject->Certifications : '';
                $profileData['Courses'] = isset( $userProfileObject->Courses ) ? $userProfileObject->Courses : '';
                $profileData['Volunteer'] = isset( $userProfileObject->Volunteer ) ? $userProfileObject->Volunteer : '';
                $profileData['RecommendationsReceived'] = isset( $userProfileObject->RecommendationsReceived ) ? $userProfileObject->RecommendationsReceived : '';
                $profileData['Languages'] = isset( $userProfileObject->Languages ) ? $userProfileObject->Languages : '';
                $profileData['Patents'] = isset( $userProfileObject->Patents ) ? $userProfileObject->Patents : '';
                $profileData['Favorites'] = isset( $userProfileObject->FavoriteThings ) ? $userProfileObject->FavoriteThings : '';
            }

            return $profileData;
        }

        /**
         * Check all user Profile data on save time
         */
        public static function check_data( $data = array() ) {
            foreach ( $data as $key => $value ) {
                
                if ( is_array( $value ) || is_object( $value ) ) {
                    $data[$key] = '';
                }
            }
            return $data;
        }
    }
    new LR_Social_Profile_Data_Function();
}

