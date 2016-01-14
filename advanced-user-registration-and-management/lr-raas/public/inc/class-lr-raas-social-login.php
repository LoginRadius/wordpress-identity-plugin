<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * The front function class of LoginRadius Raas.
 */
if ( ! class_exists( 'LR_Raas_Social_Login' ) ) {

	class LR_Raas_Social_Login {

		private static $loginRadiusVersion = LR_PLUGIN_VERSION;
		public static $loginRadiusAccessToken = '';
		private static $loginRadiusProvider;
		public static $loginRadiusProfileData;

		/**
		 * Load necessary scripts and CSS.
		 * 
		 * @global type $wpdb
		 */
		public static function init() {
			global $wpdb;

			if ( get_option( 'loginradius_version') != self::$loginRadiusVersion ) {
				$wpdb->query( "update $wpdb->usermeta set meta_key = 'loginradius_provider_id' where meta_key = 'id'" );
				$wpdb->query( "update $wpdb->usermeta set meta_key = 'loginradius_thumbnail' where meta_key = 'thumbnail'" );
				$wpdb->query( "update $wpdb->usermeta set meta_key = 'loginradius_verification_key' where meta_key = 'loginRadiusVkey'" );
				$wpdb->query( "update $wpdb->usermeta set meta_key = 'loginradius_isVerified' where meta_key = 'loginRadiusVerified'" );
				update_option( 'loginradius_version', self::$loginRadiusVersion );
			}

			add_action( 'parse_request', array( get_class(), 'connect') );
			add_filter( 'LR_logout_url', array( get_class(), 'log_out_url' ), 20, 2 );
			add_action( 'lr_raas_linking_interface', array( get_class(), 'raas_social_linking_interface') );
			
			/* change authentication links */
			if( ! empty( $lr_raas_settings['lost_password_page_id'] ) ) {
				add_filter( 'lostpassword_url', array( get_class(), 'lr_lostpassword_url'), 12, 0 );
			}
			if( ! empty( $lr_raas_settings['registration_page_id'] ) ) {
				add_filter( 'register_url', array( get_class(), 'lr_registration_url'), 12, 0 );
			}
			if( ! empty( $lr_raas_settings['login_page_id'] ) ) {
				add_filter( 'login_url', array( get_class(), 'lr_login_url'), 12, 0);
			}
			
			add_action( 'lr_update_extented_user_profile', array( get_class(), 'lr_save_raas_profile_data'),10,2 );
			add_filter( 'is_uid_exists_in_wordpress', array( get_class(), 'is_uid_exists_in_wordpress'),10,2 );	
		}

		public static function is_uid_exists_in_wordpress( $action, $userProfile ){
			global $wpdb;
			if( isset( $userProfile['uid'] ) && ! empty( $userProfile['uid'] ) ) {
				$userId = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key='lr_raas_uid' AND meta_value = %s", $userProfile['uid']));
				if ( ! empty( $userId ) ) {     // id exists
					return $userId;
				} else {                  // id doesn't exist
					return false;
				}
			}
			return $action;
		}

		public static function lr_lostpassword_url() {
			global $lr_raas_settings;
			return get_permalink( $lr_raas_settings['lost_password_page_id'] );
		}
		
		public static function lr_registration_url() {
			global $lr_raas_settings;
			return get_permalink( $lr_raas_settings['registration_page_id'] );
		}

		public static function lr_login_url() {
			global $lr_raas_settings;
			return get_permalink( $lr_raas_settings['login_page_id'] );
		}

		/**
		 * Function that uses for logout.
		 */
		public static function log_out_url() {
			$link = '<a href="' . wp_logout_url( get_permalink() ) . '" title="' . __('Logout', 'lr-plugin-slug') . '">' . __('Logout', 'lr-plugin-slug') . '</a>';
			echo apply_filters( 'Login_Radius_log_out_url', $link );
		}

		/**
		 * add raas interface script if linking enable
		 * 
		 * @global type $loginRadiusSettings
		 */
		public static function raas_social_linking_interface() {
			global $loginRadiusSettings, $lr_js_in_footer;
			if ( isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && $loginRadiusSettings['LoginRadius_socialLinking'] == 1 ) {
				wp_register_script( 'lr-raas-front-script', LR_RAAS_URL . 'assets/js/loginradiusfront.js', array('jquery-ui-datepicker' ), LR_PLUGIN_VERSION, $lr_js_in_footer);
				wp_register_script( 'lr-raas', '//cdn.loginradius.com/hub/prod/js/LoginRadiusRaaS.js', array( 'jquery', 'lr-social-login' ), LR_PLUGIN_VERSION, $lr_js_in_footer);
				wp_register_style( 'lr-raas-style', LR_RAAS_URL . 'assets/css/lr-raas-style.css', array(), LR_PLUGIN_VERSION );
                echo self::login_script() . self::raas_forms('accountlinking') . '<div id="messageinfo" class="messageinfo"></div><div id="interfacecontainerdiv" class="interfacecontainerdiv"></div>';
				echo '<script>jQuery(document).ready( function(){ jQuery(".lr_fade").hide(); } );</script>';
			}
		}

		/**
		 * update user profile data in usermeta
		 * 
		 * @param type $user_id
		 * @param type $profileData
		 */
		public static function lr_save_raas_profile_data( $user_id, $profileData ) {
			
			$user_id = wp_update_user( array( 'ID' => $user_id,
					'first_name' => $profileData['FirstName'],
					'last_name' => $profileData['LastName'],
					'user_nicename' => $profileData['NickName']
					) );
			if ( isset( $profileData['Gender'] ) && in_array( $profileData['Gender'], array('M','male','Male','m'))) {
				$gender = 'M';
			} elseif ( isset( $profileData['Gender'] ) && in_array( $profileData['Gender'], array('F','female','Female','f'))) {
				$gender = 'F';
			} else {
				$gender = 'U';
			}

			$birthdate = ! empty( $profileData['BirthDate'] ) ? $profileData['BirthDate'] : '';
			$city = ! empty( $profileData['ProfileCity'] ) ? $profileData['ProfileCity'] : '';
			$state = ! empty( $profileData['State'] ) ? $profileData['State'] : '';
			$country = ! empty( $profileData['CountryName'] ) ? $profileData['CountryName'] : '' ;
			$phone = ! empty( $profileData['PhoneNumber'] ) ? $profileData['PhoneNumber'] : '';

			update_user_meta( $user_id, 'lr_raas_uid', $profileData['uid'] );
			update_user_meta( $user_id, 'lr_raas_accountid', $profileData['ID'] );
			update_user_meta( $user_id, 'lr_birthdate', $birthdate );
			update_user_meta( $user_id, 'lr_gender',    $gender );
			update_user_meta( $user_id, 'lr_city',      $city );
			update_user_meta( $user_id, 'lr_state',     $state );
			update_user_meta( $user_id, 'lr_country',   $country );
			update_user_meta( $user_id, 'lr_phone',     $phone );
		}

		/**
		 * Print the script required for enabling social login.
		 * 
		 * @global array $loginradius_api_settings
		 * @global type $loginRadiusObject
		 * @global type $lr_raas_settings
		 * @param type $linkingWidget
		 * @param type $custom
		 */
		public static function login_script( $linkingWidget = false, $custom = false ) {
			global $loginradius_api_settings, $loginRadiusObject, $lr_raas_settings, $lr_custom_interface_settings;
			$loginradius_api_settings['LoginRadius_apikey'] = isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim($loginradius_api_settings['LoginRadius_apikey'] ) : '';

			if ( $loginRadiusObject->loginradius_is_valid_guid( $loginradius_api_settings['LoginRadius_apikey'] ) ) {

				$emailVerificationUrl = isset( $lr_raas_settings['login_page_id']) ? get_permalink( $lr_raas_settings['login_page_id'] ) : '';
				$forgotPasswordUrl = isset( $lr_raas_settings['login_page_id']) ? get_permalink( $lr_raas_settings['login_page_id'] ) : '';

				$storageVariable = '';

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'lr-raas' );
				wp_enqueue_style( 'lr-raas-style' );
				wp_enqueue_style( 'jquery-ui-style' );
				wp_enqueue_style( 'lr-form-style' );
				
				$accountLinking = '';
				$redirectTo = site_url('/');
				
				if ( is_user_logged_in() ) {
					$storageVariable = get_user_meta(get_current_user_id(), 'lr_raas_uid', true);
					$accountLinking = '&ac_linking=true';
				} else {
					$loginPageID = isset( $lr_raas_settings['login_page_id'] ) && ! empty( $lr_raas_settings['login_page_id'] ) ? $lr_raas_settings['login_page_id'] : '';
					$redirectTo = get_permalink( $loginPageID );
				}

				$args = array( 
					'api_key' => $loginradius_api_settings['LoginRadius_apikey'],
					'spinner' => LR_Core::get_spinner(),
					'local_domain' => site_url('/'),
					'v2captcha' => ! empty( $lr_raas_settings['enable_v2captcha'] ) ? $lr_raas_settings['enable_v2captcha'] : '',
					'storageVariable' => $storageVariable,
					'emailVerificationUrl' => $emailVerificationUrl,
					'forgotPasswordUrl' => $forgotPasswordUrl,
					'sitename' => ! empty( $loginradius_api_settings['sitename'] ) ? $loginradius_api_settings['sitename'] : '',
					'v2RecaptchaSiteKey' => ! empty( $lr_raas_settings['v2captcha_site_key'] ) ? $lr_raas_settings['v2captcha_site_key'] : '',
					'ajax_url' => get_admin_url() . 'admin-ajax.php',
					'login_page_url' => get_permalink( $lr_raas_settings['login_page_id'] ),
					'current_page' => get_permalink(),
					'disable_email_verify' => ! empty( $lr_raas_settings['disable_email_verify'] ) ? $lr_raas_settings['disable_email_verify'] : ''
				);
				wp_localize_script( 'lr-raas-front-script', "RaasDetails", $args );
				wp_enqueue_script( 'lr-raas-front-script' );
				
				$interface_url = LR_CUSTOM_INTERFACE_URL . 'assets/images/custom_interface/';
				
				if( is_multisite() ) {
					if( file_exists( $interface_url . get_current_blog_id() . '/' ) ) {
						$interface_url .= get_current_blog_id() . '/';
					}
				}

				if( ! empty( $lr_custom_interface_settings['custom_interface'] ) ) {
					?>
						<script type="text/html" id="loginradiuscustom_raas_tmpl">                    
							<li style="padding-bottom:0%;<# if(isLinked) { #>display:none;<# } #>">
								<a style="cursor: pointer;" class="<#= Name.toLowerCase() #> lrsociallogin" onclick="return $SL.util.openWindow('<#= Endpoint #>&is_access_token=true<?php echo $accountLinking;?>&callback=<?php echo $redirectTo ?>');">
									<img src = "<?php echo $interface_url;?><#= Name.toLowerCase() #>.png" alt="<#= Name #>" title="<#= Name #>" />
								</a>
							</li>
						</script>
					<?php
				} else {
					?>
						<script type="text/html" id="loginradiuscustom_raas_tmpl">
						    <div class="lr-social-provider-btn" onclick="return $SL.util.openWindow('<#= Endpoint #>&is_access_token=true<?php echo $accountLinking;?>&callback=<?php echo $redirectTo ?>');">
						        <span class="lr-img-icon-<#= Name.toLowerCase() #> user-reg" title="<#= Name #>"></span>
						    </div>
						</script>
					<?php
				}
			}
		}

		/**
		 * Get Social Login iframe.
		 * 
		 * @global type $loginRadiusObject
		 * @global array $loginradius_api_settings
		 * @global type $LR_Raas_Social_Login
		 * @param type $newInterface
		 * @return string
		 */
		public static function get_interface( $newInterface = false ) {
			global $loginRadiusObject, $loginradius_api_settings, $LR_Raas_Social_Login;

			$loginRadiusApiKey = isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim( $loginradius_api_settings['LoginRadius_apikey'] ) : '';
			$loginRadiusSecret = isset( $loginradius_api_settings['LoginRadius_secret'] ) ? trim( $loginradius_api_settings['LoginRadius_secret'] ) : '';

			if ( empty( $loginRadiusApiKey ) ) {
				return "<div style='background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;'><div style ='color:red; margin-bottom:5px'>LoginRadius Social Login Plugin is not configured!</div><p style='line-height:1.3; margin-bottom:4px'>To activate your plugin, navigate to <strong>LoginRadius > API Settings</strong> section in your WordPress admin panel and insert LoginRadius API Key & Secret. Follow <a href='http://support.loginradius.com/customer/portal/articles/677100-how-to-get-loginradius-api-key-and-secret' target='_blank'>this</a> document to learn how to get API Key & Secret.</p></div>";
			} elseif ( ! $loginRadiusObject->loginradius_is_valid_guid( $loginRadiusApiKey ) || ! $loginRadiusObject->loginradius_is_valid_guid( $loginRadiusSecret ) ) {
				return "<div style='background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;'><p style ='color:red;'>Your LoginRadius API key or secret is not valid, please correct it or contact LoginRadius support at <b><a href ='http://www.loginradius.com' target = '_blank'>www.LoginRadius.com</a></b></p></div>";
			} elseif ( ! $newInterface ) {
				return $LR_Raas_Social_Login->raas_forms( 'sociallogin' ) . '<div class="interfacecontainerdiv lr-input-style" id="social-registration-container"></div><div class="hr-or-rule" style="clear:both;"></div>';
			} else {
				return $LR_Raas_Social_Login->raas_forms( 'accountlinking' ) . '<div class="interfacecontainerdiv lr-input-style hr-or-rule"></div>';
			}
		}

		/**
		 * generate RaaS forms.
		 * 
		 * @param type $page
		 */
		public static function raas_forms( $page ) {
			global $lr_raas_settings;
			?>
			<script>
				jQuery(document).ready(function () {
			<?php if ($page == 'accountlinking' || $page == 'password') { ?>
						LoginRadiusRaaS.init(raasoption, "accountlinking", function (response) {
							handleResponse(true, "");
							if (response.isPosted == true) {
								handleResponse(true, 'Your account linked successfully', '.lr-user-reg-container');
							} else {
								redirect(response);
							}
						}, function (response) {
							jQuery('.lr_fade').hide();
							if (response[0].description != null) {
								handleResponse(false, response[0].description, '.lr-user-reg-container');
							}
						}, "interfacecontainerdiv");
				<?php if ( $page == 'password' ) { ?>
					LoginRadiusRaaS.passwordHandleForms("setpasswordbox", "changepasswordbox", function (israas) {
						if (israas) {
							jQuery("#changepasswordbox").show();
						} else {
							jQuery("#setpasswordbox").show();
						}
					}, function () {
						document.forms["setpassword"].action = "";
						document.forms["setpassword"].submit();
					}, function () {
					}, function () {
						document.forms["changepassword"].action = "";
						document.forms["changepassword"].submit();
					}, function () {

					});
				<?php } ?>
			<?php } elseif ($page == 'registration') { ?>
					LoginRadiusRaaS.init(raasoption, 'registration', function (response) {
						<?php							
							// Return if disable email verification is true
							if( ! empty( $lr_raas_settings['disable_email_verify'] ) ) {
								?>
									handleResponse( true, 'Registration complete, please login.', '.lr-user-reg-container' );
								<?php	
							}else {
								?>
									handleResponse( true, 'An email has been sent to ' + jQuery( "#loginradius-raas-registration-emailid" ).val() + '.Please verify your email address.', '.lr-user-reg-container' );
								<?php
							}
						?>
						
						jQuery('#social-registration-container').html('');
					}, function (errors) {
						if (errors[0].description != null) {
							handleResponse(false, errors[0].description, '.lr-user-reg-container');
						}
					}, "registration-container");
			<?php } elseif ($page == 'login') { ?>
					//Initialize Login form
					LoginRadiusRaaS.init(raasoption, 'login', function (response) {
						handleResponse(true, '', '.lr-user-reg-container');
						redirect(response.access_token);
					}, function (errors) {
						jQuery('.lr_fade').hide();
						if (errors[0].description != null) {
							handleResponse(false, errors[0].description, '.lr-user-reg-container');
						}
					}, "login-container");
			<?php } elseif ($page == 'login-widget') { ?>
					//initialize Login form
					LoginRadiusRaaS.init(raasoption, 'login', function (response) {
						handleResponse(true, '', '.lr-user-reg-container');
						redirect(response.access_token);
					}, function (errors) {
						jQuery('.lr_fade').hide();
						if (errors[0].description != null) {
							handleResponse(false, errors[0].description, '.lr-user-reg-container');
						}
					}, "login-container-widget");
			<?php } elseif ($page == 'forgotpassword') { ?>
					//initialize forgot password form
					LoginRadiusRaaS.init(raasoption, 'forgotpassword', function (response) {
						handleResponse(true, 'An email has been sent to ' + jQuery("#loginradius-raas-forgotpassword-emailid").val() + ' with reset Password link.', '.lr-user-reg-container');
						jQuery('#social-registration-container').html('');
					}, function (errors) {
						jQuery('.lr_fade').hide();
						if (errors[0].description != null) {
							handleResponse(false, errors[0].description, '.lr-user-reg-container');
						}
					}, "forgotpassword-container");
			<?php } elseif ( $page == 'sociallogin' ) { ?>
					LoginRadiusRaaS.init( raasoption, 'sociallogin', function ( response ) {
						if ( response.isPosted ) {
							handleResponse( true, 'An email has been sent to ' + jQuery( "#loginradius-raas-social-registration-emailid" ).val() + '.Please verify your email address.', '.lr-user-reg-container');
							ShowformbyId( "login-container" );
							jQuery( '#social-registration-container' ).html(' ' );
						} else {
							handleResponse( true, '', '.lr-user-reg-container' );
							redirect(response);
						}
					}, function ( errors ) {
						jQuery('.lr_fade').hide();
						if (errors[0].description != null) {
							handleResponse(false, errors[0].description, '.lr-user-reg-container');
						}
					}, "social-registration-container");
			<?php } ?>
				});
			</script>
			<?php
		}

		/**
		 * Custom Page Redirection
		 * This method redirects users away from the wp-login.php page when user registration is enabled
		 * @global type $pagenow
		 * @global type $lr_raas_settings
		 */
		public static function custom_page_redirection() {
			global $pagenow, $lr_raas_settings;

			$login_page_id = ! empty( $lr_raas_settings['login_page_id'] ) ? $lr_raas_settings['login_page_id'] : '';
			$register_page_id = ! empty( $lr_raas_settings['registration_page_id'] ) ? $lr_raas_settings['registration_page_id'] : '';
			$lost_pass_page_id = ! empty( $lr_raas_settings['lost_password_page_id'] ) ? $lr_raas_settings['lost_password_page_id'] : '';
			
			if ( 'wp-login.php' == $pagenow && ! is_user_logged_in() ) {
				$url = get_permalink( $login_page_id );
				if ( isset( $_GET['action'] ) && 'register' == $_GET['action'] ) {
					$url = get_permalink( $register_page_id );
				} elseif ( isset( $_GET['action'] ) && 'lostpassword' == $_GET['action'] ) {
					$url = get_permalink( $lost_pass_page_id );
				}
				
				if( $url ) {
					wp_redirect( $url );
					exit();
				} else {
					error_log('USER REGISTRATION NOT CONFIGURED CORRECTLY: Login, Registration or Lost Password page(s) are not set');
				}
			}
		}

		/**
		 * update user profile data if create user from admin
		 * 
		 * @global type $pagenow
		 * @param type $user_id
		 */
		public static function raas_uid_updation($user_id) {
			global $pagenow;

			if ( in_array( $pagenow, array('user-new.php')) && isset( $_POST['uid'] ) ) {
				if (isset($_POST['ID'])) {
					update_user_meta( $user_id, 'lr_raas_accountid', $_POST['ID'] );
				}if ( isset( $_POST['uid'] ) ) {
					update_user_meta( $user_id, 'lr_raas_uid', $_POST['uid'] );
				}if ( isset( $_POST['lr_birthdate'] ) ) {
					update_user_meta( $user_id, 'lr_birthdate', date( 'm-d-Y', strtotime( $_POST['lr_birthdate'] ) ) );
				}if ( isset($_POST['lr_gender'])) {
					update_user_meta( $user_id, 'lr_gender', $_POST['lr_gender'] );
				}if ( isset($_POST['lr_city'])) {
					update_user_meta( $user_id, 'lr_city', $_POST['lr_city'] );
				}if ( isset( $_POST['lr_state'] ) ) {
					update_user_meta( $user_id, 'lr_state', $_POST['lr_state'] );
				}if ( isset($_POST['lr_country'])) {
					update_user_meta( $user_id, 'lr_country', $_POST['lr_country'] );
				}if ( isset( $_POST['lr_phone'] ) ) {
					update_user_meta( $user_id, 'lr_phone', $_POST['lr_phone'] );
				}
				if( isset( $_POST['lr_raas_response']->Provider) && !empty($_POST['lr_raas_response']->Provider ) ){
					update_user_meta( $user_id, 'loginradius_provider', $_POST['lr_raas_response']->Provider );
				}
				do_action( 'lr_update_extented_user_profile', $user_id, LR_Social_Profile_Data_Function::validate_profiledata( $_POST['lr_raas_response'] ) );
			}
		}

		/**
		 * update raas user profile
		 * 
		 * @param type $errors
		 * @param type $update
		 * @param type $user
		 * @return type
		 */
		public static function raas_user_updation( $errors, $update, $user ) {
			
			$params = array(
				'firstname' => isset( $_POST['first_name'] ) ? $_POST['first_name'] : '',
				'lastname' => isset( $_POST['last_name'] ) ? $_POST['last_name'] : '',
				'gender' => isset( $_POST['lr_gender'] ) ? $_POST['lr_gender'] : '',
				'birthdate' => isset( $_POST['lr_birthdate'] ) ? date( 'm-d-Y', strtotime( $_POST['lr_birthdate'] ) ) : '',
				'city' => isset( $_POST['lr_city'] ) ? $_POST['lr_city'] : '',
				'state' => isset( $_POST['lr_state'] ) ? $_POST['lr_state'] : '',
				'country' => isset( $_POST['lr_country'] ) ? $_POST['lr_country'] : '',
				'phonenumber' => isset( $_POST['lr_phone'] ) ? $_POST['lr_phone'] : '',
			);
			if ( isset( $_POST['pass1'] ) ) {
				$params['password'] = $_POST['pass1'];
			}

			if ( false == $update ) {
				$params['emailid'] = isset( $_POST['email'] ) ? $_POST['email'] : '';
				$response = json_decode( raas_create_user( $params ) );

				if ( isset( $response->description ) ) {
					$errors->add('user_creation_error', $response->description);
					return;
				} elseif (isset($response->Uid)) {
					$_POST['uid'] = $response->Uid;
					$_POST['ID'] = $response->ID;
					$_POST['lr_raas_response'] = $response;
				}
			} else {
				$accountId = get_user_meta($_POST['user_id'], 'lr_raas_accountid', true);
				$uid = get_user_meta($_POST['user_id'], 'lr_raas_uid', true);

				if ( ! empty( $accountId ) ) {
					$response = json_decode( raas_update_user( $params, $accountId ) );
					if ( isset( $response->description ) ) {
						$errors->add('user_updation_error', $response->description );
					} else {
						$userProfile = raas_get_user( $accountId );
						if( isset( $userProfile->ID ) ){
							do_action( 'lr_update_extented_user_profile', $_POST['user_id'], LR_Social_Profile_Data_Function::validate_profiledata( $userProfile ) );
						}
					}
				}
			}
		}

		/**
		 * 
		 * @global type $pagenow
		 * @param type $user_id
		 * @return boolean
		 */
		public static function save_extra_profile_fields( $user_id ) {
			global $pagenow;

			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				return false;
			}
			$params = array(
				'firstname' => ! empty( $_POST['first_name'] ) ? $_POST['first_name'] : '',
				'lastname' => ! empty( $_POST['last_name'] ) ? $_POST['last_name'] : '',
				'gender' => ! empty( $_POST['lr_gender'] ) ? $_POST['lr_gender'] : '',
				'birthdate' => ! empty( $_POST['lr_birthdate'] ) ? $_POST['lr_birthdate'] : '',
				'city' => ! empty( $_POST['lr_city'] ) ? $_POST['lr_city'] : '',
				'state' => ! empty( $_POST['lr_state'] ) ? $_POST['lr_state'] : '',
				'country' => ! empty( $_POST['lr_country'] ) ? $_POST['lr_country'] : '',
				'phonenumber' => ! empty( $_POST['lr_phone'] ) ? $_POST['lr_phone'] : ''
			);

			$accountId = get_user_meta( $user_id, 'loginradius_current_id', true );
			if ( ! empty( $accountId ) ) {
				$response = json_decode( raas_update_user( $params, $accountId ) );
				if ( isset($response->isPosted) && $response->isPosted ) {
					$userProfile = raas_get_user( $accountId );
					if( isset( $userProfile->ID ) ) {                           
						do_action( 'lr_update_extented_user_profile', $user_id, LR_Social_Profile_Data_Function::validate_profiledata( $userProfile ) );
					}
				} else {
					// Error message and redirect
					if ( isset( $response->description ) ) {
						update_user_meta( $user_id, 'lr_profile_update_error', $response->description );
						wp_redirect( $pagenow . '?error=1' );
						exit();
					}
				}
			} else {
				return;
			}
		}

		/**
		 * Display profile files on profile page
		 * 
		 * @param type $user
		 */
		public static function display_extra_profile_fields( $user ) {
			?>
			<table class="form-table">
				<tr>
					<th><label for="email">Email Address</label><span class="description"> (required)</span></th>
					<td>
						<input type="text" name="email" id="email" value="<?php echo esc_attr(get_the_author_meta('email', $user->ID)); ?>" class="regular-text" <?php if($user->ID){echo 'readonly="readonly"';}?> />
					</td>
				</tr>
				<tr>
					<th><label for="lr_gender">Gender</label></th>
					<td>
						<select name="lr_gender" id="lr_gender">
							<?php
							$selected = 'selected="selected"';
							$genderMeta = get_the_author_meta( 'lr_gender', $user->ID );
							?>
							<option value="" <?php echo ( empty($genderMeta) || !isset($genderMeta) ) ? $selected : '' ?> ><?php _e( '--Select--', 'lr-plugin-slug' ); ?></option>
							<option value="M" <?php echo ( isset($genderMeta) && "M" == $genderMeta ) ? $selected : '' ?> ><?php _e( 'Male', 'lr-plugin-slug' ); ?></option>
							<option value="F" <?php echo ( isset($genderMeta) && "F" == $genderMeta ) ? $selected : '' ?> ><?php _e( 'Female', 'lr-plugin-slug' ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th><label for="lr_birthdate">Birth Date (MM-DD-YYYY)</label></th>
					<td>
						<input type="text" name="lr_birthdate" id="lr_birthdate" placeholder="MM-DD-YYYY" value="<?php echo esc_attr( get_the_author_meta( 'lr_birthdate', $user->ID )); ?>" class="regular-text" />
					</td>
				</tr> <tr>
					<th><label for="lr_city">City</label></th>
					<td>
						<input type="text" name="lr_city" id="lr_city" value="<?php echo esc_attr( get_the_author_meta( 'lr_city', $user->ID ) ); ?>" class="regular-text" />
					</td>
				</tr> <tr>
					<th><label for="lr_state">State</label></th>
					<td>
						<input type="text" name="lr_state" id="lr_state" value="<?php echo esc_attr( get_the_author_meta( 'lr_state', $user->ID ) ); ?>" class="regular-text" />
					</td>
				</tr> <tr>
					<th><label for="lr_country">Country</label></th>
					<td>
						<input type="text" name="lr_country" id="lr_country" value="<?php echo esc_attr( get_the_author_meta( 'lr_country', $user->ID ) ); ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th><label for="lr_phone">Phone</label></th>
					<td>
						<input type="text" name="lr_phone" id="lr_phone" value="<?php echo esc_attr( get_the_author_meta( 'lr_phone', $user->ID ) ); ?>" class="regular-text" />
					</td>
				</tr>
			</table>
			<?php
		}

		/**
		 * Display custom fields for New User creation.
		 */
		public static function custom_fields() {
			?>
			<table class="form-table" style="margin-top:0 !important">

				<tr class="form-field">
					<th scope="row"><label for="lr_gender">Gender</label></th>
					<td>
						<label for="lr_gender">

							<select name="lr_gender" id="lr_gender">
								<option value="" ><?php _e( '--Select--', 'lr-plugin-slug' ) ?></option>
								<option value="M" ><?php _e( 'Male', 'lr-plugin-slug' ) ?></option>
								<option value="F" ><?php _e( 'Female', 'lr-plugin-slug' ) ?></option>
							</select>
						</label>
					</td>
				</tr>

				<tr class="form-field">
					<th scope="row"><label for="lr_birthdate">Birth Date( MM-DD-YYYY )</label></th>
					<td>
						<label for="lr_birthdate">
							<input type="text" placeholder="MM-DD-YYYY" name="lr_birthdate" id="lr_birthdate"/>
						</label>
					</td>
				</tr>

				<tr class="form-field">
					<th scope="row"><label for="lr_city">City</label></th>
					<td>
						<label for="lr_city">
							<input type="text" name="lr_city" id="lr_city"/>
						</label>
					</td>
				</tr>

				<tr class="form-field">
					<th scope="row"><label for="lr_state">State</label></th>
					<td>
						<label for="lr_state">
							<input type="text" name="lr_state" id="lr_state" />
						</label>
					</td>
				</tr>

				<tr class="form-field">
					<th scope="row"><label for="lr_phone">Phone</label></th>
					<td>
						<label for="lr_phone">
							<input type="text" name="lr_phone" id="lr_phone" />
						</label>
					</td>
				</tr>
			</table>
			<?php
		}

		/**
		 * Remove fields from admin profile
		 */
		public static function remove_extra_profile_fields() {
			?>
			<script type="text/javascript">
				jQuery("h3:contains('About Yourself')").next('.form-table').remove();
				jQuery("h3:contains('About Yourself')").remove();
				jQuery("h3:contains('Contact Info')").next('.form-table').remove();
				jQuery("h3:contains('Contact Info')").remove();
				jQuery("h3:contains('Name')").html('User Profile');
			</script>
			<?php
		}

		/**
		 * change password on RaaS
		 * 
		 * @param type $data
		 */
		public static function change_password( $data ) {

			$user_id = get_current_user_id();
			$accountId = get_user_meta( $user_id, 'lr_raas_accountid', true );
			if ( empty( $accountId ) ) {
				$uid = get_user_meta( $user_id, 'lr_raas_uid', true );
				$raas_linked_account = '';
				if ( ! empty( $uid ) ) {
					$raas_linked_account = raas_getlink_account( $uid );
					if ( isset( $raas_linked_account->errorCode ) ) {
						update_user_meta( $user_id, 'lr_message_text', $raas_linked_account->description );
					} else {
						for ( $i = 0; $i < count( $raas_linked_account ); $i++ ) {
							if ( isset( $raas_linked_account[$i]->Provider ) && $raas_linked_account[$i]->Provider == 'RAAS' ) {
								update_user_meta( $user_id, 'lr_raas_accountid', $raas_linked_account[$i]->ID );
								break;
							}
						}
						$accountId = get_user_meta( $user_id, 'lr_raas_accountid', true );
					}
				}
			}
			if ( ! empty( $accountId ) ) {
				$params = http_build_query( $data );
				$result = json_decode( raas_update_password( $params, $accountId ) );
				if ( isset( $result->isPosted ) && $result->isPosted ) {
					$message = '<div style="color:green;">Password has been updated Successfully.</div>';
				} else {
					$message = '<div style="color:red;">' . $result->description . '</div>';
				}
				update_user_meta( $user_id, 'lr_message_text', $message );
			}
		}

		/**
		 * Set password on raas
		 * 
		 * @param type $data
		 */
		public static function set_password( $data ) {
			$user_id = get_current_user_id();
			$uid = get_user_meta( $user_id, 'lr_raas_uid', true );
			$email = isset( $data['emailid'] ) ? trim( $data['emailid'] ) : '';
			$password = isset( $data['password'] ) ? trim( $data['password'] ) : '';
			$data = array( 'accountid' => $uid, 'emailid' => $email, 'password' => $password );
			$params = http_build_query( $data );
			$result = json_decode( raas_set_password( $params ) );
			if ( isset( $result->isPosted ) && $result->isPosted == true ) {
				$message = '<div style="color:green;">Password has been Created Successfully.</div>';
			} else {
				$message = '<div style="color:red;">' . $result->description . '</div>';
			}
			update_user_meta( $user_id, 'lr_message_text', $message );
		}

		/**
		 * Check for the query string variables and authenticate user.
		 */
		public static function connect() {
			// check if permission is provided
			if ( isset( $_POST['newpassword'] ) && ! empty( $_POST['newpassword'] ) ) {
				self::change_password( $_POST );
			} else if ( isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ) {
				self::set_password( $_POST );
			}
			// Recieve Custom Object Form Fields
			do_action('lr_custom_obj_form_response');
		}

	}

}