<?php

// Exit if called directly
if (!defined('ABSPATH')) {
    exit();
}

/**
 * The main class and initialization point of the User Registration Admin section.
 */
if (!class_exists('LR_Raas_Admin')) {

    class LR_Raas_Admin {
        /*
         * Constructor
         */

        public function __construct() {

            add_action('admin_init', array($this, 'admin_init'));
            add_action('delete_user', array($this, 'delete_user'));
            if (is_multisite()) {
                add_action('wpmu_delete_user', array($this, 'delete_user'));
            }

            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

            /* Remove short code */
            remove_shortcode('LoginRadius_Linking');
            add_shortcode('LoginRadius_Linking', array($this, 'get_raas_account_linking'));
        }

        /**
         * Register LR_Raas_Settings and its sanitization callback. Replicate loginradius settings on multisites.
         */
        public function admin_init() {
            
            register_setting('lr_raas_settings', 'LR_Raas_Settings', array($this, 'validate_options'));
            add_action('lr_raas_social_linking', array($this, 'get_raas_account_linking'));

            // Replicate Raas configuration to the subblogs in the multisite network
            if (is_multisite() && is_main_site()) {
                add_action('wpmu_new_blog', array($this, 'replicate_settings_to_new_blog'));
                add_action('update_option_LR_Raas_Settings', array($this, 'update_old_blogs'));
            }
        }

        /**
         * delete user at raas
         * 
         * @param type $user_id
         */
        public function delete_user($user_id) {
            
            global $accountAPIObject;
            $raas_uid = get_user_meta($user_id, 'lr_raas_uid', true);
            if (!empty($raas_uid)) {
                try {
                    $accountAPIObject->deleteAccount($raas_uid);
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                    error_log($e->getErrorResponse()->description);
                }
            }
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
            wp_enqueue_style('lr-raas-admin-style', LR_RAAS_URL . 'assets/css/lr-raas-style-admin.css');
            wp_enqueue_script('lr-raas-admin-js', LR_RAAS_URL . 'assets/js/lr-raas-admin.js', array('jquery'), LR_PLUGIN_VERSION, $lr_js_in_footer);
        }

        /**
         * RaaS linking functionality
         * 
         * @return type
         */
        public static function get_raas_account_linking() {
            global $lr_raas_settings;

            // Return if disable email verification is true
            if (!empty($lr_raas_settings['email_verify_option']) && 'disabled' == $lr_raas_settings['email_verify_option']) {
                return;
            }

            if (is_user_logged_in()) {                
                
                $user_id = get_current_user_id();
                $uid = get_user_meta($user_id, 'lr_raas_uid', true);
                
                if (empty($uid)) {
                    printf('<div class="error notice"><p>' . __('Please verify your account to get account linking service.', 'lr-plugin-slug') . '</p></div>');
                    return;
                }
                global $socialLoginObject, $accountAPIObject, $wpdb;
                $emailVerified = $accountAPIObject->getAccounts($uid);
                
                if(empty($emailVerified[0]->EmailVerified)){
                    printf('<div class="error notice"><p>' . __('Please verify your account to get account linking service.', 'lr-plugin-slug') . '</p></div>');
                    return;
                }
                
                ?>
                <div class="metabox-holder columns-2" id="post-body">
                    <div class="stuffbox wrap">
                        <h2 style="padding-left:10px;">
                            <label><?php _e('Link your account', 'lr-plugin-slug'); ?></label>
                        </h2>
                        <hr>
                        <div class="inside" style='padding:0'>
                            <table  class="form-table editcomment">
                                <tr>
                                    <td colspan="2">
                                        <strong><?php _e('By adding another account, you can log in with the new account as well!', 'lr-plugin-slug') ?></strong>
                                        <br>
                                        <br>
                                        <?php
                                        $provider = isset($_POST['provider']) ? $_POST['provider'] : '';
                                        $accountid = isset($_POST['accountid']) ? $_POST['accountid'] : '';
                                        $message = __('An error has occurred', 'lr-plugin-slug');
                                        $type = 'error';
                                        if (!empty($accountid) && !empty($provider)) {
                                            try {
                                                $accountAPIObject->accountUnlink($uid, $accountid, $provider);
                                                delete_user_meta($user_id, 'loginradius_provider_id', $accountid);
                                                delete_user_meta($user_id, 'loginradius_thumbnail');
                                                delete_user_meta($user_id, 'loginradius_provider');
                                                delete_user_meta($user_id, 'loginradius_' . $accountid . '_thumbnail');
                                                delete_user_meta($user_id, 'loginradius_' . $provider . '_id', $accountid);
                                                $wpdb->query($wpdb->prepare('delete FROM ' . $wpdb->usermeta . ' WHERE user_id = %d AND meta_key = \'loginradius_mapped_provider\' AND meta_value = %s limit 1', $user_id, $provider));
                                                $type = 'updated settings-error';
                                                $message = __('Your account remove successfully', 'lr-plugin-slug');
                                            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                                                $message = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : $message;
                                            }
                                            printf('<div class="' . $type . ' lr_' . $type . '"><p>' . $message . '</p></div>');
                                        } elseif (isset($_POST['token']) && !empty($_POST['token']) && is_user_logged_in()) {
                                            try{
                                                $userProfileObject = $socialLoginObject->getUserProfiledata($_POST['token']);
                                            } catch( \LoginRadiusSDK\LoginRadiusException $e ) {
                                                
                                                $userProfileObject = null;
                                                $message = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : $e->getMessage();
                        error_log($message);
                        // If debug option is set and Social Profile not retrieved
                        Login_Helper::login_radius_notify($message, 'isProfileNotRetrieved');
                        return;
                                            }
                                            
                                            if (isset($userProfileObject->Provider) && isset($userProfileObject->ID)) {
                                                $linkuser = get_users('meta_value=' . $userProfileObject->ID);
//                                                
//                                                
                                                    try {
                                                       $accountAPIObject->accountLink($uid, $userProfileObject->ID, $userProfileObject->Provider);
                                                        
                                                        LR_Common::link_account($user_id, $userProfileObject->ID, $userProfileObject->Provider, $userProfileObject->ThumbnailImageUrl, $userProfileObject->ImageUrl);
                                                        $type = 'updated settings-error';
                                                        $message = __('Your account is linked successfully', 'lr-plugin-slug');
                                                    } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                                                        $message = isset($e->getErrorResponse()->description) ? $e->getErrorResponse()->description : __('An error has occurred', 'lr-plugin-slug');
                                                    }
                                                
                                            }
                                            printf('<div class="' . $type . ' lr_' . $type . '"><p>' . $message . '</p></div>');
                                        }
                                        $raas_linked_account = false;
                                        if (!empty($uid)) {
                                            try {
                                                $raas_linked_account = $accountAPIObject->getAccounts($uid);
                                            } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                                                $raas_linked_account = false;
                                            }
                                        }
                                        do_action('lr_raas_linking_interface');
                                        ?>
                                        <ul class="lr_linked_accounts">
                                            <?php
                                            if ($raas_linked_account != false) {

                                                for ($i = 0; $i < count($raas_linked_account); $i++) {
                                                    if (!isset($raas_linked_account[$i]->Provider) || $raas_linked_account[$i]->Provider == 'RAAS') {
                                                        continue;
                                                    }
                                                    printf('<li><form action="" method="post">');
                                                    if (get_user_meta($user_id, 'loginradius_current_id', true) == $raas_linked_account[$i]->ID) {
                                                        printf('<span style="color:green;">' . __('Currently connected ', 'lr-plugin-slug'));
                                                    } else {
                                                        printf('<span>' . __('Connected ', 'lr-plugin-slug'));
                                                    }
                                                    printf(__('with ', 'lr-plugin-slug') . '</span>');
                                                    printf('<span style="margin-right:5px;">');
                                                    printf('<img src="' . LR_RAAS_URL . 'assets/images/mapping/' . $raas_linked_account[$i]->Provider . '.png">');
                                                    printf('</span>');
                                                    printf('<button type="submit" class="buttondelete"><span>' . __('Remove', 'lr-plugin-slug') . '</span></button>');
                                                    printf('<input type="hidden" name="provider" value="' . $raas_linked_account[$i]->Provider . '">');
                                                    printf('<input type="hidden" name="accountid" value="' . $raas_linked_account[$i]->ID . '">');
                                                    printf('</form></li>');
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        /**
         * Replicate the raas config to the new blog created in the multisite network.
         * 
         * @global type $lr_raas_settings
         * @param type $blogId
         */
        public function replicate_settings_to_new_blog($blogId) {
            global $loginRadiusSettings;
            add_blog_option($blogId, 'LoginRadius_settings', $loginRadiusSettings);
        }

        /**
         * Update the raas options in all the old blogs.
         * 
         * @global type $loginradius_api_settings
         * @param type $oldConfig
         */
        public function update_old_blogs($oldConfig) {
            global $loginradius_api_settings;
            if (isset($loginradius_api_settings['multisite_config']) && $loginradius_api_settings['multisite_config'] == '1') {
                $loginRadiusSettings = get_option('LoginRadius_settings');
                $blogs = wp_get_sites();
                foreach ($blogs as $blog) {
                    update_blog_option($blog['blog_id'], 'LoginRadius_settings', $loginRadiusSettings);
                }
            }
        }

        /**
         * Validate raas module options,
         * Function to be called when settings save button is clicked on plugin settings page
         * 
         * @global type $loginRadiusSettings
         * @param type $settings
         * @return type
         */
        public static function validate_options($settings) {
            global $loginRadiusSettings;

            // Save additional settings for other modules 
            $settings = apply_filters('lr_raas_save_setting', $settings);

            $loginRadiusSetting = get_option('LoginRadius_settings');
            $advance_settings = array('LoginRadius_redirect', 'custom_redirect', 'username_separator', 'LoginRadius_noProvider', 'profileDataUpdate', 'LoginRadius_socialavatar', 'LoginRadius_socialLinking', 'enable_degugging');

            foreach ($advance_settings as $advance_setting) {
                if (isset($settings[$advance_setting])) {
                    $loginRadiusSetting[$advance_setting] = trim($settings[$advance_setting]);
                    unset($settings[$advance_setting]);
                } else {
                    $loginRadiusSetting[$advance_setting] = "";
                }
            }

            update_option('LoginRadius_settings', $loginRadiusSetting);
            $loginRadiusSettings = get_option('LoginRadius_settings');

            if (isset($settings['raas_autopage']) && $settings['raas_autopage'] == '1') {
                // Enable Raas.
                // Create new pages and get array of page ids.
                $options = LR_Raas_Install::activation($settings);
                // Merge new page ids with settings array.
                $settings = array_merge($settings, $options);
            }

            if (!empty($settings['v2captcha_site_key'])) {
                $settings['v2captcha_site_key'] = trim($settings['v2captcha_site_key']);
            }

            return $settings;
        }

        /*
         * Callback for add_submenu_page,
         * This is the first function which is called while plugin admin page is requested
         */

        public static function options_page() {
            include_once LR_RAAS_DIR . "admin/views/settings.php";
            LR_Raas_Admin_Settings::render_options_page();
        }

    }

    new LR_Raas_Admin();
}
