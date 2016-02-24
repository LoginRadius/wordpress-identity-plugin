<?php
/**
 * @file
 * The Admin Panel and related tasks are handled in this file.
 */
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin settings page.
 */
if ( ! class_exists( 'LR_Google_Analytics_Admin_Settings' ) ) {

    class LR_Google_Analytics_Admin_Settings {
        /**
         * Render settings page
         */
        public static function render_options_page() {

            if ( isset( $_POST['reset'] ) ) {
                LR_Google_Analytics_Install::reset_google_analytics_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">' . __( 'Google Analytics settings have been reset and default values loaded', 'lr-plugin-slug' ) . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
            $lr_google_analytics_settings = get_option('LR_Google_Analytics_Settings');
            ?>
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Google Analytics</em></h2>
                </header>
                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('lr_google_analytics_settings');
                        settings_errors();
                        ?>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Google Analytics', 'lr-plugin-slug'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-google_analytics-enable" name="LR_Google_Analytics_Settings[ga_enable]" value="1" <?php echo isset($lr_google_analytics_settings['ga_enable']) && $lr_google_analytics_settings['ga_enable'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-google_analytics-enable">
                                        <?php _e('Enable Google Analytics', 'lr-plugin-slug'); ?>
                                        <span class="lr-tooltip" data-title="<?php _e('Turn on to enable Google Analytics integration with LoginRadius', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="lr_options_container">
                            <div class="lr-option-disabled-hr lr-google_analytics" style="display: none;"></div>
                            <div class="lr-row">
                                <div>
                                    <label>
                                        <span class="lr_property_title">
                                            <?php _e( 'Tracking ID', 'lr-plugin-slug' ); ?>
                                            <span class="lr-tooltip" data-title="<?php _e( 'Enter the Tracking ID from your Google Analytics account', 'lr-plugin-slug' ); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </span>
                                        <input type="text" placeholder="UA-xxxxxxxx-x" class="lr-row-field" name="LR_Google_Analytics_Settings[ga_tracking_id]" value="<?php echo isset($lr_google_analytics_settings['ga_tracking_id']) ? $lr_google_analytics_settings['ga_tracking_id'] : ''; ?>" autofill="off" autocomplete="off">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <p class="submit">
                            <?php submit_button( 'Save Options', 'primary', 'submit', false ); ?>
                        </p>
                    </form>
                    <?php do_action( 'lr_reset_admin_ui','Google Analytics' );?>
                </div>
            </div>
            <?php
        }

    }

}