<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Mailchimp_Admin_Settings' ) ) {

	class LR_Mailchimp_Admin_Settings {

		public static function mailchimp_script() {
			global $lr_mailchimp_settings;
			$lr_mailchimp_settings = get_option( 'LR_Mailchimp_Settings' );

			wp_enqueue_script('jquery');

			?>
				<script>
					// get mailchimp lists according to the key saved
					function loginRadiusSaveMCAPIKey( mcApiKey ) {
						if(mcApiKey == ""){
							jQuery('#mc-apikey-import-message').html( '<span style="color:red; width:auto"><?php _e( "Please enter a valid Mailchimp API Key", "lr-plugin-slug" ); ?></span>' );
							return;
						}
						jQuery('#mc-apikey-import-message').html('<img width="20" height="20" src="<?php echo LR_MAILCHIMP_URL . "assets/images/loading_icon.gif"; ?>" style="float:left;margin-right: 5px;" /><span style="color:blue; width:auto"><?php _e( "Importing Mailchimp lists", "lr-plugin-slug" ); ?>...</span>');

						jQuery.ajax({
						  type: 'POST',
						  url: '<?php echo get_admin_url() ?>admin-ajax.php',
						  data: {
							  action: 'login_radius_get_mc_lists',
							  key: mcApiKey
						  },
						  dataType: 'json',
						  success: function( data, textStatus, XMLHttpRequest ) {

							if(data.success){
								var listOptions = "<option value=''>--Select a List--</option>";
								for(var i = 0; i < data.ids.length; i++){
									listOptions += '<option';
									<?php
										if( isset($lr_mailchimp_settings['mailchimp_lists']) ){
											?>
											if( data.ids[i] == '<?php echo $lr_mailchimp_settings['mailchimp_lists']; ?>' ){
												listOptions += ' selected = "selected"';
											}
											<?php
										}
									?>
									listOptions += ' value="'+data.ids[i]+'">'+data.names[i]+'</option>';
								}
								jQuery( '#login_radius_mailchimp_lists' ).html( listOptions );
								jQuery( '#mc-apikey-import-message' ).html( '<span style="color:green; width:auto"><?php _e( "Lists imported successfully.", "lr-plugin-slug" ); ?></span>' );
							}else if(data.success == false){
								jQuery( '#mc-apikey-import-message' ).html( '<span style="color:red; width:auto"><?php _e( "Please enter a valid Mailchimp API Key", "lr-plugin-slug" ); ?></span>' );
							}else{
								jQuery( '#mc-apikey-import-message' ).html( '<span style="color:red; width:auto"><?php _e( "Unknown error occurred.", "lr-plugin-slug" ); ?></span>' );
							}
						  }
						});
					}

					// get mailchimp merge vars according to the key saved
					function loginRadiusGetMCMergeVars( listId ) {

						var mcApiKey = document.getElementById( 'login_radius_mc_apikey' ).value.trim();

						if(mcApiKey == ""){
							jQuery( '#mc-apikey-import-message' ).html( '<span style="color:red; width:auto"><?php _e( "Please enter a valid Mailchimp API Key", "lr-plugin-slug"); ?></span>' );
							return;
						}

						if(listId == "") {
							return;
						}

						jQuery.ajax({
							type: 'POST',
							url: '<?php echo get_admin_url() ?>admin-ajax.php',
							data: {
								action: 'login_radius_get_mc_merge_vars',
								key: mcApiKey,
								list_id: listId
							},
							dataType: 'json',
							success: function( data, textStatus, XMLHttpRequest ) {
								if(data.success){
									<?php
										$mappingFields = LR_Advanced_Functions::login_radius_get_mapping_fields();
									?>
									var mappingHtml = "";
									for(var i = 0; i < data.tags.length; i++) {
										mappingHtml += '<div><span class="lr_property_title">' + data.names[i] + '</span></div><select name="LR_Mailchimp_Settings[mailchimp_merge_var_' + data.tags[i] + ']" id = "mailchimp_merge_var_' + data.tags[i] + '" class="lr-row-field" ><option value="">--Select a field--</option>';
										mappingHtml += '<?php foreach($mappingFields as $field) {
															echo "<option value=\"$field\">";
															$fieldParts = explode("|", $field);
															if( isset($fieldParts[1]) ) {
																$fieldParts2 = explode( "_", $fieldParts[1] );
																$fieldParts2 = array_map( array('LR_Advanced_Functions','login_radius_ucfirst_in_array'), $fieldParts2 );
																echo implode( ' ', $fieldParts2 );
															}else{
																echo $fieldParts[0];
															}
															echo "</option>";
														} ?>';
										mappingHtml += '</select></div>';
									}
									jQuery('#login_radius_mailchimp_mapping').html( mappingHtml );
									<?php
										$tempMergeVars = LR_Mailchimp_Ajax_Helper::login_radius_get_mailchimp_merger_vars( trim( $lr_mailchimp_settings['mailchimp_apikey'] ), trim( $lr_mailchimp_settings['mailchimp_lists'] ) );
										if( is_array( $tempMergeVars ) ){
											foreach( $tempMergeVars as $tempMergeVar ){
												if( isset( $lr_mailchimp_settings[ 'mailchimp_merge_var_' . $tempMergeVar['tag'] ] ) ) {
										 			?>
										 				jQuery( '#<?php echo "mailchimp_merge_var_" . $tempMergeVar["tag"]; ?>' ).val( '<?php echo $lr_mailchimp_settings["mailchimp_merge_var_".$tempMergeVar["tag"]]; ?>');
										 			<?php
										 		}
										 	}
										 }
									?>
								}else if( data.success == false ) {

								}else {
									jQuery('#mc-apikey-import-message').html( '<span style="color:red; width:auto"><?php _e( "Unknown error occurred.", "lr-plugin-slug"); ?></span>' );
								}
							}
						});
					}
				</script>
			<?php
		}

		public static function render_options_page() {
			global $lr_mailchimp_settings;
			if( isset( $_POST['reset'] ) ){
				LR_Mailchimp_Install::reset_loginradius_mailchimp_options();
				echo '<p style="display:none;" class="lr-alert-box lr-notif">Mailchimp settings have been reset and default values loaded</p>';
				echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
			}

			$lr_mailchimp_settings = get_option( 'LR_Mailchimp_Settings' );
			?>

			<div class="wrap lr-wrap cf">
				<header>
					<h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Mailchimp</em></h2>
				</header>

				<div class="lr-tab-frame lr-active">
					<form action="options.php" method="post">
						<?php
							settings_fields( 'lr_mailchimp_settings' );
							settings_errors();
						?>
						
						<div class="lr_options_container">
							<div class="lr-row">
								<h3>
									<?php _e( 'Mailchimp Integration', 'lr-plugin-slug' ); ?>
								</h3>
								<div>
									<input type="checkbox" class="lr-toggle" id="lr-mailchimp-enable" name="LR_Mailchimp_Settings[mailchimp_subscribe]" value='1' <?php echo ( isset( $lr_mailchimp_settings['mailchimp_subscribe'] ) && $lr_mailchimp_settings['mailchimp_subscribe'] == '1' ) ? 'checked' : '' ?> />
									<label class="lr-show-toggle" for="lr-mailchimp-enable">
										<?php _e( 'Enable Mailchimp' ); ?>
										<span class="lr-tooltip" data-title="<?php _e( 'Turn on, if you want to automatically subscribe users to Mailchimp List when they register through Social Login', 'lr-plugin-slug' ); ?>">
											<span class="dashicons dashicons-editor-help"></span>
										</span>
									</label>
								</div>
							</div>
						</div>

						<div style="position: relative;">
							<div class="lr-option-disabled-hr lr-mailchimp" style="display: none;"></div>
							<div class="lr_options_container">
								<div class="lr-row">
									<h3>
										<?php _e( 'Mailchimp API Key', 'lr-plugin-slug' ); ?>
									</h3>
									<div>
										<label>
											<span class="lr_property_title">
												<?php _e( 'Mailchimp API Key', 'lr-plugin-slug' ); ?>
												<span class="lr-tooltip" data-title="<?php _e('Enter your Mailchimp API Key (After entering your Mailchimp API Key, hit the Save button)', 'lr-plugin-slug' ); ?>">
													<span class="dashicons dashicons-editor-help"></span>
												</span>
											</span>
											<input type="text" name="LR_Mailchimp_Settings[mailchimp_apikey]" id="login_radius_mc_apikey" class="lr-row-field" value="<?php echo isset( $lr_mailchimp_settings['mailchimp_apikey'] ) ? trim( $lr_mailchimp_settings['mailchimp_apikey'] ) : ''; ?>" />
										</label>
									</div>

									<p class="submit">
										<input type="submit" class="button button-primary" value="Save" style="float:left" onclick="loginRadiusSaveMCAPIKey( document.getElementById( 'login_radius_mc_apikey' ).value.trim() )" />
									</p>

									<div id="mc-apikey-import-message"></div>
								</div>

								<div class="lr-row">
									<h3>
										<?php _e( 'Select the list, you want to subscribe users to.', 'lr-plugin-slug' ); ?>
									</h3>

									<select id="login_radius_mailchimp_lists" name="LR_Mailchimp_Settings[mailchimp_lists]" class="lr-row-field" onchange="loginRadiusGetMCMergeVars( this.value.trim() )">
										<option value=''>--Select a List--</option>
									</select>
								</div>

								<div class="lr-row">
									<h3>
										<?php _e( 'Map your Mailchimp List Merge Fields to the Social Login profile data.', 'lr-plugin-slug' ); ?>
									</h3>
									<div id="login_radius_mailchimp_mapping">
										<div>
											<span class="lr_property_title">
												<?php _e( 'Email', 'lr-plugin-slug' ); ?>
											</span>
											<select name="LR_Mailchimp_Settings[mailchimp_lists]" class="lr-row-field">
												<option value=''>--Select a List--</option>
											</select>
										</div>
									</div>
								</div>

								<div class="lr-row">
									<h3>
										<?php _e( 'Enable MailChimp Verification', 'lr-plugin-slug' ); ?>
									</h3>
									<div>
										<input type="checkbox" class="lr-toggle" id="lr-mailchimp-verification-enable" name="LR_Mailchimp_Settings[enable_email_confirm]" value='1' <?php echo ( isset( $lr_mailchimp_settings['enable_email_confirm'] ) && $lr_mailchimp_settings['enable_email_confirm'] == '1' ) ? 'checked' : '' ?> />
										<label class="lr-show-toggle" for="lr-mailchimp-verification-enable">
											<?php _e( 'Enable MailChimp Double Opt-In Verification' ); ?>
											<span class="lr-tooltip" data-title="<?php _e( 'Turn on, if you want Mailchimp to send an email to the user after they have been added to the selected list', 'lr-plugin-slug' ); ?>">
												<span class="dashicons dashicons-editor-help"></span>
											</span>
										</label>
									</div>
								</div>
							</div>

						</div>
						<p class="submit">
							<?php submit_button( 'Save Settings', 'primary', 'submit', false ); ?>
						</p>
					</form>
				</div>
				<?php do_action( 'lr_reset_admin_ui','Mailchimp' ); ?>
				<?php
					self::mailchimp_script();

					// Populate Mailchimp apikey and lists if saved in database.
					if ( isset( $lr_mailchimp_settings['mailchimp_subscribe'] ) && $lr_mailchimp_settings['mailchimp_subscribe'] == '1' && isset( $lr_mailchimp_settings['mailchimp_apikey'] ) && $lr_mailchimp_settings['mailchimp_apikey'] != '' ) {
						global $loginRadiusMailchimp;
						$loginRadiusMailchimp = new MCAPI( trim( $lr_mailchimp_settings['mailchimp_apikey'] ) );
						?>
							<script>
								loginRadiusSaveMCAPIKey( '<?php echo trim( $lr_mailchimp_settings['mailchimp_apikey'] ) ?>' );
							</script>
						<?php
						if ( isset( $lr_mailchimp_settings['mailchimp_lists'] ) && $lr_mailchimp_settings['mailchimp_lists'] != '' ){
							$lists = $loginRadiusMailchimp->listMergeVars( $lr_mailchimp_settings['mailchimp_lists'] );
							?>
								<script>
									loginRadiusGetMCMergeVars( '<?php echo $lr_mailchimp_settings['mailchimp_lists'] ?>' );
								</script>
							<?php
						}
					}
				?>
			</div>
		<?php }
	}

}