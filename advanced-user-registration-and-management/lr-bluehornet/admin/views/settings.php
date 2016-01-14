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
if (!class_exists('LR_BlueHornet_Admin_Settings')) {

    class LR_BlueHornet_Admin_Settings {
        public static function bluehornet_script(){
            global $lr_bluehornet_settings;
            ?>
                <script>
                    function loginRadiusGetBluehornet(bhapi, bhsecret){

                        jQuery('#login_radius_bluehornet_message').html('<img width="20" height="20" src="<?php echo LR_BLUEHORNET_PLUGIN_URL . 'assets/images/loading_icon.gif'; ?>" style="float:left;margin: 5px;" />');
                        if(bhapi == '' || bhsecret == ''){
                            jQuery('#login_radius_bluehornet_message').html('<span style="color:red; width:auto;margin-left: 10px;"><?php _e('Please enter your Bluehornet account credentials', 'LoginRadius') ?></span>');
                            return true;
                        }

                        jQuery.ajax( {
                            type: 'POST',
                            url: '<?php echo get_admin_url() ?>admin-ajax.php',
                            data: {
                            action: 'login_radius_bluehornet_segment',
                                key: bhapi,
                                secret: bhsecret
                            },
                        success: function( data, textStatus, XMLHttpRequest ){
                            var data = jQuery.parseJSON( data );
                            var bluehornet_static_segments = <?php echo json_encode(isset($lr_bluehornet_settings['bluehornet_static_segments'])?$lr_bluehornet_settings['bluehornet_static_segments']:array());?>;
                            var getbluehornetHtml = '';
                            if(data.Segments != ''){
                                var static_segments = data.Segments;
                                getbluehornetHtml += '<div><label for="bluehornet_static_segments"><span class="lr_property_title"><?php _e('Segments ', 'LoginRadius') ?><span class="lr-tooltip" data-title="<?php _e('Select BlueHornet Segments', 'LoginRadius') ?>"><span class="dashicons dashicons-editor-help"></span>';
                                getbluehornetHtml += '</span></span><select class="lr-row-field" multiple="multiple" name="LR_BlueHornet_Settings[bluehornet_static_segments][]" id = "bluehornet_static_segments">';
                                for(var key in static_segments){
                                    getbluehornetHtml += '<option value="'+key+'"';
                                    if(bluehornet_static_segments.indexOf(key) != -1){
                                        getbluehornetHtml += ' Selected';
                                    }
                                    getbluehornetHtml += '>'+static_segments[key]+'</option>';
                                }
                                getbluehornetHtml += '</select><label></div>';
                                jQuery('#login_radius_bluehornet_message').hide();
                                jQuery('.login_radius_bluehornet_campaign').show();
                                jQuery('#login_radius_get_bluehornet').html(getbluehornetHtml);
                            }else{
                                jQuery('#login_radius_bluehornet_message').html('<span style="color:red; width:auto;margin-left: 10px;"><?php _e('Invalid Request IP: Please add request_ip to the API Whitelist in your account.  (Account -> API Settings) OR create Static Segments in BlueHornet Account.', 'LoginRadius') ?></span>');
                                jQuery('#login_radius_bluehornet_message').show();
                                jQuery('.login_radius_bluehornet_campaign').hide();
                            }
                            var customfields = {};
                            if(data.CustomFields != ''){
                                var custom_fields = data.CustomFields;
                                for(var key in custom_fields){
                                    customfields[custom_fields[key]] =  custom_fields[key]+'.'+key;
                                }
                            }
                        var bluehornetmappingHtml = '<h3><?php _e('Bluehornet Data Fields Mapping (Map your bluehornet Prospect fields with User Social profile data fields.)', 'LoginRadius') ?></h3>';
                        bluehornetmappingHtml += addbhmappingdropdown(customfields);
                        jQuery('#login_radius_bluehornet_mapping').html(bluehornetmappingHtml);
                        return customfields;
                        },
                            error: function(a, b,c){
                                    //alert(JSON.stringify(a) +"\r\n"+JSON.stringify(b)+c)
                            }
                            } );
                        }
                    function addbhmappingdropdown( bluehornetmappingfields ){
                    <?php $bluehornetmappingfieldstitle=array('First Name' => 'firstname', 'Last Name' => 'lastname', 'Address'=>'address', 'City'=>'city','State'=>'state','Postal Code'=>'postal_code','Country'=>'country', 'Phone (Home)'=>'phone_hm','Phone (Work)'=>'phone_wk');?>
                            var bluehornetbasicmapping = <?php echo json_encode($bluehornetmappingfieldstitle);?>;
                            for(var key in bluehornetmappingfields){
                                bluehornetbasicmapping[key] = bluehornetmappingfields[key];
                            }
                    <?php
                    global $wpdb;
                    // manipulate the list of fields to show for mapping.
                    $bluehornetmappingFields = array('User Id', 'Username', 'First Name', 'Last Name', 'Nice Name', 'Email', 'Profile Url', 'Display Name', 'Bio', 'Phone', 'Postal Code');
                        // if basic profile data table exists
                        if($wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "lr_basic_profile_data'" ) == $wpdb->base_prefix . "lr_basic_profile_data" ) {
                            $basicProfileColumns = $wpdb->get_col( "SHOW COLUMNS FROM " . $wpdb->base_prefix . "lr_basic_profile_data" );
                            $bluehornetmappingFields = array_merge($bluehornetmappingFields, $basicProfileColumns);
                        }
                        // if extended location data table exists
                        if($wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix."lr_extended_location_data'" ) == $wpdb->base_prefix . "lr_extended_location_data" ) {
                            $extendedLocationColumns = $wpdb->get_col( "SHOW COLUMNS FROM " . $wpdb->base_prefix . "lr_extended_location_data" );
                            $bluehornetmappingFields = array_merge($bluehornetmappingFields, $extendedLocationColumns);
                            }
                        // if extended profile data table exists
                        if($wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix."lr_extended_profile_data'" ) == $wpdb->base_prefix . "lr_extended_profile_data" ) {
                            $extendedProfileColumns = $wpdb->get_col( "SHOW COLUMNS FROM " . $wpdb->base_prefix . "lr_extended_profile_data" );
                            $bluehornetmappingFields = array_merge($bluehornetmappingFields, $extendedProfileColumns);
                            }
                        $sortedArray = array();
                        foreach( $bluehornetmappingFields as $field ){
                            if( in_array( $field, array( 'User Id','basic|user_id', 'basic|loginradius_id', 'exprofile|last_profile_update', 'exprofile|repository_url'
                            , 'exprofile|provider_access_token','basic|provider','exloc|provider','basic|profile_country','basic|country_name','exprofile|provider_token_secret','exprofile|https_image_url','exprofile|favicon' ) ) ) {
                            continue;
                            }
                        $fieldParts = explode( '|', $field );
                        if(isset( $fieldParts[1] ) ) {
                            $fieldParts2 = explode('_', $fieldParts[1]);
                            $fieldParts2 = array_map( array( 'LR_Advanced_Functions','login_radius_ucfirst_in_array' ), $fieldParts2 );
                        $sortedArray[] = implode( ' ', $fieldParts2 ).'.'.$field;
                        }else{
                            $sortedArray[] = $fieldParts[0].'.'.$field;
                            }
                        }
                        sort($sortedArray);
                        ?>
                        var sortedArray = <?php echo json_encode($sortedArray);?>;
                        var lr_bluehornet_settings = <?php echo isset( $lr_bluehornet_settings['bluehornet_mapping_fields']) ? json_encode( $lr_bluehornet_settings['bluehornet_mapping_fields']):json_encode(array());?>;
                        var bluehornetmappingHtml = '';
                        for (var i in bluehornetbasicmapping) {
                            var bluehornetfieldleval = i.split(".");
                            bluehornetmappingHtml += '<div><label><span class="lr_property_title">' + bluehornetfieldleval[0].replace("_", " ") + ' <span class="lr-tooltip" data-title="<?php _e('Select LoginRadius Field for BlueHornet Field Mapping', 'LoginRadius') ?>">';
                            bluehornetmappingHtml += '<span class="dashicons dashicons-editor-help"></span></span>';
                            bluehornetmappingHtml += '</span><input value="'+bluehornetbasicmapping[i].replace(".", "")+'" type="hidden" name="LR_BlueHornet_Settings[bluehornet_fields_titles][]"/>';
                            bluehornetmappingHtml += '<select class="lr-row-field" name="LR_BlueHornet_Settings[bluehornet_mapping_fields]['+bluehornetbasicmapping[i]+']" id = "bluehornet_' + bluehornetbasicmapping[i].replace(" ", "") + '">';
                            bluehornetmappingHtml += '<option value=""><?php _e('--- Select Field ---', 'LoginRadius') ?></option>';
                            for(var j = 0; j < sortedArray.length; j++){
                                var tempParts = sortedArray[j].split(".");
                                bluehornetmappingHtml += '<option value="'+tempParts[1]+'"'
                                    if( lr_bluehornet_settings[bluehornetbasicmapping[i]] && lr_bluehornet_settings[bluehornetbasicmapping[i]]==tempParts[1] ){
                                        bluehornetmappingHtml += ' Selected';
                                }
                                bluehornetmappingHtml += '>'+tempParts[0]+'</option>';
                            }
                            bluehornetmappingHtml += '</select></label></div>';
                }
                return bluehornetmappingHtml;
                }
                </script>
                <?php
        }

        public static function render_options_page() {
            global $lr_bluehornet_settings;
            if ( isset( $_POST['reset'] ) ) {
                LR_BlueHornet_Install::reset_loginradius_bluehornet_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">'. __('BlueHornet settings have been reset and default values loaded', 'LoginRadius').'</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery( ".lr-notif" ).slideDown().delay(5000).slideUp();});</script>';
            }
            $lr_bluehornet_settings = get_option( 'LR_BlueHornet_Settings' );
            ?>
    
            <div class="wrap lr-wrap cf">
                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('lr_bluehornet_settings');
                        settings_errors();
                        ?>
                        <header>
                            <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>BlueHornet</em></h2>
                        </header>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('BlueHornet Integration', 'LoginRadius'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-bluehornet-enable" name="LR_BlueHornet_Settings[bluehornet_subscribe]" value='1' <?php echo ( isset($lr_bluehornet_settings['bluehornet_subscribe']) && $lr_bluehornet_settings['bluehornet_subscribe'] == '1' ) ? 'checked' : '' ?> />
                                    <label class="lr-show-toggle" for="lr-bluehornet-enable">
                                        <?php _e('Enable BlueHornet', 'LoginRadius'); ?>
                                        <span class="lr-tooltip" data-title="<?php _e('Turn on, if you want to automatically subscribe users to BlueHornet List when they register through Social Login?', 'LoginRadius'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div style="position: relative;">
                            <div class="lr-option-disabled-hr lr-bluehornet" style="display: none;"></div>
                            <div class="lr_options_container">
                                <div class="lr-row">
                                    <h3>
                                        <?php _e( 'BlueHornet account credentials', 'LoginRadius' ); ?>
                                    </h3>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e( 'BlueHornet API Key', 'LoginRadius' ); ?>
                                                <span class="lr-tooltip" data-title="<?php _e( 'Enter your BlueHornet API Key (After entering your BlueHornet API Key, hit the Save button)', 'LoginRadius'); ?>">
                                                      <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_BlueHornet_Settings[bluehornet_key]" id="login_radius_bluehornet_key" class="lr-row-field" value="<?php echo isset($lr_bluehornet_settings['bluehornet_key']) ? trim($lr_bluehornet_settings['bluehornet_key']) : ''; ?>" />
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('BlueHornet Secret Key', 'LoginRadius'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e( 'Enter your BlueHornet Secret Key (After entering your BlueHornet Secret Key, hit the Save button)', 'LoginRadius'); ?>">
                                                      <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_BlueHornet_Settings[bluehornet_secret]" id="login_radius_bluehornet_secret" class="lr-row-field" value="<?php echo isset($lr_bluehornet_settings['bluehornet_secret']) ? trim($lr_bluehornet_settings['bluehornet_secret']) : ''; ?>" />
                                        </label>
                                    </div>
                                    <p style="float: left;margin: 0;padding: 0;">
                                        <input type="button" class="button button-primary" value="Get Campaign" onclick="loginRadiusGetBluehornet(document.getElementById('login_radius_bluehornet_key').value.trim(), document.getElementById('login_radius_bluehornet_secret').value.trim());" >
                                        <div id="login_radius_bluehornet_message"></div>
                                    </p>
                                </div>
                            </div>
                            <div class="lr_options_container login_radius_bluehornet_campaign">
                                <div class="lr-row">
                                    <div id="login_radius_get_bluehornet"></div>
                                </div>
                            </div>
                            <div class="lr_options_container login_radius_bluehornet_campaign">
                                <div class="lr-row">
                                    <div id="login_radius_bluehornet_mapping"></div>
                                </div>
                            </div>
                        </div>
                        <p class="submit">
                            <?php submit_button( 'Save Settings', 'primary', 'submit', false ); ?>
                        </p>
                    </form>
                </div>
                <?php do_action( 'lr_reset_admin_ui','BlueHornet' );
		self::bluehornet_script();

                // Populate Mailchimp apikey and lists if saved in database.
                if ( isset( $lr_bluehornet_settings['bluehornet_subscribe'] ) && $lr_bluehornet_settings['bluehornet_subscribe'] == '1' &&
                        isset( $lr_bluehornet_settings['bluehornet_key'] ) && $lr_bluehornet_settings['bluehornet_key'] != '' &&
                        $lr_bluehornet_settings['bluehornet_secret'] && $lr_bluehornet_settings['bluehornet_secret'] != '' ) {
                    ?>
                        <script>
                            loginRadiusGetBluehornet( '<?php echo trim( $lr_bluehornet_settings['bluehornet_key'] ) ?>', '<?php echo trim($lr_bluehornet_settings['bluehornet_secret']) ?>' );
                        </script>
                <?php }?>
            </div>
        <?php        
        }
    }
}