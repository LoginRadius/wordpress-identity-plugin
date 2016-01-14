<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The LoginRadius Sailthru admin settings page.
 */
if (!class_exists('LR_Sailthru_Admin_Settings')) {

    class LR_Sailthru_Admin_Settings {

        public static function render_options_page() {

            if (isset($_POST['reset'])) {
                LR_Sailthru_Install::reset_sailthru_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">' . __('Sailthru settings have been reset and default values loaded', 'LoginRadius') . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
            $lr_sailthru_settings = get_option('LR_Sailthru_Settings');
            $sailthru_api_key = isset($lr_sailthru_settings['sailthru_api_key']) ? $lr_sailthru_settings['sailthru_api_key'] : '';
            $sailthru_api_secret = isset($lr_sailthru_settings['sailthru_api_secret']) ? $lr_sailthru_settings['sailthru_api_secret'] : '';
            ?>
            <style>
                #lr_sailthru_mapping_area img{margin: 6px 0 0 3px;cursor: pointer;}
                .show_sailthru_options{display: none;}
                .error{color: #ff0000;}
                .success{color: #8BA870;}
            </style>
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Sailthru</em></h2>
                </header>

                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('lr_sailthru_settings');
                        settings_errors();
                        ?>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Sailthru Settings', 'LoginRadius'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-comment-enable" name="LR_Sailthru_Settings[sailthru_enable]" value="1" <?php echo isset($lr_sailthru_settings['sailthru_enable']) && $lr_sailthru_settings['sailthru_enable'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-comment-enable">
                                        <?php _e('Enable Sailthru', 'LoginRadius'); ?>
                                        <span class="lr-tooltip" data-title="<?php _e('Turn on to enable Sailthru functionality with user registration.', 'LoginRadius'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>                                    
                                    <label>
                                        <span class="lr_property_title"><?php _e('Sailthru API key', 'LoginRadius'); ?>
                                            <span class="lr-tooltip" data-title="<?php _e('Enter Sailthru API Key to sync user profile data.', 'LoginRadius'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </span>
                                        <input type="text" class="lr-row-field" id="sailthru_api_key" name="LR_Sailthru_Settings[sailthru_api_key]" value="<?php echo $sailthru_api_key; ?>" autofill="off" autocomplete="off">
                                    </label>
                                    <label>
                                        <span class="lr_property_title"><?php _e('Sailthru API Secret', 'LoginRadius'); ?>
                                            <span class="lr-tooltip" data-title="<?php _e('Enter Sailthru API Secret to sync user profile data.', 'LoginRadius'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </span>
                                        <input type="text" class="lr-row-field" id="sailthru_api_secret" name="LR_Sailthru_Settings[sailthru_api_secret]" value="<?php echo $sailthru_api_secret; ?>" autofill="off" autocomplete="off">
                                    </label>
                                    <div>
                                        <span onclick="getLists()" class="button button-primary"><?php _e('Get Lists', 'LoginRadius'); ?></span>
                                        <span id="lr_sailthru_message"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lr_options_container show_sailthru_options">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Subscriber Lists Settings', 'LoginRadius'); ?>
                                </h3>
                                <div>
                                    <label>
                                        <span class="lr_property_title"><?php _e('Select Subscriber Lists', 'LoginRadius'); ?>
                                            <span class="lr-tooltip" data-title="<?php _e('Select Subscriber Lists to sync user profile data on selected Lists.', 'LoginRadius'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </span>
                                        <select id="lr_get_sailthru_list_option" class="lr-row-field" name="LR_Sailthru_Settings[sailthru_subscriber_lists][]" multiple="multiple"></select>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="lr_options_container show_sailthru_options">
                            <div class="lr-row" id="lr_sailthru_mapping_area">
                                <h3>
                                    <?php
                                    _e('Mapping Fields Settings', 'LoginRadius');
                                    $sailthruValueFields = !empty($lr_sailthru_settings['sailthru_value_fields']) ? $lr_sailthru_settings['sailthru_value_fields'] : array('');
                                    $sailthruLabelFields = !empty($lr_sailthru_settings['sailthru_label_fields']) ? $lr_sailthru_settings['sailthru_label_fields'] : array('email');
                                    ?>
                                </h3>
                                <div>
                                    <span class="lr_property_title" style="margin:0;"><b><?php _e('Sailthru mapping fields', 'LoginRadius'); ?></b>
                                        <span class="lr-tooltip" data-title="<?php _e('Select Sailthru mapping fields for create and update user profile data on selected Lists.', 'LoginRadius'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>

                                    </span>
                                    <span class="lr-row-field"><b><?php _e('Loginradius mapping fields', 'LoginRadius'); ?></b>
                                        <span class="lr-tooltip" data-title="<?php _e('Select LoginRadius mapping fields for sync user profile data on selected Lists.', 'LoginRadius'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </span>
                                    <br><br>
                                </div>
                                <div id="sailthru_mapping_fields">   
                                    <?php
                                    $mappingFields = LR_Advanced_Functions::login_radius_get_mapping_fields();
                                    if (count($sailthruValueFields) > 0) {
                                        $count_id = 1;
                                        foreach ($sailthruValueFields as $label => $value) {
                                            echo self::sailthru_mapping_field($count_id, $mappingFields, $sailthruLabelFields[$label], $value);
                                            $count_id++;
                                        }
                                    }
                                    ?>
                                </div>
                                <img src="<?php echo LR_SAILTHRU_URL; ?>assets/images/add.png" id="sailthru_add_<?php echo $count_id; ?>" onclick="addmapfield('<?php echo $count_id; ?>')" />
                            </div>
                        </div>

                        <div class="lr_options_container show_sailthru_options">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Sailthru Advance Settings', 'LoginRadius'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-sailthru-update" name="LR_Sailthru_Settings[sailthru_userprofile_update]" value="1" <?php echo isset($lr_sailthru_settings['sailthru_userprofile_update']) && $lr_sailthru_settings['sailthru_userprofile_update'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-sailthru-update">
                                        <?php _e('Update User Profile Sailthru', 'LoginRadius'); ?>
                                        <span class="lr-tooltip" data-title="<?php _e('Turn on, To update user profile on every user login.', 'LoginRadius'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p class="submit">
                            <?php submit_button('Save Options', 'primary', 'submit', false); ?>
                        </p>
                    </form>
                    <?php do_action('lr_reset_admin_ui', 'Sailthru'); ?>
                </div>
            </div>
            <script type="text/javascript">
                function getLists() {
                    jQuery('#lr_sailthru_message').html('Loading...');
                    var sailthru_api_key = jQuery('#sailthru_api_key').val();
                    var sailthru_api_secret = jQuery('#sailthru_api_secret').val();
                    if (sailthru_api_key == '' || sailthru_api_secret == '') {
                        jQuery('#lr_sailthru_message').html('<span class="error">Please Enter Sailthru Api and Secret.</span>');
                        return;
                    }
                    jQuery.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        data: {
                            action: 'get_sailthru_subscriber_list',
                            sailthru_api_key: sailthru_api_key,
                            sailthru_api_secret: sailthru_api_secret
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.status == 'success') {
                                jQuery('#lr_get_sailthru_list_option').html(response.html);
                                jQuery('.show_sailthru_options').show();
                            } else {
                                jQuery('.show_sailthru_options').hide();
                            }
                            jQuery('#lr_sailthru_message').html('<span class="' + response.status + '">' + response.message + '</span>');
                        }
                    });
                }
                function removemapfield(id) {
                    jQuery('#sailmap_' + id).remove();
                }
                function addmapfield(id) {
                    var newid = Number(id) + Number(1);
                    var fieldhtml = '<div id="sailmap_' + id + '"><div style="clear:both;"></div>';
                    fieldhtml += '<input class="lr-row-field" placeholder="<?php _e('Enter sailthru field name', 'LoginRadius'); ?>" style="margin: 0;width: 26% !important;float: left;" type="text" name="LR_Sailthru_Settings[sailthru_label_fields][]" autofill="off" autocomplete="off">';
                    fieldhtml += '<?php echo self::lr_mapping_fields_with_sailthru(LR_Advanced_Functions::login_radius_get_mapping_fields(), true); ?>';
                    fieldhtml += '<img src="<?php echo LR_SAILTHRU_URL; ?>assets/images/remove.png" style="float:left;" onclick="removemapfield(\'' + id + '\')" /></div>'
                    jQuery('#sailthru_mapping_fields').append(fieldhtml);
                    jQuery('#sailthru_add_' + id).attr('onclick', 'addmapfield("' + newid + '")');
                    jQuery('#sailthru_add_' + id).attr('id', 'sailthru_add_' + newid);
                }
            <?php if (!empty($sailthru_api_key) && !empty($sailthru_api_secret)) { ?>
                    jQuery(document).ready(function () {
                        getLists();
                    });
            <?php } ?>
            </script>
            <?php
        }

        /**
         * 
         * @param type $id
         * @param type $label
         * @param type $value
         * @return string
         */
        public static function sailthru_mapping_field($id, $mappingFields, $label = '', $value = '') {
            $output = '<div id="sailmap_' . $id . '"><div style="clear:both;"></div>';
            $hide = 'text';
            $is_js = true;
            if (!empty($label)) {
                $output .= '<span class="lr_property_title" style="margin: 0;width: 26% !important;float: left;">' . $label . '</span>';
                $hide = 'hidden';
                $is_js = false;
            }
            $output .= '<input placeholder="' . __('Enter sailthru field name', 'LoginRadius') . '" class="lr-row-field" style="margin: 0;width: 26% !important;float: left;" type="' . $hide . '" name="LR_Sailthru_Settings[sailthru_label_fields][]" value="' . $label . '" autofill="off" autocomplete="off">';
            $output .= self::lr_mapping_fields_with_sailthru($mappingFields, $is_js, $value);
            $output .= '<img src="' . LR_SAILTHRU_URL . 'assets/images/remove.png" style="float:left;" onclick="removemapfield(\'' . $id . '\')" /></div>';
            return $output;
        }

        /**
         * 
         * @param type $is_js
         * @param type $default
         * @return string
         */
        public static function lr_mapping_fields_with_sailthru($mappingFields, $is_js = true, $default = '') {
            $output = '<select class="lr-row-field" style="float:left;';
            if ($is_js) {
                $output .= 'margin-left: 10px;';
            }
            $output .= 'padding: 5px;" name="LR_Sailthru_Settings[sailthru_value_fields][]">';
            $output .= '<option value="">' . __('--- Select Field ---', 'LoginRadius') . '</option>';
            foreach ($mappingFields as $field) {
                if (in_array($field, array('User Id', 'basic|id', 'exprofile|id', 'exloc|id', 'basic|wp_users_id', 'exprofile|wp_users_id', 'exprofile|website', 'exloc|wp_users_id', 'basic|user_id', 'basic|loginradius_id', 'exprofile|last_profile_update', 'exprofile|repository_url', 'exprofile|provider_access_token', 'basic|provider', 'exloc|provider', 'basic|profile_country', 'basic|country_name', 'exprofile|provider_token_secret', 'exprofile|https_image_url', 'exprofile|favicon'))) {
                    continue;
                }
                $tempFields = explode('|', $field);
                $output .= '<option value="' . $field . '"';
                if ($default == $field) {
                    $output .= ' Selected="selected"';
                }
                $optionLabel = isset($tempFields[1]) ? $tempFields[1] : $tempFields[0];
                $output .= '>' . ucwords(str_replace('_', ' ', $optionLabel)) . '</option>';
            }
            $output .= '</select>';
            return $output;
        }

    }

}