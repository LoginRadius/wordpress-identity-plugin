<?php
// LR_Social_Sharing_Settings
// Exit if called directly
if ( ! defined('ABSPATH') ) {
    exit();
}

/**
 * The main class and initialization point of the plugin settings page.
 */
if ( ! class_exists('LR_Social_Share_Settings') ) {

    class LR_Social_Share_Settings {

        private static function share_provider() {
            return array('Delicious', 'Digg', 'Email', 'Facebook', 'GooglePlus', 'Google', 'LinkedIn', 'MySpace', 'Pinterest', 'Print', 'Reddit', 'Tumblr', 'Twitter', 'Vkontakte');
        }

        private static function counter_provider() {
            return array('Facebook Like', 'Twitter Tweet', 'StumbleUpon Badge', 'Google+ Share', 'Facebook Recommend', 'Pinterest Pin it', 'Reddit', 'Hybridshare', 'Facebook Send', 'LinkedIn Share', 'Google+ +1');
        }

        private static function vertical_share_interface_position( $page, $settings ) {
            echo '<div class="lr-show-options">';
            $interface_location = array('Top Left', 'Top Right', 'Bottom Left', 'Bottom Right');
            foreach ($interface_location as $location) {
                ?>
                <label>
                    <input type="checkbox" class="lr-clicker-vr-<?php echo strtolower($page); ?>-options default" name="LoginRadius_share_settings[vertical_position][<?php echo $page; ?>][<?php echo $location; ?>]" value="<?php echo $location; ?>" <?php echo ( isset($settings['vertical_position'][$page][$location]) && $settings['vertical_position'][$page][$location] == $location ) ? 'checked' : ''; ?> />
                    <span class="lr-text"><?php _e( str_replace(' ', '-', $location ) . ' of the content', 'lr-plugin-slug' ); ?></span>
                </label>
            <?php
            }
            echo '</div>';
        }

        private static function horizontal_share_interface_position( $page, $settings ) {
            echo '<div class="lr-show-options">';
            $interface_location = array( 'Top', 'Bottom' );
            foreach ($interface_location as $location) {
                ?>
                <label>
                    <input type="checkbox" class="lr-clicker-hr-<?php echo strtolower($page); ?>-options default" name="LoginRadius_share_settings[horizontal_position][<?php echo $page; ?>][<?php echo $location; ?>]" value="<?php echo $location; ?>" <?php echo ( isset($settings['horizontal_position'][$page][$location]) && $settings['horizontal_position'][$page][$location] == $location ) ? 'checked' : ''; ?> />
                    <span class="lr-text"><?php _e( $location . ' of the content', 'lr-plugin-slug' ); ?></span>
                </label>
            <?php
            }
            echo '</div>';
        }

        private static function vertical_settings( $settings ) {
            ?>
            <!-- Vertical Sharing -->
            <div id="lr_options_tab-2" class="lr-tab-frame">
                <!-- Vertical Options -->
                <div class="lr_options_container">

                    <!-- Vertical Switch -->
                    <div class="lr_enable_switch lr-row">
                        <label for="lr-enable-vertical" class="lr-toggle">
                            <input type="checkbox" class="lr-toggle" id="lr-enable-vertical" name="LoginRadius_share_settings[vertical_enable]" value="1" <?php echo ( isset($settings['vertical_enable']) && $settings['vertical_enable'] == '1') ? 'checked' : ''; ?> <?php _e('Yes', 'lr-plugin-slug') ?> />
                            <span class="lr-toggle-name"><?php _e('Enable Vertical Widget', 'lr-plugin-slug'); ?></span>
                        </label>
                    </div>

                    <div class="lr-option-disabled-vr"></div>
                    <div class="lr_vertical_interface lr-row cf">
                        <h3><?php _e('Select the sharing theme', 'lr-plugin-slug'); ?></h3>
                        <div>
                            <input type="radio" id="lr-vertical-32-v" name="LoginRadius_share_settings[vertical_share_interface]" value="32-v" <?php echo (!isset($settings['vertical_share_interface']) || $settings['vertical_share_interface'] == '32-v' ) ? 'checked' : ''; ?> />
                            <label class="lr_vertical_interface_img" for="lr-vertical-32-v"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/32-v.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-vertical-16-v" name="LoginRadius_share_settings[vertical_share_interface]" value="16-v" <?php echo ( $settings['vertical_share_interface'] == '16-v' ) ? 'checked' : ''; ?> />
                            <label class="lr_vertical_interface_img" for="lr-vertical-16-v"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/16-v.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-vertical-hybrid-v-v" name="LoginRadius_share_settings[vertical_share_interface]" value="hybrid-v-v" <?php echo ( $settings['vertical_share_interface'] == 'hybrid-v-v' ) ? 'checked' : ''; ?> />
                            <label class="lr_vertical_interface_img" for="lr-vertical-hybrid-v-v"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/hybrid-v-v.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-vertical-hybrid-v-h" name="LoginRadius_share_settings[vertical_share_interface]" value="hybrid-v-h" <?php echo ( $settings['vertical_share_interface'] == 'hybrid-v-h' ) ? 'checked' : ''; ?> />
                            <label class="lr_vertical_interface_img" for="lr-vertical-hybrid-v-h"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/hybrid-v-h.png" ?>" /></label>
                        </div>
                    </div>

                    <div id="lr_ve_theme_options" class="lr-row cf">
                        <h3><?php _e('Select the sharing networks', 'lr-plugin-slug'); ?>
                            <span class="lr-tooltip" data-title="<?php _e('Selected sharing networks will be displayed in the widget', 'lr-plugin-slug'); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div id="lr_ve_hz_theme_options" class="cf" style="display:block;">
                            <?php
                            $share_providers = self::share_provider();
                            foreach ($share_providers as $provider) {
                                ?>
                                <label class="-lr-share-networks-list">
                                    <input type="checkbox" class="LoginRadius_ve_share_providers" name="LoginRadius_share_settings[vertical_sharing_providers][Default][<?php echo $provider; ?>]" value="<?php echo $provider; ?>" <?php echo ( isset($settings['vertical_sharing_providers']['Default'][$provider]) && $settings['vertical_sharing_providers']['Default'][$provider] == $provider ) ? 'checked' : ''; ?> />
                                    <span class="lr-text lr-icon-<?php echo strtolower($provider); ?>"><?php echo $provider; ?></span>
                                </label>
                            <?php } ?>
                            <div id="loginRadiusVerticalSharingLimit" class="lr-alert-box" style="display:none; margin-bottom: 5px;"><?php _e('You can select only eight providers', 'lr-plugin-slug') ?>.</div>
                            <p class="lr-footnote">*<?php _e('All other icons will be included in the pop-up', 'lr-plugin-slug'); ?></p>
                        </div>

                        <!-- Other than square sharing -->
                        <div id="lr_ve_ve_theme_options" style="display:none;">
                                <?php
                                $counter_providers = self::counter_provider();
                                foreach ($counter_providers as $provider) {
                                    ?>
                                <label>
                                    <input type="checkbox" class="LoginRadius_ve_ve_share_providers" name="LoginRadius_share_settings[vertical_sharing_providers][Hybrid][<?php echo $provider; ?>]" value="<?php echo $provider; ?>" <?php echo ( isset($settings['vertical_sharing_providers']['Hybrid'][$provider]) && $settings['vertical_sharing_providers']['Hybrid'][$provider] == $provider ) ? 'checked' : ''; ?> />
                                    <span class="lr-text"><?php echo $provider; ?></span>
                                </label>
                            <?php } ?>
                            <div id="loginRadiusVerticalVerticalSharingLimit" class="lr-alert-box" style="display:none; margin-bottom: 5px;">
                                <?php _e('You can select only eight providers', 'lr-plugin-slug') ?>.
                            </div>
                            <p class="lr-footnote"></p>
                        </div>
                    </div>

                    <div class="lr-row cf" id="login_radius_vertical_rearrange_container">
                        <h3 class="lr-column2">
                                <?php _e('Select the sharing networks order', 'lr-plugin-slug') ?>
                            <span class="lr-tooltip" data-title="<?php _e('Drag the icons around to set the order', 'lr-plugin-slug'); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div class="lr-column2 lr-vr-sortable">
                            <ul id="loginRadiusVerticalSortable" class="cf">
                                <?php
                                if ( isset( $settings['vertical_rearrange_providers'] ) && count( $settings['vertical_rearrange_providers'] ) > 0 ) {
                                    foreach ( $settings['vertical_rearrange_providers'] as $provider ) {
                                        ?>
                                        <li title="<?php echo $provider ?>" id="loginRadiusVerticalLI<?php echo $provider ?>" class="lrshare_iconsprite32 lr-icon-<?php echo strtolower($provider) ?>">
                                            <input type="hidden" name="LoginRadius_share_settings[vertical_rearrange_providers][]" value="<?php echo $provider ?>" />
                                        </li>
                                    <?php
                                }
                            }
                            ?>
                            </ul>
                            <ul class="lr-static">
                                <li title="More" id="loginRadiusHorizontalLImore" class="lr-pin lr-icon-more lrshare_evenmore">
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="lr-row">
                        <h3>
                            <?php _e('Choose the location(s) to display the widget', 'lr-plugin-slug') ?>
                            <span class="lr-tooltip" data-title="<?php _e('Sharing widget will be displayed at the selected location(s)', 'lr-plugin-slug'); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-vr-home" name="LoginRadius_share_settings[lr-clicker-vr-home]" value="1" <?php echo ( isset($settings['lr-clicker-vr-home']) && $settings['lr-clicker-vr-home'] == '1') ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-vr-home">
                                <?php _e('Home Page', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Home page of your blog', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::vertical_share_interface_position('Home', $settings); ?>
                        </div>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-vr-post" name="LoginRadius_share_settings[lr-clicker-vr-post]" value="1" <?php echo ( isset($settings['lr-clicker-vr-post']) && $settings['lr-clicker-vr-post'] == '1' ) ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-vr-post">
                                <?php _e('Blog Posts','lr-plugin-slug');?>
                                <span class="lr-tooltip" data-title="<?php _e('Each post of your blog', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::vertical_share_interface_position('Post', $settings); ?>
                        </div>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-vr-static" name="LoginRadius_share_settings[lr-clicker-vr-static]" value="1" <?php echo ( isset($settings['lr-clicker-vr-static']) && $settings['lr-clicker-vr-static'] == '1' ) ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-vr-static">
                                <?php _e('Static Pages', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Static pages of your blog (e.g &ndash; about, contact, etc.)', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::vertical_share_interface_position('Static', $settings); ?>
                        </div><!-- unnamed div -->
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-vr-custom" name="LoginRadius_share_settings[lr-clicker-vr-custom]" value="1" <?php echo ( isset($settings['lr-clicker-vr-custom']) && $settings['lr-clicker-vr-custom'] == '1' ) ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-vr-custom">
                                <?php _e('Custom Post Types', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Custom Post Types', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::vertical_share_interface_position('Custom', $settings); ?>
                        </div><!-- unnamed div -->
                    </div>
                </div><!-- Container -->
                <!-- End Vertical Sharing -->
            </div><?php
        }

        private static function horizontal_settings($settings) {
            ?>
            <!-- Horizontal Sharing -->
            <div id="lr_options_tab-1" class="lr-tab-frame lr-active">

                <!-- Horizontal Options -->
                <div class="lr_options_container">

                    <!-- Horizontal Switch -->
                    <div class="lr_enable_switch lr-row">
                        <label for="lr-enable-horizontal" class="lr-toggle">
                            <input type="checkbox" class="lr-toggle" id="lr-enable-horizontal" name="LoginRadius_share_settings[horizontal_enable]" value="1" <?php echo ( isset($settings['horizontal_enable']) && $settings['horizontal_enable'] == '1') ? 'checked' : ''; ?> />
                            <span class="lr-toggle-name"><?php _e('Enable Horizontal Widget', 'lr-plugin-slug'); ?></span>
                        </label>
                    </div>
                    <div class="lr-option-disabled-hr"></div>
                    <div class="lr_horizontal_interface lr-row">
                        <h3><?php _e('Select the sharing theme', 'lr-plugin-slug'); ?></h3>
                        <div>
                            <input type="radio" id="lr-horizontal-responsive" name="LoginRadius_share_settings[horizontal_share_interface]" value="responsive" <?php echo ( ! isset( $settings['horizontal_share_interface'] ) || $settings['horizontal_share_interface'] == 'responsive' ) ? 'checked' : ''; ?> />
                            <label class="lr_horizontal_interface_img" for="lr-horizontal-responsive"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/responsive.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-horizontal-lrg" name="LoginRadius_share_settings[horizontal_share_interface]" value="32-h" <?php echo ( $settings['horizontal_share_interface'] == '32-h' ) ? 'checked' : ''; ?> />
                            <label class="lr_horizontal_interface_img" for="lr-horizontal-lrg"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/32-h.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-horizontal-responce" name="LoginRadius_share_settings[horizontal_share_interface]" value="16-h" <?php echo ( $settings['horizontal_share_interface'] == '16-h' ) ? 'checked' : ''; ?> />
                            <label class="lr_horizontal_interface_img" for="lr-horizontal-sml"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/16-h.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-single-lg-h" name="LoginRadius_share_settings[horizontal_share_interface]" value="single-lg-h" <?php echo ( $settings['horizontal_share_interface'] == 'single-lg-h' ) ? 'checked' : ''; ?> />
                            <label class="lr_horizontal_interface_img" for="lr-single-lg-h"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/single-lg-h.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-single-sm-h" name="LoginRadius_share_settings[horizontal_share_interface]" value="single-sm-h" <?php echo ( $settings['horizontal_share_interface'] == 'single-sm-h' ) ? 'checked' : ''; ?> />
                            <label class="lr_horizontal_interface_img" for="lr-single-sm-h"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/single-sm-h.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-sharing/hybrid-h-h" name="LoginRadius_share_settings[horizontal_share_interface]" value="hybrid-h-h" <?php echo ( $settings['horizontal_share_interface'] == 'hybrid-h-h' ) ? 'checked' : ''; ?> />
                            <label class="lr_horizontal_interface_img" for="lr-sharing/hybrid-h-h"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/hybrid-h-h.png" ?>" /></label>
                        </div>
                        <div>
                            <input type="radio" id="lr-hybrid-h-v" name="LoginRadius_share_settings[horizontal_share_interface]" value="hybrid-h-v" <?php echo ( $settings['horizontal_share_interface'] == 'hybrid-h-v' ) ? 'checked' : ''; ?> />
                            <label class="lr_horizontal_interface_img" for="lr-hybrid-h-v"><img src="<?php echo LR_SHARE_PLUGIN_URL . "/assets/images/sharing/hybrid-h-v.png" ?>" /></label>
                        </div>
                    </div>
                    <div id="lr_hz_theme_options" class="lr-row cf">
                        <h3><?php _e( 'Select the sharing networks', 'lr-plugin-slug' ); ?>
                            <span class="lr-tooltip" data-title="<?php _e( 'Selected sharing networks will be displayed in the widget', 'lr-plugin-slug' ); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div id="lr_hz_hz_theme_options" style="display:block;">
                            <?php
                            $share_providers = self::share_provider();
                            foreach ($share_providers as $provider) {
                                ?>
                                <label class="lr-sharing-cb">
                                    <input type="checkbox" class="LoginRadius_hz_share_providers" name="LoginRadius_share_settings[horizontal_sharing_providers][Default][<?php echo $provider; ?>]" value="<?php echo $provider; ?>" <?php echo ( isset($settings['horizontal_sharing_providers']['Default'][$provider]) && $settings['horizontal_sharing_providers']['Default'][$provider] == $provider ) ? 'checked' : ''; ?> />
                                    <span class="lr-text lr-icon-<?php echo strtolower($provider); ?>"><?php echo $provider; ?></span>
                                </label>
                            <?php } ?>
                            <div id="loginRadiusHorizontalSharingLimit" class="lr-alert-box" style="display:none; margin-bottom: 5px;"><?php _e('You can select only eight providers', 'lr-plugin-slug') ?>.</div>
                            <p class="lr-footnote">*<?php _e('All other icons will be included in the pop-up', 'lr-plugin-slug'); ?></p>
                        </div>
                        <div id="lr_hz_ve_theme_options" style="display:none;">
                            <?php
                            $counter_providers = self::counter_provider();
                            foreach ($counter_providers as $provider) {
                                ?>
                                <label class="lr-sharing-cb">
                                    <input type="checkbox" class="LoginRadius_hz_ve_share_providers" name="LoginRadius_share_settings[horizontal_sharing_providers][Hybrid][<?php echo $provider; ?>]" value="<?php echo $provider; ?>" <?php echo ( isset($settings['horizontal_sharing_providers']['Hybrid'][$provider]) && $settings['horizontal_sharing_providers']['Hybrid'][$provider] == $provider ) ? 'checked' : ''; ?> />
                                    <span class="lr-text"><?php echo $provider; ?></span>
                                </label>
                            <?php } ?>
                            <div id="loginRadiusHorizontalVerticalSharingLimit" class="lr-alert-box" style="display:none; margin-bottom: 5px;">
                            <?php _e('You can select only eight providers', 'lr-plugin-slug') ?>.
                            </div>
                            <p class="lr-footnote"></p>
                        </div>
                    </div>
                    <div class="lr-row" id="login_radius_horizontal_rearrange_container">
                        <h3>
                        <?php _e('Select the sharing networks order', 'lr-plugin-slug') ?>
                            <span class="lr-tooltip" data-title="Drag the icons around to set the order">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>

                        <ul id="loginRadiusHorizontalSortable" class="cf">
                            <?php
                            if (isset($settings['horizontal_rearrange_providers']) && count($settings['horizontal_rearrange_providers']) > 0) {
                                foreach ($settings['horizontal_rearrange_providers'] as $provider) {
                                    ?>
                                    <li title="<?php echo $provider ?>" id="loginRadiusHorizontalLI<?php echo $provider ?>" class="lrshare_iconsprite32 lr-icon-<?php echo strtolower($provider) ?>">
                                        <input type="hidden" name="LoginRadius_share_settings[horizontal_rearrange_providers][]" value="<?php echo $provider ?>" />
                                    </li>
                                <?php
                            }
                        }
                        ?>
                        </ul>
                        <ul class="lr-static">
                            <li title="More" id="loginRadiusHorizontalLImore" class="lr-pin lr-icon-more lrshare_evenmore"></li>
                            <li title="Counter" id="loginRadiusHorizontalLIcounter" class="lr-pin lr-counter">1.2m</li>
                        </ul>
                    </div>
                    <div class="lr-row cf">
                        <h3><?php _e('Choose the location(s) to display the widget', 'lr-plugin-slug'); ?>
                            <span class="lr-tooltip" data-title="Sharing widget will be displayed at the selected location(s)">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-hr-home" name="LoginRadius_share_settings[lr-clicker-hr-home]" value="1" <?php echo ( isset($settings['lr-clicker-hr-home']) && $settings['lr-clicker-hr-home'] == '1') ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-hr-home">
                                <?php _e('Home Page', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Home page of your blog', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::horizontal_share_interface_position('Home', $settings); ?>
                        </div>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-hr-post" name="LoginRadius_share_settings[lr-clicker-hr-post]" value="1" <?php echo ( isset($settings['lr-clicker-hr-post']) && $settings['lr-clicker-hr-post'] == '1') ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-hr-post">
                                <?php _e('Blog Post', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Each post of your blog', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::horizontal_share_interface_position('Posts', $settings); ?>
                        </div>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-hr-static" name="LoginRadius_share_settings[lr-clicker-hr-static]" value="1" <?php echo ( isset($settings['lr-clicker-hr-static']) && $settings['lr-clicker-hr-static'] == '1') ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-hr-static">
                                <?php _e('Static Pages', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Static pages of your blog (e.g &ndash; about, contact, etc.)', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::horizontal_share_interface_position('Pages', $settings); ?>
                        </div>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-hr-excerpts" name="LoginRadius_share_settings[lr-clicker-hr-excerpts]" value="1" <?php echo ( isset($settings['lr-clicker-hr-excerpts']) && $settings['lr-clicker-hr-excerpts'] == '1') ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-hr-excerpts">
                                <?php _e('Post Excerpts', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Post excerpts page', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::horizontal_share_interface_position('Excerpts', $settings); ?>
                        </div>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-hr-custom" name="LoginRadius_share_settings[lr-clicker-hr-custom]" value="1" <?php echo ( isset($settings['lr-clicker-hr-custom']) && $settings['lr-clicker-hr-custom'] == '1') ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-hr-custom">
                                <?php _e('Custom Post Types', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e('Custom Post Types', 'lr-plugin-slug'); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </label>
                            <?php self::horizontal_share_interface_position('Custom', $settings); ?>
                        </div>
                    </div><!-- row -->
                </div><!-- Container -->
                <!-- End Horizontal Sharing -->
            </div>
            <?php
        }

        private static function advance_settings( $settings ) {
            ?>
            <!-- Advanced Settings -->
            <div id="lr_options_tab-3" class="lr-tab-frame">
                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3><?php _e( 'Short Code for Sharing widget', 'lr-plugin-slug' ); ?>
                            <span class="lr-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display a horizontal sharing widget', 'lr-plugin-slug'); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div>
                            <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[LoginRadius_Share]</textarea>
                        </div>
                        <span><?php _e( 'Additional shortcode examples can be found <a target="_blank" href="http://ish.re/9WBX/#shortcode" >Here</a>', 'lr-plugin-slug' ); ?></span>
                    </div><!-- lr-row -->
                    <div class="lr-row">
                        <h3><?php _e('Mobile Friendly', 'lr-plugin-slug'); ?>
                            <span class="lr-tooltip tip-bottom" data-title="<?php _e('Enable this option to show a mobile sharing interface to mobile users', 'lr-plugin-slug'); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div>
                            <label for="lr-enable-mobile-friendly" class="lr-toggle">
                                <input type="checkbox" class="lr-toggle" id="lr-enable-mobile-friendly" name="LoginRadius_share_settings[mobile_enable]" value="1" <?php echo ( isset($settings['mobile_enable']) && $settings['mobile_enable'] == '1') ? 'checked' : ''; ?> />
                                <span class="lr-toggle-name"><?php _e('Enable Mobile Friendly', 'lr-plugin-slug'); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Render social sharing settings page.
         */
        public static function render_options_page() {
            global $loginradius_share_settings;
            
            $loginradius_share_settings = get_option('LoginRadius_share_settings');
            
            if ( isset( $_POST['reset'] ) ) {
                LR_Sharing_Install::reset_share_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">Sharing settings have been reset and default values have been applied to the plug-in</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
            
            ?>
            <!-- LR-wrap -->
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//www.loginradius.com" target="_blank">LoginRadius</a><em>Simplified Social Share</em></h2>
                </header>
                <div id="lr_options_tabs" class="cf">
                    <ul class="lr-options-tab-btns">
                        <li class="nav-tab lr-active" data-tab="lr_options_tab-1"><?php _e( 'Horizontal Sharing', 'lr-plugin-slug' ) ?></li>
                        <li class="nav-tab" data-tab="lr_options_tab-2"><?php _e( 'Vertical Sharing', 'lr-plugin-slug' ) ?></li>
                        <li class="nav-tab" data-tab="lr_options_tab-3"><?php _e( 'Advanced Settings', 'lr-plugin-slug' ) ?></li>
                    </ul>

                    <!-- Settings -->
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('loginradius_share_settings');
                        settings_errors();
                        self::horizontal_settings( $loginradius_share_settings );
                        self::vertical_settings( $loginradius_share_settings );
                        self::advance_settings( $loginradius_share_settings );
                        submit_button('Save changes');
                        ?>
                    </form>
                    <?php do_action( 'lr_reset_admin_ui','Social Sharing' );?>
                </div>
                <!-- Settings -->
                <div class="lr-sidebar">
                    <div class="lr-frame">
                        <h4><?php _e('Help', 'lr-plugin-slug'); ?></h4>
                        <div>
                            <a target="_blank" href="http://ish.re/YDLT"><?php _e('Plugin Installation, Configuration and Troubleshooting', 'lr-plugin-slug') ?></a>
                            <a target="_blank" href="http://ish.re/8PJ7"><?php _e('About LoginRadius', 'lr-plugin-slug') ?></a>
                            <a target="_blank" href="http://ish.re/5P2D"><?php _e('LoginRadius Products', 'lr-plugin-slug') ?></a>
                            <a target="_blank" href="http://ish.re/C8E7"><?php _e('CMS Plugins', 'lr-plugin-slug') ?></a>
                            <a target="_blank" href="http://ish.re/C9F7"><?php _e('API Libraries', 'lr-plugin-slug') ?></a>
                        </div>
                    </div><!-- lr-frame -->
                    <div class="lr-frame">
                        <h4><?php _e('Follow Us', 'lr-plugin-slug'); ?></h4>
                        <div style="text-align: center;">
                            <a class="lrshare_iconsprite42 lr-icon-facebook" href="http://www.facebook.com/loginradius" target="_blank"></a>
                            <a class="lrshare_iconsprite42 lr-icon-twitter" href="http://twitter.com/LoginRadius" target="_blank"></a>
                            <a class="lrshare_iconsprite42 lr-icon-googleplus" href="http://plus.google.com/+Loginradius" target="_blank"></a>
                            <a class="lrshare_iconsprite42 lr-icon-linkedin" href="http://www.linkedin.com/company/loginradius" target="_blank"></a>
                        </div>
                    </div>
                </div>
            </div><!-- End LR-wrap -->

            <?php
        }

    }

    new LR_Social_Share_Settings();
}