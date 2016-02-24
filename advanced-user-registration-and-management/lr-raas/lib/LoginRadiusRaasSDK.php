<?php

global $loginradius_api_settings;
define('RAAS_DOMAIN', 'https://api.loginradius.com');
define('RAAS_API_KEY', $loginradius_api_settings['LoginRadius_apikey']);
define('RAAS_SECRET_KEY', $loginradius_api_settings['LoginRadius_secret']);

/**
 * 
 * @param type $params
 * @return type
 */
function raas_create_user( $params ) {
    $url = RAAS_DOMAIN . "/raas/v1/user?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY;
    return raas_get_response_from_raas( $url, $params, 'application/x-www-form-urlencoded' );
}

/**
 * 
 * @param type $params
 * @return type
 */
function raas_register_user( $params ) {
    $url = RAAS_DOMAIN . "/raas/v1/user/register?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY;
    return raas_get_response_from_raas( $url, json_encode( $params ), 'application/json' );
}

/**
 * 
 * @param type $params
 * @param type $userid
 * @return type
 */
function raas_update_user( $params, $userid ) {
    $url = RAAS_DOMAIN . "/raas/v1/user?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY . "&userid=" . $userid;
    return raas_get_response_from_raas( $url, json_encode( $params ), 'application/json' );
}

/**
 * 
 * @param type $params
 * @param type $uid
 * @return type
 */
function raas_block_user( $params, $uid ) {
    $url = RAAS_DOMAIN . "/raas/v1/user/status?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY . "&uid=" . $uid;
    return raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded');
}

/**
 * 
 * @param type $uid
 * @return type
 */
function raas_delete_user( $uid ) {
    $url = RAAS_DOMAIN . "/raas/v1/user/delete?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY . "&UID=" . $uid;
    $result = wp_remote_get($url, array('timeout' => 15));
    return isset($result['body']) ? $result['body'] : '';
}

/**
 * raas_get_user
 * get profile data by user ID
 * @param  string $accountId ID 
 * @return object profile data
 */
function raas_get_user( $accountId ) {
    $url = RAAS_DOMAIN . "/raas/v1/user?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY . "&userid=" . $accountId;
    $result = wp_remote_get( $url, array('timeout' => 60) );
    return isset($result['body']) ? json_decode( $result['body'] ) : '';
}

/**
 * 
 * @param type $params
 * @param type $uid
 * @return type
 */
function raas_update_password( $params, $uid ) {
    $url = RAAS_DOMAIN . "/raas/v1/user/password?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY . "&userid=" . $uid;
    return raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded');
}

/**
 * 
 * @param type $params
 * @return type
 */
function raas_set_password( $params ) {
    $url = RAAS_DOMAIN . "/raas/v1/account/profile?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY;
    return raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded');
}

/**
 * 
 * @param type $uid
 * @param type $provider
 * @param type $providerid
 * @return type
 */
function raas_link_account( $uid, $provider, $providerid ) {
    $url = RAAS_DOMAIN . "/raas/v1/account/link?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY;
    $params = http_build_query(array('accountid' => $uid, 'provider' => $provider, 'providerid' => $providerid));
    return json_decode(raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded'));
}

/**
 * 
 * @param type $uid
 * @param type $provider
 * @param type $providerid
 * @return type
 */
function raas_unlink_account( $uid, $provider, $providerid ) {
    $url = RAAS_DOMAIN . "/raas/v1/account/unlink?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY;
    $params = http_build_query(array('accountid' => $uid, 'provider' => $provider, 'providerid' => $providerid));
    return json_decode(raas_get_response_from_raas($url, $params, 'application/x-www-form-urlencoded'));
}

/**
 * 
 * @param type $uid
 * @return type
 */
function raas_getlink_account( $uid ) {
    $url = RAAS_DOMAIN . "/raas/v1/account?appkey=" . RAAS_API_KEY . "&appsecret=" . RAAS_SECRET_KEY . "&accountid=" . $uid;
    $result = wp_remote_get($url, array('timeout' => 105, 'content-type' => 'application/x-www-form-urlencoded'));
    return isset($result['body']) ? json_decode($result['body']) : '';
}

/**
 * 
 * @param type $url
 * @param type $params
 * @param type $contentType
 * @return type
 */
function raas_get_response_from_raas( $url, $params, $contentType ) {
    $raasResponse = wp_remote_post($url, array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'headers' => array(
            'Content-Type' => $contentType,
        ),
        'cookies' => array(),
        'body' => $params,
            )
    );
    if (is_wp_error($raasResponse)) {
        $customError = array('wp_errr' => true, 'description' => 'There was a problem retrieving the response from the server.');
        return json_encode($customError);
    } else {
        return $raasResponse['body'];
    }
}

?>