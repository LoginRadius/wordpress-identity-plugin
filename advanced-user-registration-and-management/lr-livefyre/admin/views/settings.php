<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * The disqus SSO admin settings page.
 */
if ( ! class_exists( 'LR_LiveFyre_Admin_Settings' ) ) {

	class LR_LiveFyre_Admin_Settings {

		public static function render_options_page() {
			global $lr_livefyre_settings;
			
			if( isset( $_POST['reset'] ) ){
				LR_LiveFyre_Install::reset_options();
				echo '<p style="display:none;" class="lr-alert-box lr-notif">LoginRadius LiveFyre settings have been reset and default values loaded</p>';
				echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
			}
			?>
			<div class="wrap lr-wrap cf">
				<header>
					<h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>LiveFyre Integration</em></h2>
				</header>

				<div class="lr-tab-frame lr-active">
					<form action="options.php" method="post">
						<?php
						settings_fields( 'lr_livefyre_settings' );
						settings_errors();
						?>

						<div class="lr_options_container">
							<div class="lr_enable_switch lr-row">
								<h3>
									<?php _e( 'Enable LiveFyre Integration', 'LoginRadius' ); ?>
								</h3>
								<div>
									<input type="checkbox" class="lr-toggle" id="lr-livefyre-enable" name="LR_LiveFyre_Settings[enable_livefyre]" value="1" <?php echo isset( $lr_livefyre_settings['enable_livefyre'] ) && $lr_livefyre_settings['enable_livefyre'] == '1' ? 'checked' : ''; ?> />
									<label class="lr-show-toggle" for="lr-livefyre-enable">
										<?php _e('Enable LiveFyre', 'LoginRadius'); ?>
										<span class="lr-tooltip" data-title="<?php _e('Turn on to enable LiveFyre integration with LoginRadius. Note: Your LiveFyre Enterprise account must be enabled and configured in LiveFyre Apps WordPress plugin', 'LoginRadius'); ?>">
										    <span class="dashicons dashicons-editor-help"></span>
										</span>
									</label>
								</div>
							</div>
							<div style="position:relative;">
								<div class="lr-option-disabled-hr livefyre"></div>
								<div class="lr-row">
									<div>
										<input type="checkbox" class="lr-toggle" id="lr-livefyre-wp-enable" name="LR_LiveFyre_Settings[enable_login]" value="1" <?php echo isset( $lr_livefyre_settings['enable_login'] ) && $lr_livefyre_settings['enable_login'] == '1' ? 'checked' : ''; ?> />
										<label class="lr-show-toggle" for="lr-livefyre-wp-enable">
											<?php _e('Enable Wordpress Login/Logout LiveFyre integration', 'LoginRadius'); ?>
											<span class="lr-tooltip" data-title="<?php _e('Turn on to enable LiveFyre integration with WordPress Login/Logout methods, Enabled by default', 'LoginRadius'); ?>">
											    <span class="dashicons dashicons-editor-help"></span>
											</span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<p class="submit">
							<?php submit_button( 'Save Options', 'primary', 'submit', false ); ?>
						</p>
					</form>
					<?php do_action( 'lr_reset_admin_ui','LiveFyre' ); ?>
				</div>
			</div>

			<?php
		}
	}
}