<?php
/**
 * @file
 * The Admin Panel and related tasks are handled in this file.
 */
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin settings page.
 */
if (!class_exists('LR_Salesforce_Admin_Settings')) {

    class LR_Salesforce_Admin_Settings {

        /**
         * 
         * @global type $lr_salesforce_settings
         */
        public static function render_options_page() {
            global $lr_salesforce_settings;
            if (isset($_POST['reset'])) {
                LR_Salesforce_Install::reset_loginradius_salesforce_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Salesforce settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(5000).slideUp();});</script>';
            }

            $lr_salesforce_settings = get_option('LR_Salesforce_Settings');
            ?>
            <script>
                function loginRadiusucfirst(str) {
                    var f = str.charAt(0).toUpperCase();
                    return f + str.substr(1);
                }
                function loginRadiusSalesforceValidation() {
                    jQuery('#login_radius_api_response_salesforce').html('<img width="20" height="20" src="<?php echo LR_SALESFORCE_PLUGIN_URL . 'assets/images/loading_icon.gif'; ?>" style="float:left;margin: 5px;" />');
                    jQuery.ajax({
                        type: 'POST',
                        url: '<?php echo get_admin_url() ?>admin-ajax.php',
                        data: {
                            action: 'login_radius_verify_salesforce_keys',
                            sfKey: jQuery('#login_radius_salesforce_key').val().trim(),
                            sfSecret: jQuery('#login_radius_salesforce_secret').val().trim(),
                            sfUsername: jQuery('#login_radius_salesforce_username').val().trim(),
                            sfPassword: jQuery('#login_radius_salesforce_password').val().trim(),
                        },
                        success: function (data, textStatus, XMLHttpRequest) {
                            var validationResponse = JSON.parse(data);
                            if (validationResponse.success) {
                                jQuery('#login_radius_api_response_salesforce').css("color", "green");
                                jQuery('#login_radius_api_response_salesforce').html("Verified Salesforce credentials, your settings have been saved.");
                                salesforceObjectHtmlImplementation();
                                <?php $salesforce_object_type = isset($lr_salesforce_settings['salesforce_object_type']) && $lr_salesforce_settings['salesforce_object_type'] != 'Lead';?>
                                loginRadiusSalesforceAuthentication('<?php echo $salesforce_object_type; ?>');
                            } else {
                                jQuery('#login_radius_api_response_salesforce').css("color", "red");
                                if (validationResponse.isEmpty) {
                                    jQuery('#login_radius_api_response_salesforce').html(validationResponse.errorField + " Field is required");
                                } else {
                                    jQuery('#login_radius_api_response_salesforce').html(validationResponse.errorMessage);

                                }
                                jQuery('.login_radius_salesforce_object_div').hide();
                                jQuery('.login_radius_salesforce_mapping_div,.login_radius_salesforce_update_div').hide();
                            }
                        }
                    });
                }
                function salesforceObjectHtmlImplementation() {
                    var salesforceobjectHtml = '<div><label for="salesforce_object_label">';
                    salesforceobjectHtml += '<span class="lr_property_title">Salesforce object <span class="lr-tooltip" data-title="Select Salesforce object to store the data">';
                    salesforceobjectHtml += '<span class="dashicons dashicons-editor-help"></span></span></span>';
                    salesforceobjectHtml += '<select class="lr-row-field" onchange="loginRadiusSalesforceAuthentication( this.value.trim())" name="LR_Salesforce_Settings[salesforce_object_type]" id="salesforce_object_label">';
                    salesforceobjectHtml += '<option value="">--- Select Object ---</option>';
                    salesforceobjectHtml += '<option value="Lead" <?php if (isset($lr_salesforce_settings['salesforce_object_type']) && $lr_salesforce_settings['salesforce_object_type'] == 'Lead') {echo 'selected="selected"';} ?>>Lead</option>';
                    salesforceobjectHtml += '<option value="Account" <?php if (isset($lr_salesforce_settings['salesforce_object_type']) && $lr_salesforce_settings['salesforce_object_type'] == 'Account') {echo 'selected="selected"';} ?>>Account</option>';
                    salesforceobjectHtml += '<option value="Contact" <?php if (isset($lr_salesforce_settings['salesforce_object_type']) && $lr_salesforce_settings['salesforce_object_type'] == 'Contact') {echo 'selected="selected"';} ?>>Contact</option></select><div id="salesforce_object_loading_image" style="padding-top: 13px;"></div></label></div>';
                    jQuery('#login_radius_object_salesforce').html(salesforceobjectHtml);
                    jQuery('.login_radius_salesforce_object_div').show();
                }
            // get salesforce object fields lists according to the keys saved
                function loginRadiusSalesforceAuthentication(sObjectType) {
                    <?php 
                    $mappingFields = LR_Advanced_Functions::login_radius_get_mapping_fields();
                    $sortedArray = array();
                    foreach($mappingFields as $field){
                        if(in_array($field, array('User Id','basic|id','exprofile|id','exloc|id','basic|wp_users_id','exprofile|wp_users_id','exprofile|website','exloc|wp_users_id','basic|user_id', 'basic|loginradius_id', 'exprofile|last_profile_update', 'exprofile|repository_url', 'exprofile|provider_access_token','basic|provider','exloc|provider','basic|profile_country','basic|country_name','exprofile|provider_token_secret','exprofile|https_image_url','exprofile|favicon'))){
                            continue;
                        }
                        $tempFields = explode('|', $field);
                        if(isset($tempFields[1])){
                            $sortedArray[] = $tempFields[1].'-'.$tempFields[0];
                        }else{
                            $sortedArray[] = $tempFields[0];
                        }
                    }
                    sort($sortedArray);
                    ?>
                    jQuery('#salesforce_object_label').css('float', "left");
                    jQuery('#salesforce_object_loading_image').show();
                    jQuery('#salesforce_object_loading_image').html('<img width="20" height="20" src="<?php echo LR_SALESFORCE_PLUGIN_URL . 'assets/images/loading_icon.gif'; ?>" style="float:left;margin: 5px;" />');
                    jQuery.ajax({
                        type: 'POST',
                        url: '<?php echo get_admin_url() ?>admin-ajax.php',
                        data: {
                            action: 'login_radius_sf_authentication',
                            oType: sObjectType,
                            sfKey: jQuery('#login_radius_salesforce_key').val().trim(),
                            sfSecret: jQuery('#login_radius_salesforce_secret').val().trim(),
                            sfUsername: jQuery('#login_radius_salesforce_username').val().trim(),
                            sfPassword: jQuery('#login_radius_salesforce_password').val().trim()
                        },
                        dataType: 'json',
                        success: function (data, textStatus, XMLHttpRequest) {
                            if (data.success) {
                                var salesforceMappingFields = <?php echo json_encode($sortedArray); ?>;
                                var lr_salesforce_settings = <?php echo isset($lr_salesforce_settings['salesforce_mapping_fields']) ? json_encode($lr_salesforce_settings['salesforce_mapping_fields']) : json_encode(array()); ?>;
                                var mappingHtml = '<h3>Map your Salesforce objects fields to Social Login profile data.</h3>';
                                for (var key in data.fields) {
                                    mappingHtml += '<div><label><span class="lr_property_title">' + key + ' <span class="lr-tooltip" data-title="Select LoginRadius Field for Salesforce Field Mapping"><span class="dashicons dashicons-editor-help"></span></span></span>';
                                    mappingHtml += '<select class="lr-row-field" name="LR_Salesforce_Settings[salesforce_mapping_fields][' + key + ']" id = "salesforce_fields' + key + '"><option value="">--Select a field--</option>';

                                    for (var j in salesforceMappingFields) {
                                        var tempParts = salesforceMappingFields[j].split("-");
                                        mappingHtml += '<option value="' + salesforceMappingFields[j] + '"';
                                        if (lr_salesforce_settings[key] && lr_salesforce_settings[key] == salesforceMappingFields[j]) {
                                            mappingHtml += ' Selected';
                                        }
                                        mappingHtml += '>' + loginRadiusucfirst(tempParts[0].replace(/\_{1,}/g, " ")) + '</option>';
                                    }
                                    mappingHtml += '</select>';
                                    mappingHtml += '<input type="text" class="lr-row-readonly" readonly="readonly" name="LR_Salesforce_Settings[salesforce_mapping_fields_dataType][' + key + ']" id="salesforce_fields_dataType' + key + '"  value="' + data.oDataType[key] + '"/>';
                                    mappingHtml += '</label></div>';
                                }
                                jQuery('#salesforce_object_label').removeAttr('style');
                                jQuery('#salesforce_object_loading_image').hide();
                                jQuery('#login_radius_mapping_salesforce').html(mappingHtml);
                                jQuery('.login_radius_salesforce_mapping_div,.login_radius_salesforce_update_div').show();
                            } else if (data.success == false) {
                                jQuery('#salesforce_object_loading_image').html('<span style="color:red; width:auto"><?php _e('Error in retrieving Salesforce Access Token ', 'LoginRadius') ?></span>');
                                jQuery('.login_radius_salesforce_mapping_div,.login_radius_salesforce_update_div').hide();
                            } else {
                                jQuery('#salesforce_object_loading_image').html('<span style="color:red; width:auto"><?php _e('Unknown error occurred.', 'LoginRadius') ?></span>');
                                jQuery('.login_radius_salesforce_mapping_div,.login_radius_salesforce_update_div').hide();
                            }
                        }
                    });
                }
            </script>
            <div class="wrap lr-wrap cf">
                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('lr_salesforce_settings');
                        settings_errors();
                        ?>
                        <header>
                            <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Salesforce</em></h2>
                        </header>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Salesforce Integration', 'LoginRadius'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-salesforce-enable" name="LR_Salesforce_Settings[salesforce_subscribe]" value='1' <?php echo ( isset($lr_salesforce_settings['salesforce_subscribe']) && $lr_salesforce_settings['salesforce_subscribe'] == '1' ) ? 'checked' : '' ?> />
                                    <label class="lr-show-toggle" for="lr-salesforce-enable">
                                        <?php _e('Enable Salesforce', 'LoginRadius'); ?>
                                        <span class="lr-tooltip" data-title="Turn on, if you want to automatically subscribe users to Salesforce List when they register through Social Login?">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div style="position: relative;">
                            <div class="lr-option-disabled-hr lr-salesforce" style="display: none;"></div>
                            <div class="lr_options_container">
                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Salesforce account credentials', 'LoginRadius'); ?>
                                    </h3>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Consumer Key', 'LoginRadius'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Enter your Salesforce API Key (After entering your Salesforce API Key, hit the Save button)','LoginRadius'); ?>">
                                                      <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_Salesforce_Settings[salesforce_key]" id="login_radius_salesforce_key" class="lr-row-field" value="<?php echo isset($lr_salesforce_settings['salesforce_key']) ? trim($lr_salesforce_settings['salesforce_key']) : ''; ?>" />
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Consumer Secret', 'LoginRadius'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Enter your Salesforce API Secret (After entering your Salesforce API Key, hit the Save button)','LoginRadius'); ?>">
                                                      <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_Salesforce_Settings[salesforce_secret]" id="login_radius_salesforce_secret" class="lr-row-field" value="<?php echo isset($lr_salesforce_settings['salesforce_secret']) ? trim($lr_salesforce_settings['salesforce_secret']) : ''; ?>" />
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Username', 'LoginRadius'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Enter your Salesforce Username (After entering your Salesforce API Key, hit the Save button)','LoginRadius'); ?>">
                                                      <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_Salesforce_Settings[salesforce_username]" id="login_radius_salesforce_username" class="lr-row-field" value="<?php echo isset($lr_salesforce_settings['salesforce_username']) ? trim($lr_salesforce_settings['salesforce_username']) : ''; ?>" />
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Password', 'LoginRadius'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Enter your Salesforce Password (After entering your Salesforce API Key, hit the Save button)','LoginRadius'); ?>">
                                                      <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="password" name="LR_Salesforce_Settings[salesforce_password]" id="login_radius_salesforce_password" class="lr-row-field" value="<?php echo isset($lr_salesforce_settings['salesforce_password']) ? trim($lr_salesforce_settings['salesforce_password']) : ''; ?>" />
                                        </label>
                                    </div>
                                    <p style="float: left;margin: 0;padding: 0;margin-right: 10px;">
                                        <input type="button" class="button button-primary" value="Verify" onclick="loginRadiusSalesforceValidation();" >
                                        <div id="login_radius_api_response_salesforce"></div>
                                    </p>
                                </div>
                            </div>
                            <div class="lr_options_container login_radius_salesforce_object_div" style="display: none;">
                                <div class="lr-row">
                                    <div id="login_radius_object_salesforce"></div>
                                </div>
                            </div>
                            <div class="lr_options_container login_radius_salesforce_mapping_div" style="display: none;">
                                <div class="lr-row">
                                    <div id="login_radius_mapping_salesforce"></div>
                                </div>
                            </div>
                        </div>

                        <div class="lr_options_container login_radius_salesforce_update_div" style="display: none;">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Update Salesforce data', 'LoginRadius'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-salesforce-update-enable" name="LR_Salesforce_Settings[salesforce_subscribe_update]" value='1' <?php echo ( isset($lr_salesforce_settings['salesforce_subscribe_update']) && $lr_salesforce_settings['salesforce_subscribe_update'] == '1' ) ? 'checked' : '' ?> />
                                    <label class="lr-show-toggle" for="lr-salesforce-update-enable">
                                        <?php _e('Update Salesforce data', 'LoginRadius'); ?>
                                        <span class="lr-tooltip" data-title="Do you want to update Salesforce data on each login (It will overwrite the data in Salesforce)?">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p class="submit">
                            <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                        </p>
                    </form>
                </div>
                <?php do_action('lr_reset_admin_ui', 'Salesforce');
                if (isset($lr_salesforce_settings['salesforce_subscribe']) && $lr_salesforce_settings['salesforce_subscribe'] == '1' &&
                        isset($lr_salesforce_settings['salesforce_key']) && $lr_salesforce_settings['salesforce_key'] != '' &&
                        isset($lr_salesforce_settings['salesforce_secret']) && $lr_salesforce_settings['salesforce_secret'] != '' &&
                        isset($lr_salesforce_settings['salesforce_username']) && $lr_salesforce_settings['salesforce_username'] != '' &&
                        isset($lr_salesforce_settings['salesforce_password']) && $lr_salesforce_settings['salesforce_password'] != '') {
                    ?>
                    <script>
                        loginRadiusSalesforceValidation();
                    </script>
            <?php } ?>
            </div>
            <?php
        }

    }

}