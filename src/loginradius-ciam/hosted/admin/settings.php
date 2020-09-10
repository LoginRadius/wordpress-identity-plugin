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
            global $ciam_credentials;

            if (!isset($ciam_credentials['apikey']) || empty($ciam_credentials['apikey']) || !isset($ciam_credentials['secret']) || empty($ciam_credentials['secret'])) {
                return;
            }
            add_action('hosted_page', array($this, 'render_hosted_setting'));
        }

        /*
         * Render html to the user
         */

        public function render_hosted_setting() {
            global $ciam_setting;
            ?>
                <input type="hidden" name="ciam_authentication_settings[enable_hostedpage]">
                <label class="active-toggle">                
                    <input type="checkbox" class="active-toggle" id="ciam_enable_hostedPage" name="ciam_authentication_settings[enable_hostedpage]" value="1" <?php echo ( isset($ciam_setting['enable_hostedpage']) && $ciam_setting['enable_hostedpage'] == '1' ) ? 'checked' : ''; ?> />
                    <span class="active-toggle-name">
                        <?php _e('Enable Identity Experience Framework.', 'CIAM'); ?> 
                    </span>
                </label>   
                <span class="hostedpage-tooltip ciam-tooltip" data-title="<?php _e('From here, Identity Experience Framework functionality can be enabled. It is recommended that SSO should be enabled with the Identity Experience Framework.', 'ciam-plugin-slug'); ?>">
                    <span class="dashicons dashicons-editor-help"></span>
                </span><br>

            <script>
            jQuery(document).ready(function ($) {
            <?php
            if (isset($ciam_setting['enable_hostedpage']) && ($ciam_setting['enable_hostedpage'] == 1)) {
                ?>
                        $("#ciam-shortcodes,#autopage-generate").hide();
                <?php }
            ?>
                });
            </script>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    if (jQuery("#ciam_enable_hostedPage").prop("checked") == true) {             
                        jQuery("#autopage-generate,#ciam-shortcodes").hide();
                        jQuery('[data-tab="ciam_options_tab-2"],[data-tab="ciam_options_tab-3"],[data-tab="ciam_options_tab-4"],[data-tab="ciam_options_tab-5"],[data-tab="ciam_options_tab-6"],[data-tab="ciam_options_tab-7"],[data-tab="ciam_options_tab-9"]').hide();
                    } else {
                        jQuery("#autopage-generate,#ciam-shortcodes").show();
                        jQuery(".custom-hub-domain-settings-field").hide();
                        jQuery('[data-tab="ciam_options_tab-2"],[data-tab="ciam_options_tab-3"],[data-tab="ciam_options_tab-4"],[data-tab="ciam_options_tab-5"],[data-tab="ciam_options_tab-6"],[data-tab="ciam_options_tab-7"],[data-tab="ciam_options_tab-9"]').show();
                    }
                    jQuery("#ciam_enable_hostedPage").on('change', function () {
                        if (jQuery(this).prop("checked") == true) {                   
                            jQuery(".custom-hub-domain-settings-field").show();
                            jQuery("#autopage-generate,#ciam-shortcodes").hide();
                            jQuery('[data-tab="ciam_options_tab-2"],[data-tab="ciam_options_tab-3"],[data-tab="ciam_options_tab-4"],[data-tab="ciam_options_tab-5"],[data-tab="ciam_options_tab-6"],[data-tab="ciam_options_tab-7"],[data-tab="ciam_options_tab-9"]').hide();
                        } else {                   
                            jQuery(".custom-hub-domain-settings-field").hide();
                            jQuery("#autopage-generate,#ciam-shortcodes").show();
                            jQuery('[data-tab="ciam_options_tab-2"],[data-tab="ciam_options_tab-3"],[data-tab="ciam_options_tab-4"],[data-tab="ciam_options_tab-5"],[data-tab="ciam_options_tab-6"],[data-tab="ciam_options_tab-7"],[data-tab="ciam_options_tab-9"]').show();
                        }
                    });
                });
            </script>
            <?php
            /* action for debug mode */
            do_action("ciam_debug", __FUNCTION__, func_get_args(), get_class(), '');
        }
    }
    new ciam_hostedpage_settings();
}

