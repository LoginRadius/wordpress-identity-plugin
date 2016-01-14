<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Raas_Admin_Settings' ) ) {

    class LR_Raas_Admin_Settings {
        
        /**
         * generate raas page selection option
         * 
         * @param type $pages
         * @param type $settings
         * @param type $name
         * @return string
         */
        private static function select_field( $pages, $settings, $name ) {
            $output = '<select class="lr-row-field" name="LR_Raas_Settings[' . $name . ']" id="lr_login_page_id">';
            $output .= '<option value="">' . __( ' --- Select Page --- ', 'lr-plugin-slug' ) .'</option>';
            foreach ($pages as $page) {
                $select_page = '';
                if ( isset( $settings[$name] ) && $page->ID == $settings[$name] ) {
                    $select_page = ' selected="selected"';
                }
                $output .= '<option value="' . $page->ID . '" ' . $select_page . '>' . $page->post_title . '</option>';
            }
            $output .= '</select>';
            return $output;
        }

        /**
         * create raas admin UI
         * 
         * @global type $lr_raas_settings
         * @global type $loginRadiusLoginIsBpActive
         */
        public static function render_options_page() {
            global $lr_raas_settings,$loginRadiusLoginIsBpActive;

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
            $pages = get_pages( $args );


            if ( isset( $_POST['reset'] ) ) {
                LR_Raas_Install::reset_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">'.__('User Registration settings have been reset and default values loaded','lr-plugin-slug').'</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }

            $lr_raas_settings = get_option('LR_Raas_Settings');
            $loginRadiusSettings = get_option('LoginRadius_settings');
            ?>

            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>User Registration</em></h2>
                </header>
                <div class="cf">
                    <ul class="lr-options-tab-btns">
                        <li class="nav-tab lr-active" data-tab="lr_options_tab-1"><?php _e( 'User Registration', 'lr-plugin-slug' ) ?></li>
                        <li class="nav-tab" data-tab="lr_options_tab-2"><?php _e( 'Advanced Settings', 'lr-plugin-slug' ) ?></li>
                        <?php apply_filters( 'add_raas_tab', '' );?>
                    </ul>
                    
                    <div id="lr_options_tab-1" class="lr-tab-frame lr-active">

                        <form action="options.php" method="post">
                            <?php
                            settings_fields('lr_raas_settings');
                            settings_errors();
                            ?>

                            <div class="lr_options_container">
                                <div class="lr-row">
                                    <h3>
                                        <?php _e('User Registration Integration', 'lr-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <input type="checkbox" class="lr-toggle" id="lr-raas-autopage" name="LR_Raas_Settings[raas_autopage]" value='1' <?php echo ( isset($lr_raas_settings['raas_autopage']) && $lr_raas_settings['raas_autopage'] == '1' ) ? 'checked' : '' ?> />
                                        <label class="lr-show-toggle" for="lr-raas-autopage">
                                            <?php _e('Enable Auto Generate User Registration'); ?>
                                            <span class="lr-tooltip" data-title="<?php _e('Turn on, if you want to enable Auto Generate User Registration pages','lr-plugin-slug'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                        <div class="lr-custom-page-settings">
                                            <div>
                                                <label>
                                                    <span class="lr_property_title">
                                                        <?php _e('Login Page', 'lr-plugin-slug'); ?>
                                                        <span class="lr-tooltip" data-title="<?php _e('Add Login page Short Code from Advance Setting tab in selected page.','lr-plugin-slug');?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field( $pages, $lr_raas_settings, 'login_page_id' ); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label>
                                                    <span class="lr_property_title">
                                                        <?php _e('Registration Page', 'lr-plugin-slug'); ?>
                                                        <span class="lr-tooltip" data-title="<?php _e('Add Registration page Short Code from Advance Setting tab in selected page.','lr-plugin-slug');?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field( $pages, $lr_raas_settings, 'registration_page_id' ); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label>
                                                    <span class="lr_property_title">
                                                        <?php _e('Change Password Page', 'lr-plugin-slug'); ?>
                                                        <span class="lr-tooltip" data-title="<?php _e('Add Change Password page Short Code from Advance Setting tab in selected page.','lr-plugin-slug');?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field($pages, $lr_raas_settings, 'change_password_page_id'); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label>
                                                    <span class="lr_property_title">
                                                        <?php _e('Forgot Password Page', 'lr-plugin-slug'); ?>
                                                        <span class="lr-tooltip" data-title="<?php _e('Add Forgot Password page Short Code from Advance Setting tab in selected page.','lr-plugin-slug');?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field($pages, $lr_raas_settings, 'lost_password_page_id'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <?php
                                            if ( is_multisite() && is_main_site() ) {
                                                ?>
                                        <div class="lr-warning-box"><?php _e('NOTE :- Changes done on user registration integration will not reflect on other sites, need to save it.','lr-plugin-slug');?></div>
                                            <?php }?>
                                    </div>
                                </div>
                            </div>
                            <div class="lr_options_container">
                                <div class="lr-row">
                                    <div>
                                        <h4>
                                            <?php _e('Redirection settings after login ', 'lr-plugin-slug'); ?>
                                            <span class="lr-tooltip" data-title="<?php _e('Page the user is redirected to after login', 'lr-plugin-slug'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </h4>
                                        <label>
                                            <input type="radio" class="loginRedirectionRadio" name="LR_Raas_Settings[LoginRadius_redirect]" value="samepage" <?php echo Admin_Helper:: is_radio_checked('login', 'samepage'); ?> /> 
                                            <span><?php _e('Redirect to the same page where the user logged in', 'lr-plugin-slug'); ?></span>
                                        </label>
                                        <label>
                                            <input type="radio" class="loginRedirectionRadio" name="LR_Raas_Settings[LoginRadius_redirect]" value="homepage" <?php echo Admin_Helper:: is_radio_checked('login', 'homepage'); ?> /> 
                                            <span><?php _e('Redirect to the home page of your WordPress site', 'lr-plugin-slug'); ?></span>
                                        </label>
                                        <label>
                                            <input type="radio" class="loginRedirectionRadio" name="LR_Raas_Settings[LoginRadius_redirect]" value="dashboard" <?php echo Admin_Helper:: is_radio_checked('login', 'dashboard'); ?> /> 
                                            <span><?php _e('Redirect to the user\'s account dashboard', 'lr-plugin-slug'); ?></span>
                                        </label>
                                        <?php
                                        if (isset($loginRadiusLoginIsBpActive) && $loginRadiusLoginIsBpActive) {
                                            ?>
                                            <label>
                                                <input type="radio" class="loginRedirectionRadio" name="LR_Raas_Settings[LoginRadius_redirect]" value="bp" <?php echo Admin_Helper:: is_radio_checked('login', 'bp'); ?> />
                                                <span><?php _e('Redirect to Buddypress profile page', 'lr-plugin-slug'); ?></span>
                                            </label>
                                            <?php
                                        }
                                        ?>
                                        <label>
                                            <input type="radio" class="loginRedirectionRadio custom" name="LR_Raas_Settings[LoginRadius_redirect]" value="custom" <?php echo Admin_Helper:: is_radio_checked('login', 'custom'); ?> />
                                            <span><?php _e('Redirect to a custom URL'); ?></span>
                                            <?php
                                            if (isset($loginRadiusSettings['LoginRadius_redirect']) && $loginRadiusSettings['LoginRadius_redirect'] == 'custom') {
                                                $inputBoxValue = htmlspecialchars($loginRadiusSettings['custom_redirect']);
                                            } else {
                                                $inputBoxValue = site_url();
                                            }
                                            ?>
                                            <input type="text" id="loginRadiusCustomLoginUrl" name="LR_Raas_Settings[custom_redirect]" size="60" value="<?php echo $inputBoxValue; ?>">
                                        </label>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <div id="lr_options_tab-2" class="lr-tab-frame">
                        <div class="lr_options_container">
                            <div class="lr-row lr-ur-shortcodes">
                                <h3><?php _e( 'User Registration Short Codes', 'lr-plugin-slug' ); ?></h3>
                                <div>
                                    <h4><?php _e( 'Login Form', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the Login Form', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[raas_login_form]</textarea>
                                </div>

                                
                                <div>
                                    <h4><?php _e('Registration Form', 'lr-plugin-slug'); ?>
                                        <span class="lr-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the Registration Form', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[raas_registration_form]</textarea>
                                </div>

                                
                                <div>
                                    <h4><?php _e('Forgotten Password Form', 'lr-plugin-slug'); ?>
                                        <span class="lr-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the Forgotten Password Form', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[raas_forgotten_form]</textarea>
                                </div>

                                
                                <div>
                                    <h4><?php _e( 'Change Password Form', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip tip-bottom" data-title="<?php _e( 'Copy and paste the following shortcode into a page or post to display Change Password Form', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[raas_password_form]</textarea>
                                </div>

                                
                                <div>
                                    <h4><?php _e( 'Default WP Login Form', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip tip-bottom" data-title="<?php _e( 'Copy and paste the following shortcode into a page or post to display the default Wordpress Login Form. This can be used while configuring your site', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[raas_wp_default_login]</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3><?php _e( 'Social Login User Settings', 'lr-plugin-slug' ); ?></h3>
                                <div>
                                    <h4>
                                        <?php _e( 'Select how you would like the WordPress username to be generated', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'During account creation, a separator is automatically added between the user\'s first name and last name', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <label>
                                        <input name="LR_Raas_Settings[username_separator]" type="radio"  <?php echo ! isset( $loginRadiusSettings['username_separator'] ) ? 'checked="checked"' : Admin_Helper:: is_radio_checked( 'seperator', 'dash' ); ?> value="dash" />
                                        <span><?php _e( 'Dash: Firstname-Lastname [Ex: John-Doe]', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                    <label>
                                        <input name="LR_Raas_Settings[username_separator]" type="radio"  <?php echo Admin_Helper::is_radio_checked( 'seperator', 'dot' ); ?> value="dot"/>
                                        <span><?php _e( 'Dot: Firstname.Lastname [Ex: John.Doe]', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                    <label>
                                        <input name="LR_Raas_Settings[username_separator]" type="radio"  <?php echo Admin_Helper::is_radio_checked( 'seperator', 'space' ); ?> value='space'/>
                                        <span><?php _e( 'Space: Firstname Lastname [Ex: John Doe]', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                </div>

                                <div>
                                    <h4>
                                        <?php _e('Select whether to display the social network(s) the user is connected with in the user list', 'lr-plugin-slug'); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'Select Yes, if you want to see the list of social providers the user account is linked with in the user list', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <label>
                                        <input type="radio" name="LR_Raas_Settings[LoginRadius_noProvider]" value="1" <?php echo ( isset($loginRadiusSettings['LoginRadius_noProvider']) && $loginRadiusSettings['LoginRadius_noProvider'] == 1 ) ? 'checked' : ''; ?> />
                                        <span><?php _e( 'Yes, display the social network(s) that the user connected with in the user list', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                    <label>
                                        <input type="radio" name="LR_Raas_Settings[LoginRadius_noProvider]" value='0' <?php echo ( !isset($loginRadiusSettings['LoginRadius_noProvider']) || $loginRadiusSettings['LoginRadius_noProvider'] == 0 ) ? 'checked' : ''; ?> />
                                        <span><?php _e( 'No, do not display the social network(s) that the user is connected with', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                </div>

                                <div>
                                    <h4>
                                        <?php _e( 'Select whether the user profile data should be updated in your WordPress database, every time a user logs in', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'If you disable this option, the user profile data will be saved only once when the user logs in for the first time on your website, and this data will not be updated again in your WordPress database, even if the user updates their social account.', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <label>
                                        <input type="radio" name="LR_Raas_Settings[profileDataUpdate]" value='1' <?php echo ( ! isset( $loginRadiusSettings['profileDataUpdate']) || $loginRadiusSettings['profileDataUpdate'] == 1 ) ? 'checked' : ''; ?> />
                                        <span><?php _e('Yes', 'lr-plugin-slug') ?></span>
                                    </label>
                                    <label>
                                        <input type="radio" name="LR_Raas_Settings[profileDataUpdate]" value="0" <?php echo ( isset( $loginRadiusSettings['profileDataUpdate']) && $loginRadiusSettings['profileDataUpdate'] == 0 ) ? 'checked' : ''; ?> />
                                        <span><?php _e('No', 'lr-plugin-slug'); ?></span>
                                    </label>
                                </div>

                                <div>
                                    <h4>
                                        <?php _e( 'Select whether to let users use their social profile picture as an avatar on your website', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'Select Yes, if you want to let users use their profile picture from their linked social account as an avatar on your website', 'lr-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <label>
                                        <input name ="LR_Raas_Settings[LoginRadius_socialavatar]" type="radio"  <?php echo Admin_Helper::is_radio_checked( 'avatar', 'socialavatar'); ?> value="socialavatar" />
                                        <span><?php _e( 'Yes', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                    <label>
                                        <input name ="LR_Raas_Settings[LoginRadius_socialavatar]" type="radio" <?php echo Admin_Helper::is_radio_checked( 'avatar', 'defaultavatar'); ?> value="defaultavatar" />
                                        <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                </div>

                                <div>
                                    <h4>
                                        <?php _e( 'Enable account linking', 'lr-plugin-slug' ); ?>
                                    </h4>
                                    <input type="checkbox" class="lr-toggle" id="lr-social-link-enable" name="LR_Raas_Settings[LoginRadius_socialLinking]" value='1' <?php echo ( ( isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && $loginRadiusSettings['LoginRadius_socialLinking'] == 1 ) || ! isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) ) ? 'checked' : ''; ?> />
                                    <label class="lr-show-toggle" for="lr-social-link-enable">
                                        <?php _e( 'Enable account linking' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'Select Yes, If you want to enable social account linking. This option will also shows users the linking interface on the wordpress dashboard that allows users to link their other social providers', 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- reCAPTCHA Options -->
                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3><?php _e( 'reCAPTCHA Options', 'lr-plugin-slug' ); ?></h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-v2captcha-enable" name="LR_Raas_Settings[enable_v2captcha]" value='1' <?php echo ( isset( $lr_raas_settings['enable_v2captcha'] ) && $lr_raas_settings['enable_v2captcha'] == '1' ) ? 'checked' : '' ?> />
                                    <label class="lr-show-toggle" for="lr-v2captcha-enable">
                                        <?php _e( 'Enable v2reCAPTCHA' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'Turn on, to enable v2reCAPTCHA. This will also need to be enabled by your LoginRadius Account Manager.', 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                    <div class="lr-row lr-v2captcha-key">
                                        <label>
                                            <?php _e( 'v2reCAPTCHA Site Key' ); ?>
                                            <span class="lr-tooltip" data-title="<?php _e( 'Enter your Google reCAPTCHA Site Key to activate v2 reCAPTCHA. (Required)', 'lr-plugin-slug' ); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                        <input type="text" name="LR_Raas_Settings[v2captcha_site_key]" value="<?php echo ! empty( $lr_raas_settings['v2captcha_site_key'] ) ? $lr_raas_settings['v2captcha_site_key'] : ''; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Disable Email Verification Options -->
                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3><?php _e( 'Email Verification', 'lr-plugin-slug' ); ?></h3>
                                <div>
                                    <input type="checkbox" class="lr-toggle" id="lr-disable-email-verify" name="LR_Raas_Settings[disable_email_verify]" value='1' <?php echo ( isset( $lr_raas_settings['disable_email_verify'] ) && $lr_raas_settings['disable_email_verify'] == '1' ) ? 'checked' : '' ?> />
                                    <label class="lr-show-toggle" for="lr-disable-email-verify">
                                        <?php _e( 'Disable Email Verification' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'Turn on, to disable user registration email verification. This will also need to be enabled by your LoginRadius Account Manager.', 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Plugin Debug option. -->
                        <div class="lr_options_container">
                            <div class="lr-row">
                                <h3><?php _e( 'Debug', 'lr-plugin-slug' ); ?></h3>
                                <div>
                                    <h4>
                                        <?php _e( 'Do you want to enable LoginRadius error reporting?', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip" data-title="<?php _e( 'Select Yes, if you want to Social Login errors reported', 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <label>
                                        <input name="LR_Raas_Settings[enable_degugging]" type="radio"  value="1" <?php echo ( isset( $loginRadiusSettings['enable_degugging'] ) && $loginRadiusSettings['enable_degugging'] == '1' ) ? 'checked = "checked"' : ''; ?> />
                                        <span><?php _e( 'Yes', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                    <label>
                                        <input name="LR_Raas_Settings[enable_degugging]" type="radio" value="0" <?php echo ( ! isset( $loginRadiusSettings['enable_degugging'] ) || $loginRadiusSettings['enable_degugging'] == '0' ) ? 'checked="checked"' : ''; ?> />
                                        <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php apply_filters( 'add_raas_tab_body','' );?>
                    
                    <div style="position: relative;">
                        <div class="lr-option-disabled-hr" style="display: none;"></div>
                    </div>
                    <p class="submit">
                        <?php submit_button( 'Save Settings', 'primary', 'submit', false); ?>
                    </p>
                    </form>
                        <?php do_action( 'lr_reset_admin_ui','User Registration' ); ?>
                </div>
            </div>
            <?php
        }

    }

}