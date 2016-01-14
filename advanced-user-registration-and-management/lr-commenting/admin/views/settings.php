<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The commenting admin settings page.
 */
if ( ! class_exists( 'LR_Commenting_Admin_Settings' ) ) {

    class LR_Commenting_Admin_Settings {

        public static function render_options_page() {
            global $lr_commenting_settings;

            if ( isset( $_POST['reset'] ) ) {
                LR_Commenting_Install:: reset_loginradius_commenting_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Commenting settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
            ?>
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Social Commenting</em></h2>
                </header>

                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('lr_commenting_settings');
                        settings_errors();
                        ?>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Social Commenting Settings', 'lr-plugin-slug' ); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-comment-enable" name="LR_Commenting_Settings[commenting_enable]" value="1" <?php echo isset($lr_commenting_settings['commenting_enable']) && $lr_commenting_settings['commenting_enable'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-comment-enable">
                                        <?php _e('Enable Social Commenting'); ?>
                                        <span class="lr-tooltip" data-title="Turn on, if you want to enable Social Commenting">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div style="position: relative;">
                            <div class="lr-option-disabled-hr lr-commenting" style="display: none;"></div>
                            <div class="lr_options_container">

                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Social Commenting Features', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="lr-comment-sharing" name="LR_Commenting_Settings[sharing_enable]" value="1" <?php echo isset($lr_commenting_settings['sharing_enable']) && $lr_commenting_settings['sharing_enable'] == '1' ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle" for="lr-comment-sharing">
                                            <?php _e('Enable Comment Sharing', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="Turn on, if you want to enable comment sharing with Facebook, Twitter and LinkedIn">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="lr-comment-formatting" name="LR_Commenting_Settings[editor_enable]" value="1" <?php echo isset($lr_commenting_settings['editor_enable']) && $lr_commenting_settings['editor_enable'] == '1' ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle" for="lr-comment-formatting">
                                            <?php _e('Enable Comment Formatting', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="<?php _e('Turn on, if you want to enable the formatting of comments to display options like bold, italic, etc.','lr-plugin-slug'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                        <div class="lr-row lr-comment-images">
                                            <input type="checkbox" class="lr-toggle" id="lr-comment-image-upload" name="LR_Commenting_Settings[image_upload_enable]" value="1" <?php echo isset($lr_commenting_settings['image_upload_enable']) && $lr_commenting_settings['image_upload_enable'] == '1' ? 'checked' : ''; ?> />
                                            <label class="lr-show-toggle" for="lr-comment-image-upload">
                                                <?php _e('Enable Image Upload', 'lr-plugin-slug'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Turn on, if you want to enable image upload in the commenting interface.','lr-plugin-slug'); ?>">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Comment Type Settings', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Comment Type', 'lr-plugin-slug'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Select the type of comments to be displayed in the commenting area', 'lr-plugin-slug'); ?>">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <select name="LR_Commenting_Settings[display_comment_type]" class="lr-row-field">
                                                <option value="all" <?php echo ( isset( $lr_commenting_settings['display_comment_type'] ) && $lr_commenting_settings['display_comment_type'] == 'all' ) ? "selected" : ""; ?> >All</option>
                                                <option value="comment" <?php echo ( isset( $lr_commenting_settings['display_comment_type'] ) && $lr_commenting_settings['display_comment_type'] == 'comment' ) ? "selected" : ""; ?> >Comments</option>
                                                <option value="pingback" <?php echo ( isset( $lr_commenting_settings['display_comment_type'] ) && $lr_commenting_settings['display_comment_type'] == 'pingback' ) ? "selected" : ""; ?> >Pingbacks</option>
                                                <option value="trackback" <?php echo ( isset( $lr_commenting_settings['display_comment_type'] ) && $lr_commenting_settings['display_comment_type'] == 'trackback' ) ? "selected" : ""; ?> >Trackbacks</option>
                                                <option value="pings" <?php echo ( isset( $lr_commenting_settings['display_comment_type'] ) && $lr_commenting_settings['display_comment_type'] == 'pings' ) ? "selected" : ""; ?> >Trackbacks &amp; Pingbacks</option>
                                            </select>
                                        </label>
                                    </div>
                                </div>

                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Approval Settings', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="lr-comment-aproval" name="LR_Commenting_Settings[approve_social_user_comments]" value="1" <?php echo isset($lr_commenting_settings['approve_social_user_comments']) && $lr_commenting_settings['approve_social_user_comments'] == '1' ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle" for="lr-comment-aproval">
                                            <?php _e('Auto Approve Comments for Users that Log in with Social ID Providers', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="<?php _e('Turn on, if you want to approve comments made by users who are using social ID provider for login [Note: This option is ON by default as bots cannot bypass Social Login]','lr-plugin-slug'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="wp-comment-aproval" name="LR_Commenting_Settings[approve_wp_user_comments]" value="1" <?php echo isset($lr_commenting_settings['approve_wp_user_comments']) && $lr_commenting_settings['approve_wp_user_comments'] == '1' ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle" for="wp-comment-aproval">
                                            <?php _e('Auto Approve Comments for Existing Registered Users', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="Turn on, if you want to approve comments for existing registered users">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Message Settings -->
                            <div class="lr_options_container">
                                <div class="lr-row">
                                    <h3><?php _e('Custom Messages', 'lr-plugin-slug'); ?></h3>

                                    <!-- Comment Editor Title -->
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Comment Editor Title', 'lr-plugin-slug'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Set the title for the Comment editor section','lr-plugin-slug'); ?>">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_Commenting_Settings[commenting_title]" class="lr-row-field" value= "<?php echo htmlspecialchars($lr_commenting_settings['commenting_title']); ?>" />
                                        </label>
                                    </div>

                                    <!-- No Comment Message -->
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('No Comment Message', 'lr-plugin-slug'); ?>
                                                <span class="lr-tooltip" data-title="<?php _e('Set the message to show to user when there are no comments','lr-plugin-slug'); ?>">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_Commenting_Settings[no_comment_msg]" class="lr-row-field" value= "<?php echo htmlspecialchars($lr_commenting_settings['no_comment_msg']); ?>" />
                                        </label>
                                    </div>
                                </div>

                                <!-- Activate Moderation -->
                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Moderation Settings', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="wp-enable-moderation-msg" name="LR_Commenting_Settings[enable_moderation_msg]" value="1" <?php echo isset($lr_commenting_settings['enable_moderation_msg']) && $lr_commenting_settings['enable_moderation_msg'] == '1' ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle" for="wp-enable-moderation-msg">
                                            <?php _e('Show a Moderation Message on comments made by logged in users awaiting approval', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="Turn on, if you want to show the moderation approval message">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                    </div>
                                    <!-- Moderation Message -->
                                    <div class="lr-row lr-moderation-msg">
                                        <span class="lr_property_title">
                                            <?php _e('Moderation Message', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="Set the message for moderation approval">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </span>
                                        <input type="text" name="LR_Commenting_Settings[moderation_msg]" class="lr-row-field" value= "<?php echo htmlspecialchars($lr_commenting_settings['moderation_msg']); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="submit">
                            <?php submit_button('Save Options', 'primary', 'submit', false); ?>
                        </p>
                    </form>
                    <?php do_action( 'lr_reset_admin_ui','Social Commenting' ); ?>
                </div>
            </div>
            <?php
        }

    }

}