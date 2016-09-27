<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The LoginRadius SSO admin settings page.
 */
if ( ! class_exists( 'LR_SSO_Admin_Settings' ) ) {

    class LR_SSO_Admin_Settings {

        public static function render_options_page() {

            if ( isset( $_POST['reset'] ) ) {
                LR_SSO_Install::reset_sso_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Single Sign On settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }

            $lr_sso_settings = get_option('LR_SSO_Settings');
            
            ?>
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Single Sign On</em></h2>
                </header>

                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields( 'lr_sso_settings' );
                        settings_errors();
                        ?>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e( 'Single Sign On', 'lr-plugin-slug' ); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-comment-enable" name="LR_SSO_Settings[sso_enable]" value="1" <?php echo isset( $lr_sso_settings['sso_enable'] ) && $lr_sso_settings['sso_enable'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-comment-enable">
                                        <?php _e( 'Enable Single Sign On (SSO)', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'Turn on to enable LoginRadius Single Sign On.', 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p class="submit">
                            <?php submit_button( 'Save Options', 'primary', 'submit', false ); ?>
                        </p>
                    </form>
                    <?php do_action( 'lr_reset_admin_ui','Single Sign On' );?>
                </div>
            </div>
            <?php
        }

    }

}