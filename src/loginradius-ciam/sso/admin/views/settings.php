<?php
/**

 * The activation settings class.

 */
// Exit if called directly

if (!defined('ABSPATH')) {

    exit();
}



if (!class_exists('CIAM_Sso_Settings')) {



    class CIAM_Sso_Settings {
        /*

         * Render html to end user. 

         */

        public function render_options_page() {
            global $ciam_sso_page_settings;
            $ciam_sso_page_settings = get_option('Ciam_Sso_Page_settings');              
            ?>

            <div class="wrap active-wrap cf">
                <header>
                    <h1 class="logo"><a href="//www.loginradius.com" target="_blank">Single Sign On</a></h1>
                </header>
                <div class="cf">   
                    <form action="options.php" method="post">
                    <?php
                    settings_fields('Ciam_Sso_Page_settings');
                    settings_errors();
                    ?>
                        <div class="ciam_options_container">
                            <div class="active-row">
                                <h3><?php _e('Enable SSO', 'CIAM'); ?></h3>
                                <label class="active-toggle">
                                    <input type="checkbox" class="active-toggle" name="Ciam_Sso_Page_settings[sso_enable]" value="1" <?php echo ( isset($ciam_sso_page_settings['sso_enable']) && $ciam_sso_page_settings['sso_enable'] == '1' ) ? 'checked' : ''; ?> />
                                    <span class="active-toggle-name">
                                        <?php _e('Do you want to enable Single Sign On (SSO)', 'CIAM'); ?>
                                        <span class="ciam-tooltip" data-title="<?php _e('This feature allows Single Sign On to be enabled on different sites with common LoginRadius app.', 'ciam-plugin-slug'); ?>"> 
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <p class="submit">
                              <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                        </p>
                    </form>
                </div>      

            </div>        
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), "");
        }
    }
}