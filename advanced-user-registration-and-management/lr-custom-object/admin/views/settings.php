<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if ( ! class_exists( 'LR_Custom_Object_Admin_Settings' ) ) {

    class LR_Custom_Object_Admin_Settings {

        function __construct() {
            
            add_filter( 'add_raas_tab', array( $this, 'create_custom_object_tab' ) );
            add_filter( 'add_raas_tab_body', array( $this, 'create_custom_object_tab_body' ) );
            add_filter( 'admin_head', array( $this, 'load_custom_scripts' ) );
        }

        function load_custom_scripts() {
            global $lr_js_in_footer;
            wp_enqueue_script( 'lr_customobject_admin_script', LR_CUSTOM_OBJECT_URL . 'assets/js/lr_customobject.js', array( 'jquery' ), LR_PLUGIN_VERSION, $lr_js_in_footer );
        }

        function create_custom_object_tab() {
            ?>
                <li class="nav-tab" data-tab="lr_options_tab-3"><?php _e( 'Custom Object', 'lr-plugin-slug' ) ?></li>
            <?php
        }

        public static function create_custom_object_tab_body() {
            global $lr_Custom_Obj_Fields, $lr_raas_custom_obj_settings;
            ?>
            <div id="lr_options_tab-3" class="lr-tab-frame">
                <div class="lr_options_container">
                    <div class="lr-row">
                        <label for="lr-enable-custom-obj" class="lr-toggle">
                            <input type="checkbox" class="lr-toggle" id="lr-enable-custom-obj" name="LR_Raas_Settings[enable_custom_obj]" value="1" <?php echo ( isset( $lr_raas_custom_obj_settings['enable_custom_obj'] ) && $lr_raas_custom_obj_settings['enable_custom_obj'] == '1' ) ? 'checked' : ''; ?> />
                            <span class="lr-toggle-name"><?php _e( 'Enable Custom Object Fields', 'lr-plugin-slug' ); ?></span>
                        </label>
                    </div>
                </div>
                <div class="lr_options_container">
                    <div class="lr-row">
                        <span class="custom_option">
                            <label class="lr_property_title"><?php _e( 'Custom Object ID', 'lr-plugin-slug' ); ?></label>
                            <input type="text" class="lr-row-field" name="LR_Raas_Settings[custom_obj_id]" placeholder="<?php _e( 'Custom Object ID', 'lr-plugin-slug' ); ?>" value="<?php echo isset( $lr_raas_custom_obj_settings['custom_obj_id'] ) ? $lr_raas_custom_obj_settings['custom_obj_id'] : ''; ?>" />
                        </span>
                    </div>
                </div>

                <div class="lr_options_container">
                    <div class="lr-option-disabled-hr lr-customobject" style="display: none;"></div>
                    <div class="lr-row">
                        <?php foreach ( $lr_Custom_Obj_Fields as $field ) { ?>
                        <div style="clear: both;">
                                <input type="checkbox" class="lr-toggle" id="lr-show-raas-custom-field-<?php echo $field; ?>" name="LR_Raas_Settings[show_custom_<?php echo $field; ?>]" value="1" <?php echo isset( $lr_raas_custom_obj_settings[ 'show_custom_' . $field ] ) && $lr_raas_custom_obj_settings[ 'show_custom_' . $field ] == '1' ? 'checked' : ''; ?> />
                                <label class="lr-show-toggle" for="lr-show-raas-custom-field-<?php echo $field; ?>">
                                    <?php _e( 'Custom Object Field ' . $field, 'lr-plugin-slug' ); ?>
                                    <span class="lr-tooltip tip-bottom" data-title="<?php _e( 'Show custom object field ' . $field, 'lr-plugin-slug' ); ?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </label>
                                <span class="custom_option">
                                    <label class="lr_property_title"><?php _e( 'Field Title', 'lr-plugin-slug' ); ?></label>
                                    <input type="text" style="float:left;" class="lr-row-field" name="LR_Raas_Settings[custom_<?php echo $field; ?>_title]" placeholder="<?php _e( 'Custom Object Field ' . $field . ' Name', 'lr-plugin-slug' ); ?>" value="<?php echo isset( $lr_raas_custom_obj_settings['custom_' . $field . '_title'] ) ? $lr_raas_custom_obj_settings['custom_' . $field . '_title'] : ''; ?>" />
                                    <input type="checkbox" style="margin-left:10px;" id="lr-raas-custom-field-<?php echo $field; ?>-required" name="LR_Raas_Settings[custom_<?php echo $field; ?>_required]" value="1" <?php echo isset( $lr_raas_custom_obj_settings[ 'custom_' . $field . '_required' ] ) && $lr_raas_custom_obj_settings['custom_' . $field . '_required' ] == '1' ? 'checked' : ''; ?> />
                                    <label class="required_option" for="lr-raas-custom-field-<?php echo $field; ?>-required">
                                        <?php _e( 'Required', 'lr-plugin-slug' ); ?>
                                        <span class="lr-tooltip tip-bottom" data-title="<?php _e( 'Show custom object field ' . $field, 'lr-plugin-slug' ); ?>">
                                            <span class="dashicons dashicons-editor-help"></span>
                                        </span>
                                    </label>
                                </span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        }

    }

    new LR_Custom_Object_Admin_Settings();
}