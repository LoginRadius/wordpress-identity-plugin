<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The commenting admin settings page.
 */
if ( ! class_exists( 'LR_Social_Profile_Data_Admin_Settings' ) ) {

    class LR_Social_Profile_Data_Admin_Settings {

        public static function render_options_page() {
            global $wpdb;
            
            if ( isset( $_POST['reset'] ) ) {
                LR_Social_Profile_Data_Install::reset_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Social Profile Data settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
            $lr_social_profile_data_settings = get_option('LoginRadius_Social_Profile_Data_settings');

            // Dropdown table name - used for dropdown messages.
            $dropdown_table = $wpdb->base_prefix . "lr_popup_custom_fields_dropdown";
            ?>
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Social Profile Data</em></h2>
                </header>

                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields( 'lr_social_profile_data_settings' );
                        settings_errors();
                        ?>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Profile Data Options', 'lr-plugin-slug'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-display-admin-profile-data" name="LoginRadius_Social_Profile_Data_settings[viewProfileData]" value='1' <?php echo( isset( $lr_social_profile_data_settings['viewProfileData'] ) && $lr_social_profile_data_settings['viewProfileData'] == '1' ) ? 'checked' : ''; ?> />

                                    <label class="lr-show-toggle" for="lr-display-admin-profile-data">
                                        <?php _e( 'Enable this option to show the view profile data option in ', 'lr-plugin-slug' ); ?><a href="<?php echo get_admin_url() ?>users.php" target="_self" ><?php _e( 'WordPress Users List?', 'lr-plugin-slug' ); ?></a>
                                        <span class="lr-tooltip" data-title="<?php _e( 'If enabled, a link will be added to each user in the sites User List to view the saved data collected.', 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>

                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-display-profile-data" name="LoginRadius_Social_Profile_Data_settings[display_UserProfileData]" value="1" <?php echo isset( $lr_social_profile_data_settings['display_UserProfileData'] ) && $lr_social_profile_data_settings['display_UserProfileData'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-display-profile-data">
                                        <?php _e( 'Enable this option to display a profile data link to your website users in their account dashboard?', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'If enabled, registered users will see a list of their saved pofile data on the profile page in the WordPress admin panel.', 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="lr_options_container">

                            <div class="lr-row inline-block vertical-align-top inline-width-50">
                                <h3>
                                    <?php _e( 'Save Profile Data', 'lr-plugin-slug' ); ?>
                                </h3>
                                <h5>
                                    <?php _e( 'Please select the user profile data fields you would like to save in your database:', 'lr-plugin-slug' ); ?>
                                </h5>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-basic" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="basic" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'basic', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-basic">
                                        <?php _e('Basic Profile Data', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-extended" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="extended" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'extended', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-extended">
                                        <?php _e('Extended Profile Data', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-extended-location" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="exlocation" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'exlocation', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-extended-location">
                                        <?php _e('Extended Location Data', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-likes" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="likes" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'likes', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-likes">
                                        <?php _e('Likes', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-albums" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="albums" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'albums', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-albums">
                                        <?php _e('Albums', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-mentions" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="mentions" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'mentions', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-mentions">
                                        <?php _e('Mentions', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-groups" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="groups" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'groups', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-groups">
                                        <?php _e('Groups', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-events" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="events" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'events', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-events">
                                        <?php _e('Events', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-posts" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="posts" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'posts', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-posts">
                                        <?php _e('Posts', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                 <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-contacts" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="contacts" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'contacts', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-contacts">
                                        <?php _e('Contacts', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-linkedincompanies" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="linkedin_companies" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'linkedin_companies', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-linkedincompanies">
                                        <?php _e('Companies', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-profile-data-status" name="LoginRadius_Social_Profile_Data_settings[profiledata][]" value="status" <?php echo isset( $lr_social_profile_data_settings['profiledata'] ) && in_array( 'status', $lr_social_profile_data_settings['profiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-profile-data-status">
                                        <?php _e( 'Status', 'lr-plugin-slug' ); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="lr-row inline-block vertical-align-top inline-width-50">
                                <h3>
                                    <?php _e('Display Profile Data', 'lr-plugin-slug'); ?>
                                </h3>
                                <h5>
                                    <?php _e('Please select the user profile data fields you would like to display:', 'lr-plugin-slug'); ?>
                                </h5>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-basic" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="basic" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'basic', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-basic">
                                        <?php _e('Basic Profile Data', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-extended" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="extended" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'extended', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-extended">
                                        <?php _e('Extended Profile Data', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-extended-location" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="exlocation" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'exlocation', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-extended-location">
                                        <?php _e('Extended Location Data'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-likes" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="likes" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'likes', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-likes">
                                        <?php _e('Likes', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-albums" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="albums" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'albums', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-albums">
                                        <?php _e('Albums', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-mentions" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="mentions" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'mentions', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-mentions">
                                        <?php _e('Mentions', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-groups" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="groups" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'groups', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-groups">
                                        <?php _e('Groups', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-events" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="events" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'events', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-events">
                                        <?php _e('Events', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-posts" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="posts" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'posts', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-posts">
                                        <?php _e('Posts', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-contacts" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="contacts" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'contacts', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-contacts">
                                        <?php _e('Contacts', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-linkedin_companies" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="linkedin_companies" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'linkedin_companies', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-profile-data-linkedin_companies">
                                        <?php _e('Companies', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-show-status" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="status" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'status', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-show-status">
                                        <?php _e('Status', 'lr-plugin-slug'); ?>
                                    </label>
                                </div>
                                <?php if ( class_exists( 'LR_Social_Login' ) && ! class_exists( 'LR_Raas_Install' ) ) { ?>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="lr-show-profile-data-custom-fields" name="LoginRadius_Social_Profile_Data_settings[showprofiledata][]" value="custom_fields" <?php echo isset( $lr_social_profile_data_settings['showprofiledata'] ) && in_array( 'custom_fields', $lr_social_profile_data_settings['showprofiledata'] ) ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle" for="lr-show-profile-data-custom-fields">
                                            <?php _e('Custom Popup Fields Data'); ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="lr-row">
                                <h5>
                                    <?php _e('Please visit the following section to see the complete listing of supported Data points using LoginRadius API: ', 'lr-plugin-slug'); ?><a href="www.loginradius.com/datapoints">here</a>
                                </h5>
                            </div>
                        </div>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3><?php _e('Profile Data Short Code', 'lr-plugin-slug'); ?>
                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e( 'Use the following shortcode on a page or post to display the logged in users profile data', 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </h3>
                                <div>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[LoginRadius_ProfileData]</textarea>
                                </div>
                            </div><!-- lr-row -->
                        </div>

                        <?php if ( class_exists( 'LR_Social_Login' ) && ! class_exists( 'LR_Raas_Install' ) ) { ?>
                            <div class="lr_options_container">
                                <div class="lr-row cf">	
                                    <div>
                                        <h3>
                                            <?php _e('Custom Popup Settings', 'lr-plugin-slug'); ?>
                                        </h3>
                                        <input type="checkbox" class="lr-toggle" id="lr-enable-custom-popup" name="LoginRadius_Social_Profile_Data_settings[enable_custom_popup]" value="1" <?php echo isset($lr_social_profile_data_settings['enable_custom_popup']) && $lr_social_profile_data_settings['enable_custom_popup'] == '1' ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle custom-options-header" for="lr-enable-custom-popup">
                                            <?php _e('Enable Custom Popup'); ?>
                                            <span class="lr-tooltip tip-bottom" data-title="<?php _e('Enable a custom popup with the selected fields to obtain extra data not provided by the social provider', 'lr-plugin-slug'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>

                                        <div class="custom-options-frame">
                                            <h5 class="lr-custom-popup-settings">
                                                <?php _e('Default Popup Fields', 'lr-plugin-slug'); ?>
                                            </h5>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-email-field" name="LoginRadius_Social_Profile_Data_settings[show_email]" value="1" <?php echo isset($lr_social_profile_data_settings['show_email']) && $lr_social_profile_data_settings['show_email'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-email-field">
                                                    <?php _e('Email', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show email field if blank', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-gender-field" name="LoginRadius_Social_Profile_Data_settings[show_gender]" value="1" <?php echo isset($lr_social_profile_data_settings['show_gender']) && $lr_social_profile_data_settings['show_gender'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-gender-field">
                                                    <?php _e('Gender', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show gender field if blank', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-birthdate-field" name="LoginRadius_Social_Profile_Data_settings[show_birthdate]" value="1" <?php echo isset($lr_social_profile_data_settings['show_birthdate']) && $lr_social_profile_data_settings['show_birthdate'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-birthdate-field">
                                                    <?php _e('Birthdate', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show birthdate field if blank', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-phonenumber-field" name="LoginRadius_Social_Profile_Data_settings[show_phonenumber]" value="1" <?php echo isset($lr_social_profile_data_settings['show_phonenumber']) && $lr_social_profile_data_settings['show_phonenumber'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-phonenumber-field">
                                                    <?php _e('Phone Number', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show phone number field if blank', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-city-field" name="LoginRadius_Social_Profile_Data_settings[show_city]" value="1" <?php echo isset($lr_social_profile_data_settings['show_city']) && $lr_social_profile_data_settings['show_city'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-city-field">
                                                    <?php _e('City', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show city field if blank', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-postalcode-field" name="LoginRadius_Social_Profile_Data_settings[show_postalcode]" value="1" <?php echo isset($lr_social_profile_data_settings['show_postalcode']) && $lr_social_profile_data_settings['show_postalcode'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-postalcode-field">
                                                    <?php _e('Postal Code/Zip Code', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show postalcode/zipcode field if blank', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-relationshipstatus-field" name="LoginRadius_Social_Profile_Data_settings[show_relationshipstatus]" value="1" <?php echo isset($lr_social_profile_data_settings['show_relationshipstatus']) && $lr_social_profile_data_settings['show_relationshipstatus'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-relationshipstatus-field">
                                                    <?php _e('Relationship Status', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show relationship status field if blank', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="custom-options-frame">
                                            <h5 class="lr-custom-popup-settings">
                                                <?php _e('Custom Popup Custom Fields', 'lr-plugin-slug'); ?>
                                            </h5>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-custom-field-one" name="LoginRadius_Social_Profile_Data_settings[show_custom_one]" value="1" <?php echo isset($lr_social_profile_data_settings['show_custom_one']) && $lr_social_profile_data_settings['show_custom_one'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-custom-field-one">
                                                    <?php _e('Custom Field One', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show custom field one', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field One Title', 'lr-plugin-slug'); ?>
                                                    <input type="text" name="LoginRadius_Social_Profile_Data_settings[custom_one_title]" placeholder="Custom Field One Name" value="<?php echo isset($lr_social_profile_data_settings['custom_one_title']) ? $lr_social_profile_data_settings['custom_one_title'] : ''; ?>" />
                                                </span>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field One Field Type', 'lr-plugin-slug'); ?>
                                                    <select name="LoginRadius_Social_Profile_Data_settings[custom_one_type]" value>
                                                        <option value="text" <?php echo (isset($lr_social_profile_data_settings['custom_one_type']) && $lr_social_profile_data_settings['custom_one_type'] == "text") ? "selected" : ""; ?> >Text</option>
                                                        <option value="dropdown" <?php echo (isset($lr_social_profile_data_settings['custom_one_type']) && $lr_social_profile_data_settings['custom_one_type'] == "dropdown") ? "selected" : ""; ?>>Dropdown</option>
                                                    </select>
                                                    <span class="dropdown_msg">
                                                        <?php $dropdown_id = $wpdb->get_var($wpdb->prepare("SELECT field_id FROM " . $wpdb->base_prefix . "lr_popup_custom_fields_map WHERE field_name = %s", 'field_1')); ?>
                                                        <?php _e('Insert dropdown values into table ', 'lr-plugin-slug'); ?><b><?php echo $dropdown_table; ?></b> <?php _e('with a field_id of ', 'lr-plugin-slug'); ?><b><?php echo $dropdown_id; ?></b>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-custom-field-two" name="LoginRadius_Social_Profile_Data_settings[show_custom_two]" value="1" <?php echo isset($lr_social_profile_data_settings['show_custom_two']) && $lr_social_profile_data_settings['show_custom_two'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-custom-field-two">
                                                    <?php _e('Custom Field Two', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show custom field two', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>

                                                </label>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field Two Title', 'lr-plugin-slug'); ?>
                                                    <input type="text" name="LoginRadius_Social_Profile_Data_settings[custom_two_title]" placeholder="Custom Field Two Name" value="<?php echo isset($lr_social_profile_data_settings['custom_two_title']) ? $lr_social_profile_data_settings['custom_two_title'] : ''; ?>" />
                                                </span>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field Two Field Type', 'lr-plugin-slug'); ?>
                                                    <select name="LoginRadius_Social_Profile_Data_settings[custom_two_type]">
                                                        <option value="text" <?php echo (isset($lr_social_profile_data_settings['custom_two_type']) && $lr_social_profile_data_settings['custom_two_type'] == "text") ? "selected" : ""; ?> >Text</option>
                                                        <option value="dropdown" <?php echo (isset($lr_social_profile_data_settings['custom_two_type']) && $lr_social_profile_data_settings['custom_two_type'] == "dropdown") ? "selected" : ""; ?>>Dropdown</option>
                                                    </select>
                                                    <span class="dropdown_msg">
                                                        <?php $dropdown_id = $wpdb->get_var($wpdb->prepare("SELECT field_id FROM " . $wpdb->base_prefix . "lr_popup_custom_fields_map WHERE field_name = %s", 'field_2')); ?>
                                                        <?php _e('Insert dropdown values into table ', 'lr-plugin-slug'); ?><b><?php echo $dropdown_table; ?></b><?php _e(' with a field_id of ', 'lr-plugin-slug'); ?><b><?php echo $dropdown_id; ?></b>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-custom-field-three" name="LoginRadius_Social_Profile_Data_settings[show_custom_three]" value="1" <?php echo isset($lr_social_profile_data_settings['show_custom_three']) && $lr_social_profile_data_settings['show_custom_three'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-custom-field-three">
                                                    <?php _e('Custom Field Three', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show custom field three', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>

                                                </label>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field Three Title', 'lr-plugin-slug'); ?>
                                                    <input type="text" name="LoginRadius_Social_Profile_Data_settings[custom_three_title]" placeholder="Custom Field Three Name" value="<?php echo isset($lr_social_profile_data_settings['custom_three_title']) ? $lr_social_profile_data_settings['custom_three_title'] : ''; ?>" />
                                                </span>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field Three Field Type', 'lr-plugin-slug'); ?>
                                                    <select name="LoginRadius_Social_Profile_Data_settings[custom_three_type]">
                                                        <option value="text" <?php echo (isset($lr_social_profile_data_settings['custom_three_type']) && $lr_social_profile_data_settings['custom_three_type'] == "text") ? "selected" : ""; ?> >Text</option>
                                                        <option value="dropdown" <?php echo (isset($lr_social_profile_data_settings['custom_three_type']) && $lr_social_profile_data_settings['custom_three_type'] == "dropdown") ? "selected" : ""; ?>>Dropdown</option>
                                                    </select>
                                                    <span class="dropdown_msg">
                                                        <?php $dropdown_id = $wpdb->get_var($wpdb->prepare("SELECT field_id FROM " . $wpdb->base_prefix . "lr_popup_custom_fields_map WHERE field_name = %s", 'field_3')); ?>
                                                        <?php _e('Insert dropdown values into table ', 'lr-plugin-slug'); ?><b><?php echo $dropdown_table; ?></b><?php _e(' with a field_id of ', 'lr-plugin-slug'); ?><b><?php echo $dropdown_id; ?></b>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-custom-field-four" name="LoginRadius_Social_Profile_Data_settings[show_custom_four]" value="1" <?php echo isset($lr_social_profile_data_settings['show_custom_four']) && $lr_social_profile_data_settings['show_custom_four'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-custom-field-four">
                                                    <?php _e('Custom Field Four', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show custom field four', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>

                                                </label>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field Four Title', 'lr-plugin-slug'); ?>
                                                    <input type="text" name="LoginRadius_Social_Profile_Data_settings[custom_four_title]" placeholder="Custom Field Four Name" value="<?php echo isset($lr_social_profile_data_settings['custom_four_title']) ? $lr_social_profile_data_settings['custom_four_title'] : ''; ?>" />
                                                </span>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field Four Field Type', 'lr-plugin-slug'); ?>
                                                    <select name="LoginRadius_Social_Profile_Data_settings[custom_four_type]">
                                                        <option value="text" <?php echo ( isset( $lr_social_profile_data_settings['custom_four_type'] ) && $lr_social_profile_data_settings['custom_four_type'] == "text" ) ? "selected" : ""; ?> >Text</option>
                                                        <option value="dropdown" <?php echo ( isset( $lr_social_profile_data_settings['custom_four_type'] ) && $lr_social_profile_data_settings['custom_four_type'] == "dropdown" ) ? "selected" : ""; ?>>Dropdown</option>
                                                    </select>
                                                    <span class="dropdown_msg">
                                                        <?php $dropdown_id = $wpdb->get_var( $wpdb->prepare( "SELECT field_id FROM " . $wpdb->base_prefix . "lr_popup_custom_fields_map WHERE field_name = %s", 'field_4' ) ); ?>
                                                        <?php _e( 'Insert dropdown values into table ', 'lr-plugin-slug' ); ?><b><?php echo $dropdown_table; ?></b><?php _e(' with a field_id of ', 'lr-plugin-slug'); ?><b><?php echo $dropdown_id; ?></b>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="lr-custom-popup-settings">
                                                <input type="checkbox" class="lr-toggle lr-custom-popup-options" id="lr-show-custom-field-five" name="LoginRadius_Social_Profile_Data_settings[show_custom_five]" value="1" <?php echo isset($lr_social_profile_data_settings['show_custom_five']) && $lr_social_profile_data_settings['show_custom_five'] == '1' ? 'checked' : ''; ?> />
                                                <label class="lr-show-toggle" for="lr-show-custom-field-five">
                                                    <?php _e( 'Custom Field Five', 'lr-plugin-slug' ); ?>
                                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Show custom field five', 'lr-plugin-slug'); ?>">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </label>
                                                <span class="custom_option">
                                                    <?php _e( 'Custom Field Five Title', 'lr-plugin-slug' ); ?>
                                                    <input type="text" name="LoginRadius_Social_Profile_Data_settings[custom_five_title]" placeholder="Custom Field Five Name" value="<?php echo isset($lr_social_profile_data_settings['custom_five_title']) ? $lr_social_profile_data_settings['custom_five_title'] : ''; ?>" />
                                                </span>
                                                <span class="custom_option">
                                                    <?php _e('Custom Field Five Field Type', 'lr-plugin-slug'); ?>
                                                    <select name="LoginRadius_Social_Profile_Data_settings[custom_five_type]">
                                                        <option value="text" <?php echo ( isset($lr_social_profile_data_settings['custom_five_type'] ) && $lr_social_profile_data_settings['custom_five_type'] == "text" ) ? "selected" : ""; ?> >Text</option>
                                                        <option value="dropdown" <?php echo ( isset( $lr_social_profile_data_settings['custom_five_type'] ) && $lr_social_profile_data_settings['custom_five_type'] == "dropdown" ) ? "selected" : ""; ?>>Dropdown</option>
                                                    </select>
                                                    <span class="dropdown_msg">
                                                        <?php $dropdown_id = $wpdb->get_var( $wpdb->prepare("SELECT field_id FROM " . $wpdb->base_prefix . "lr_popup_custom_fields_map WHERE field_name = %s", 'field_5' ) ); ?>
                                                        <?php _e( 'Insert dropdown values into table ', 'lr-plugin-slug' ); ?><b><?php echo $dropdown_table; ?></b><?php _e('  with a field_id of ', 'lr-plugin-slug' ); ?><b><?php echo $dropdown_id; ?></b>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php } ?>

                        <p class="submit">
                            <?php submit_button('Save Options', 'primary', 'submit', false); ?>
                        </p>
                    </form>
                    <?php do_action('lr_reset_admin_ui','Social Profile Data');?>
                </div>
            </div>

            <?php
        }

    }

}