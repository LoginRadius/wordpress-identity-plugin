<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The LoginRadius HOSTED admin settings page.
 */
if (!class_exists('LR_HOSTED_Admin_Settings')) {

    class LR_HOSTED_Admin_Settings {

        function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
            add_action('admin_menu', array($this, 'adjust_the_wp_menu'), 999);
        }

        function adjust_the_wp_menu() {
            global $lr_raas_settings;
            if(isset($lr_raas_settings['hosted_enable']) && $lr_raas_settings['hosted_enable'] == '1') {
                remove_submenu_page('LoginRadius', 'loginradius_customization');
            }
        }

        public static function render_options_page($lr_raas_settings) {
            ?>
            <div class="lr_options_container">
                <div class="lr-row">
                    <h3>
            <?php _e('Hosted Services Settings', 'LoginRadius'); ?>
                    </h3>
                    <div>
                        <input type="checkbox" class="lr-toggle" id="lr-hosted-enable" name="LR_Raas_Settings[hosted_enable]" value="1" <?php echo isset($lr_raas_settings['hosted_enable']) && $lr_raas_settings['hosted_enable'] == '1' ? 'checked' : ''; ?> />
                        <label class="lr-show-toggle" for="lr-hosted-enable">
            <?php _e('Enable Hosted Page Services', 'LoginRadius'); ?>
                            <span class="lr-tooltip" data-title="<?php _e('Turn on to enable Hosted page functionality with user registration.', 'LoginRadius'); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * admin script
         * 
         * @param type $hook
         * @return type
         */
        public function admin_enqueue_scripts($hook) {
            if ($hook != 'loginradius_page_User_Registration') {
                return;
            }
            global $lr_js_in_footer;
            wp_enqueue_script('lr-hosted-admin-js', LR_ROOT_URL . 'lr-hosted/assets/js/lr-hosted-admin.js', array('jquery'), LR_PLUGIN_VERSION, $lr_js_in_footer);
        }

    }

    new LR_HOSTED_Admin_Settings();
}