<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
if (!class_exists('LR_Social_Profile_Data_Install')) {

    /**
     * class responsible for setting default settings for social invite.
     */
    class LR_Social_Profile_Data_Install {

        /**
         * Loads global social_profile_data options used for init and reset.
         *
         * @global social_profile_data_options
         */
        private static $options = array(
            'social_profile_data_enable' => '1',
            'viewProfileData' => '',
            'profiledata' => array(),
            'showprofiledata' => array(),
            'display_UserProfileData' => '',
            'custom_one_title' => '',
            'custom_one_type' => 'text',
            'custom_two_title' => '',
            'custom_two_type' => 'text',
            'custom_three_title' => '',
            'custom_three_type' => 'text',
            'custom_four_title' => '',
            'custom_four_type' => 'text',
            'custom_five_title' => '',
            'custom_five_type' => 'text'
        );

        /**
         * Function for adding default social_profile_data settings at activation.
         */
        public static function set_default_options($blog_id) {
            global $wpdb, $lr_social_profile_data_settings;
            if ($blog_id) {
                if (!get_blog_option($blog_id, 'LoginRadius_Social_Profile_Data_settings')) {
                    update_blog_option($blog_id, 'LoginRadius_Social_Profile_Data_settings', self::$options);
                    $lr_social_profile_data_settings = get_blog_option($blog_id, 'LoginRadius_Social_Profile_Data_settings');
                }
            } else {
                if (!get_option('LoginRadius_Social_Profile_Data_settings')) {
                    update_option('LoginRadius_Social_Profile_Data_settings', self::$options);
                    $lr_social_profile_data_settings = get_option('LoginRadius_Social_Profile_Data_settings');
                }
            }


            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_basic_profile_data` ( 
                `id` int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `social_id` varchar( 150 ) NOT NULL,
                `provider` varchar( 20 ) DEFAULT NULL,
                `prefix` varchar( 100 ) DEFAULT NULL,
                `first_name` varchar( 100 ) DEFAULT NULL,
                `middle_name` varchar( 100 ) DEFAULT NULL,
                `last_name` varchar( 100 ) DEFAULT NULL,
                `suffix` varchar( 100 ) DEFAULT NULL,
                `nick_name` varchar( 200 ) DEFAULT NULL,
                `profile_name` varchar( 100 ) DEFAULT NULL,
                `profile_url` varchar( 300 ) DEFAULT NULL,
                `birth_date` varchar( 20 ) DEFAULT NULL,
                `gender` enum( \'male\',\'female\',\'unknown\' ) DEFAULT NULL,
                `website` varchar( 300 ) DEFAULT NULL,
                `thumbnail_image_url` varchar( 300 ) DEFAULT NULL,
                `image_url` varchar( 300 ) DEFAULT NULL
            )');

            $wpdb->query('ALTER TABLE `' . $wpdb->base_prefix . 'lr_basic_profile_data` MODIFY `birth_date` varchar(50)');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_emails` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `email_type` varchar( 15 ) DEFAULT NULL,
                `email` varchar( 100 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_extended_location_data` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `address_line_1` varchar( 300 ) DEFAULT NULL,
                `address_line_2` varchar( 300 ) DEFAULT NULL,
                `hometown` varchar( 100 ) DEFAULT NULL,
                `city` varchar( 100 ) DEFAULT NULL,
                `state` varchar( 100 ) DEFAULT NULL,
                `postal_code` varchar( 50 ) DEFAULT NULL,
                `country` varchar( 100 ) DEFAULT NULL,
                `region` varchar( 100 ) DEFAULT NULL,
                `local_city` varchar( 50 ) DEFAULT NULL,
                `profile_city` varchar( 50 ) DEFAULT NULL,
                `local_language` varchar( 10 ) DEFAULT NULL,
                `language` varchar( 15 ) DEFAULT NULL,
                `local_country` varchar( 50 ) DEFAULT NULL,
                `profile_country` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_extended_profile_data` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `website` varchar( 300 ) DEFAULT NULL,
                `favicon` varchar( 300 ) DEFAULT NULL,
                `industry` varchar( 1000 ) DEFAULT NULL,
                `about` varchar( 1500 ) DEFAULT NULL,
                `timezone` varchar( 100 ) DEFAULT NULL,
                `verified` varchar( 15 ) DEFAULT NULL,
                `last_profile_update` datetime DEFAULT NULL,
                `created` varchar( 100 ) DEFAULT NULL,
                `relationship_status` varchar( 30 ) DEFAULT NULL,
                `quote` varchar( 1000 ) DEFAULT NULL,
                `interested_in` varchar( 1000 ) DEFAULT NULL,
                `interests` varchar( 1000 ) DEFAULT NULL,
                `religion` varchar( 1000 ) DEFAULT NULL,
                `political_view` varchar( 1000 ) DEFAULT NULL,
                `https_image_url` varchar( 300 ) DEFAULT NULL,
                `followers_count` int( 11 ) DEFAULT NULL,
                `friends_count` int( 11 ) DEFAULT NULL,
                `is_geo_enabled` enum( \'0\',\'1\' ) DEFAULT NULL,
                `total_status_count` int( 11 ) DEFAULT NULL,
                `number_of_recommenders` int( 11 ) DEFAULT NULL,
                `hirable` enum( \'0\', \'1\' ) DEFAULT NULL,
                `repository_url` varchar( 300 ) DEFAULT NULL,
                `age` int( 3 ) DEFAULT NULL,
                `age_range_min` int(3) DEFAULT NULL,
                `age_range_max` int(3) DEFAULT NULL,
                `professional_headline` varchar( 300 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_positions` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `position` varchar( 100 ) DEFAULT NULL,
                `summary` text DEFAULT NULL,
                `start_date` varchar( 50 ) DEFAULT NULL,
                `end_date` varchar( 50 ) DEFAULT NULL,
                `is_current` enum( \'0\',\'1\' ) DEFAULT NULL,
                `company` int( 11 ) DEFAULT NULL,
                `location` varchar( 255 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_companies` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `company_name` varchar( 100 ) DEFAULT NULL,
                `company_type` varchar( 50 ) DEFAULT NULL,
                `industry` varchar( 150 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_education` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `school` varchar( 100 ) DEFAULT NULL,
                `year` varchar( 50 ) DEFAULT NULL,
                `type` varchar( 50 ) DEFAULT NULL,
                `notes` varchar( 100 ) DEFAULT NULL,
                `activities` varchar( 100 ) DEFAULT NULL,
                `degree` varchar( 100 ) DEFAULT NULL,
                `field_of_study` varchar( 100 ) DEFAULT NULL,
                `start_date` varchar( 50 ) DEFAULT NULL,
                `end_date` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_phone_numbers` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `number_type` varchar( 20 ) DEFAULT NULL,
                `phone_number` varchar( 20 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_imaccounts` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `account_type` varchar( 20 ) DEFAULT NULL,
                `account_username` varchar( 100 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_addresses` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `type` varchar( 20 ) DEFAULT NULL,
                `address_line1` varchar( 100 ) DEFAULT NULL,
                `address_line2` varchar( 100 ) DEFAULT NULL,
                `city` varchar( 100 ) DEFAULT NULL,
                `state` varchar( 100 ) DEFAULT NULL,
                `postal_code` varchar( 20 ) DEFAULT NULL,
                `region` varchar( 100 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_sports` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `sport_id` varchar( 20 ) DEFAULT NULL,
                `sport` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_inspirational_people` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `social_id` varchar( 20 ) DEFAULT NULL,
                `name` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_skills` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `skill_id` varchar( 20 ) DEFAULT NULL,
                `name` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_current_status` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `status_id` varchar( 30 ) DEFAULT NULL,
                `status` varchar( 1500 ) DEFAULT NULL,
                `source` varchar( 500 ) DEFAULT NULL,
                `created_date` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_certifications` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `certification_id` varchar( 30 ) DEFAULT NULL,
                `certification_name` varchar( 50 ) DEFAULT NULL,
                `authority` varchar( 50 ) DEFAULT NULL,
                `license_number` varchar( 50 ) DEFAULT NULL,
                `start_date` varchar( 50 ) DEFAULT NULL,
                `end_date` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_courses` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `course_id` varchar( 30 ) DEFAULT NULL,
                `course` varchar( 100 ) DEFAULT NULL,
                `course_number` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_volunteer` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `volunteer_id` varchar( 30 ) DEFAULT NULL,
                `role` varchar( 50 ) DEFAULT NULL,
                `organization` varchar( 50 ) DEFAULT NULL,
                `cause` varchar( 100 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_recommendations_received` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `recommendation_id` varchar( 30 ) DEFAULT NULL,
                `recommendation_type` varchar( 100 ) DEFAULT NULL,
                `recommendation_text` varchar( 1500 ) DEFAULT NULL,
                `recommender` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_languages` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `language_id` varchar( 30 ) DEFAULT NULL,
                `language` varchar( 30 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_patents` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `patent_id` varchar( 30 ) DEFAULT NULL,
                `title` varchar( 100 ) DEFAULT NULL,
                `date` varchar( 30 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_favorites` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `social_id` varchar( 30 ) DEFAULT NULL,
                `name` varchar( 100 ) DEFAULT NULL,
                `type` varchar( 50 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_facebook_likes` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `like_id` varchar( 40 ) DEFAULT NULL,
                `name` varchar( 300 ) DEFAULT NULL,
                `category` varchar( 50 ) DEFAULT NULL,
                `created_date` datetime DEFAULT NULL,
                `website` varchar( 300 ) DEFAULT NULL,
                `description` varchar( 1500 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_facebook_events` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `event_id` varchar( 30 ) NOT NULL,
                `description` varchar( 3000 ),
                `name` varchar( 500 ) NOT NULL,
                `start_time` datetime DEFAULT NULL,
                `end_time` datetime DEFAULT NULL,
                `privacy` varchar( 100 ),
                `rsvp_status` varchar( 50 ) DEFAULT NULL,
                `location` varchar( 100 ) DEFAULT NULL,
                `owner_id` varchar( 100 ),
                `owner_name` varchar( 300 ),
                `updated_date` datetime
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_facebook_posts` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `post_id` varchar( 50 ) NOT NULL,
                `name` varchar( 100 ) DEFAULT NULL,
                `title` varchar( 300 ) DEFAULT NULL,
                `start_time` datetime DEFAULT NULL,
                `update_time` datetime DEFAULT NULL,
                `message` varchar( 2000 ),
                `place` varchar( 50 ) DEFAULT NULL,
                `picture` varchar( 1000 ) DEFAULT NULL,
                `likes` int( 8 ) DEFAULT NULL,
                `shares` int( 8 ) DEFAULT NULL,
                `type` varchar( 50 ) DEFAULT NULL
            )');



            if (in_array('post_id', $wpdb->get_col("DESC " . $wpdb->prefix . 'lr_facebook_posts', 0))) {
                $wpdb->query('ALTER TABLE `' . $wpdb->base_prefix . 'lr_facebook_posts` CHANGE `post_id` `post_ids` varchar(50)');
            }

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_albums` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `album_id` varchar( 40 ) DEFAULT NULL,
                `owner_id` varchar( 40 ) DEFAULT NULL,
                `title` varchar( 100 ) DEFAULT NULL,
                `description` varchar( 1500 ) DEFAULT NULL,
                `location` varchar( 100 ) DEFAULT NULL,
                `type` varchar( 100 ) DEFAULT NULL,
                `created_date` datetime DEFAULT NULL,
                `updated_date` datetime DEFAULT NULL,
                `cover_image_url` varchar( 300 ) DEFAULT NULL,
                `image_count` varchar( 10 ) DEFAULT NULL,
                `directory_url` varchar( 300 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_contacts`( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `social_id` varchar( 255 ) DEFAULT NULL,
                `provider` varchar( 20 ) NOT NULL,
                `name` varchar( 100 ) DEFAULT NULL,
                `email` varchar( 100 ) DEFAULT NULL,
                `phone_number` varchar( 30 ) DEFAULT NULL,
                `profile_url` varchar( 1000 ) DEFAULT NULL,
                `image_url` varchar( 1000 ) DEFAULT NULL,
                `status` varchar( 1500 ) DEFAULT NULL,
                `industry` varchar( 50 ) DEFAULT NULL,
                `country` varchar( 20 ) DEFAULT NULL,
                `gender` varchar( 10 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_groups`( 
                `id` int( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `provider` varchar( 30 ) NOT NULL,
                `country` varchar( 100 ),
                `description` varchar( 1500 ),
                `email` varchar( 300 ),
                `group_id` varchar( 50 ) NOT NULL,
                `image` varchar ( 300 ),
                `logo` varchar ( 300 ),
                `member_count` varchar ( 10 ),
                `name` varchar( 100 ) DEFAULT NULL,
                `postal_code` varchar ( 50 ),
                `type` varchar ( 100 )        
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_status` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `provider` varchar( 20 ) NOT NULL,
                `status_id` varchar( 20 ) NOT NULL,
                `status` varchar( 1500 ),
                `date_time` varchar( 100 ) DEFAULT NULL,
                `likes` int( 8 ) DEFAULT NULL,
                `place` varchar( 100 ) DEFAULT NULL,
                `source` varchar( 500 ) DEFAULT NULL,
                `image_url` varchar( 1000 ) DEFAULT NULL,
                `link_url` varchar( 1000 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_twitter_mentions` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `mention_id` varchar( 30 ) NOT NULL,
                `tweet` varchar( 200 ) DEFAULT NULL,
                `date_time` varchar( 30 ) DEFAULT NULL,
                `likes` int( 8 ) DEFAULT NULL,
                `place` varchar( 100 ) DEFAULT NULL,
                `source` varchar( 300 ) DEFAULT NULL,
                `image_url` varchar( 1000 ) DEFAULT NULL,
                `link_url` varchar( 1000 ) DEFAULT NULL,
                `mentioned_by` varchar( 100 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_linkedin_companies` ( 
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `company_id` varchar( 20 ) NOT NULL,
                `company_name` varchar( 200 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_popup_custom_fields_map` (
                `field_id` int(2) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `field_name` varchar( 40 ) DEFAULT NULL,
                `field_type` varchar( 40 ) DEFAULT NULL,
                `field_title` varchar( 100 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_popup_custom_fields_dropdown` (
                `field_id` int(2) NOT NULL,
                `field_value` varchar( 100 ) DEFAULT NULL
            )');

            $wpdb->query('CREATE TABLE IF NOT EXISTS `' . $wpdb->base_prefix . 'lr_popup_custom_fields_data` (
                `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `wp_users_id` int( 11 ) NOT NULL,
                `field_title` varchar( 100 ) DEFAULT NULL,
                `field_value` varchar( 100 ) DEFAULT NULL
            )');

            // Count number of rows in lr_popup_custom_fields_map table, create 5 new records if count is 0
            $num_custom_fields_map_rows = $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->base_prefix . 'lr_popup_custom_fields_map');
            if ($num_custom_fields_map_rows == 0) {
                for ($i = 0; $i < 5; $i++) {
                    $row_num = $i + 1;
                    $wpdb->insert($wpdb->base_prefix . 'lr_popup_custom_fields_map', array('field_name' => 'field_' . $row_num . '', 'field_type' => 'text', 'field_title' => ''));
                }
            }
        }

    }

    new LR_Social_Profile_Data_Install();
}