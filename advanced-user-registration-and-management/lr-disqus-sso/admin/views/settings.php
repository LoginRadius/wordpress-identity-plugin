<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * The disqus SSO admin settings page.
 */
if ( ! class_exists( 'LR_Disqus_Admin_Settings' ) ) {

	class LR_Disqus_Admin_Settings {

		/**
         * generate page selection options
         * 
         * @param type $pages
         * @param type $settings
         * @param type $name
         * @return string
         */
        private static function select_field($pages, $settings, $name) {
            $output = '<select class="lr-row-field" name="LR_Disqus_Settings[' . $name . ']">';
            $output .= '<option value="">'.__(' --- Select Page --- ','LoginRadius').'</option>';
            foreach ($pages as $page) {
                $select_page = '';
                if (isset($settings[$name]) && $page->ID == $settings[$name]) {
                    $select_page = ' selected="selected"';
                }
                $output .= '<option value="' . $page->ID . '" ' . $select_page . '>' . $page->post_title . '</option>';
            }
            $output .= '</select>';
            return $output;
        }

		public static function render_options_page() {
			global $lr_disqus_settings;
			
			$args = array(
                'sort_order' => 'ASC',
                'sort_column' => 'post_title',
                'hierarchical' => 1,
                'exclude' => '',
                'include' => '',
                'meta_key' => '',
                'meta_value' => '',
                'authors' => '',
                'child_of' => 0,
                'parent' => -1,
                'exclude_tree' => '',
                'number' => '',
                'offset' => 0,
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $pages = get_pages($args);

			if( isset( $_POST['reset'] ) ){
				LR_Disqus_Install::reset_options();
				echo '<p style="display:none;" class="lr-alert-box lr-notif">Disqus SSO settings have been reset and default values loaded</p>';
				echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
			}
			?>
			<div class="wrap lr-wrap cf">
				<header>
					<h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Disqus SSO</em></h2>
				</header>

				<div class="lr-tab-frame lr-active">
					<form action="options.php" method="post">
						<?php
						settings_fields( 'lr_disqus_settings' );
						settings_errors();
						?>

						<div class="lr_options_container">
							<div class="lr_enable_switch lr-row">
								<h3>
									<?php _e( 'Enable Disqus SSO Integration', 'LoginRadius' ); ?>
								</h3>
								<div>
									<input type="checkbox" class="lr-toggle" id="lr-disqus-enable" name="LR_Disqus_Settings[disqus_sso_enable]" value="1" <?php echo isset( $lr_disqus_settings['disqus_sso_enable'] ) && $lr_disqus_settings['disqus_sso_enable'] == '1' ? 'checked' : ''; ?> />
									<label class="lr-show-toggle" for="lr-disqus-enable">
										<?php _e('Enable Disqus SSO', 'LoginRadius'); ?>
										<span class="lr-tooltip" data-title="<?php _e('Turn on to enable Disqus SSO with LoginRadius. Note: The Disqus Wordpress plugin must be installed and enabled for SSO', 'LoginRadius'); ?>">
										    <span class="dashicons dashicons-editor-help"></span>
										</span>
									</label>
								</div>
							</div>
							<div style="position:relative;">
								<div class="lr-option-disabled-hr disqus"></div>
								<div class="lr-row">
									<div>
	                                    <label>
	                                        <span class="lr_property_title">
	                                            <?php _e('Disqus Popup Page', 'LoginRadius'); ?>
	                                            <span class="lr-tooltip" data-title="<?php _e( 'Select the page you would like to use as your Disqus popup.','LoginRadius' );?>">
	                                                <span class="dashicons dashicons-editor-help"></span>
	                                            </span>
	                                        </span>
	                                        <?php echo self::select_field( $pages, $lr_disqus_settings, 'lr_disqus_sso_page_id' ); ?>
	                                    </label>
	                                </div>
								</div>
								<div class="lr-row">
	                                <label>
	                                    <span class="lr_property_title">
	                                        <?php _e('Login Interface Title', 'LoginRadius'); ?>
	                                        <span class="lr-tooltip" data-title="Set the title for the social login interface shown in the popup">
	                                            <span class="dashicons dashicons-editor-help"></span>
	                                        </span>
	                                    </span>
	                                    <input type="text" name="LR_Disqus_Settings[popup_title]" class="lr-row-field" value= "<?php echo htmlspecialchars( $lr_disqus_settings['popup_title'] ); ?>" />
	                                </label>
								</div>
							</div>
						</div>
						<p class="submit">
							<?php submit_button( 'Save Options', 'primary', 'submit', false ); ?>
						</p>
					</form>
					<?php do_action( 'lr_reset_admin_ui','Disqus SSO' ); ?>
				</div>
			</div>

			<?php
		}
	}
}