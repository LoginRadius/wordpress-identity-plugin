<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Display_Social_Profile_Data' ) ) {

    class LR_Display_Social_Profile_Data {

        /**
         * Construct the plugin object
         */
        public function __construct() {

            add_action( 'admin_menu', array( $this, 'user_admin_menu' ) );
            // ajax to show user profile data
            add_action( 'wp_ajax_lr_get_profile_data', array( $this, 'get_profile_data' ) );

            // shortcode to show social profile data to end-user
            add_shortcode( 'LoginRadius_ProfileData', array( $this, 'get_profile_data' ) );
            add_filter( 'manage_users_columns', array( $this, 'add_provider_column' ) );
            add_action( 'manage_users_custom_column', array( $this, 'show_user_column' ), 10, 3 );
        }

        /**
         * Add provider column on users list page.
         *
         * global $lr_social_profile_data_settings.
         */
        public static function add_provider_column( $columns ) {
            global $lr_social_profile_data_settings;
            if ( ! empty( $lr_social_profile_data_settings['viewProfileData'] ) ) {
                $columns['lr-profile-data'] = 'Profile Data';
            }
            return $columns;
        }

        /**
         * Show social ID provider in the provider column.
         *
         * global lr_social_profile_data_settings
         */
        public function show_user_column( $value, $columnName, $userId ) {
            global $lr_social_profile_data_settings;

            if ( $value != '' ) {
                return $value;
            }

            if ( 'lr-profile-data' == $columnName ) {
                // show profile data column
                if ( ! empty( $lr_social_profile_data_settings['viewProfileData'] ) ) {
                    return '<a href="javascript:void( 0 )" title="Click to view Profile Data" alt="Click to view Profile Data" onclick="javascript:loginRadiusGetProfileData( ' . $userId . ' )">View</a>';
                }
            }
        }

        /**
         * Show user profile data.
         *
         */
        public static function show_data( $array, $subTable = false ) {
            $html = '';
            if ( $subTable ) {
                $html .= '<tfoot>';
                $count = 1;
                foreach ($array as $temp) {
                    $html .= '<tr ';
                    if ( ( $count % 2 ) == 0 ) {
                        $html .= 'style="background-color:#eaeaea"';
                    }
                    $html .= '>';
                    foreach ($temp as $key => $val) {
                        if ($key == 'id') {
                            continue;
                        } elseif ( $key == 'wp_users_id' ) {
                            continue;
                        } else {
                            $html .= '<th scope="col" class="manage-colum loginRadiusColumn">' . ucfirst($val) . '</th>';
                        }
                    }
                    $html .= '</tr>';
                    $count++;
                }
                $html .= '</tfoot>';
            } else {
                $html .= '<table class="wp-list-table widefat fixed users loginRadiusImagetable" cellspacing="0"><tfoot>';
                $count = 1;
                if (isset($array[0])) {
                    foreach ($array[0] as $key => $value) {

                        if ($value != '') {
                            if ($key == 'id') {
                                continue;
                            }
                            if ($key == 'wp_users_id') {
                                continue;
                            }
                            $html .= '<tr ';
                            if (( $count % 2 ) == 0) {
                                $html .= 'style="background-color:#eaeaea"';
                            }
                            $html .= '>';
                            $keyParts = explode( '_', $key );
                            $keyParts = array_map( array( 'LR_Advanced_Functions','login_radius_ucfirst_in_array' ), $keyParts );
                            $html .= '<th scope="col" class="manage-colum">' . ( count( $keyParts ) > 1 ? implode(' ', $keyParts) : ucfirst( $key ) ) . '</th><th scope="col" class="manage-colum loginRadiusColumn">' . ucfirst($value) . '</th></tr>';
                            $count++;
                        }
                    }
                }
                $html .= '</tfoot></table>';
            }
            return $html;
        }

        /**
         * Prepare profile data html to print
         */
        public static function prepare_profile_data( $userId ) {
            global $wpdb, $lr_social_profile_data_settings;
            $noProfileData = true;
            $html = '<div class="menu_div" id="login_radius_profile_tabs"><ul>';

            if( ! empty( $lr_social_profile_data_settings['showprofiledata'] ) ) {
                // Display Basic Profile Data #tab-1
                if ( is_super_admin() || isset( $userId ) && in_array( 'basic', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // basic profile data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_basic_profile_data'" ) == $wpdb->base_prefix . 'lr_basic_profile_data' ) {
                        $basicProfileResult = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_basic_profile_data` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $basicProfileResult ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-1">' . __( 'Basic Profile Data', 'lr-plugin-slug' ) . '</a></li>';
                        }
                    }
                }

                // Display Extended Location Data #tab-2
                if ( is_super_admin() || isset( $userId ) && in_array( 'exlocation', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // extended location data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_extended_location_data'" ) == $wpdb->base_prefix . 'lr_extended_location_data' ) {
                        $extendedLocationResult = $wpdb->get_results( $wpdb->prepare('SELECT * FROM `' . $wpdb->base_prefix . 'lr_extended_location_data` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $extendedLocationResult ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-2">' . __('Extended Location Data', 'lr-plugin-slug') . '</a></li>';
                        }
                    }
                }

                // Display Extended Profile Data Tab. #tabs-3
                if ( is_super_admin() || isset( $userId ) && in_array( 'extended', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // extended profile data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_extended_profile_data'" ) == $wpdb->base_prefix . 'lr_extended_profile_data' ) {
                        $extendedProfileResult = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_extended_profile_data` WHERE wp_users_id = %d', $userId));
                        if ( count( $extendedProfileResult ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-3">' . __('Extended Profile Data', 'lr-plugin-slug') . '</a></li>';
                        }
                    }
                }

                // Display Contacts Data Tab. #tabs-4
                if ( is_super_admin() || isset( $userId ) && in_array( 'contacts', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // extended profile data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_contacts'" ) == $wpdb->base_prefix . 'lr_contacts' ) {
                        $contacts = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_contacts` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $contacts ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-4">' . __('Contacts', 'lr-plugin-slug') . '</a></li>';
                        }
                    }
                }

                // linkedin_companies Companies Data Tab. #tabs-5
                if ( is_super_admin() || isset( $userId ) && in_array( 'linkedin_companies', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // extended profile data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_linkedin_companies'") == $wpdb->base_prefix . 'lr_linkedin_companies' ) {
                        $linkedin = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_linkedin_companies` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $linkedin ) > 0) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-5">' . __( 'LinkedIn Companies', 'lr-plugin-slug' ) . '</a></li>';
                        }
                    }
                }

                // Status Data Tab. #tabs-6
                if ( is_super_admin() || isset( $userId ) && in_array( 'status', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // extended profile data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_status'" ) == $wpdb->base_prefix . 'lr_status' ) {
                        $status = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_status` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $status ) > 0) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-6">' . __( 'Status', 'lr-plugin-slug' ) . '</a></li>';
                        }
                    }
                }

                // Mentions Data Tab. #tabs-7
                if ( is_super_admin() || isset( $userId ) && in_array( 'mentions', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // extended profile data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_twitter_mentions'" ) == $wpdb->base_prefix . 'lr_twitter_mentions' ) {
                        $mentions = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_twitter_mentions` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $mentions ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-7">' . __( 'Twitter Mentions', 'lr-plugin-slug' ) . '</a></li>';
                        }
                    }
                }

                // Groups Data Tab. #tabs-8
                if ( is_super_admin() || isset( $userId ) && in_array( 'groups', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // extended profile data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_groups'" ) == $wpdb->base_prefix . 'lr_groups' ) {
                        $groups = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_groups` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $groups ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-8">' . __( 'Groups', 'lr-plugin-slug' ) . '</a></li>';
                        }
                    }
                }

                // FaceBook like data #tabs-9
                if ( is_super_admin() || isset( $userId ) && in_array( 'likes', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // FaceBook like data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_facebook_likes'" ) == $wpdb->base_prefix . 'lr_facebook_likes' ) {
                        $likes = $wpdb->get_results( $wpdb->prepare('SELECT * FROM `' . $wpdb->base_prefix . 'lr_facebook_likes` WHERE wp_users_id = %d', $userId ) );
                        if ( count($likes) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-9">' . __( 'FaceBook Likes', 'lr-plugin-slug') . '</a></li>';
                        }
                    }
                }

                // FaceBook events #tabs-10
                if ( is_super_admin() || isset( $userId ) && in_array( 'events', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // FaceBook like data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_facebook_events'" ) == $wpdb->base_prefix . 'lr_facebook_events' ) {
                        $events = $wpdb->get_results( $wpdb->prepare('SELECT * FROM `' . $wpdb->base_prefix . 'lr_facebook_events` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $events ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-10">' . __( 'FaceBook Events', 'lr-plugin-slug') . '</a></li>';
                        }
                    }
                }

                // FaceBook posts #tabs-11
                if ( is_super_admin() || isset( $userId ) && in_array( 'posts', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // FaceBook like data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_facebook_posts'" ) == $wpdb->base_prefix . 'lr_facebook_posts' ) {
                        $posts = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_facebook_posts` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $posts ) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-11">' . __( 'FaceBook Posts', 'lr-plugin-slug' ) . '</a></li>';
                        }
                    }
                }

                // FaceBook Albums data #tabs-12
                if ( is_super_admin() || isset( $userId ) && in_array( 'albums', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    //Albums Data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_albums'" ) == $wpdb->base_prefix . 'lr_albums' ) {
                        $albums = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_albums` WHERE wp_users_id = %d', $userId ) );
                        if ( count($albums) > 0 ) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-12">' . __( 'Albums', 'lr-plugin-slug') . '</a></li>';
                        }
                    }
                }

                // Display Custom Fields Popup Data. #tabs-13
                if ( is_super_admin() || isset( $userId ) && in_array( 'custom_fields', $lr_social_profile_data_settings['showprofiledata'] ) ) {
                    // Custom fields data
                    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_popup_custom_fields_data'" ) == $wpdb->base_prefix . 'lr_popup_custom_fields_data' ) {
                        $customfields = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_popup_custom_fields_data` WHERE wp_users_id = %d', $userId ) );
                        if ( count( $customfields ) > 0) {
                            $noProfileData = false;
                            $html .= '<li style="float: left; list-style: none;"><a style="margin:0; font-size:12px; font-weight:bold" class="nav-tab" href="#tabs-13">' . __( 'Custom Fields Data', 'lr-plugin-slug' ) . '</a></li>';
                        }
                    }
                }
            } else {
                error_log( 'LoginRadius Display Profile Data settings have not been saved properly. Please select and save the Display Profile Data settings' );
            }

            $html .= '</ul>';
            if ( $noProfileData ) {
                $html .= '<strong>' . __( 'Profile data not found.', 'lr-plugin-slug' ) . '</strong>';
            }

            if ( isset( $basicProfileResult ) && count( $basicProfileResult ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'basic', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-1"><table class="wp-list-table widefat fixed users loginRadiusImagetable" cellspacing="0"><tfoot>';
                $count = 1;
                if ( isset( $basicProfileResult[0] ) ) {
                    foreach ( $basicProfileResult[0] as $key => $value ) {
                        if ( $value != '' ) {
                            if ( $key == 'wp_users_id' ) {
                                //continue;
                                $key = 'Wordpress User ID';
                            } elseif ( $key == 'id' ) {
                                continue;
                            } elseif ( $key == 'social_id' ) {
                                $key = 'Social ID';
                            } elseif ( $key == 'birth_date' ) {
                                $key .= ' (mm-dd-yyyy)';
                                
                                $value = explode( ' ', $value );

                                if( '0000-00-00' != $value[0] ) {
                                    $value = strtotime( $value[0] );
                                    $value = date( 'm-d-Y', $value );
                                } else {
                                    $value = 'Unknown';
                                }
                            }
                            $html .= '<tr ';
                            if ( ( $count % 2 ) == 0 ) {
                                $html .= 'style="background-color:#eaeaea"';
                            }
                            $html .= '>';
                            $keyParts = explode( '_', $key );
                            $keyParts = array_map( array('LR_Advanced_Functions','login_radius_ucfirst_in_array' ), $keyParts );
                            $html .= '<th scope="col" class="manage-colum">';
                            count( $keyParts ) > 1 ? $html .= implode( ' ', $keyParts ) : $html .= ucfirst( $key );
                            $html .= '</th><th scope="col" class="manage-colum loginRadiusColumn">' . ucfirst( $value ) . '</th></tr>';
                            $count++;
                        }
                    }
                }

                // Emails
                $rows = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `' . $wpdb->base_prefix . 'lr_emails` WHERE wp_users_id = %d', $userId ) );
                if ( count( $rows ) > 0) {
                    $html .= '<tr ';
                    if ( ( $count % 2 ) == 0 ) {
                        $html .= 'style="background-color:#eaeaea"';
                    }
                    $html .= '><th scope="col" class="manage-colum">' . __( 'Email', 'lr-plugin-slug' ) . '</th>';
                    foreach ( $rows as $row ) {
                        $html .= '<th scope="col" class="manage-colum loginRadiusColumn">' . $row->email;
                        if ($row->email_type != '') {
                            $html .= ' ( ' . ucfirst($row->email_type) . ' )';
                        }
                        $html .= '</th>';
                    }
                    $html .= '</tr>';
                }
                $html .= '</tfoot></table></div>';
            }

            // Extended Location
            if ( isset( $extendedLocationResult ) && count( $extendedLocationResult ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'exlocation', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-2">';
                $html .= self::show_data( $extendedLocationResult );
                $html .= '</div>';
            }

            // Extended Profile Data
            if ( isset( $extendedProfileResult ) && count( $extendedProfileResult ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'extended', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-3"><div class="wrap">';
                $html .= self::show_data( $extendedProfileResult );
                $html .= '</div></div>';
            }

            if ( isset( $contacts ) && count( $contacts ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'contacts', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-4"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col"><strong>' . __('Social ID', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Provider', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Name', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Email', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Phone Number', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Profile URL', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Image URL', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Status', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Industry', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Country', 'lr-plugin-slug') . '</strong></th>
                                <th scope="col"><strong>' . __('Gender', 'lr-plugin-slug') . '</strong></th>
                            </tr>
                        </thead>';
                $html .= self::show_data($contacts, true);
                $html .= '</table>';
                $html .= '</div></div>';
            }

            if ( isset( $linkedin ) && count( $linkedin ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'linkedin_companies', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-5"><div class="wrap">';
                $html .= self::show_data( $linkedin );
                $html .= '</div></div>';
            }

            if ( isset( $status ) && count( $status ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'status', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-6"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col"><strong>' . __( 'Provider', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Status ID', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Status', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Date', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Likes', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Place', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Image URL', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Link URL', 'lr-plugin-slug' ) . '</strong></th>
                            </tr>
                        </thead>';
                $html .= self::show_data( $status , true );
                $html .= '</table>';
                $html .= '</div></div>';
            }

            // Twitter Mentions data #tabs-7
            if ( isset( $mentions ) && count( $mentions ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'mentions', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-7"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col"><strong>' . __( 'Mention ID', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Tweet', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Date', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Likes', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Place', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Source', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Image URL', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Link URL', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Mentioned By', 'lr-plugin-slug' ) . '</strong></th>
                            </tr>
                        </thead>';
                $html .= self::show_data( $mentions, true );
                $html .= '</table>';
                $html .= '</div></div>';
            }

            // Groups
            if ( isset( $groups ) && count( $groups ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'groups', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-8"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col"><strong>' . __( 'Provider', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Country', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Description', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Email', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Group ID', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Image', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Logo', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Member Count', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Name', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Postal Code', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Type', 'lr-plugin-slug' ) . '</strong></th>
                            </tr>
                        </thead>';
                $html .= self::show_data( $groups, true );
                $html .= '</table>';
                $html .= '</div></div>';
            }

            // FaceBook Like Data #tabs-9
            if ( isset( $likes ) && count( $likes ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'likes', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-9"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                        <thead>
                            <tr>
                                <th scope="col"><strong>' . __( 'Like ID', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Name', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Category', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Created Date', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Website', 'lr-plugin-slug' ) . '</strong></th>
                                <th scope="col"><strong>' . __( 'Description', 'lr-plugin-slug' ) . '</strong></th>
                            </tr>
                        </thead>';
                $html .= self::show_data($likes, true);
                $html .= '</table>';
                $html .= '</div></div>';
            }

            // FaceBook Events #tabs-10
            if ( isset( $events ) && count( $events ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'events', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-10"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col"><strong>' . __( 'Event ID', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Description', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Name', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Start Time', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'End Time', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Privacy', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'RSVP Status', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Location', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Owner ID', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Owner Name', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Updated Time', 'lr-plugin-slug' ) . '</strong></th>
                                </tr>
                            </thead>';
                $html .= self::show_data( $events, true );
                $html .= '</table>';
                $html .= '</div></div>';
            }

            // FaceBook Posts #tabs-11
            if ( isset( $posts ) && count( $posts ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'posts', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-11"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col"><strong>' . __( 'Post ID', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Name', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Title', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Start Time', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Update Time', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Message', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Place', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Picture', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Likes', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Shares', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Type', 'lr-plugin-slug' ) . '</strong></th>
                                </tr>
                            </thead>';
                $html .= self::show_data( $posts, true );
                $html .= '</table>';
                $html .= '</div></div>';
            }

            // FaceBook Albums data #tabs-12
            if ( isset( $albums ) && count( $albums ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'albums', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-12"><div class="wrap">';
                $html .= '<table class="wp-list-table widefat users loginRadiusImagetable" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col"><strong>' . __( 'Album ID', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Owner ID', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Title', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Description', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Location', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Type', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Created Date', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Updated Date', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Cover Image Url', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Image Count', 'lr-plugin-slug' ) . '</strong></th>
                                    <th scope="col"><strong>' . __( 'Directory Url', 'lr-plugin-slug' ) . '</strong></th>
                                </tr>
                            </thead>';
                $html .= self::show_data($albums, true);
                $html .= '</table>';
                $html .= '</div></div>';
            }

            // Custom Fields data #tabs-13
            if ( isset( $customfields ) && count( $customfields ) > 0 && ( is_super_admin() || isset( $userId ) && in_array( 'custom_fields', $lr_social_profile_data_settings['showprofiledata'] ) ) ) {
                $html .= '<div class="menu_containt_div" id="tabs-13">';
                $html .= self::show_data( $customfields );
                $html .= '</div>';
            }

            $html .= '</div>
                    <script type="text/javascript">
                    // initialize tabs
                    function login_radius_initialize_tabs(){
                        if ( typeof jQuery.ui != \'undefined\' && jQuery.isFunction(jQuery.fn.tabs) ) {
                                jQuery.noConflict();
                                jQuery( \'#login_radius_profile_tabs\' ).tabs();
                        }else {
                                // load jQuery UI dynamically
                                login_radius_load_jquery( \'http://code.jquery.com/ui/1.10.3/jquery-ui.min.js\', login_radius_initialize_tabs );
                        }
                    }';
            if ( isset( $userId ) ) {
                $html .= 'login_radius_initialize_tabs();';
            } else {
                $html .= 'window.onload = function(){
                        if ( typeof jQuery != \'undefined\' ){
                            // ajax to authenticate user
                            login_radius_initialize_tabs();
                        }else {
                            // load jQuery dynamically
                            login_radius_load_jquery( \'http://code.jquery.com/jquery-latest.min.js\', login_radius_initialize_tabs );
                        }
                }';
                }
                $html .= '// load jquery library
                function login_radius_load_jquery( url, success ){
                    var script = document.createElement( \'script\' );
                    script.src = url;
                    var head = document.getElementsByTagName( \'head\' )[0],
                            done = false;
                    // Attach handlers for all browsers
                    script.onload = script.onreadystatechange = function() {
                      if ( !done && ( !this.readyState
                               || this.readyState == \'loaded\'
                               || this.readyState == \'complete\' ) ) {
                            done = true;
                            success();
                            script.onload = script.onreadystatechange = null;
                            head.removeChild( script );
                      }
                    };
                    head.appendChild( script );
                }';
            $html .= '</script>';
            return $html;
        }

        // get user profile data
        public function get_profile_data() {
            global $pagenow;

            if ( isset( $_GET['user_id'] ) ) {
                $userId = trim( $_GET['user_id'] );
            } else {
                global $user_ID;
                $userId = $user_ID;
                if ( $userId < 1 ) {
                    return 'Please login to see your profile data';
                }
            }
            if ( isset( $_GET['user_id'] ) || $pagenow == 'admin.php' ) {
                echo self::prepare_profile_data( $userId );
            } else {
                wp_enqueue_style( 'lr_profile_data_front_style', LR_SOCIAL_PROFILE_DATA_URL . 'assets/css/lr_profile_data_front_style.css' );
                return self::prepare_profile_data( $userId );
            }
            die;
        }

        /**
         * Add the LoginRadius menu in the left sidebar in the admin
         */
        public function user_admin_menu() {
            global $lr_social_profile_data_settings;
            if ( isset( $lr_social_profile_data_settings['display_UserProfileData'] ) && $lr_social_profile_data_settings['display_UserProfileData'] == 1 && ! is_super_admin() ) {
                add_menu_page( 'LoginRadiusProfile', 'Social Profile Data', 'read', 'loginradius-profile', array( $this, 'get_profile_data' ), LR_CORE_URL . 'assets/images/favicon.ico' );
            }
        }

    }

}

