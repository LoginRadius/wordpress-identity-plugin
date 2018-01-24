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
                    <div class="ciam_options_container">
                    <div class="lr-frame">
                    <h3><?php _e('Help & Documentations', 'lr-plugin-slug'); ?></h3>
                    <table class="wp-list-table widefat">
                                    <tr>
                                        <td><a target="_blank" href="http://ish.re/1MC8L"><?php _e('Plugin Installation, Configuration and Troubleshooting', 'lr-plugin-slug'); ?></a></td>
        </tr>
        <tr>
                                        <td><a target="_blank" href="http://ish.re/96M7"><?php _e('About LoginRadius', 'lr-plugin-slug'); ?></a></td>
        </tr>
                                        <tr>
                                        <td><a target="_blank" href="http://ish.re/1MC8P"><?php _e('LoginRadius Products', 'lr-plugin-slug'); ?></a></td>
        </tr>
        </table>
                </div>
                        </div>

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
                                
                                <table class="wp-list-table widefat">
                                    <tr>
                                        <td width="150"><span class="ciam_property_title custom_ciam_property_title requires"><?php _e('LoginRadius API Key', 'CIAM'); ?></span></td>
                                        <td><input type="text" id="apikey" class="active-row-field custom_active-row-field" name="Ciam_API_settings[apikey]" value="<?php echo ( isset($ciam_credencials['apikey']) && !empty($ciam_credencials['apikey']) ) ? $ciam_credencials['apikey'] : ''; ?>" autofill='off' autocomplete='off' /></td>
                                        <td colspan="2"><a target="_blank" href="http://ish.re/1EVFR"><?php _e('How to get LoginRadius API Key', 'lr-plugin-slug'); ?></a></td>
                                    </tr>
                                    <tr>
                                        <td width="150"><span class="ciam_property_title custom_ciam_property_title requires"><?php _e('LoginRadius API Secret', 'CIAM'); ?></span></td>
                                        <td><div class="ciam_ciamsecrettoggle"><input type="password" id="secret" class="active-row-field custom_active-row-field" style="float:left;" name="Ciam_API_settings[secret]" value="<?php echo ( isset($ciam_credencials['secret']) && !empty($ciam_credencials['secret']) ) ? $ciam_credencials['secret'] : ''; ?>" autofill='off' autocomplete='off' /><div onclick="ciamsecrettoggle();" class="ciam_show_button"><?php _e('Show', 'CIAM'); ?></div></div></td>
                                        <td colspan="2"><a target="_blank" href="http://ish.re/1EVFR"><?php _e('How to get LoginRadius API Secret', 'lr-plugin-slug'); ?></a></td>
                                    </tr>
                                </table>
                                <input type="hidden" id="update_plugin" class="active-row-field" name="Ciam_API_settings[update_plugin]" value="true" />
                                    <input type="hidden" id="ciam-appname" class="active-row-field" name="Ciam_API_settings[sitename]" value="<?php echo ( isset($ciam_credencials['sitename']) && !empty($ciam_credencials['sitename']) ) ? $ciam_credencials['sitename'] : ''; ?>" />

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