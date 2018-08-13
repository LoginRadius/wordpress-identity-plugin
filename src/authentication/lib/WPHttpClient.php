<?php

/**
 * @link : http://www.loginradius.com
 * @category : LoginRadiusSDK
 * @package : LoginRadius
 * @author : LoginRadius Team
 * @version : 3.2.2
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
        
        if ($query_array !== false) { 
           
            $secure = "secret";
            if (isset($options['authentication']) && $options['authentication'] == 'headsecure') {
                $options = array_merge($options, Functions::authentication(array(), $options['authentication']));
                $query_array = isset($options['authentication']) ? $query_array : $query_array;
            }
            else {
                $query_array = isset($options['authentication']) ? Functions::authentication($query_array, $options['authentication']) : $query_array;
            }
           // $query_array = (isset($options['authentication']) && ($options['authentication'] == false)) ? $query_array : Functions::authentication($query_array,$secure);
            if (strpos($request_url, "?") === false) {
                $request_url .= "?";
            } else {
                $request_url .= "&";
            }
            $request_url .= Functions::queryBuild($query_array);
            
        }
        
        
        $argument = array('timeout' => 500);
        $argument['method'] = isset($options['method']) ? strtoupper($options['method']) : 'GET';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $content_type = isset($options['content_type']) ? trim($options['content_type']) : 'x-www-form-urlencoded';
         $sott_header_content = isset($options['X-LoginRadius-Sott']) ? trim($options['X-LoginRadius-Sott']) : '';
        $apikey_header_content = isset($options['X-LoginRadius-ApiKey']) ? trim($options['X-LoginRadius-ApiKey']) : '';
        $secret_header_content = isset($options['X-LoginRadius-ApiSecret']) ? trim($options['X-LoginRadius-ApiSecret']) : '';
        
            $argument['headers'] = array('content-type'=>'application/' . $content_type,
                'X-LoginRadius-ApiKey'=>$apikey_header_content,
                'X-LoginRadius-ApiSecret'=>$secret_header_content,
                'X-LoginRadius-Sott'=>$sott_header_content);
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
