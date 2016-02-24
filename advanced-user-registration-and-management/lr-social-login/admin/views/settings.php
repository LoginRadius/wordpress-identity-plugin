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
if ( ! class_exists( 'LR_Social_Login_Admin_Settings' ) ) {

    class LR_Social_Login_Admin_Settings {

        /**
         * Function for rendering Help tab on plugin on settings page
         */
        private static function help_options() {
            ?>
            <div class="lr-sidebar">
                <div class="lr-frame">
                    <h4><?php _e( 'Help & Documentations', 'lr-plugin-slug' ); ?></h4>
                    <div>
                        <a target="_blank" href="http://ish.re/BENH"><?php _e( 'Plugin Installation, Configuration and Troubleshooting', 'lr-plugin-slug' ); ?></a>
                        <a target="_blank" href="http://ish.re/9VBI"><?php _e( 'How to get LoginRadius API Key & Secret', 'lr-plugin-slug' ); ?></a>
                        <a target="_blank" href="http://ish.re/BGT3"><?php _e( 'WP Multisite Feature', 'lr-plugin-slug' ); ?></a>
                        <a target="_blank" href="http://ish.re/8PG2"><?php _e( 'Discussion Forum', 'lr-plugin-slug' ); ?></a>
                        <a target="_blank" href="http://ish.re/96M7"><?php _e( 'About LoginRadius', 'lr-plugin-slug' ); ?></a>
                        <a target="_blank" href="http://ish.re/8PG5"><?php _e( 'LoginRadius Products', 'lr-plugin-slug' ); ?></a>
                        <a target="_blank" href="http://ish.re/C8E7"><?php _e( 'Social Plugins', 'lr-plugin-slug' ); ?></a>
                        <a target="_blank" href="http://ish.re/NQV2"><?php _e( 'Social SDKs', 'lr-plugin-slug' ); ?></a>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Function for rendering Social Login tab on plugin on settings page
         */
        private static function login_options( $loginRadiusSettings ) {
            global $loginRadiusLoginIsBpActive;
            ?>
            <div id="lr_options_tab-1" class="lr-tab-frame lr-active">
                <!-- Social Login Interface Display Settings -->
                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3>
                            <?php _e( 'Interface Display Settings', 'lr-plugin-slug' ); ?>
                        </h3>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-login-form" name="LoginRadius_settings[LoginRadius_loginform]" value="1" <?php echo isset( $loginRadiusSettings['LoginRadius_loginform'] ) && $loginRadiusSettings['LoginRadius_loginform'] == '1' ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-login-form"><?php _e( 'Login page of your WordPress site', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="Default login page provided by WordPress">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                                <?php
                                    if ( isset( $loginRadiusLoginIsBpActive ) && $loginRadiusLoginIsBpActive ) {
                                        ?>
                                            <div class="lr-login-form-options">
                                                <label>
                                                    <input type="radio" name="LoginRadius_settings[LoginRadius_loginformPosition]" value="embed" <?php echo ( isset( $loginRadiusSettings['LoginRadius_loginformPosition'] ) && $loginRadiusSettings['LoginRadius_loginformPosition'] == 'embed' ) ? 'checked = "checked"' : ''; ?> />
                                                    <span><?php _e( 'Display the Social Login interface below the Wordpress login form', 'lr-plugin-slug' ); ?></span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="LoginRadius_settings[LoginRadius_loginformPosition]" value="beside" <?php echo ( isset( $loginRadiusSettings['LoginRadius_loginformPosition'] ) && $loginRadiusSettings['LoginRadius_loginformPosition'] == 'beside' ) ? 'checked = "checked"' : ''; ?> />
                                                    <span><?php _e( 'Display the Social Login interface beside the Buddypress login form', 'lr-plugin-slug' ); ?></span>
                                                </label>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </label>
                        </div>
                        <div>
                            <input type="checkbox" class="lr-toggle" id="lr-clicker-reg-form" name="LoginRadius_settings[LoginRadius_regform]" value="1" <?php echo isset( $loginRadiusSettings['LoginRadius_regform'] ) && $loginRadiusSettings['LoginRadius_regform'] == 1 ? 'checked' : ''; ?> />
                            <label class="lr-show-toggle" for="lr-clicker-reg-form"><?php _e( 'Registration page of your WordPress site', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="Default registration page provided by WordPress">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                                <?php
                                    if ( isset( $loginRadiusLoginIsBpActive ) && $loginRadiusLoginIsBpActive ) {
                                        ?>
                                            <div class="lr-reg-form-options">
                                                <label>
                                                    <input type="radio" name="LoginRadius_settings[LoginRadius_regformPosition]" value="embed" <?php echo ( isset( $loginRadiusSettings['LoginRadius_regformPosition'] ) && $loginRadiusSettings['LoginRadius_regformPosition'] == 'embed' ) ? 'checked = "checked"' : ''; ?> />
                                                    <span><?php _e( 'Display the Social Login interface below the Wordpress registration form', 'lr-plugin-slug' ); ?></span>
                                                </label>
                                                <label>
                                                    <input type="radio" name="LoginRadius_settings[LoginRadius_regformPosition]" value="beside" <?php echo ( isset( $loginRadiusSettings['LoginRadius_regformPosition'] ) && $loginRadiusSettings['LoginRadius_regformPosition'] == 'beside' ) ? 'checked = "checked"' : ''; ?> />
                                                    <span><?php _e( 'Display the Social Login interface above the Buddypress registration form', 'lr-plugin-slug' ); ?></span>
                                                </label>
                                            </div>
                                        <?php
                                    }
                                ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3><?php _e( 'Redirection Settings', 'lr-plugin-slug' ); ?></h3>
                        <div>
                            <h4>
                                <?php _e( 'Redirection settings after login', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'Page the user is redirected to after login', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input type="radio" class="loginRedirectionRadio" name="LoginRadius_settings[LoginRadius_redirect]" value="samepage" <?php echo Admin_Helper::is_radio_checked( 'login', 'samepage' ); ?> /> 
                                <span><?php _e( 'Redirect to the same page where the user logged in', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input type="radio" class="loginRedirectionRadio" name="LoginRadius_settings[LoginRadius_redirect]" value="homepage" <?php echo Admin_Helper::is_radio_checked( 'login', 'homepage' ); ?> /> 
                                <span><?php _e( 'Redirect to the home page of your WordPress site', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input type="radio" class="loginRedirectionRadio" name="LoginRadius_settings[LoginRadius_redirect]" value="dashboard" <?php echo Admin_Helper::is_radio_checked( 'login', 'dashboard' ); ?> /> 
                                <span><?php _e( 'Redirect to the user\'s account dashboard', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <?php
                                if ( isset( $loginRadiusLoginIsBpActive ) && $loginRadiusLoginIsBpActive ) {
                                    ?>
                                    <label>
                                        <input type="radio" class="loginRedirectionRadio" name="LoginRadius_settings[LoginRadius_redirect]" value="bp" <?php echo Admin_Helper::is_radio_checked( 'login', 'bp' ); ?> />
                                        <span><?php _e( 'Redirect to Buddypress profile page', 'lr-plugin-slug' ); ?></span>
                                    </label>
                                    <?php
                                }
                            ?>
                            <label>
                                <input type="radio" class="loginRedirectionRadio custom" name="LoginRadius_settings[LoginRadius_redirect]" value="custom" <?php echo Admin_Helper::is_radio_checked( 'login', 'custom' ); ?> />
                                <span><?php _e( 'Redirect to a custom URL', 'lr-plugin-slug' ); ?></span>
                                <?php
                                    if ( isset( $loginRadiusSettings['LoginRadius_redirect'] ) && $loginRadiusSettings['LoginRadius_redirect'] == 'custom' ) {
                                        $inputBoxValue = htmlspecialchars( $loginRadiusSettings['custom_redirect'] );
                                    } else {
                                        $inputBoxValue = site_url();
                                    }
                                ?>
                                <input type="text" id="loginRadiusCustomLoginUrl" name="LoginRadius_settings[custom_redirect]" size="60" value="<?php echo $inputBoxValue; ?>" >
                            </label>
                        </div>
                        <div>
                            <h4>
                                <?php _e( 'Redirection settings after registration', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'Page the user is redirected to after registration', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input type="radio" class="registerRedirectionRadio" name="LoginRadius_settings[LoginRadius_regRedirect]" value="samepage" <?php echo Admin_Helper::is_radio_checked( 'register', 'samepage' ); ?> /> 
                                <span><?php _e('Redirect to the same page where the user registered', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input type="radio" class="registerRedirectionRadio" name="LoginRadius_settings[LoginRadius_regRedirect]" value="homepage" <?php echo Admin_Helper::is_radio_checked( 'register', 'homepage' ); ?> /> 
                                <span><?php _e('Redirect to the home page of your WordPress site', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input type="radio" class="registerRedirectionRadio" name="LoginRadius_settings[LoginRadius_regRedirect]" value="dashboard" <?php echo Admin_Helper::is_radio_checked( 'register', 'dashboard' ); ?> /> 
                                <span><?php _e('Redirect to the user\'s account dashboard', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <?php
                                if ( isset( $loginRadiusLoginIsBpActive ) && $loginRadiusLoginIsBpActive ) {
                                    ?>
                                        <label>
                                            <input type="radio" class="registerRedirectionRadio" name="LoginRadius_settings[LoginRadius_regRedirect]" value="bp" <?php echo Admin_Helper::is_radio_checked( 'register', 'bp' ); ?> />
                                            <span><?php _e('Redirect to Buddypress profile page', 'lr-plugin-slug' ); ?></span>
                                        </label>
                                    <?php
                                }
                            ?>
                            <label>
                                <input type="radio" class="registerRedirectionRadio custom" id="loginRadiusCustomRegRadio" name="LoginRadius_settings[LoginRadius_regRedirect]" value="custom" <?php echo Admin_Helper::is_radio_checked( 'register', 'custom' ); ?> />
                                <span><?php _e( 'Redirect to a custom URL', 'lr-plugin-slug' ); ?></span>
                                <?php
                                    if ( isset( $loginRadiusSettings['custom_regRedirect'] ) && $loginRadiusSettings['LoginRadius_regRedirect'] == 'custom' ) {
                                        $inputBoxValue = htmlspecialchars( $loginRadiusSettings['custom_regRedirect'] );
                                    } else {
                                        $inputBoxValue = site_url();
                                    }
                                ?>
                                <input type="text" id="loginRadiusCustomRegistrationUrl" name="LoginRadius_settings[custom_regRedirect]" size="60" value="<?php echo $inputBoxValue; ?>" />
                            </label>
                        </div>
                        <div>
                            <h4>
                                <?php _e( 'Redirection settings after logging out with Social Login widget', 'lr-plugin-slug' ) ?>
                                <span class="lr-tooltip" data-title="<?php _e('Page the user is redirected to after logout [Note: The logout function only works when clicking \'Logout\' in the social login widget area. In all other cases, WordPress\' default logout function will be applied.]', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input type="radio" class="logoutRedirectionRadio" name="LoginRadius_settings[LoginRadius_loutRedirect]" value="homepage" <?php echo Admin_Helper::is_radio_checked( 'logoutUrl', 'homepage' ); ?> /> 
                                <span><?php _e('Redirect to the home page', 'lr-plugin-slug'); ?></span>
                            </label>

                            <label>
                                <input type="radio" class="logoutRedirectionRadio custom" name="LoginRadius_settings[LoginRadius_loutRedirect]" value="custom" <?php echo Admin_Helper::is_radio_checked( 'logoutUrl', 'custom' ); ?> />
                                <span><?php _e( 'Redirect to a custom URL', 'lr-plugin-slug' ); ?></span>
                                <?php
                                    if ( isset( $loginRadiusSettings['LoginRadius_loutRedirect'] ) && $loginRadiusSettings['LoginRadius_loutRedirect'] == 'custom' ) {
                                        $inputBoxValue = htmlspecialchars( $loginRadiusSettings['custom_loutRedirect'] );
                                    } else {
                                        $inputBoxValue = site_url();
                                    }
                                ?>
                                <input type="text" id="loginRadiusCustomLogoutUrl" name="LoginRadius_settings[custom_loutRedirect]" size="60" value="<?php echo $inputBoxValue; ?>">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         *
         * function for rendering Customization Settings tab on settings page.
         */
        private static function customization_options( $loginRadiusSettings ) {
            global $lr_custom_interface_settings;
            ?>
            <div id="lr_options_tab-3" class="lr-tab-frame">
                <!-- Social Login Interface Customization -->
                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3>
                            <?php _e( 'Social Login Interface', 'lr-plugin-slug' ); ?>
                        </h3>
                        <div>
                            <label>
                                <span class="lr_property_title" >
                                    <?php _e( 'Title', 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip" data-title="<?php _e( 'Enter the title of the Social Login interface', 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </span>
                                <input type="text" name="LoginRadius_settings[LoginRadius_title]" style="margin-left:280px;width:300px;" value= "<?php if( isset( $loginRadiusSettings['LoginRadius_title'] ) ) { echo htmlspecialchars( $loginRadiusSettings['LoginRadius_title'] ); } ?>" />
                            </label>
                        </div>
                        <?php if ( ! isset( $lr_custom_interface_settings['custom_interface'] ) || $lr_custom_interface_settings['custom_interface'] != '1' ) {?>
                        <div>
                            <label style="line-height:41px;">
                                <span class="lr_property_title" style="margin-top:0px;">
                                    <?php _e( 'Social Login Icon Size', 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip" data-title="<?php _e( 'Select the size of the icons in your Social Login interface. This option does not apply to all Social Login themes.', 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </span>
                                <input type="radio" style="margin-left:280px;" name="LoginRadius_settings[LoginRadius_interfaceSize]" value='large' <?php echo ( ! isset( $loginRadiusSettings['LoginRadius_interfaceSize'] ) || $loginRadiusSettings['LoginRadius_interfaceSize'] == 'large' ) ? 'checked' : ''; ?> />
                                <span><?php _e( 'Large', 'lr-plugin-slug' ); ?></span>
                                <input type="radio" name="LoginRadius_settings[LoginRadius_interfaceSize]" value="small" <?php echo ( isset( $loginRadiusSettings['LoginRadius_interfaceSize'] ) && $loginRadiusSettings['LoginRadius_interfaceSize'] == 'small' ) ? 'checked' : ''; ?> /> 
                                <span><?php _e( 'Small', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>
                        <div>
                            <label>
                                <span class="lr_property_title">
                                    <?php _e( 'Number of Social Icons Per Row', 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip" data-title="<?php _e( 'Enter the number of social icons to display in each row', 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </span>
                                <input type="text" name="LoginRadius_settings[LoginRadius_numColumns]" style="margin-left:280px;width:100px;" maxlength="2" value="<?php
                                if ( isset($loginRadiusSettings['LoginRadius_numColumns'] ) ) {
                                    echo sanitize_text_field( trim($loginRadiusSettings['LoginRadius_numColumns'] ) );
                                }
                                ?>" />
                            </label>
                        </div>
                        <div>
                            <label>
                                <span class="lr_property_title">
                                    <?php _e( 'Background Color', 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip" data-title="<?php _e( 'Select the background color of the Social Login interface', 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </span>
                                <?php
                                    if ( isset( $loginRadiusSettings['LoginRadius_backgroundColor'] ) ) {
                                        $colorValue = esc_html( trim( $loginRadiusSettings['LoginRadius_backgroundColor'] ) );
                                    } else {
                                        $colorValue = '';
                                    }
                                ?>
                                <div class="lr-color-picker-container">
                                    <input type="text" class="color_picker" name="LoginRadius_settings[LoginRadius_backgroundColor]" value="<?php echo $colorValue; ?>" />
                                </div>
                            </label>
                        </div>
                        <?php }?>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         *
         * function for rendering Social Commenting tab on settings page.
         */
        private static function commenting_options( $loginRadiusSettings ) {
            ?>
            <div id="lr_options_tab-2" class="lr-tab-frame">
                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3>
                            <?php _e('Enable Social Commenting', 'lr-plugin-slug'); ?>
                            <span class="lr-tooltip tip-bottom" data-title="<?php _e( 'Turn on, if you want to enable Social Commenting', 'lr-plugin-slug' ); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div>
                            <div>
                                <input type="checkbox" class="lr-toggle" id="lr-clicker-commenting" name="LoginRadius_settings[LoginRadius_commentEnable]" value="1" <?php echo ( ( isset($loginRadiusSettings['LoginRadius_commentEnable']) && $loginRadiusSettings['LoginRadius_commentEnable'] == '1' ) ) ? 'checked' : '' ?> />
                                <label class="lr-show-toggle" for="lr-clicker-commenting">
                                </label>
                            </div>
                            <div class="lr-commenting-options">
                                <h4>
                                    <?php _e('Choose where you want the Social Login interface to be displayed on the WordPress commenting form', 'lr-plugin-slug'); ?>
                                    <span class="lr-tooltip" data-title="<?php _e( 'Select the position of the Social Login interface on WordPress commenting form', 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </h4>
                                <label>
                                    <input type="radio" name="LoginRadius_settings[LoginRadius_commentInterfacePosition]" value="after_leave_reply" <?php echo ( ( isset( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) && $loginRadiusSettings['LoginRadius_commentInterfacePosition'] == 'after_leave_reply' ) || ! isset( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) ) ? 'checked="checked"' : '' ?> />
                                    <span><?php _e( 'After the \'Leave a Reply\' caption', 'lr-plugin-slug' ); ?></span>
                                </label>
                                <label>
                                    <input type="radio" name="LoginRadius_settings[LoginRadius_commentInterfacePosition]" value="very_top" <?php echo ( isset( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) && $loginRadiusSettings['LoginRadius_commentInterfacePosition'] == 'very_top' ) ? 'checked="checked"' : '' ?> />
                                    <span><?php _e( 'At the very top of the comment form', 'lr-plugin-slug' ); ?></span>
                                </label>
                                <label>
                                    <input type="radio" name="LoginRadius_settings[LoginRadius_commentInterfacePosition]" value="very_bottom" <?php echo isset( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) && $loginRadiusSettings['LoginRadius_commentInterfacePosition'] == 'very_bottom' ? 'checked="checked"' : '' ?> />
                                    <span><?php _e( 'At the very bottom of the comment form', 'lr-plugin-slug' ); ?></span>
                                </label>
                                <label>
                                    <input type="radio" name="LoginRadius_settings[LoginRadius_commentInterfacePosition]" value="before_fields" <?php echo isset( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) && $loginRadiusSettings['LoginRadius_commentInterfacePosition'] == 'before_fields' ? 'checked="checked"' : '' ?> />
                                    <span><?php _e( 'Before the comment form input fields', 'lr-plugin-slug' ); ?></span>
                                </label>
                                <label>
                                    <input type="radio" name="LoginRadius_settings[LoginRadius_commentInterfacePosition]" value="after_fields" <?php echo isset( $loginRadiusSettings['LoginRadius_commentInterfacePosition'] ) && $loginRadiusSettings['LoginRadius_commentInterfacePosition'] == 'after_fields' ? 'checked="checked"' : '' ?> />
                                    <span><?php _e( 'Before the comment box', 'lr-plugin-slug' ); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Function for rendering advanced tab on plugin on settings page
         */
        private static function advanced_options( $loginRadiusSettings ) {
            ?>
            <div id="lr_options_tab-4" class="lr-tab-frame">
                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3><?php _e( 'Short Code for Social Login', 'lr-plugin-slug' ); ?>
                            <span class="lr-tooltip tip-bottom" data-title="<?php _e( 'Copy and paste the following shortcode into a page or post to display a social login interface', 'lr-plugin-slug' ); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </h3>
                        <div>
                            <textarea rows="1" onclick="this.select()" spellcheck="false" class="lr-shortcode" readonly="readonly">[LoginRadius_Login]</textarea>
                        </div>
                        <span><?php _e( "Additional shortcode examples can be found <a target='_blank' href='http://ish.re/BENH/#shortcode' >Here</a>", 'lr-plugin-slug' ); ?></span>
                    </div><!-- lr-row -->
                </div>
                <!-- Social Login Email Settings -->
                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3><?php _e( 'Social Login Email Settings', 'lr-plugin-slug' ); ?></h3>
                        <div>
                            <h4>
                                <?php _e( 'A few Social Networks do not supply user email address as part of user profile data. Do you want users to provide their email before completing the registration process?', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'Turn on, if you would like to prompt users for their email address in a separate pop-up', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <div>
                                <input type="checkbox" class="lr-toggle" id="lr-clicker-get-email" name="LoginRadius_settings[LoginRadius_dummyemail]" value="notdummyemail" <?php echo ( isset( $loginRadiusSettings['LoginRadius_dummyemail'] ) && $loginRadiusSettings['LoginRadius_dummyemail'] == 'notdummyemail' ) ? 'checked="checked"' : ''; ?> />
                                <label class="lr-show-toggle" for="lr-clicker-get-email">
                                </label>
                            </div>
                        </div>
                        <div class="lr-row lr-get-email-messages">
                            <h4>
                                <?php _e('Enter the pop-up message asking users to enter their email address', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'The name of the social provider will be automatically filled in if you use @provider', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <textarea name="LoginRadius_settings[msg_email]" cols="100" rows="3" ><?php echo $loginRadiusSettings['msg_email']; ?></textarea>
                            </label>
                            <h4>
                                <?php _e( 'Enter the message the user receives when their email address is already registered', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'This is the message that will be displayed to the user if the email address they are registering with is already taken', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <textarea name="LoginRadius_settings[msg_existemail]" cols="100" rows="3"><?php echo $loginRadiusSettings['msg_existemail']; ?></textarea>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Social Login User Settings -->
                <div class="lr_options_container">
                    <div class="lr-row">
                        <h3><?php _e( 'Social Login User Settings', 'lr-plugin-slug' ); ?></h3>
                        <div>
                            <h4>
                                <?php _e( 'Select how you would like the WordPress username to be generated', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'During account creation, a separator is automatically added between the user\'s first name and last name', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input name="LoginRadius_settings[username_separator]" type="radio"  <?php echo ! isset( $loginRadiusSettings[ 'username_separator'] ) ? 'checked="checked"' : Admin_Helper:: is_radio_checked( 'seperator', 'dash' ); ?> value="dash" />
                                <span><?php _e( 'Dash: Firstname-Lastname [Ex: John-Doe]', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input name="LoginRadius_settings[username_separator]" type="radio"  <?php echo Admin_Helper::is_radio_checked( 'seperator', 'dot' ); ?> value="dot"/>
                                <span><?php _e( 'Dot: Firstname.Lastname [Ex: John.Doe]', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input name="LoginRadius_settings[username_separator]" type="radio"  <?php echo Admin_Helper::is_radio_checked( 'seperator', 'space' ); ?> value='space'/>
                                <span><?php _e( 'Space: Firstname Lastname [Ex: John Doe]', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>

                        <div>
                            <h4>
                                <?php _e('Select whether you would like to control account activation and deactivation', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'You can enable/disable the user from the Status column on the Users page in WordPress admin screens', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input type="radio" id="controlActivationYes" name="LoginRadius_settings[LoginRadius_enableUserActivation]" value='1' <?php echo ( isset( $loginRadiusSettings['LoginRadius_enableUserActivation'] ) && $loginRadiusSettings['LoginRadius_enableUserActivation'] == 1 ) ? 'checked' : ''; ?> />
                                <span><?php _e( 'Yes, display activate/deactivate option in the ', 'lr-plugin-slug' ) ?> <a href="<?php echo get_admin_url() ?>users.php" target="_blank" ><?php _e( 'User list', 'lr-plugin-slug' ); ?></a></span>
                            </label>
                            <label>
                                <input type="radio" id="controlActivationNo" name="LoginRadius_settings[LoginRadius_enableUserActivation]" value="0" <?php echo ( ( isset( $loginRadiusSettings['LoginRadius_enableUserActivation'] ) && $loginRadiusSettings['LoginRadius_enableUserActivation'] == 0 ) ) || ! isset( $loginRadiusSettings['LoginRadius_enableUserActivation'] ) ? 'checked' : ''; ?> /> 
                                <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <div id="loginRadiusDefaultStatus" class="lr-row">
                                <h5>
                                    <?php _e( 'Select the default status of the user when he/she registers on your website', 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip" data-title="<?php _e( 'Select whether you would like the user to be set as an active or inactive user after the initial registration process', 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </h5>
                                <label>
                                    <input type="radio" name="LoginRadius_settings[LoginRadius_defaultUserStatus]" value='1' <?php echo ( ( isset( $loginRadiusSettings['LoginRadius_defaultUserStatus'] ) && $loginRadiusSettings['LoginRadius_defaultUserStatus'] == 1 ) ) || !isset($loginRadiusSettings['LoginRadius_defaultUserStatus']) ? 'checked' : ''; ?> />
                                    <span><?php _e( 'Active', 'lr-plugin-slug' ); ?></span>
                                </label>
                                <label>
                                    <input type="radio" name="LoginRadius_settings[LoginRadius_defaultUserStatus]" value="0" <?php echo ( isset( $loginRadiusSettings['LoginRadius_defaultUserStatus'] ) && $loginRadiusSettings['LoginRadius_defaultUserStatus'] == 0 ) ? 'checked' : ''; ?>/>
                                    <span><?php _e( 'Inactive', 'lr-plugin-slug' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <h4>
                                <?php _e('Select whether to display the social network(s) the user is connected with in the user list', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'Select Yes, if you want to see the list of social providers the user account is linked with in the user list', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input type="radio" name="LoginRadius_settings[LoginRadius_noProvider]" value="1" <?php echo ( $loginRadiusSettings['LoginRadius_noProvider'] == 1 ) ? 'checked' : ''; ?> />
                                <span><?php _e( 'Yes, display the social network(s) that the user is connected with in the user list', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input type="radio" name="LoginRadius_settings[LoginRadius_noProvider]" value='0' <?php echo ( $loginRadiusSettings['LoginRadius_noProvider'] == 0 ) ? 'checked' : ''; ?> />
                                <span><?php _e( 'No, do not display the social network(s) that the user is connected with in the user list', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>

                        <div>
                            <h4>
                                <?php _e('Select whether the user profile data should be updated in your WordPress database, every time a user logs in', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'If you disable this option, the user profile data will be saved only once when the user logs in for the first time on your website, and this data will not be updated again in your WordPress database, even if the user updates their social account.', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input type="radio" name="LoginRadius_settings[profileDataUpdate]" value='1' <?php echo ( ! isset( $loginRadiusSettings['profileDataUpdate'] ) || $loginRadiusSettings['profileDataUpdate'] == 1 ) ? 'checked' : ''; ?> />
                                <span><?php _e( 'Yes', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input type="radio" name="LoginRadius_settings[profileDataUpdate]" value="0" <?php echo ( isset( $loginRadiusSettings['profileDataUpdate'] ) && $loginRadiusSettings['profileDataUpdate'] == 0 ) ? 'checked' : ''; ?> />
                                <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>

                        <div>
                            <h4>
                                <?php _e('Select whether to let users use their social profile picture as an avatar on your website', 'lr-plugin-slug' ); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'Select Yes, if you want to let users use their profile picture from their linked social account as an avatar on your website', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input name ="LoginRadius_settings[LoginRadius_socialavatar]" type="radio"  <?php echo Admin_Helper:: is_radio_checked( 'avatar', 'socialavatar' ); ?> value="socialavatar" />
                                <span><?php _e( 'Yes, use the small avatars', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input name="LoginRadius_settings[LoginRadius_socialavatar]" type="radio" <?php echo Admin_Helper:: is_radio_checked( 'avatar', 'largeavatar' ); ?> value="largeavatar" />
                                <span><?php _e( 'Yes, use the large avatars', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input name ="LoginRadius_settings[LoginRadius_socialavatar]" type="radio" <?php echo Admin_Helper:: is_radio_checked( 'avatar', 'defaultavatar' ); ?> value="defaultavatar" />
                                <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>

                        <div>
                            <h4>
                                <?php _e("Enable account linking", 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'Select Yes, If you want to enable social account linking. This option will also shows users the linking interface on the wordpress dashboard that allows users to link their other social providers', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input type="radio" name="LoginRadius_settings[LoginRadius_socialLinking]" value='1' <?php echo ( ( isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && $loginRadiusSettings['LoginRadius_socialLinking'] == 1 ) || ! isset($loginRadiusSettings['LoginRadius_socialLinking']) ) ? 'checked' : ''; ?> />
                                <span><?php _e( 'Yes', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input type="radio" name="LoginRadius_settings[LoginRadius_socialLinking]" value="0" <?php checked('0', @$loginRadiusSettings['LoginRadius_socialLinking'] ); ?> />
                                <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>

                        <div>
                            <h4>
                                <?php _e('Send email to user with their username and password after registration', 'lr-plugin-slug'); ?>
                                <span class="lr-tooltip" data-title="<?php _e( 'Choose Yes, if you want the user to receive an email notification about their WordPress username and password after registration', 'lr-plugin-slug' ); ?>">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </span>
                            </h4>
                            <label>
                                <input name="LoginRadius_settings[LoginRadius_sendemail]" type="radio"  value="sendemail" <?php echo Admin_Helper:: is_radio_checked( 'send_email', 'sendemail' ); ?> />
                                <span><?php _e( 'Yes', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input name="LoginRadius_settings[LoginRadius_sendemail]" type="radio" value="notsendemail" <?php echo Admin_Helper:: is_radio_checked( 'send_email', 'notsendemail' ); ?> />
                                <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>
                    </div><!-- lr-row -->
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
                                <input name="LoginRadius_settings[enable_degugging]" type="radio"  value="1" <?php echo ( isset( $loginRadiusSettings['enable_degugging'] ) && $loginRadiusSettings['enable_degugging'] == '1' ) ? 'checked = "checked"' : ''; ?> />
                                <span><?php _e( 'Yes', 'lr-plugin-slug' ); ?></span>
                            </label>
                            <label>
                                <input name="LoginRadius_settings[enable_degugging]" type="radio" value="0" <?php echo ( ! isset( $loginRadiusSettings['enable_degugging'] ) || $loginRadiusSettings['enable_degugging'] == '0' ) ? 'checked="checked"' : ''; ?> />
                                <span><?php _e( 'No', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * Render settings page
         */
        public static function render_options_page() {
            global $loginRadiusSettings;

            if ( isset( $_POST['reset'] ) ) {
                LR_Social_Login_Install::reset_loginradius_login_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">' . __( 'Login settings have been reset and default values loaded', 'lr-plugin-slug' ) . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }
            $loginRadiusSettings = get_option( 'LoginRadius_settings' );
            ?>
            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo">
                        <a href="//loginradius.com" target="_blank">LoginRadius</a>
                        <em>Social Login</em>
                    </h2>
                </header>

                <div id="lr_options_tabs" class="cf">
                    <div class="cf">
                        <ul class="lr-options-tab-btns">
                            <li class="nav-tab lr-active" data-tab="lr_options_tab-1"><?php _e( 'Social Login', 'lr-plugin-slug' ); ?></li>
                            <?php if ( ! class_exists( 'LR_Disqus' ) && ! class_exists( 'LR_Commenting' ) ) { ?>
                                <li class="nav-tab" data-tab="lr_options_tab-2"><?php _e( 'Social Commenting', 'lr-plugin-slug' ); ?></li>
                            <?php } ?>
                            <li class="nav-tab" data-tab="lr_options_tab-3"><?php _e( 'Customization Settings', 'lr-plugin-slug' ); ?></li>
                            <li class="nav-tab" data-tab="lr_options_tab-4"><?php _e( 'Advanced Settings', 'lr-plugin-slug' ); ?></li>
                        </ul>
                        <form action="options.php" method="post">
                            <?php
                            settings_fields( 'LoginRadius_setting_options' );
                            settings_errors();
                            self::login_options( $loginRadiusSettings );
                            if ( ! class_exists( 'LR_Disqus' ) && ! class_exists( 'LR_Commenting' ) ) {
                                self::commenting_options( $loginRadiusSettings );
                            }
                            self::customization_options( $loginRadiusSettings );
                            self::advanced_options( $loginRadiusSettings );
                            ?>
                            <p class="submit">
                                <a href="<?php echo htmlspecialchars( add_query_arg( array( 'preview' => 1, 'template' => get_option( 'template' ), 'stylesheet' => get_option( 'stylesheet' ), 'preview_iframe' => true, 'TB_iframe' => 'true' ), get_option( 'home' ) . '/' ) ); ?>" class="thickbox thickbox-preview" id="preview" >
                                    <?php _e('Preview', 'lr-plugin-slug'); ?>
                                </a>
                                <input style="margin-left:10px" type="submit" name="save" class="button button-primary" value="<?php _e( 'Save Changes', 'lr-plugin-slug' ); ?>" />
                            </p>
                        </form>
                    </div><!-- Unnamed Tabs Content -->
                    <?php do_action( 'lr_reset_admin_ui', 'Social Login' ); ?>
                </div><!-- LR Options Tabs -->
                <?php self::help_options(); ?>
            </div><!-- lr-wrap -->
            <?php
        }

    }

}