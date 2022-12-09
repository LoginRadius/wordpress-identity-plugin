<?php

/**
 * @link : http://www.loginradius.com
 * @category : LoginRadiusSDK
 * @package : LoginRadius
 * @author : LoginRadius Team
 * @version : 4.4.0
 * @license : https://opensource.org/licenses/MIT
 */

namespace LoginRadiusSDK\Clients;

use LoginRadiusSDK\Utility\Functions;
use LoginRadiusSDK\LoginRadiusException;
use LoginRadiusSDK\Clients\IHttpClientInterface;

/**
 * Class DefaultHttpClient
 *
 * Use default Curl/fsockopen to get response from LoginRadius APIs.
 *
 * @package LoginRadiusSDK\Clients
 */
class WPHttpClient implements IHttpClientInterface 
{
    /**
     * @param $path
     * @param array $queryArray
     * @param array $options
     * @return type
     * @throws \LoginRadiusSDK\LoginRadiusException
     */
    public function request($path, $queryArray = array(), $options = array())
    {      
        $parseUrl = parse_url($path);
        $requestUrl = '';
        $endpoint = '';
        if (!isset($parseUrl['scheme']) || empty($parseUrl['scheme'])) {
            $requestUrl .= API_DOMAIN;
        }

        $requestUrl .= $path;
        $endpoint .= $path;
        
        if (defined('API_REGION') && API_REGION != "") {
            $queryArray['region'] = API_REGION;
        }
        if (defined('API_REQUEST_SIGNING') && API_REQUEST_SIGNING != "") {
            $options['api_request_signing'] = API_REQUEST_SIGNING;
        } else {
            $options['api_request_signing'] = false;
        }
        if ($queryArray !== false) {
            if (isset($options['authentication']) && $options['authentication'] == 'secret') {
                if (($options['api_request_signing'] === false) || ($options['api_request_signing'] === 'false')) {
                    $options = array_merge($options, Functions::authentication(array(), $options['authentication']));
                }
                $queryArray = isset($options['authentication']) ? Functions::authentication($queryArray) : $queryArray;
            } else {
                $queryArray = isset($options['authentication']) ? Functions::authentication($queryArray, $options['authentication']) : $queryArray;
            }
            $requestUrl .= (strpos($requestUrl, "?") === false) ? "?" : "&";
            $requestUrl .= Functions::queryBuild($queryArray);

            if (isset($options['authentication']) && $options['authentication'] == 'secret') {
                if (($options['api_request_signing'] === true) || ($options['api_request_signing'] === 'true')) {
                    $options = array_merge($options, Functions::authentication($options, 'hashsecret', $requestUrl));
                }
            }
        }
        
        $argument = array('timeout' => 500);
        $argument['method'] = isset($options['method']) ? strtoupper($options['method']) : 'GET';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $content_type = isset($options['content_type']) ? trim($options['content_type']) : 'x-www-form-urlencoded';
        $auth_access_token = isset($options['access-token']) ? trim($options['access-token']) : '';
        $sott_header_content = isset($options['X-LoginRadius-Sott']) ? trim($options['X-LoginRadius-Sott']) : '';
        $secret_header_content = isset($options['X-LoginRadius-ApiSecret']) ? trim($options['X-LoginRadius-ApiSecret']) : '';
        $expiry_time = isset($options['X-Request-Expires']) ? trim($options['X-Request-Expires']) : '';
        $digest = isset($options['digest']) ? trim($options['digest']) : '';


            if ($auth_access_token != '') {
                $argument['headers']['Authorization'] = $auth_access_token;
            }if ($content_type != '') {
                $argument['headers']['Content-type'] = 'application/' . $content_type;
            }
            if ($sott_header_content != '') {
                $argument['headers']['X-LoginRadius-Sott'] = $sott_header_content;
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
            if (!empty($data)) {
                if (($contentType == 'json') && (is_array($data) || is_object($data))) {
                    $data = json_encode($data);
                }
            }
            if($data !== true) {
                $argument['body'] = $data;
            }
            $response = wp_remote_request($requestUrl, $argument);

        $requestedData = [
            'GET' => $queryArray,
            'POST' => (isset($options['post_data']) ? $options['post_data'] : []),
          ];
        if (defined('WP_DEBUG') && true === WP_DEBUG) {
            $responseType = 'error';
            if (!empty($response)) {
              $res = $response['body'] != "" ? json_decode($response['body']) : "";        
              if (!isset($res->errorCode)) {
                $responseType = 'success';
              }
            }
            if (array_key_exists("apiSecret",$requestedData['GET'])) {
                unset($requestedData['GET']['apiSecret']);      
            }
            $logData['endpoint'] = $endpoint;
            $logData['method'] = $argument['method'];
            $logData['data'] = !empty($requestedData) ? json_encode($requestedData) : '';
            $logData['response'] = json_encode($response);
            $logData['response_type'] = ucfirst($responseType);

            $log_message = '[==================================================== '."\r\n".' LoginRadius Log'."\r\n" . date("F j, Y, g:i a e O") . ']' . "API Endoint :" . "\r\n" . $logData['endpoint'] . "\r\n" . "Method :" . "\r\n" . $logData['method'] . "\r\n" . "Data :" . "\r\n" . $logData['data'] . "\r\n" . "Function Output :" . "\r\n" . $logData['response'] . "\r\n". "Response Type :" . "\r\n" . $logData['response_type'] . "\r\n".'====================================================]'."\r\n";
            error_log($log_message, 3, CIAM_PLUGIN_DIR . 'ciam_debug.log');
        }

        if (!empty($response)) {
            if(isset($response->errors)) {
                $error = isset($response->errors['http_request_failed'][0])?$response->errors['http_request_failed'][0]:'An error occurred';
                throw new LoginRadiusException($error, $response);
            }
            elseif(isset($response['body'])) {
                $result = json_decode($response['body']);
                if (isset($result->errorCode) && !empty($result->errorCode)) {
                    throw new LoginRadiusException($result->description, $result);
                }
            }
        }
        
        return $response['body'];
    }

    
}
