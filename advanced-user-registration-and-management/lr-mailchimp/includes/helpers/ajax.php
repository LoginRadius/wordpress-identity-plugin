<?php

class LR_Mailchimp_Ajax_Helper {

	public function __construct() {

			// ajax for importing mailchimp lists
			add_action( 'wp_ajax_login_radius_get_mc_lists', array( $this, 'login_radius_get_mc_lists'), 1 );
			// ajax for importing mailchimp lists
			add_action( 'wp_ajax_login_radius_get_mc_merge_vars', array( $this, 'login_radius_get_mc_merge_vars'), 1 );

	}

	// Import mailchimp lists
	public function login_radius_get_mc_lists() {
		$mailchimpApiKey = $_POST['key'];
		global $loginRadiusMailchimp;
		$loginRadiusMailchimp = new MCAPI( $mailchimpApiKey );
		// if everything is fine show lists in a dropdown
		if ( ! $loginRadiusMailchimp->errorCode ){
			$ids   = array();
			$names = array();
			$count = 0;
			do {
				$lists = $loginRadiusMailchimp->lists( array(), $count * 100, 100 );
				foreach ( $lists['data'] as $list ){
					$ids[]   = $list['id'];
					$names[] = $list['name'];
				}
				$count++;
			}
			while ( count( $lists['data'] ) > 0 );
			die( json_encode( array( 'success' => true, 'ids' => $ids, 'names' => $names, ) ) );
		}else {
			die( json_encode( array( 'success' => false ) ) );
		}
		die( 'error' );
	}

	// Import mailchimp lists
	public static function login_radius_get_mc_merge_vars(){
		$mailchimpApiKey = $_POST['key'];
		$mailchimpListId = $_POST['list_id'];
		$mergeVars = self::login_radius_get_mailchimp_merger_vars( $mailchimpApiKey, $mailchimpListId );
		// if everything is fine show lists in a dropdown
		if ( is_array( $mergeVars ) ){
			$tags  = array();
			$names = array();
			foreach ( $mergeVars as $mergeVar ){
				$tags[]  = $mergeVar['tag'];
				$names[] = $mergeVar['name'];
			}
			die( json_encode( array( 'success' => true, 'tags' => $tags, 'names' => $names, ) ) );
		}else {
			die( json_encode( array( 'success' => false ) ) );
		}
		die( 'error' );
	}

	/**
	 * get mailchimp list merge vars
	 */
	public static function login_radius_get_mailchimp_merger_vars( $mailchimpApiKey, $mailchimpListId ){
		global $loginRadiusMailchimp;
		$loginRadiusMailchimp = new MCAPI( $mailchimpApiKey );
		$mergeVars = $loginRadiusMailchimp->listMergeVars( $mailchimpListId );
		if ( ! $loginRadiusMailchimp -> errorCode ){
			return $mergeVars;
		}
		return false;
	}

}


