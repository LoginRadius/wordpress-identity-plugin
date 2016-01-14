<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The Social Invite Admin Settings Page.
 */
if ( ! class_exists( 'LR_Social_Invite_Admin_Settings' ) ) {

    class LR_Social_Invite_Admin_Settings {

        public static function render_options_page() {
            global $lr_social_invite_settings;

            if (isset($_POST['reset'])) {
                LR_Social_Invite_Install:: reset_loginradius_social_invite_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Social Invite settings have been reset and default values loaded</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
            ?>
            <div class="wrap lr-wrap cf">

                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Social Invite</em></h2>
                </header>

                <div class="lr-tab-frame lr-active">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('lr_social_invite_settings');
                        settings_errors();
                        ?>
                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Social Invite Settings', 'lr-plugin-slug'); ?>
                                </h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-social-invite-enable" name="LR_Social_Invite_Settings[social_invite_enable]" value="1" <?php echo isset($lr_social_invite_settings['social_invite_enable']) && $lr_social_invite_settings['social_invite_enable'] == '1' ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-social-invite-enable">
                                        <?php _e('Enable Social Invite'); ?>
                                        <span class="lr-tooltip" data-title="Turn on, if you want to enable Social Invite">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3>
                                    <?php _e('Short Code for Social Invite','lr-plugin-slug'); ?>
                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the social invite interface','LoginRadius'); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </h3>
                                <div>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[LoginRadius_Social_Invite]</textarea>
                                </div>
                            </div><!-- lr-row -->
                        </div>

                        <div style="position: relative;">
                            <div class="lr-option-disabled-hr lr-social-invite" style="display: none;"></div>
                            <div class="lr_options_container">

                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Sorting Settings', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <span class="lr_property_title">
                                            <?php _e('Sort By', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="Sort by Name or Social Provider">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </span>
                                        <select name="LR_Social_Invite_Settings[sort_by]" class="lr-row-field">
                                            <option value="name" <?php echo (isset($lr_social_invite_settings['sort_by']) && $lr_social_invite_settings['sort_by'] == "name") ? "selected" : ""; ?> >Name</option>
                                            <option value="provider" <?php echo (isset($lr_social_invite_settings['sort_by']) && $lr_social_invite_settings['sort_by'] == "provider") ? "selected" : ""; ?> >Provider</option>
                                        </select>
                                    </div>

                                    <div>
                                        <span class="lr_property_title">
                                            <?php _e('Sort Direction', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="Ascending/Decending sort direction">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </span>
                                        <select name="LR_Social_Invite_Settings[sort_direction]" class="lr-row-field">
                                            <option value="asc" <?php echo (isset($lr_social_invite_settings['sort_direction']) && $lr_social_invite_settings['sort_direction'] == "asc") ? "selected" : ""; ?> >Ascending</option>
                                            <option value="desc" <?php echo (isset($lr_social_invite_settings['sort_direction']) && $lr_social_invite_settings['sort_direction'] == "desc") ? "selected" : ""; ?> >Descending</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Message Settings', 'lr-plugin-slug'); ?>
                                    </h3>

                                    <div>
                                        <div>
                                            <input type="checkbox" class="lr-toggle" id="wp-enable-si-editable" name="LR_Social_Invite_Settings[enable_editable]" value="1" <?php echo isset($lr_social_invite_settings['enable_editable']) && $lr_social_invite_settings['enable_editable'] == '1' ? 'checked' : ''; ?> />
                                            <label class="lr-show-toggle" for="wp-enable-si-editable">
                                                <?php _e('Make text areas editable', 'lr-plugin-slug'); ?>
                                                <span class="lr-tooltip" data-title="Allows the user to edit the subject and message areas of social invites.">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Social Invite Subject', 'lr-plugin-slug'); ?>
                                                <span class="lr-tooltip" data-title="Enter the subject for all social invites">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_Social_Invite_Settings[subject]" class="lr-row-field" value= "<?php echo htmlspecialchars($lr_social_invite_settings['subject']); ?>" />
                                        </label>
                                    </div>

                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e( 'Social Invite Message', 'lr-plugin-slug' ); ?>
                                                <span class="lr-tooltip" data-title="Enter the message for all social invites">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <textarea name="LR_Social_Invite_Settings[message]" class="lr-row-field"><?php echo htmlspecialchars($lr_social_invite_settings['message']); ?></textarea>
                                        </label>
                                    </div>
                                </div>

                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Facebook ID', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <label>
                                            <span class="lr_property_title">
                                                <?php _e('Facebook ID', 'lr-plugin-slug'); ?>
                                                <span class="lr-tooltip" data-title="Facebook ID used in Social Invite Interface">
                                                    <span class="dashicons dashicons-editor-help"></span>
                                                </span>
                                            </span>
                                            <input type="text" name="LR_Social_Invite_Settings[fb_id]" class="lr-row-field" value= "<?php echo htmlspecialchars($lr_social_invite_settings['fb_id']); ?>" />
                                        </label>
                                    </div>
                                </div>

                                <!-- Activate Custom Email -->
                                <div class="lr-row">
                                    <h3>
                                        <?php _e('Custom Email Settings', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="lr-enable-custom-email" name="LR_Social_Invite_Settings[enable_custom_email]" value="1" <?php echo isset($lr_social_invite_settings['enable_custom_email']) && $lr_social_invite_settings['enable_custom_email'] == '1' ? 'checked' : ''; ?> />
                                        <label class="lr-show-toggle" for="lr-enable-custom-email">
                                            <?php _e('Enable Custom Email', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="Enable custom email for email invites. User email address and name will be used as default.">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                    </div>

                                    <div class="lr-row lr-custom-email-settings">
                                        <div>
                                            <label>
                                                <span class="lr_property_title">
                                                    <?php _e('From Name', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip" data-title="From name used for email messages">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </span>
                                                <input type="text" name="LR_Social_Invite_Settings[email_name]" class="lr-row-field" value= "<?php echo htmlspecialchars($lr_social_invite_settings['email_name']); ?>" />
                                            </label>
                                        </div>
                                        <div>
                                            <label>
                                                <span class="lr_property_title">
                                                    <?php _e('From Email Address ', 'lr-plugin-slug'); ?>
                                                    <span class="lr-tooltip" data-title="From email address used for email messages">
                                                        <span class="dashicons dashicons-editor-help"></span>
                                                    </span>
                                                </span>
                                                <input type="text" name="LR_Social_Invite_Settings[email_address]" class="lr-row-field" value= "<?php echo htmlspecialchars($lr_social_invite_settings['email_address']); ?>" />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="submit">
                            <?php submit_button('Save Options', 'primary', 'submit', false); ?>
                        </p>
                    </form>
                    <?php do_action( 'lr_reset_admin_ui','Social Invite' ); ?>
                </div>
            </div>

            <?php
        }

    }

}