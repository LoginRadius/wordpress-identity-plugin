<?php

if (!class_exists('LR_Advanced_Functions')) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Advanced_Functions {

        /**
         * Constructor
         */
        public function __construct() {
            
        }
        public static function login_radius_ucfirst_in_array($element){
            return ucfirst($element);
        }
        /**
         * print mappable fields as checkboxes
         */
        public function login_radius_print_mappable_fields_checkboxes($surveygizmo = true) {
            global $loginRadiusAdvanced;
            $mappingFields = LR_Advanced_Functions::login_radius_get_mapping_fields();
            $mappingFields[] = 'Positions';
            $mappingFields[] = 'Phone Numbers';
            $mappingFields[] = 'Instant Messenger Accounts';
            foreach ($mappingFields as $field) {
                $fieldParts = explode('|', $field);
                if (isset($fieldParts[1])) {
                   echo $fieldParts2 = explode('_', $fieldParts[1]);
                   echo $fieldParts2 = array_map(array('LR_Advanced_Functions','login_radius_ucfirst_in_array'), $fieldParts2);
                    $label = implode(' ', $fieldParts2);
                } else {
                    $label = $fieldParts[0];
                }
                $fieldsToRemove = array('User Id', 'Loginradius Id', 'Thumbnail Image Url', 'Image Url', 'Favicon', 'Verified', 'Created', 'Last Profile Update', 'Https Image Url', 'Is Geo Enabled', 'Honors', 'Hirable', 'Repository Url', 'Provider Access Token', 'Provider Token Secret');
                if (in_array($label, $fieldsToRemove)) {
                    continue;
                }
                echo '<div class="loginRadiusSurveyPrepopulate" ';
                if (in_array($label, array('Relationship Status', 'Followers Count', 'Total Status Count', 'Number Of Recommenders', 'Professional Headline', 'Instant Messenger Accounts'))) {
                    echo 'style="width:500px" ';
                }
                echo '><input style="float:left" type="checkbox"';
                if (isset($loginRadiusAdvanced['surveygizmo_prepopulate_fields']) && in_array($field, $loginRadiusAdvanced['surveygizmo_prepopulate_fields'])) {
                    echo ' checked = "checked"';
                }
                echo ' name="LoginRadius_advanced[surveygizmo_prepopulate_fields][]" value="' . $field . '" /><div style="float:left; width:120px">';
                $fieldParts = explode('|', $field);
                if (isset($fieldParts[1])) {
                    $fieldParts2 = explode('_', $fieldParts[1]);
                    $fieldParts2 = array_map(array('LR_Advanced_Functions','login_radius_ucfirst_in_array'), $fieldParts2);
                    echo implode(' ', $fieldParts2);
                } else {
                    echo $fieldParts[0];
                }
                echo '</div><div class="surveygizmoMergeVar">';
                if ($surveygizmo) {
                    echo '[url( "';
                }
                if (strpos($field, ' ') !== false) {
                    $field = str_replace(' ', '_', $field);
                } elseif (strpos($field, '|') !== false) {
                    $field = str_replace('|', '_', $field);
                }
                echo $field;
                if ($surveygizmo) {
                    echo '" )]';
                }
                echo '</div></div>';
            }
        }

        /**
         * remove duplicate elements from array
         */
        public static function login_radius_remove_duplicate($array, $match) {
            foreach ($match as $key => $val) {
                $fieldParts2 = explode('_', $val);
                if (count($fieldParts2) > 1) {
                    $fieldParts2 = array_map(array('LR_Advanced_Functions','login_radius_ucfirst_in_array'), $fieldParts2);
                    if (in_array(implode(' ', $fieldParts2), $array)) {
                        unset($match[$key]);
                    }
                }
            }
            return $match;
        }

        /**
         * Append prefix "basic|" to the table columns
         */
        public static function login_radius_append_basic($element) {
            return 'basic|' . $element;
        }

        /**
         * Append prefix "exloc|" to the table columns
         */
        public static function login_radius_append_exloc($element) {
            return 'exloc|' . $element;
        }

        /**
         * Append prefix "exprofile|" to the table columns
         */
        public static function login_radius_append_exprofile($element) {
            return 'exprofile|' . $element;
        }

        /**
         * Get mappable profile data fields
         */
        public static function login_radius_get_mapping_fields() {
            global $wpdb;
            // manipulate the list of fields to show for mapping.
            $mappingFields = array('User ID', 'Username', 'First Name', 'Last Name', 'Nicename', 'Email', 'Profile Url', 'Registration Date', 'Display Name', 'Bio','Phone');

            // if basic profile data table exists
            if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_basic_profile_data'") == $wpdb->base_prefix . 'lr_basic_profile_data') {
                $basicProfileColumns = $wpdb->get_col('SHOW COLUMNS FROM ' . $wpdb->base_prefix . 'lr_basic_profile_data');
                $basicProfileColumns = self::login_radius_remove_duplicate($mappingFields, $basicProfileColumns);
                $basicProfileColumns = array_map(array(__CLASS__, 'login_radius_append_basic'), $basicProfileColumns);
                $mappingFields = array_merge($mappingFields, $basicProfileColumns);
            }
            // if extended location data table exists
            if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_extended_location_data'") == $wpdb->base_prefix . 'lr_extended_location_data') {
                $extendedLocationColumns = $wpdb->get_col('SHOW COLUMNS FROM ' . $wpdb->base_prefix . 'lr_extended_location_data');
                $extendedLocationColumns = self::login_radius_remove_duplicate($mappingFields, $extendedLocationColumns);
                $extendedLocationColumns = array_map(array(__CLASS__, 'login_radius_append_exloc'), $extendedLocationColumns);
                $mappingFields = array_merge($mappingFields, $extendedLocationColumns);
            }
            // if extended profile data table exists
            if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_extended_profile_data'") == $wpdb->base_prefix . 'lr_extended_profile_data') {
                $mappingFields[] = 'Company';
                $extendedProfileColumns = $wpdb->get_col('SHOW COLUMNS FROM ' . $wpdb->base_prefix . 'lr_extended_profile_data');
                $extendedProfileColumns = self::login_radius_remove_duplicate($mappingFields, $extendedProfileColumns);
                $extendedProfileColumns = array_map(array(__CLASS__, 'login_radius_append_exprofile'), $extendedProfileColumns);
                $mappingFields = array_merge($mappingFields, $extendedProfileColumns);
            }
            return $mappingFields;
        }

    }

}