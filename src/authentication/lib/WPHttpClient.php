<?php

/**
 * @link : http://www.loginradius.com
 * @category : LoginRadiusSDK
 * @package : LoginRadius
 * @author : LoginRadius Team
 * @version : 3.3.0
 * @license : https://opensource.org/licenses/MIT
 */

namespace LoginRadiusSDK\Clients;

use LoginRadiusSDK\Utility\Functions;
use LoginRadiusSDK\LoginRadiusException;

/**
 * Class DefaultHttpClient
 *
 * Use default Curl/fsockopen to get response from LoginRadius APIs.
 *
 * @package LoginRadiusSDK\Clients
 */
class WPHttpClient implements IHttpClient {

    public function __construct($apikey = '', $apisecret = '', $customize_options = array()) { 
        new Functions($apikey, $apisecret, $customize_options);
    }

    public function request($path, $query_array = array(), $options = array()) {
       
        $parse_url = parse_url($path);
        $request_url = '';
        if (!isset($parse_url['scheme']) || empty($parse_url['scheme'])) {
            $request_url .= API_DOMAIN;
        }

        $request_url .= $path;
        if (isset($options['api_region']) && !empty($options['api_region'])) {
            $query_array['region'] = $options['api_region'];
        }
        if (!isset($options['api_request_signing']) || empty($options['api_request_signing'])) {
            $options['api_request_signing'] = false;
        }
        if ($query_array !== false) {
            if (isset($options['authentication']) && $options['authentication'] == 'secret') {
                if (($options['api_request_signing'] === false) || ($options['api_request_signing'] === 'false')) {
                    $options = array_merge($options, Functions::authentication(array(), $options['authentication']));
                }
                $query_array = isset($options['authentication']) ? Functions::authentication($query_array) : $query_array;
            } else {
                $query_array = isset($options['authentication']) ? Functions::authentication($query_array, $options['authentication']) : $query_array;
            }
            $request_url .= (strpos($request_url, "?") === false) ? "?" : "&";
            $request_url .= Functions::queryBuild($query_array);

            if (isset($options['authentication']) && $options['authentication'] == 'secret') {
                if (($options['api_request_signing'] === true) || ($options['api_request_signing'] === 'true')) {
                    $options = array_merge($options, Functions::authentication($options, 'hashsecret', $request_url));
                }
            }
        }

        
        
        $argument = array('timeout' => 500);
        $argument['method'] = isset($options['method']) ? strtoupper($options['method']) : 'GET';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $content_type = isset($options['content_type']) ? trim($options['content_type']) : 'x-www-form-urlencoded';
        $auth_access_token = isset($options['access-token']) ? trim($options['access-token']) : '';
         $sott_header_content = isset($options['X-LoginRadius-Sott']) ? trim($options['X-LoginRadius-Sott']) : '';
        $apikey_header_content = isset($options['X-LoginRadius-ApiKey']) ? trim($options['X-LoginRadius-ApiKey']) : '';
        $secret_header_content = isset($options['X-LoginRadius-ApiSecret']) ? trim($options['X-LoginRadius-ApiSecret']) : '';
        $expiry_time = isset($options['X-Request-Expires']) ? trim($options['X-Request-Expires']) : '';
        $digest = isset($options['digest']) ? trim($options['digest']) : '';
        $api_request_signing = isset($options['api_request_signing']) ? trim($options['api_request_signing']) : '';
        $authentication = isset($options['authentication']) ? trim($options['authentication']) : '';

           

            if ($auth_access_token != '') {
                $argument['headers']['Authorization'] = $auth_access_token;
            }if ($content_type != '') {
                $argument['headers']['Content-type'] = 'application/' . $content_type;
            }
            if ($sott_header_content != '') {
                $argument['headers']['X-LoginRadius-Sott'] = $sott_header_content;
            }
            if ($api_request_signing != '') {
                $argument['headers']['api_request_signing'] = $api_request_signing;
            }
            if ($authentication != '') {
                $argument['headers']['authentication'] = $authentication;
            }
            if ($secret_header_content != '') {
                $argument['headers']['X-LoginRadius-ApiSecret'] = $secret_header_content;
            }
            if ($expiry_time != '') {
                $argument['headers']['X-Request-Expires'] = $expiry_time;
            }
            if ($digest != '') {
                $argument['headers']['digest'] = $digest;
            }
               
            if($content_type == 'json'){
               if(!is_string($data)){
                $data = json_encode($data);
               }
            }
            
            if($data !== true){
                $argument['body'] = $data;
            }
        $response = wp_remote_request($request_url, $argument);

        if (!empty($response)) {
            if(isset($response->errors)){
                
                $error = isset($response->errors['http_request_failed'][0])?$response->errors['http_request_failed'][0]:'An error occurred';
                throw new LoginRadiusException($error, $response);
            }
            elseif(isset($response['body'])){
                $result = json_decode($response['body']);
                if (isset($result->errorCode) && !empty($result->errorCode)) {
                    throw new LoginRadiusException($result->description, $result);
                }
            }
        }
       
        
        return $response['body'];
    }

    
}
