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

        public function render_options_page() {

            global $ciam_credencials;

            ?>

            <div class="wrap active-wrap cf">

                <header>

                    <h1 class="logo"><a href="//www.loginradius.com" target="_blank"><?php _e('CIAM Configuration', 'CIAM'); ?></a></h1>

                </header>

                <div class="cf">

                    <form action="options.php" method="post">

                        <?php

                        settings_fields('Ciam_API_settings');

                        settings_errors();

                        ?>
                        <div id="error_msg" class="error updated" style="display:none">
                        
                        </div>
                        <div class="ciam_options_container">                           

                            <div class="active-row">

                                <h3><?php _e('LoginRadius API Configurations', 'CIAM'); ?></h3>

                                <label>

                                    <span class="ciam_property_title requires"><?php _e('LoginRadius Site Name', 'CIAM'); ?></span>

                                    <input type="text" id="sitename" class="active-row-field" name="Ciam_API_settings[sitename]" value="<?php echo (isset($ciam_credencials['sitename']) && !empty($ciam_credencials['sitename']) ? $ciam_credencials['sitename'] : ''); ?>" autofill='off' autocomplete='off' />

                                </label>

                                <label>

                                    <span class="ciam_property_title requires"><?php _e('LoginRadius API Key', 'CIAM'); ?></span>

                                    <input type="text" id="apikey" class="active-row-field" name="Ciam_API_settings[apikey]" value="<?php echo ( isset($ciam_credencials['apikey']) && !empty($ciam_credencials['apikey']) ) ? $ciam_credencials['apikey'] : ''; ?>" autofill='off' autocomplete='off' />

                                </label>

                                <label>

                                    <span class="ciam_property_title requires"><?php _e('LoginRadius API Secret', 'CIAM'); ?></span>

                                    <input type="text" id="secret" class="active-row-field" name="Ciam_API_settings[secret]" value="<?php echo ( isset($ciam_credencials['secret']) && !empty($ciam_credencials['secret']) ) ? $ciam_credencials['secret'] : ''; ?>" autofill='off' autocomplete='off' />

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

            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');

        }

    }

}