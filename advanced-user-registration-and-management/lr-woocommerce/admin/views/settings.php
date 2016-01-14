<?php
// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the plugin admin.
 */
if (!class_exists('LR_Woocommerce_Admin_Settings')) {

    class LR_Woocommerce_Admin_Settings {

        public static function render_options_page() {
            global $lr_woocommerce_settings;
            if (isset($_POST['reset'])) {
                LR_Woocommerce_Install:: reset_woocommerce_options();
                echo '<p style="display:none;" class="lr-alert-box lr-notif">' . __('Woocommerce settings have been reset and default values loaded', 'lr-plugin-slug') . '</p>';
                echo '<script type="text/javascript">jQuery(function(){jQuery(".lr-notif").slideDown().delay(3000).slideUp();});</script>';
            }

            $lr_woocommerce_settings = get_option('LR_Woocommerce_Settings');
            ?>

            <div class="wrap lr-wrap cf">
                <header>
                    <h2 class="logo"><a href="//loginradius.com" target="_blank">LoginRadius</a><em>Woocommerce</em></h2>
                </header>
                <form action="options.php" method="post">
                    <?php
                    settings_fields('lr_woocommerce_settings');
                    settings_errors();
                    ?>

                    <div class="lr_options_container">
                        <div class="lr-row">
                            <h3>
                                <?php _e('Woocommerce Integration', 'lr-plugin-slug'); ?>
                            </h3>
                            <div>
                                <input type="checkbox" class="lr-toggle" id="lr-woocommerce-enable" name="LR_Woocommerce_Settings[woocommerce_enable]" value='1' <?php echo ( isset($lr_woocommerce_settings['woocommerce_enable']) && $lr_woocommerce_settings['woocommerce_enable'] == '1' ) ? 'checked' : '' ?> />
                                <label class="lr-show-toggle" for="lr-woocommerce-enable">
                                    <?php _e('Enable Woocommerce'); ?>
                                    <span class="lr-tooltip" data-title="<?php _e('Turn on, if you want to Enable Woocommerce Checkout data store on cloud.','lr-plugin-slug');?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </label>
                            </div>
                            <div>
                                <input type="checkbox" class="lr-toggle" id="lr-woocommerce-update" name="LR_Woocommerce_Settings[woocommerce_update_checkout]" value='1' <?php echo ( isset($lr_woocommerce_settings['woocommerce_update_checkout']) && $lr_woocommerce_settings['woocommerce_update_checkout'] == '1' ) ? 'checked' : '' ?> />
                                <label class="lr-show-toggle" for="lr-woocommerce-update">
                                    <?php _e('Update customer profile on success checkout.'); ?>
                                    <span class="lr-tooltip" data-title="<?php _e('Turn on, if you want to Update customer profile on success checkout on cloud.','lr-plugin-slug');?>">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <p class="submit">
                        <?php submit_button('Save Settings', 'primary', 'submit', false); ?>
                    </p>
                </form>
                <?php do_action( 'lr_reset_admin_ui','Woocommerce' ); ?>               
            </div>
            <?php
        }

    }

}