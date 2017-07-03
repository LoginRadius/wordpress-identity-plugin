<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
/**
 * The activation settings class.
 */
if (!class_exists('ciam_authentication_settings')) {

    class ciam_authentication_settings {

        public function __construct() {
            global $ciam_credencials;


            if (!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])) {
                return;
            }
        }

        /**
         * generate ciam page selection option
         * 
         * @param type $pages
         * @param type $settings
         * @param type $name
         * @return string
         */
        private static function select_field($pages, $settings, $name) {
            $output = '<select class="ciam-row-field" name="ciam_authentication_settings[' . $name . ']" id="ciam_login_page_id">';
            $output .= '<option value="">' . __(' --- Select Page --- ', 'ciam-plugin-slug') . '</option>';
            foreach ($pages as $page) {
                $select_page = '';

                if (isset($settings[$name]) && $page->ID == $settings[$name]) {
                    $select_page = ' selected="selected"';
                }
                $output .= '<option value="' . $page->ID . '" ' . $select_page . '>' . $page->post_title . '</option>';
            }
            $output .= '</select>';
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), $output);
            return $output;
        }

        public static function render_options_page() {
            global $ciam_authentication_settings;


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


            $ciam_authentication_settings = get_option('Ciam_Authentication_settings');
            ?>

            <div class="wrap active-wrap cf">
                <header>
                    <h2 class="logo"><a href="//www.loginradius.com" target="_blank">Authentication Page Configuration</a></h2>
                </header>
                <div class="cf"> 
                    <ul class="ciam-options-tab-btns">
                        <li class="nav-tab ciam-active" data-tab="ciam_options_tab-1"><?php _e('User Registration', 'ciam-plugin-slug') ?></li>
                        <li class="nav-tab" data-tab="ciam_options_tab-2"><?php _e('Advanced Settings', 'ciam-plugin-slug') ?></li>
                    </ul>
                    <div id="ciam_options_tab-1" class="ciam-tab-frame ciam-active">
                        <form action="options.php" method="post">
                            <?php
                            settings_fields('ciam_authentication_settings');
                            settings_errors();
                            /* action for hosted page */
                            do_action("hosted_page");
                            ?>
                            <div class="ciam_options_container" id="autopage-generate">

                                <div class="ciam-row">
                                    <h3>
                                        <?php _e('User Registration Integration', 'ciam-plugin-slug'); ?>
                                    </h3>
                                    <div>
                                        <input type="checkbox" class="ciam-toggle" id="ciam-autopage" name="ciam_authentication_settings[ciam_autopage]" value='1' <?php echo ( isset($ciam_authentication_settings['ciam_autopage']) && $ciam_authentication_settings['ciam_autopage'] == '1' ) ? 'checked' : '' ?> />
                                        <label class="ciam-show-toggle" for="ciam-autopage">
                                            <?php _e('Enable Auto Generate User Registration'); ?>
                                            <span class="ciam-tooltip" data-title="<?php _e('Turn on, if you want to enable Auto Generate User Registration pages', 'ciam-plugin-slug'); ?>">
                                                <span class="dashicons dashicons-editor-help"></span>
                                            </span>
                                        </label>
                                        <div class="ciam-custom-page-settings">
                                            <div>
                                                <label>
                                                    <span class="ciam_property_title">
                                                        <?php _e('Login Page', 'ciam-plugin-slug'); ?>
                                                        <span class="ciam-tooltip" data-title="<?php _e('Add Login page Short Code from Advance Setting tab in selected page.', 'ciam-plugin-slug'); ?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field($pages, $ciam_authentication_settings, 'login_page_id'); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label>
                                                    <span class="ciam_property_title">
                                                        <?php _e('Registration Page', 'ciam-plugin-slug'); ?>
                                                        <span class="ciam-tooltip" data-title="<?php _e('Add Registration page Short Code from Advance Setting tab in selected page.', 'ciam-plugin-slug'); ?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field($pages, $ciam_authentication_settings, 'registration_page_id'); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label>
                                                    <span class="ciam_property_title">
                                                        <?php _e('Reset Password Page', 'ciam-plugin-slug'); ?>
                                                        <span class="ciam-tooltip" data-title="<?php _e('Add Reset Password page Short Code from Advance Setting tab in selected page.', 'ciam-plugin-slug'); ?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field($pages, $ciam_authentication_settings, 'change_password_page_id'); ?>
                                                </label>
                                            </div>
                                            <div>
                                                <label>
                                                    <span class="ciam_property_title">
                                                        <?php _e('Forgot Password Page', 'ciam-plugin-slug'); ?>
                                                        <span class="ciam-tooltip" data-title="<?php _e('Add Forgot Password page Short Code from Advance Setting tab in selected page.', 'ciam-plugin-slug'); ?>">
                                                            <span class="dashicons dashicons-editor-help"></span>
                                                        </span>
                                                    </span>
                                                    <?php echo self::select_field($pages, $ciam_authentication_settings, 'lost_password_page_id'); ?>
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ciam_options_container">
                                <div class="active-row">
                                    <h4>
                                        <?php _e('Redirection settings after login ', 'CIAM'); ?>
                                        <span class="active-tooltip" data-title="<?php _e('Page the user is redirected to after login', 'CIAM'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <label>
                                        <input type="radio" class="loginRedirectionRadio" name="ciam_authentication_settings[after_login_redirect]" value="samepage" <?php echo (!isset($ciam_authentication_settings['after_login_redirect']) || $ciam_authentication_settings['after_login_redirect'] == 'samepage' ) ? 'checked' : ''; ?>/> 
                                        <span><?php _e('Redirect to the same page where the user logged in', 'ciam-plugin-slug'); ?></span>
                                    </label>

                                    <label>
                                        <input type="radio" class="loginRedirectionRadio" name="ciam_authentication_settings[after_login_redirect]" value="homepage" <?php echo ( isset($ciam_authentication_settings['after_login_redirect']) && $ciam_authentication_settings['after_login_redirect'] == 'homepage' ) ? 'checked' : ''; ?>/> 
                                        <span><?php _e('Redirect to the home page of your WordPress site', 'ciam-plugin-slug'); ?></span>
                                    </label>
                                    <label>
                                        <input type="radio" class="loginRedirectionRadio" name="ciam_authentication_settings[after_login_redirect]" value="dashboard" <?php echo ( isset($ciam_authentication_settings['after_login_redirect']) && $ciam_authentication_settings['after_login_redirect'] == 'dashboard' ) ? 'checked' : ''; ?> /> 
                                        <span><?php _e('Redirect to the user\'s account dashboard', 'ciam-plugin-slug'); ?></span>
                                    </label>

                                    <label>
                                        <input type="radio" class="loginRedirectionRadio custom" id="customUrl" name="ciam_authentication_settings[after_login_redirect]" value="custom"  <?php echo ( isset($ciam_authentication_settings['after_login_redirect']) && $ciam_authentication_settings['after_login_redirect'] == 'custom' ) ? 'checked' : ''; ?>/>
                                        <span><?php _e('Redirect to a custom URL'); ?></span>
                                        <div id="customRedirectUrlField">

                                            <label>
                                                <span><?php _e('Redirect to a custom URL'); ?></span>

                                                <input type="text" id="customRedirectOther" name="ciam_authentication_settings[custom_redirect_other]" value="<?php echo (isset($ciam_authentication_settings['custom_redirect_other'])) ? $ciam_authentication_settings['custom_redirect_other'] : ''; ?>" autofill='off' autocomplete='off' >
                                            </label>
                                        </div>
                                    </label>
                                </div>
                            </div>
                    </div>
                    <div id="ciam_options_tab-2" class="ciam-tab-frame">
                        <div class="ciam_options_container" id="ciam-shortcodes">
                            <div class="ciam-row ciam-ur-shortcodes">
                                <h3><?php _e('User Registration Short Codes', 'ciam-plugin-slug'); ?></h3>
                                <div class="ciam_shortcode_div">
                                    <h4><?php _e('Login Form', 'ciam-plugin-slug'); ?>
                                        <span class="ciam-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the Login Form', 'ciam-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly="readonly">[ciam_login_form]</textarea>
                                </div>


                                <div class="ciam_shortcode_div">
                                    <h4><?php _e('Registration Form', 'ciam-plugin-slug'); ?>
                                        <span class="ciam-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the Registration Form', 'ciam-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly="readonly">[ciam_registration_form]</textarea>
                                </div>


                                <div class="ciam_shortcode_div">
                                    <h4><?php _e('Forgotten Password Form', 'ciam-plugin-slug'); ?>
                                        <span class="ciam-tooltip tip-bottom" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the Forgotten Password Form', 'ciam-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly="readonly">[ciam_forgotten_form]</textarea>
                                </div>


                                <div class="ciam_shortcode_div">
                                    <h4><?php _e('Change Password Form', 'ciam-plugin-slug'); ?>
                                        <span class="ciam-tooltip tip-top" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display Change Password Form', 'ciam-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly="readonly">[ciam_password_form]</textarea>
                                </div>


                                <div class="ciam_shortcode_div">
                                    <h4><?php _e('Default WP Login Form', 'ciam-plugin-slug'); ?>
                                        <span class="ciam-tooltip tip-top" data-title="<?php _e('Copy and paste the following shortcode into a page or post to display the default Wordpress Login Form. This can be used while configuring your site', 'ciam-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </h4>
                                    <textarea rows="1" onclick="this.select()" spellcheck="false" class="ciam-shortcode" readonly="readonly">[ciam_wp_default_login]</textarea>
                                </div>
                            </div>



                        </div>


                        <div class="ciam_options_container">
                            <div class="ciam-row ciam-ur-shortcodes">

                                <label class="active-toggle">
                                    <input type="checkbox" class="active-toggle" name="ciam_authentication_settings[debug_enable]" value="1" <?php echo ( isset($ciam_authentication_settings['debug_enable']) && $ciam_authentication_settings['debug_enable'] == '1' ) ? 'checked' : ''; ?> />
                                    <span class="active-toggle-name">
                                        <?php _e('Do you want to enable log ?', 'CIAM'); ?> 
                                       
                                    </span>
                                    <span class="ciam-tooltip tip-top" data-title="<?php _e('Turn on,if you want to auto generate logs', 'ciam-plugin-slug'); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                </label>   


                            </div>
                        </div>


                    </div>


                    <div style="position: relative;">
                        <div class="ciam-option-disabled-hr" style="display: none;"></div>
                    </div>
                    <p class="submit">
                        <?php submit_button('Save Settings', 'primary', 'submit', false); ?>

                    </p>
                    </form>

                </div>        
            </div>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), "");
        }

    }

}