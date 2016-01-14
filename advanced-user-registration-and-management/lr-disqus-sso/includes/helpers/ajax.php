<?php

class Ajax_Login_Helper {


	private static $loginRadiusProfileData;

	public function __construct() {

			add_action( 'wp_ajax_loginradius_login', array( $this, 'loginradius_login' ), 1 );
			add_action( 'wp_ajax_nopriv_loginradius_login', array( $this, 'loginradius_login' ), 1 );
	}

	/**
	 * AJAX function used to login/register.
	 */
	function loginradius_login() {
		global $loginRadiusObject, $wpdb, $loginradius_api_settings, $loginRadiusSettings;
		$login_helper = new Login_Helper();
		$lr_common = new LR_Common();
		$error_msg = '';

		// Set Authorization Token
		$auth_token = isset( $_POST['token'] ) ? $_POST['token'] : '';

		// Log In User
		if ( ! is_user_logged_in() && ! empty( $auth_token ) && $auth_token != null ) {
			
			// Is request token is set.
			$loginRadiusSecret = isset( $loginradius_api_settings['LoginRadius_secret'] ) ? $loginradius_api_settings['LoginRadius_secret'] : '';

			// Fetch user profile using access token.
			try{
				$responseFromLoginRadius = $loginRadiusObject->loginradius_get_user_profiledata( $auth_token );
			} catch( LoginRadiusException $e ) {
				error_log( $e );
				$responseFromLoginRadius = null;
			}
			
			// Retrieve profile data.
			if ( isset( $responseFromLoginRadius->ID ) && $responseFromLoginRadius->ID != null ) {
				// If profile data is retrieved successfully
				$loginRadiusProfileData = $login_helper->filter_loginradius_data_for_wordpress_use( $responseFromLoginRadius );
			} else {
				$message = isset( $responseFromLoginRadius->description ) ? $responseFromLoginRadius->description : $responseFromLoginRadius;
				// Profile not retrieved;
				echo json_encode( array(
					"Error" => "Profile not retrieved " . $message
				));
				die();
			}

			// Check for userId.
			$userId = $login_helper->is_socialid_exists_in_wordpress( $loginRadiusProfileData );
			if ( $userId ) {
				// if Social id exists in wordpress database
				if ( 1 == get_user_meta( $userId, $loginRadiusProfileData['Provider'] . 'LrVerified', true ) ) {
					// if user is verified, provide login.
					$login_helper->login_user( $userId, $loginRadiusProfileData, false, false, false );
				} else {
					// If not verified then return error.
					echo json_encode( array(
						"Error" => 'Please verify your email by clicking the confirmation link sent to you.'
					));
					die();
				}
			}else {
				// check if id already exists.
				$loginRadiusUserId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE meta_key='loginradius_provider_id' AND meta_value = %s", $loginRadiusProfileData['ID'] ) );
				
				if ( ! empty( $loginRadiusUserId ) ) {
					// Id exists in usermeta loginradius_provider_id
					$tempUserId = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $loginRadiusUserId ) );

					if ( isset( $tempUserId ) ) {
						// check if verification field exists.
						$isVerified = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $loginRadiusUserId ) );
						
						if ( $isVerified == '1' ) {                             // if email is verified
							$login_helper->login_user( $loginRadiusUserId, $loginRadiusProfileData, false, false, false );
						} else {
							echo json_encode( array(
								"Error" => 'Please verify your email by clicking the confirmation link sent to you.'
							));
							die();
						}
					} else {
						$login_helper->login_user( $loginRadiusUserId, $loginRadiusProfileData, false, false, false );
					}
				} else {
					
					if ( empty( $loginRadiusProfileData['Email'] ) ) {
						
						if( ! empty( $loginRadiusSettings['LoginRadius_dummyemail'] ) ) {
							if( ! empty( $_POST['email'] ) ) {
								$loginRadiusProfileData['Email'] = $_POST['email'];
								$response = $login_helper->register_user( $loginRadiusProfileData, true, false );
								// Email is already registered
								if( 'wp_error' == $response ) {
									$response = _( 'This email is already registered. Please log in with this email and link any additional ID providers via account linking on your profile page.' );
								}

								echo json_encode( array(
									"Error" => $response
								));
								die();
							} else {
								$error_msg = 'Email is required';
								echo json_encode( array(
									"Error" => $error_msg
								));
								die();
							}
						} else {
							// email not required according to plugin settings
							$loginRadiusProfileData['Email'] = $login_helper->generate_dummy_email( $loginRadiusProfileData );
							$login_helper->register_user( $loginRadiusProfileData, false, false );
						}
					} else {
						// email is not empty
						$userObject = get_user_by( 'email', $loginRadiusProfileData['Email'] );
						$loginRadiusUserId = is_object( $userObject ) ? $userObject->ID : '';
						
						if ( ! empty( $loginRadiusUserId ) ) {        // email exists
							$isVerified = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM " . $wpdb->usermeta . " WHERE user_id = %d and meta_key='loginradius_isVerified'", $loginRadiusUserId ) );
							
							if ( ! empty( $isVerified ) ) {
								if ( $isVerified == '1' ) {
									// social linking
									$lr_common->link_account( $loginRadiusUserId, $loginRadiusProfileData['ID'], $loginRadiusProfileData['Provider'], $loginRadiusProfileData['Thumbnail'], $loginRadiusProfileData['Provider'], '' );
									// Login user
									$login_helper->login_user( $loginRadiusUserId, $loginRadiusProfileData, false, false, false );
								} else {
									$directorySeparator = DIRECTORY_SEPARATOR;
									require_once( getcwd() . $directorySeparator . 'wp-admin' . $directorySeparator . 'inc' . $directorySeparator . 'user.php' );
									wp_delete_user( $loginRadiusUserId );
									$login_helper->register_user( $loginRadiusProfileData, false, false );
								}
							} else {
								if ( get_user_meta( $loginRadiusUserId, 'loginradius_provider_id', true ) != false ) {
									// social linking
									$lr_common->link_account( $loginRadiusUserId, $loginRadiusProfileData['ID'], $loginRadiusProfileData['Provider'], $loginRadiusProfileData['Thumbnail'], $loginRadiusProfileData['Provider'], '' );
								} else {
									// Traditional account
									// Social linking
									if ( isset( $loginRadiusSettings['LoginRadius_socialLinking'] ) && ( $loginRadiusSettings['LoginRadius_socialLinking'] == '1' ) ) {
										$lr_common->link_account( $loginRadiusUserId, $loginRadiusProfileData['ID'], $loginRadiusProfileData['Provider'], $loginRadiusProfileData['Thumbnail'], $loginRadiusProfileData['Provider'], '' );
									}
								}
								// Login user
								$login_helper->login_user( $loginRadiusUserId, $loginRadiusProfileData, false, false, false );
							}
						} else {
							$login_helper->register_user( $loginRadiusProfileData, false, false );// create new user
						}
					}
				}
			}
		} // Authentication ends

		if( is_user_logged_in() ) {
			$success = _( 'Logged In Successfully' );
		} else {
			$error_msg = _( 'Login failed, please contact site administrator' );
		}

		echo json_encode( array(
			"Success" => isset($success) ? $success : 'Not Logged In',
			"Error" => $error_msg
		));

		die();
	}
}

