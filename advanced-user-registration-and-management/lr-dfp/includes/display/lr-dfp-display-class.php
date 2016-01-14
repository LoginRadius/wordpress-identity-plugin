<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

if ( ! class_exists( 'LR_DFP_Display' ) ) {
	
	class LR_DFP_Display {

		public function __construct() {
            global $lr_dfp_settings;

            if( isset( $lr_dfp_settings['enable'] ) && $lr_dfp_settings['enable'] == '1' ) {
                add_action( 'wp_login', array( $this, 'dfp' ) );
            }
		}

		public function dfp() {
			global $loginRadiusObject, $loginRadiusCustomObject, $loginradius_api_settings, $lr_raas_custom_obj_settings, $lr_dfp_settings;

            if( isset( $_POST['token'] ) && ! empty( $_POST['token'] ) ) {

                $user_id = get_current_user_id();                   
                
                // RaaS
                $lr_api_key = $loginradius_api_settings['LoginRadius_apikey'];
                $lr_api_secret = $loginradius_api_settings['LoginRadius_secret'];
                
                // Social
                $token = isset( $_POST['token'] ) ? $_POST['token'] : '';

                $uid = get_user_meta( $user_id, 'lr_raas_uid', true );
                $social_id = get_user_meta( $user_id, 'lr_raas_accountid', true );

                try {
                    $userprofile = $loginRadiusObject->loginradius_get_user_profiledata( $token );
                } catch ( LoginRadiusException $e ) {
                    error_log( $e );
                    $userprofile = null;
                }

                // Targets defined by admin settings
                $target = isset( $lr_dfp_settings['target'] ) ? $lr_dfp_settings['target'] : null;
                
                $user_profile = array();
                foreach( $userprofile as $key => $value ) {
                    if( in_array( $key, $target ) ) {
                        $user_profile[$key] = $value;
                    }
                }

                try {
                    $like_object = $loginRadiusObject->loginradius_get_likes( $token );
                } catch ( Exception $e ) {
                    $like_object = null;
                }

                if( class_exists( 'LR_Custom_Obj_Install' ) ) {

                    $objectid = isset( $lr_raas_custom_obj_settings['custom_obj_id'] ) ? $lr_raas_custom_obj_settings['custom_obj_id'] : '';
                    
                    $custom_object_response = $loginRadiusCustomObject->get_custom_obj_by_accountid( $objectid, $uid );

                    // Custom Object Does not exist
                    if( isset( $custom_object_response->errorCode ) ) {
                        $custom_object_response = null;
                        $lr_custom_object = null;
                    }else {
                        foreach( $custom_object_response as $key => $value ) {
                            if( "CustomObject" == $key ) {
                                $lr_custom_object = $value;
                            }
                        }
                    }

                    setcookie( 'lr_custom_object', json_encode( $lr_custom_object ), time() + (86400), '/' );
                }

                setcookie( 'lr_user_profile', json_encode( $user_profile ), time() + (86400), '/' );
                
                setcookie( 'lr_user_likes', json_encode( $like_object ), time() + (86400), '/' );                
    		}
		}
	}
	new LR_DFP_Display();
}
