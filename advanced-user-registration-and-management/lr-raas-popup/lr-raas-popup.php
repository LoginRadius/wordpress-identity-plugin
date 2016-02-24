<?php

// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( 'LR_Raas_Popup' ) ) {

    /**
     * The main class and initialization point of the plugin.
     */
    class LR_Raas_Popup {

        /**
         * Constructor
         */
        public function __construct() {
        	global $lr_raas_settings;

            // Return if Raas Module does not exist
        	if( ! class_exists( 'LR_Raas_Install' ) ) {
        		return;
        	}
        	$this->define_constants();
            
            // Add Raas Popup Tab to Raas Settings Page
            add_filter( 'add_raas_tab', array( $this, 'add_raas_tab') );
            add_filter( 'add_raas_tab_body', array( $this, 'add_raas_tab_body' ) );
            
            // Exit if Raas popup is disabled
            if( empty( $lr_raas_settings['popup_forms_enable'] ) ) {
                return;
            }

            $this->load_dependencies();

        	add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        	add_action('wp_footer', array( $this, 'popup_forms' ) );

        }

        public function add_raas_tab( ) {
            ?>
                <li class="nav-tab" data-tab="raas-popup-forms"><?php _e( 'Popup Forms', 'lr-plugin-slug' ) ?></li>
            <?php
        }


        public function add_raas_tab_body(  ) {
            global $lr_raas_settings;
            ?>
                <div id="raas-popup-forms" class="lr-tab-frame">
                    <div class="lr_options_container">
                        <div class="lr-row">
                            <label for="lr-enable-raas-popup-forms" class="lr-toggle">
                                <input type="checkbox" class="lr-toggle" id="lr-enable-raas-popup-forms" name="LR_Raas_Settings[popup_forms_enable]" value="1" <?php echo ( isset( $lr_raas_settings['popup_forms_enable'] ) && $lr_raas_settings['popup_forms_enable'] == '1' ) ? 'checked' : ''; ?> />
                                <span class="lr-toggle-name"><?php _e( 'Enable User Registration Popup Forms', 'lr-plugin-slug' ); ?></span>
                            </label>
                        </div>
                    </div>
                </div>  
            <?php
        }
        /**
         * Define constants needed across the plug-in.
         */
        private function define_constants() {
            define( 'LR_RAAS_POPUP_DIR', plugin_dir_path( __FILE__ ) );
            define( 'LR_RAAS_POPUP_URL', plugin_dir_url( __FILE__ ) );
        }

        private function load_dependencies() {
            
            // Load required files.
            require_once( LR_RAAS_POPUP_DIR . 'widgets/lr-raas-popup-menu-widget.php' );
        }

        static function enqueue_scripts(  ) {
        	wp_enqueue_script( 'jquery' );
        	wp_enqueue_style( 'lr-raas-popup-style' );
        }

        static function js_response() {
        	?>
        		<meta name="fragment" content="!">
        		<script>
                    function hideAllPopupForms() {
                        jQuery('#popup-login-container,#popup-resetpassword-container').hide();
                    }

        			function showPopupForm( hash ){
                        jQuery('#lr-pop-group').addClass('lr-show-layover');
                        jQuery('#lr_popup_' + hash + '_form').addClass('lr-show');
                    }

                    (function($) {
        				
						$('#lr-overlay,.lr-popup-close-span').click(function(){
							window.location.hash = '';
                            $('.lr-show-layover').removeClass('lr-show-layover');
							$('.lr-show').removeClass('lr-show');
                            //jQuery('.lr-popup-container.lr-show .lr-column.full-size').removeClass('full-size');
						});
                        var forms = ['changepassword', 'login', 'register', 'forgotpassword'];
						
                        $("a[href*=#]").click(function(e) {

							var hash = this.href.split('#')[1].replace('!', '');
							if( 'changepassword' != hash && 'login' != hash && 'register' != hash && 'forgotpassword' != hash ) {
                                return;
                            }

							$('.lr-show-layover').removeClass('lr-show-layover');
							$('.lr-show').removeClass('lr-show');

							<?php if( ! is_user_logged_in() ) { ?>
								if( 'changepassword' === hash ) {
									return;
								}
							<?php } else { ?>
								if( 'login' === hash || 'register' === hash || 'forgotpassword' === hash ) {
									return;
								}
							<?php } ?>

							$('#lr-pop-group').addClass('lr-show-layover');
							$('#lr_popup_' + hash + '_form').addClass('lr-show');					
						});

                        var hash = window.location.hash.replace('#', '').replace('!', '');
                        if( '-1' != $.inArray( hash, forms ) ) {

                            <?php if( is_user_logged_in() ) { ?>
                                if( 'login' === hash || 'register' === hash || 'forgotpassword' === hash ) {
                                    return;
                                }
                            <?php } ?>
                            showPopupForm( hash );
                        }
        			})(jQuery);
        		</script>

        	<?php
        }

        /**
         * raas_forms
         * Renders required javaScript for User Registration popup forms
         * @param  string $page popup form name
         * @param  string $id   ID used to idetify unique Social Login interfaces
         * @return javascript   renders javaScript on page
         */
        public static function raas_forms( $page, $id = 'lr-popup-sociallogin-container' ) {
            global $lr_raas_settings;
            ?>
                <script>
                    jQuery(document).ready(function () {            
                        <?php if ( $page == 'lr-popup-accountlinking' || $page == 'lr-popup-password' ) { ?>
                            LoginRadiusRaaS.init(raasoption, "accountlinking", function ( response ) {
                                handleResponse(true, "");
                                if ( response.isPosted == true ) {
                                    handleResponse( true, 'Your account linked successfully.' );
                                } else {
                                    redirect(response);
                                }
                            }, function (response) {
                                jQuery('.lr_fade').hide();
                                if (response[0].description != null) {
                                    handleResponse(false, response[0].description);
                                }
                            }, 'interfacecontainerdiv', 'popup');
                        <?php } ?>
                 		<?php if ( $page == 'lr-popup-password' ) { ?>
                            LoginRadiusRaaS.passwordHandleForms("popup-setpasswordbox", "popup-changepasswordbox", function ( israas ) {
                                if ( israas ) {
                                    jQuery("#popup-changepasswordbox").show();
                                } else {
                                    jQuery("#popup-setpasswordbox").show();
                                }
                            }, function () {
                                document.forms["loginradius-raas-setpassword"].action = "";
                                document.forms["loginradius-raas-setpassword"].submit();
                            }, function () {
                            }, function () {
                                document.forms["loginradius-raas-changepassword"].action = "";
                                document.forms["loginradius-raas-changepassword"].submit();
                            }, function () {

                        	});
    		            <?php } elseif ( $page == 'lr-popup-registration') { ?>
                                    //Initialize registration popup form
    		                        LoginRadiusRaaS.init(raasoption, 'registration', function ( response ) {
    		                            <?php
                                            // Return if disable email verification is true
                                            if( ! empty( $lr_raas_settings['email_verify_option'] ) && 'disabled' == $lr_raas_settings['email_verify_option'] ) {
                                                ?>
                                                    handleResponse( true, 'Registration complete, please login.', '#lr-popup-body-container' );
                                                <?php   
                                            }else {
                                                ?>
                                                    handleResponse( true, 'An email has been sent to ' + jQuery( "#popupregistration-emailid" ).val() + '.Please verify your email address.', '#lr-popup-body-container' );
                                                <?php
                                            }
                                        ?>
    		                        }, function (errors) {
    		                            if (errors[0].description != null) {
    		                                 handleResponse(false, errors[0].description,'#lr-popup-body-container');
    		                            }
    		                        }, "popup-registration-container", "popup");
    		            <?php } elseif ( $page == 'lr-popup-login') { ?>
    		                        //Initialize Login popup form
    		                        LoginRadiusRaaS.init(raasoption, 'login', function ( response ) {
                                        handleResponse(true, "", '#lr-popup-body-container');
    		                            redirect(response.access_token);
    		                        }, function (errors) {
    		                            jQuery('.lr_fade').hide();
    		                            if (errors[0].description != null) {
    		                                handleResponse(false, errors[0].description, '#lr-popup-body-container');
    		                            }
    		                        }, "popup-login-container", 'popup');
    		            <?php } elseif ( $page == 'lr-popup-forgotpassword' ) { ?>
    	                        //Initialize forgot password popup form
    	                        LoginRadiusRaaS.init(raasoption, 'forgotpassword', function ( response ) {
    	                            handleResponse(true, 'An email has been sent to ' + jQuery("#popupforgotpassword-emailid").val() + ' with reset Password link.', '#lr-popup-body-container' );
    	                            jQuery('#social-registration-container').html('');
    	                        }, function (errors) {
    	                            jQuery('.lr_fade').hide();
    	                            if (errors[0].description != null) {
    	                                handleResponse(false, errors[0].description, '#lr-popup-body-container');
    	                            }
    	                        }, "popup-forgotpassword-container", 'popup');
                        <?php } elseif ( $page == 'sociallogin') { ?>
                            //Initialize Social Login popup interfaces
                            LoginRadiusRaaS.init( raasoption, 'sociallogin', function ( response ) {
                                if ( response.isPosted ) {
                                    handleResponse(true, 'An email has been sent to ' + jQuery("#loginradius-raas-social-registration-emailid").val() + '.Please verify your email address.', '#lr-popup-body-container');
                                    ShowformbyId("popup-login-container");
                                    jQuery('#lr-popup-sociallogin-container').html('');
                                } else {
                                    handleResponse(true, "", '#lr-popup-body-container');
                                    redirect(response);
                                }
                            }, function (errors) {
                                jQuery('.lr_fade').hide();
                                if (errors[0].description != null) {
                                    handleResponse(false, errors[0].description, '#lr-popup-body-container');
                                }
                            }, '<?php echo $id; ?>', 'popup' );
                        <?php } ?>
                    });
                </script>
            <?php
        }

        /**
         * popup_header
         * Returns the popup header html with corresponding
         * name value for each popup form
         * @param  string $name Popup form name to be displayed in header
         * @return string HTML of header to be added into each popup form
         */
        private static function popup_header( $name = '' ) {
        	return '<div class="lr-popup-header"><span class="lr-popup-close-span"><a class="lr-popup-close-btn">×</a></span><div class="lr-header-logo"><img src="" alt="Logo" class="lr-header-logo-img"><p class="lr-header-caption">' . $name . '</p></div></div>';
        }

        /**
         * get_interface.
         * Gets Social Login interface
         * @global type $loginRadiusObject
         * @global array $loginradius_api_settings
         * @global type $LR_Raas_Social_Login
         * @param type $id ID name of each interface, should be unique
         * @return string HTML of interface
         */
        public static function get_interface( $id = 'lr-popup-sociallogin-container' ) {
            global $loginRadiusObject, $loginradius_api_settings, $LR_Raas_Social_Login;

            $loginRadiusApiKey = isset( $loginradius_api_settings['LoginRadius_apikey']) ? trim( $loginradius_api_settings['LoginRadius_apikey']) : '';
            $loginRadiusSecret = isset( $loginradius_api_settings['LoginRadius_secret']) ? trim( $loginradius_api_settings['LoginRadius_secret']) : '';

            if ( empty( $loginRadiusApiKey ) ) {
                return "<div style='background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;'><div style ='color:red; margin-bottom:5px'>LoginRadius Social Login Plugin is not configured!</div><p style='line-height:1.3; margin-bottom:4px'>To activate your plugin, navigate to <strong>LoginRadius > API Settings</strong> section in your WordPress admin panel and insert LoginRadius API Key & Secret. Follow <a href='http://support.loginradius.com/customer/portal/articles/677100-how-to-get-loginradius-api-key-and-secret' target='_blank'>this</a> document to learn how to get API Key & Secret.</p></div>";
            } elseif ( ! $loginRadiusObject->loginradius_is_valid_guid( $loginRadiusApiKey ) || ! $loginRadiusObject->loginradius_is_valid_guid( $loginRadiusSecret ) ) {
                return "<div style='background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;'><p style ='color:red;'>Your LoginRadius API key or secret is not valid, please correct it or contact LoginRadius support at <b><a href ='http://www.loginradius.com' target = '_blank'>www.LoginRadius.com</a></b></p></div>";
            } else {
                return self::raas_forms( 'sociallogin', $id ) . '<div class="interfacecontainerdiv lr-sl-shaded-brick-frame lr-column" id="' . $id . '"></div>';
            }
        }

        static function popup_forms() {
        	global $LR_Raas_Social_Login;
            
			$message = '<div class="messageinfo"></div>';
			ob_start();
			$html = self::js_response();
			$html .= '<div id="lr-pop-group">';
			$html .= '<div id="lr-overlay"></div>';
			$html .= $LR_Raas_Social_Login->login_script();
            
            if ( ! is_user_logged_in() ) {
                
                $html .= '<div id="lr_popup_login_form" class="lr-popup-container">';
                $html .= self::popup_header('Login');
                $html .= '<div id="lr-popup-body-container">';
                $html .= $message;
                $html .= '<div id="custom-object-container" class="lr-input-style lr-input-frame">';
                $html .= self::get_interface( 'lr-popup-sociallogin-login' );
                $html .= self::raas_forms('lr-popup-login');
                $html .= '<div class="lr-column hr-or-rule vr">';
                $html .= '<div id="popup-resetpassword-container" class="lr-input-style"></div>
                <div id="popup-login-container" class="lr-input-style"></div>
                <div class="various-grid accout-login" id="reset_from" ></div>
                <span class="lr-link"><a href="#!register">Register</a></span>
                <span class="lr-link"><a href="#!forgotpassword">Lost Password</a></span>
                </div></div></div></div>';
                
                $html .= '<div id="lr_popup_register_form" class="lr-popup-container">';
                $html .= self::popup_header('Register');
                $html .= '<div id="lr-popup-body-container">';
                $html .= $message;
                $html .= '<div id="custom-object-container" class="lr-input-style lr-input-frame">';
                $html .= self::get_interface( 'lr-popup-sociallogin-register' );
                $html .= self::raas_forms('lr-popup-registration');
                $html .= '<div class="lr-column hr-or-rule vr">';
                $html .= '<div id="popup-registration-container" class="lr-input-style"></div>
                <span class="lr-link"><a href ="#!login">Login</a></span>
                <span class="lr-link"><a href="#!forgotpassword">Forgot Password</a></span>
                </div></div></div></div>';

                $html .= '<div id="lr_popup_forgotpassword_form" class="lr-popup-container">';
                $html .= self::popup_header('Lost Password');
                $html .= '<div id="lr-popup-body-container">';
                $html .= $message;
                $html .= '<div id="custom-object-container" class="lr-input-style lr-input-frame">';
                $html .= self::raas_forms('lr-popup-forgotpassword');
                $html .= '<div class="lr-column">';
                $html .= '<div id="popup-forgotpassword-container" class="lr-input-style"></div>
                <span class="lr-link"><a href = "#!login">Login</a></span>
                <span class="lr-link"><a href="#!register">Register</a></span>
                </div></div></div></div>';
                $html .= '</div>';
                echo $html . ob_get_clean();
            } else{
            	$db_message = get_user_meta(get_current_user_id(), 'lr_message_text', true);
                if ( ! empty( $db_message ) ) {
                    delete_user_meta( get_current_user_id(), 'lr_message_text' );
                }
                $html .= '<div id="lr_popup_changepassword_form" class="lr-popup-container">';
                $html .= self::popup_header('Change Password');
                $html .= '<div id="lr-popup-body-container">';
                $html .= '<div class="messageinfo">' . $db_message . '</div>';
                $html .= '<div id="custom-object-container" class="lr-input-style lr-input-frame">';
                $html .= self::raas_forms('lr-popup-password');
                $html .= '<div class="lr-column">';
                $html .= '<div id="popup-changepasswordbox" class="lr-input-style" style="display:none;"></div>
                <div id="popup-setpasswordbox" class="lr-input-style"></div>
                </div></div></div></div>';
                $html .= '</div>';
                echo $html . ob_get_clean();
            }   
        }
    }
    new LR_Raas_Popup();
}