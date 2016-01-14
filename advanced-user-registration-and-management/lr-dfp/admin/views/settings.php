<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_DFP_Settings' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_DFP_Settings {

        /**
         * Constructor
         */
        public function __construct() {
        }

        public function render_options_page() {
            global $lr_dfp_settings;
            
            if( isset( $_POST['reset'] ) ) {
                LR_DFP_Install::reset_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">DFP settings have been reset and default values</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery( ".lr-notif" ).slideDown().delay(3000).slideUp();});</script>';
            }
            ?>
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>DFP</em></h2>
                </header>

                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields( 'lr_dfp_settings' );
                        settings_errors();
                        ?>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e( 'Enable DFP', 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip" data-title="Turn on to enable DFP cookie output">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-comment-enable" name="LR_DFP_Settings[enable]" value="1" <?php echo isset( $lr_dfp_settings['enable'] ) && $lr_dfp_settings['enable'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-comment-enable">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e( 'Enable DFP', 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip" data-title="Turn on to enable DFP cookie output">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </h3>
                                <div>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="Age" <?php echo in_array( 'Age', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Age</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="BirthDate" <?php echo in_array( 'BirthDate', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >BirthDate</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="Gender" <?php echo in_array( 'Gender', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Gender</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="Religion" <?php echo in_array( 'Religion', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Religion</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="Addresses" <?php echo in_array( 'Addresses', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Addresses</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="FavoriteThings" <?php echo in_array( 'FavoriteThings', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Favorite Things</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="InterestedIn" <?php echo in_array( 'InterestedIn', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Interested In</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="Interests" <?php echo in_array( 'Interests', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Interests</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="Books" <?php echo in_array( 'Books', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?>>Books</span>
                                    <span><input type="checkbox" name="LR_DFP_Settings[target][]" value="CustomFields" <?php echo in_array( 'CustomFields', $lr_dfp_settings['target'] ) ? 'checked' : ''; ?> >Custom Fields</span>
                                </div>
                            </div>
                        </div>
                        <p class="submit">
                            <?php submit_button( 'Save Options', 'primary', 'submit', false ); ?>
                        </p>
                    </form>
                    <?php do_action( 'lr_reset_admin_ui','DFP' ); ?>
                </div>
            </div>
            <?php
        }
    }
}
