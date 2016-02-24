<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Common' ) ) {

    /**
     * This class contains method which are used by admin as well as front
     */
    class LR_Common {

        /**
         * Check if ID can be link or not. if yes the link account.
         */
        public static function link_account_if_possible() {
            global $loginRadiusObject, $wpdb, $user_ID;

            $loginRadiusMappingData = array();
            if ( null == $loginRadiusObject ) {
                $loginRadiusObject = new LoginRadius();
            }
            if ( isset( $_REQUEST['token'] ) && is_user_logged_in() ) {
                try{
                  $loginRadiusUserprofile = $loginRadiusObject->loginradius_get_user_profiledata( $_REQUEST['token'] );
                } catch ( LoginRadiusException $e ) {
                    // Error Handling
                    if ( $loginRadiusSettings['enable_degugging'] == '0' ) {
                        // if debugging is off and Social profile not recieved, redirect to home page.
                        wp_redirect(site_url());
                        exit();
                    } else {
                        $loginRadiusUserprofile = null;
                        $message = isset( $e->getErrorResponse()->description ) ? $e->getErrorResponse()->description : $e->getMessage();
                        error_log( $message );
                        // If debug option is set and Social Profile not retrieved
                        Login_Helper::login_radius_notify( $message, 'isProfileNotRetrieved' );
                        return;
                    }
                }
                
                $loginRadiusMappingData['id'] = ( ! empty( $loginRadiusUserprofile->ID ) ? $loginRadiusUserprofile->ID : '' );
                $loginRadiusMappingData['provider'] = ( ! empty( $loginRadiusUserprofile->Provider ) ? $loginRadiusUserprofile->Provider : '' );
                $loginRadiusMappingData['thumbnail'] = ( ! empty( $loginRadiusUserprofile->ThumbnailImageUrl ) ? trim( $loginRadiusUserprofile->ThumbnailImageUrl ) : '' );
                if ( empty( $loginRadiusMappingData['thumbnail'] ) && $loginRadiusMappingData['provider'] == 'facebook' ) {
                    $loginRadiusMappingData['thumbnail'] = 'http://graph.facebook.com/' . $loginRadiusMappingData['id'] . '/picture?type=large';
                }
                $loginRadiusMappingData['pictureUrl'] = ( ! empty( $loginRadiusUserprofile->ImageUrl ) ? trim( $loginRadiusUserprofile->ImageUrl ) : '' );
                $wp_user_id = $wpdb->get_var($wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key="loginradius_provider_id" AND meta_value = %s', $loginRadiusMappingData['id'] ) );
                if ( ! empty( $wp_user_id ) ) {
                    // Check if verified field exist or not.
                    $loginRadiusVfyExist = $wpdb->get_var( $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE user_id = %d AND meta_key = "loginradius_isVerified"', $wp_user_id ) );
                    if ( ! empty( $loginRadiusVfyExist ) ) {
                        // if verified field exists
                        $loginRadiusVerify = $wpdb->get_var( $wpdb->prepare( 'SELECT meta_value FROM ' . $wpdb->usermeta . ' WHERE user_id = %d AND meta_key = "loginradius_isVerified"', $wp_user_id ) );
                        if ($loginRadiusVerify != '1') {
                            self::link_account( $user_ID, $loginRadiusMappingData['id'], $loginRadiusMappingData['provider'], $loginRadiusMappingData['thumbnail'], $loginRadiusMappingData['pictureUrl'] );
                            return true;
                        } else {
                            //account already mapped
                            return false;
                        }
                    } else {
                        return false;
                    }
                } else {
                    $loginRadiusMappingProvider = $loginRadiusMappingData['provider'];
                    $wp_user_lrid = $wpdb->get_var( $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key="' . $loginRadiusMappingProvider . 'Lrid" AND meta_value = %s', $loginRadiusMappingData['id'] ) );
                    if ( ! empty( $wp_user_lrid ) ) {
                        $lrVerified = get_user_meta( $wp_user_lrid, $loginRadiusMappingProvider . 'LrVerified', true );
                        if ( $lrVerified == '1' ) {
                            // Check if lrid is the same that verified email.
                            // account already mapped
                            return false;
                        } else {
                            // map account
                            self::link_account( $user_ID, $loginRadiusMappingData['id'], $loginRadiusMappingData['provider'], $loginRadiusMappingData['thumbnail'], $loginRadiusMappingData['pictureUrl'] );
                            return true;
                        }
                    } else {
                        // map account
                        self::link_account( $user_ID, $loginRadiusMappingData['id'], $loginRadiusMappingData['provider'], $loginRadiusMappingData['thumbnail'], $loginRadiusMappingData['pictureUrl'] );
                        return true;
                    }
                }
            }
        }

        /**
         * Get current protocol ( http OR https )
         */
        public static function get_protocol() {
            if ( isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) {
                return 'https://';
            } else {
                return 'http://';
            }
        }

        /**
         * Update usermeta to store linked account information
         */
        public static function link_account( $id, $lrid, $provider, $thumb, $pictureUrl ) {

            add_user_meta( $id, 'loginradius_provider_id', $lrid );
            add_user_meta( $id, 'loginradius_mapped_provider', $provider );
            add_user_meta( $id, 'loginradius_' . $provider . '_id', $lrid );
            if ( $thumb != '' ) {
                add_user_meta( $id, 'loginradius_' . $lrid . '_thumbnail', $thumb );
            }
            if ( $pictureUrl != '' ) {
                add_user_meta( $id, 'loginradius_' . $lrid . '_picture', $pictureUrl );
            }
        }

        /**
         * Check if scripts are to be loaded in footer according to plugin option
         */
        public static function scripts_in_footer_enabled() {
            global $loginradius_api_settings;

            if ( isset( $loginradius_api_settings['scripts_in_footer'] ) && $loginradius_api_settings['scripts_in_footer'] == '1' ) {
                return true;
            }
            return false;
        }

        /**
         * Perform linking operation and return parameters if account mapped or not accordingly
         */
        public static function perform_linking_operation() {

            // Public function call.
            if ( LR_Common::link_account_if_possible() === true ) {
                $linked = 1;
            } else {
                $linked = 0;
            }

            $redirectionUrl = LR_Common::get_protocol() . htmlspecialchars( $_SERVER['HTTP_HOST'] ) . remove_query_arg('lrlinked');
            if (strpos($redirectionUrl, '?') !== false) {
                $redirectionUrl .= '&lrlinked=' . $linked;
            } else {
                $redirectionUrl .= '?lrlinked=' . $linked;
            }
            wp_redirect($redirectionUrl);
            exit();
        }

        public static function enqueue_login_scripts() {
            global $lr_custom_interface_settings;
            wp_enqueue_script( 'lr-sdk' );
            if ( isset( $lr_custom_interface_settings['custom_interface'] ) && $lr_custom_interface_settings['custom_interface'] == '1' ) {
                wp_enqueue_script( 'lr-custom-interface' );
            } else {
                wp_enqueue_script( 'lr-social-login' );
            }
        }

        /**
         * Loading Login Script for loggedin user to provide account linking
         */
        public static function load_login_script( $isLinkingWidget = false ) {
            global $loginRadiusObject, $loginRadiusSettings, $loginradius_api_settings, $lr_custom_interface_settings, $lr_raas_settings;
            if( ! class_exists( 'LR_Raas_Install' ) ) {

              $loginradius_api_settings['LoginRadius_apikey'] = isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim( $loginradius_api_settings['LoginRadius_apikey'] ) : '';
              if ( ! class_exists( 'Login_Helper' ) ) {
                  require_once LOGINRADIUS_PLUGIN_DIR . 'public/inc/login/class-login-helper.php';
              }

              $register = Login_Helper::is_register_page();
              $location = urlencode( Login_Helper::get_redirect_url( '', $register ) );

              if ( isset( $lr_custom_interface_settings['custom_interface']) && $lr_custom_interface_settings['custom_interface'] == '1' ) {
                if ( isset( $lr_custom_interface_settings['selected_providers'] ) && $lr_custom_interface_settings['selected_providers'] != '' ) {
                  $selected_providers = implode( '\',\'', $lr_custom_interface_settings['selected_providers'] );
                }
                $interface_url = LR_CUSTOM_INTERFACE_URL.'assets/images/custom_interface/';
                if( is_multisite() ){
                    $interface_url .= get_current_blog_id().'/';
                }
                ?>
                  <script type="text/javascript">
                    function detectmob() {
                      if ( navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i) ) {
                        return true;
                      } else {
                        return false;
                      }
                    }
                  </script>
                  <script type="text/javascript">
                      jQuery(document).ready( function() {
                        $LRIC.util.ready( function() {
                          var options = {};
                          options.apikey = "<?php echo isset($loginradius_api_settings['LoginRadius_apikey']) ? trim($loginradius_api_settings['LoginRadius_apikey']) : '';?>";
                          options.appname = "<?php echo isset($loginradius_api_settings['sitename']) ? $loginradius_api_settings['sitename'] : '';?>";
                          
                          <?php if( isset( $lr_custom_interface_settings['selected_providers'] ) ) { ?>
                            options.providers = [<?php echo "'" . $selected_providers . "'";?>];
                          <?php } ?>
                          if ( detectmob() ) {
                              options.templatename = "loginradiuscustom_tmpl_mobile";
                          } else {
                              options.templatename = "loginradiuscustom_tmpl";
                          }
                          $LRIC.renderInterface( "interfacecontainerdiv", options );
                        });
                      });
                  </script>
                  <script type="text/html" id="loginradiuscustom_tmpl">
                    <a class="lr_custom_provider" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&callback=<?php echo $location?>&is_access_token=true');"><img src="<?php echo $interface_url;?><%=Name.toLowerCase() %>.png" /></a>
                  </script>
                  <script type="text/html" id="loginradiuscustom_tmpl_mobile">
                    <a class="lr_custom_provider" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&callback=<?php echo $location?>&callbacktype=hash&isParentWindowLogin=true&is_access_token=true');"><img src="<?php echo $interface_url;?><%=Name.toLowerCase() %>.png" /></a>
                  </script>
              <?php
              } else {
                ?>
                  <script type="text/javascript">
                    function detectmob() {
                      if ( navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || navigator.userAgent.match(/Windows Phone/i) ) {
                        return true;
                      } else {
                        return false;
                      }
                    }
                  </script>
                  <script type="text/javascript">
                      jQuery(document).ready(function() {
                        
                        var loginRadiusOptions = {};
                        loginRadiusOptions.login = true;

                        //Disabling console log messages - please delete when api has been updated
                        LoginRadius_SocialLogin.console.log = function(){};
                        
                        LoginRadius_SocialLogin.util.ready( function() {
                          $ui = LoginRadius_SocialLogin.lr_login_settings;
                          $ui.interfacesize = '';
                          $ui.apikey = "<?php echo $loginradius_api_settings['LoginRadius_apikey'];?>";
                          $ui.callback = "<?php echo $location;?>";
                          $ui.lrinterfacecontainer = "interfacecontainerdiv";
                          $ui.is_access_token = true;
                          if( detectmob() ) {
                            $ui.callbacktype = "hash";
                            $ui.isParentWindowLogin = true;
                          }
                          <?php
                          if ( isset( $loginRadiusSettings["LoginRadius_interfaceSize"] ) && $loginRadiusSettings["LoginRadius_interfaceSize"] == "small" ) {
                          echo '$ui.interfacesize ="small";';
                          }
                          if ( isset( $loginRadiusSettings['LoginRadius_numColumns'] ) && trim( $loginRadiusSettings['LoginRadius_numColumns'] ) != '') {
                          echo '$ui.noofcolumns = ' . trim( $loginRadiusSettings['LoginRadius_numColumns']) . ';';
                          }
                          if ( isset( $loginRadiusSettings['LoginRadius_backgroundColor'] ) ) {
                          echo '$ui.lrinterfacebackground = "' . trim( $loginRadiusSettings['LoginRadius_backgroundColor'] ) . '";';
                          }
                          ?>
                          LoginRadius_SocialLogin.init( loginRadiusOptions );
                        });
                      });
                  </script>
              <?php
              }?>
                <script type="text/javascript">
                    jQuery(document).ready( function(){
                      LoginRadiusSDK.setLoginCallback( function() {
                          var form = document.createElement('form');
                          form.action = "<?php echo urldecode($location);?>";
                          form.method = 'POST';
                          var hiddenToken = document.createElement('input');
                          hiddenToken.type = 'hidden';
                          hiddenToken.value = LoginRadiusSDK.getToken();
                          hiddenToken.name = "token";
                          form.appendChild( hiddenToken );
                          document.body.appendChild( form );
                          form.submit();
                      });
                    });
                </script>
              <?php
            }
        }

        /**
         * Check linking parameters and display message if account linked successfully or not
         */
        public static function check_linking_status_parameters() {
            $html = '';
            if (isset($_GET['lrlinked'])) {
                if ($_GET['lrlinked'] == 1) {
                    $html .= '<div id="loginRadiusSuccess" style="background-color: #FFFFE0; border:1px solid #E6DB55; padding:5px; margin:5px; color: #000">';
                    $html .= __('Account mapped successfully', 'lr-plugin-slug');
                } else {
                    $html .= '<div id="loginRadiusError" style="background-color: #FFEBE8; border:1px solid #CC0000; padding:5px; margin:5px; color: #000;">';
                    $html .= __('This account is already mapped', 'lr-plugin-slug');
                }
                $html .= '</div>';
                return $html;
            }
        }

        /**
         * Display connectd/linked providers on user wp profile page
         */
        public static function get_connected_providers_list() {
            global $user_ID;
            $html = '';
            $loginRadiusMappings = get_user_meta( $user_ID, 'loginradius_mapped_provider', false);
            $loginRadiusMappings = array_unique( $loginRadiusMappings);
            $connected = false;
            $loginRadiusLoggedIn = get_user_meta( $user_ID, 'loginradius_current_id', true );
            $totalAccounts = get_user_meta($user_ID, 'loginradius_provider_id' );
            $location = LR_Common::get_protocol() . $_SERVER['HTTP_HOST'] . remove_query_arg( array('lrlinked', 'loginradius_linking', 'loginradius_post', 'loginradius_invite', 'loginRadiusMappingProvider', 'loginRadiusMap', 'loginRadiusMain'));

            if ( count( $loginRadiusMappings ) > 0 ) {
                foreach ( $loginRadiusMappings as $map ) {
                    $loginRadiusMappingId = get_user_meta( $user_ID, 'loginradius_' . $map . '_id' );

                    if ( count( $loginRadiusMappingId ) > 0 ) {
                        foreach ( $loginRadiusMappingId as $tempId ) {
                            $html .= '<tr>';

                            if ( $loginRadiusLoggedIn == $tempId ) {
                                $append = '<span style=\'color:green\'>Currently </span>';
                                $connected = true;
                            } else {
                                $append = '';
                            }

                            $html .= '<td>' . $append;
                            $html .= __( 'Connected with', 'lr-plugin-slug' );
                            $html .= '<strong> ' . ucfirst($map) . '</strong><img src=\'' . LOGINRADIUS_PLUGIN_URL . 'assets/images/linking/' . $map . '.png' . '\' align=\'absmiddle\' style=\'margin-left:5px\' /></td><td>';
                            if ( count( $totalAccounts) > 1 ) {
                                $html .= '<a href=' . $location . ( strpos($location, '?') !== false ? '&' : '?' ) . 'loginRadiusMap=' . $tempId . '&loginRadiusMappingProvider=' . $map . ' ><input type=\'button\' class=\'button-primary\' value="' . __('Remove', 'lr-plugin-slug') . '" /></a>';
                            }
                            $html .= '</td></tr>';
                        }
                    }
                }
            }
            $map = get_user_meta( $user_ID, 'loginradius_provider', true );
            if ($map != false) {
                $html .= '<tr>';
                $tempId = $loginRadiusLoggedIn;
                $append = ! $connected ? '<span style=\'color:green\'>Currently </span>' : '';
                $html .= '<td>' . $append;
                $html .= __( 'Connected with', 'lr-plugin-slug' );
                $html .= '<strong> ' . ucfirst( $map ) . '</strong> <img src=\'' . LOGINRADIUS_PLUGIN_URL . 'assets/images/linking/' . $map . '.png' . '\' align=\'absmiddle\' style=\'margin-left:5px\' /></td><td>';
                if ( count( $totalAccounts ) != 1) {
                    $html .= '<a href=' . $location . ( strpos( $location, '?' ) !== false ? '&' : '?') . 'loginRadiusMain=1&loginRadiusMap=' . $tempId . '&loginRadiusMappingProvider=' . $map . ' ><input type="button" class="button-primary" value="' . __('Remove', 'lr-plugin-slug') . '" /></a>';
                }
                $html .= '</td></tr>';
            }
            return $html;
        }

        /**
         * Display provider , user is currently connected with
         */
        public static function display_currently_connected_provider() {
            global $user_ID;
            $loginRadiusLoggedIn = get_user_meta( $user_ID, 'loginradius_current_id', true );
            $totalAccounts = get_user_meta($user_ID, 'loginradius_provider_id');
            $location = LR_Common::get_protocol() . $_SERVER['HTTP_HOST'] . remove_query_arg( array('lrlinked', 'loginradius_linking', 'loginradius_post', 'loginradius_invite', 'loginRadiusMappingProvider', 'loginRadiusMap', 'loginRadiusMain'));
            $html = '';
            $map = get_user_meta($user_ID, 'loginradius_provider', true);
            if ($map != false) {
                $html .= '<tr>';
                $tempId = $loginRadiusLoggedIn;
                $append = '<span style=\'color:green\'>Currently </span>';
                $html .= '<td>' . $append;
                $html .= __('Connected with', 'lr-plugin-slug' );
                $html .= '<strong> ' . ucfirst($map) . '</strong> <img src=\'' . LOGINRADIUS_PLUGIN_URL . 'assets/images/linking/' . $map . '.png' . '\' align=\'absmiddle\' style=\'margin-left:5px\' /></td><td>';
                if (count($totalAccounts) != 1) {
                    $html .= '<a href=' . $location . (strpos($location, '?') !== false ? '&' : '?') . 'loginRadiusMain=1&loginRadiusMap=' . $tempId . '&loginRadiusMappingProvider=' . $map . ' ><input type="button" class="button-primary" value="' . __('Remove', 'lr-plugin-slug') . '" /></a>';
                }
                $html .= '</td></tr>';
            }
            return $html;
        }

        /**
         * Function which sends email on user activation to admin and users
         */
        public static function login_radius_send_verification_email( $loginRadiusEmail, $loginRadiusKey, $loginRadiusProvider = '', $emailType = '', $username = '') {

            $loginRadiusSubject = '';
            $loginRadiusMessage = '';
            switch ( $emailType ) {
                case "activation":
                    $loginRadiusSubject = '[' . htmlspecialchars( trim( get_option('blogname') ) ) . '] AccountActivation';
                    $loginRadiusMessage = 'Hi ' . $username . ", \r\n" .
                            'Your account has been activated at ' . site_url() . '. Now you can login to your account.';
                    break;
                case "admin notification":
                    $user = get_userdata($username);
                    $loginRadiusSubject = '[' . htmlspecialchars( trim( get_option('blogname') ) ) . '] New User Registration';
                    $loginRadiusMessage = 'New user registration on your site ' . htmlspecialchars( trim( get_option( 'blogname') ) ) . ": \r\n" .
                            'Username: ' . $user->user_login . " \r\n" .
                            'E-mail: ' . $user->user_email . '';
                    break;
                default:
                    $loginRadiusSubject = '[' . htmlspecialchars( trim( get_option( 'blogname' ) ) ) . '] Email Verification';
                    $loginRadiusUrl = site_url() . '?loginRadiusVk=' . $loginRadiusKey;
                    if ( ! empty( $loginRadiusProvider ) ) {
                        $loginRadiusUrl .= '&loginRadiusProvider=' . $loginRadiusProvider;
                    }
                    $loginRadiusMessage = "Please click on the following link or paste it in browser to verify your email \r\n" . $loginRadiusUrl;
                    break;
            }
            $headers = "MIME-Version: 1.0\n" .
                    "Content-Type: text/plain; charset='" .
                    get_option( 'blog_charset' ) . "\"\n" .
                    'From: <no-reply@loginradius.com>';
            wp_mail( $loginRadiusEmail, $loginRadiusSubject, $loginRadiusMessage, $headers );
        }

    }
}