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

            global $ciam_credentials;
            if(isset($ciam_credentials['apikey']) && !empty($ciam_credentials['apikey']) && isset($ciam_credentials['secret']) && !empty($ciam_credentials['secret']))
            {
            $configAPI = new \LoginRadiusSDK\CustomerRegistration\Advanced\ConfigurationAPI();
            try {
                $config = $configAPI->getConfigurations();
                    if(isset($config) && isset($config->IsPhoneLogin) && $config->IsPhoneLogin) {
                    echo '<div class="notice notice-warning is-dismissible">
                        <p>If only the Phone Id Login options is enabled for the App, a random Email Id will be generated if a user registered using the PhoneID. Format of random email id is: "randomid+timestamp@yourdomain.com"</p>
                    </div>';
                    }
                } catch (\LoginRadiusSDK\LoginRadiusException $e) {
                  error_log($e->getErrorResponse()->Description);
                }
            }
            ?>

            <div class="wrap active-wrap cf">

                <header>
                    <h1 class="logo"><a href="//www.loginradius.com" target="_blank"><?php _e('CIAM Configuration', 'CIAM'); ?></a></h1>
                </header>

                <div class="cf">               

                    <form action="options.php" method="post">

                        <?php

                        settings_fields('ciam_api_settings');

                        settings_errors();
                        if(isset($ciam_credentials['secret']) && $ciam_credentials['secret'] !== ''){
                            $decrypted_key = $this->encrypt_and_decrypt( $ciam_credentials['secret'], $ciam_credentials['apikey'], $ciam_credentials['apikey'], 'd' );  
                        }

                        ?>
                        <div id="error_msg" class="error updated" style="display:none">
                        
                        </div>
                        <div class="ciam_options_container">  
                           
                            <div class="active-row">
                                <h3><?php _e('LoginRadius API Configurations', 'CIAM'); ?></h3>
                                <p colspan="2">To access the LoginRadius web service please enter the credentials below. <a target="_blank" href="https://www.loginradius.com/docs/api/v2/admin-console/platform-security/api-key-and-secret/"><?php _e('(How to get it?)', 'lr-plugin-slug'); ?></a></p>
                                <table class="wp-list-table widefat">
                                    <tr>
                                        <td style="width: 15%;"><span class="ciam_property_title custom_ciam_property_title requires"><?php _e('LoginRadius API Key', 'CIAM'); ?></span></td>
                                        <td style="padding-right: 50%;"><input type="text" id="apikey" class="active-row-field custom_active-row-field" name="ciam_api_settings[apikey]" value="<?php echo ( isset($ciam_credentials['apikey']) && !empty($ciam_credentials['apikey']) ) ? $ciam_credentials['apikey'] : ''; ?>" autofill='off' autocomplete='off' /></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 15%;"><span class="ciam_property_title custom_ciam_property_title requires"><?php _e('LoginRadius API Secret', 'CIAM'); ?></span></td>
                                        <td style="padding-right: 50%;">
                                                <div class="ciam_ciamsecrettoggle">
                                                    <input type="password" id="secret" class="active-row-field custom_active-row-field" style="float:left;" name="ciam_api_settings[secret]" value="<?php echo ( isset($ciam_credentials['secret']) && !empty($ciam_credentials['secret']) ) ? $decrypted_key : ''; ?>" autofill='off' autocomplete='off' />
                                                    <div onclick="ciamsecrettoggle();" class="ciam_show_button"><?php _e('Show', 'CIAM'); ?></div>
                                                </div>
                                        </td>                  
                                    </tr>   
                                </table>
                                    <input type="hidden" id="update_plugin" class="active-row-field" name="ciam_api_settings[update_plugin]" value="true" />
                                    <input type="hidden" id="ciam-appname" class="active-row-field" name="ciam_api_settings[sitename]" value="<?php echo ( isset($ciam_credentials['sitename']) && !empty($ciam_credentials['sitename']) ) ? $ciam_credentials['sitename'] : ''; ?>" />
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
        public function encrypt_and_decrypt( $string, $secretKey, $secretIv, $action) {
            // you may change these values to your own
            $secret_key = $secretKey;
            $secret_iv = $secretIv;
            $output = false;
            $encrypt_method = "AES-256-CBC";
            $key = hash( 'sha256', $secret_key );
            $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
            if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
            }
            else if( $action == 'd' ){           
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
            }
            return $output;
            }

    }

}