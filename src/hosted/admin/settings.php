<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}
/**
 * The activation settings class.
 */
if (!class_exists('ciam_hostedpage_settings')) {


    class ciam_hostedpage_settings {

        public function __construct() {
            global $ciam_credencials;

            if (!isset($ciam_credencials['apikey']) || empty($ciam_credencials['apikey']) || !isset($ciam_credencials['secret']) || empty($ciam_credencials['secret'])) {
                return;
            }
           add_action('hosted_page', array($this,'render_hosted_setting'));
           
           /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class($this), '');
        }

        public static function render_hosted_setting() {
            global $ciam_authentication_settings;
           
            ?>
            <div class="ciam_options_container">
                <div class="active-row">
                    <label class="active-toggle">
                        <input type="checkbox" class="active-toggle" id="ciam_enable_hostedPage" name="ciam_authentication_settings[enable_hostedpage]" value="1" <?php echo ( isset($ciam_authentication_settings['enable_hostedpage']) && $ciam_authentication_settings['enable_hostedpage'] == '1' ) ? 'checked' : ''; ?> />
                        <span class="active-toggle-name">
                            <?php _e('Do you want to enable Hosted Page ?', 'CIAM'); ?> 
                        </span>
                    </label>   
                </div>
            </div>

            <script>
                jQuery(document).ready(function ($) {
            <?php
            if (isset($ciam_authentication_settings['enable_hostedpage']) && ($ciam_authentication_settings['enable_hostedpage'] == 1)) {
                ?>
                    $("#ciam-shortcodes,#autopage-generate").hide();
                <?php
            } ?>
            });
            </script>
            <script type="text/javascript">
                jQuery(document).ready(function () {

                    jQuery("#ciam_enable_hostedPage").on('change', function () {
                        if (jQuery(this).prop("checked") == true) {
                            jQuery("#autopage-generate,#ciam-shortcodes").hide();
                        } else {
                            jQuery("#autopage-generate,#ciam-shortcodes").show();
                        }
                    });
                });
            </script>
            <?php

            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_called_class(), '');
            
            }

    }

    new ciam_hostedpage_settings();
}

