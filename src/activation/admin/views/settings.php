<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
/**
 * The activation settings class.
 */
if (!class_exists('CIAM_Activation_Settings')) {

    class CIAM_Activation_Settings {

        public static function render_options_page() {
            global $ciam_api_settings;
            $ciam_api_settings = get_option('Ciam_API_settings');
            ?>

            <div class="wrap active-wrap cf">
                <header>
                    <h2 class="logo"><a href="//www.loginradius.com" target="_blank">CIAM Configuration</a></h2>

                </header>

                <div class="cf">
                    <div class="cf">

                        
                        <form action="options.php" method="post">
                            <?php
                            settings_fields('Ciam_API_settings');
                            settings_errors();
                            ?>
                            
                            <div class="ciam_options_container">
                                <span id="error_msg"></span>
                                <div class="active-row">
                                    <label >
                                        <span class="ciam_property_title requires"><?php _e('LoginRadius Site Name', 'CIAM'); ?></span>
                                        <input type="text" id="sitename" class="active-row-field" name="Ciam_API_settings[sitename]" value="<?php echo (isset($ciam_api_settings['sitename']) && !empty($ciam_api_settings['sitename']) ? $ciam_api_settings['sitename'] : ''); ?>" autofill='off' autocomplete='off' />
                                    </label>

                                    <label>
                                        <span class="ciam_property_title requires"><?php _e('LoginRadius API Key', 'CIAM'); ?></span>
                                        <input type="text" id="apikey" class="active-row-field" name="Ciam_API_settings[apikey]" value="<?php echo ( isset($ciam_api_settings['apikey']) && !empty($ciam_api_settings['apikey']) ) ? $ciam_api_settings['apikey'] : ''; ?>" autofill='off' autocomplete='off' />
                                    </label>

                                    <label>
                                        <span class="ciam_property_title requires"><?php _e('LoginRadius API Secret', 'CIAM'); ?></span>
                                        <input type="text" id="secret" class="active-row-field" name="Ciam_API_settings[secret]" value="<?php echo ( isset($ciam_api_settings['secret']) && !empty($ciam_api_settings['secret']) ) ? $ciam_api_settings['secret'] : ''; ?>" autofill='off' autocomplete='off' />
                                    </label>

                                </div>
                            </div>
                            <p class="submit">
                                <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                            </p>


                        </form>
                    </div>
                </div>        
            </div>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), '');
        }

    }

}

