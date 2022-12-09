<?php

/**
 * @link : http://www.loginradius.com
 * @category : Clients
 * @package : DefaultHttpClient
 * @author : LoginRadius Team
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
class DefaultHttpClient implements IHttpClientInterface
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
        if (!isset($parseUrl['scheme']) || empty($parseUrl['scheme'])) {
            $requestUrl .= API_DOMAIN;
        }

        $requestUrl .= $path;
        
        if (defined('API_REGION') && API_REGION != "") {
            $queryArray['region'] = API_REGION;
        }
        if (defined('ORIGIN_IP') && ORIGIN_IP != "") {
            $options['ORIGIN_IP'] = ORIGIN_IP;
        }
        if (defined('REFERER') && REFERER != "") {
            if ($path == "/identity/v2/auth/register" || $path == "/identity/v2/auth/register/captcha") {
                $options['Referer'] = REFERER;
            }
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

        if (in_array('curl', get_loaded_extensions())) {
            $response = $this->curlApiMethod($requestUrl, $options);
        } elseif (ini_get('allow_url_fopen')) {
            $response = $this->fsockopenApiMethod($requestUrl, $options);
        } else {
            throw new LoginRadiusException('cURL or FSOCKOPEN is not enabled, enable cURL or FSOCKOPEN to get response from LoginRadius API.');
        }
        if (!empty($response)) {
            
            if(isset($response['statuscode'])  && $response['statuscode']==429) {    
                $response["response"]=(object) array(
                    "Description"=>"Too Many Request in a particular time frame"
                );

                throw new LoginRadiusException("Too Many Request in a particular time frame",$response["response"]);
          
            } else{

                $result = $response['response'] != "" ? json_decode($response['response']) : "";
                if ((isset($result->ErrorCode) && !empty($result->ErrorCode)) || (isset($result->errorCode) && !empty($result->errorCode)) || (isset($response['statuscode']) && $response['statuscode'] != 200)) {
                    if(isset($result->description)){
                        throw new LoginRadiusException($result->description, $result);
                    } elseif (isset($result->Description)) {
                        throw new LoginRadiusException($result->Description, $result);
                    } else {
                        throw new LoginRadiusException("The request responded with ". $response['statuscode'] . " status code", $response['response']);
                    }
                    
                }
            }
       }
        return $response['response'];
    }

    /**
     * Access LoginRadius API server by curl method
     *
     * @param type $requestUrl
     * @param type $options
     * @return type
     */
    private function curlApiMethod($requestUrl, $options = array())
    {
        $sslVerify = isset($options['ssl_verify']) ? $options['ssl_verify'] : false;
        $method = isset($options['method']) ? strtoupper($options['method']) : 'GET';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $contentType = isset($options['content_type']) ? trim($options['content_type']) : 'x-www-form-urlencoded';
        $authAccessToken = isset($options['access-token']) ? trim($options['access-token']) : '';
        $sottHeaderContent = isset($options['X-LoginRadius-Sott']) ? trim($options['X-LoginRadius-Sott']) : '';
        $secretHeaderContent = isset($options['X-LoginRadius-ApiSecret']) ? trim($options['X-LoginRadius-ApiSecret']) : '';
        $expiryTime = isset($options['X-Request-Expires']) ? trim($options['X-Request-Expires']) : '';
        $digest = isset($options['digest']) ? trim($options['digest']) : '';
        $originIp = isset($options['ORIGIN_IP']) ? trim($options['ORIGIN_IP']) : '';
        $referer = isset($options['Referer']) ? trim($options['Referer']) : '';

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $requestUrl);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 50);
        curl_setopt($curlHandle, CURLOPT_ENCODING, "gzip");
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, $sslVerify);
        $optionsArray = array('Content-type: application/' . $contentType);
        if ($authAccessToken != '') {
            $optionsArray[] = 'Authorization:' . $authAccessToken;
        }
        if ($sottHeaderContent != '') {
            $optionsArray[] = 'X-LoginRadius-Sott:' . $sottHeaderContent;
        }
        if ($secretHeaderContent != '') {
            $optionsArray[] = 'X-LoginRadius-ApiSecret:' . $secretHeaderContent;
        }
        if ($expiryTime != '') {
            $optionsArray[] = 'X-Request-Expires:' . $expiryTime;
        }
        if ($digest != '') {
            $optionsArray[] = 'digest:' . $digest;
        }
        if($originIp!=''){
            $optionsArray[]='X-Origin-IP: '. $originIp;
        } 
        if ($referer != '') {
            $optionsArray[] = 'Referer: ' . $referer;
        }
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $optionsArray);
        if(defined('PROTOCOL') && PROTOCOL != "" && defined('HOST') && HOST != "" && defined('PORT') && PORT != "" && defined('USER') && USER != "" && defined('PASSWORD') && PASSWORD != "") {
            curl_setopt($curlHandle, CURLOPT_PROXY, PROTOCOL . '://' . USER . ':' . PASSWORD . '@' . HOST . ':' . PORT);
        }

        if (!empty($data)) {
            if (($contentType == 'json') && (is_array($data) || is_object($data))) {
                $data = json_encode($data);
            }
        }
            if (in_array($method, array('POST', 'PUT', 'DELETE'))) {
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, (($contentType == 'json') ? $data : Functions::queryBuild($data)));
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, $method);
            }
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        $output = array();
        $output['response'] = curl_exec($curlHandle);
        $output['statuscode'] = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        
        if (curl_error($curlHandle)) {
            $output['response'] = curl_error($curlHandle);
        }
        curl_close($curlHandle);

        return $output;
    }

    /**
     * Access LoginRadius API server by fsockopen method
     *
     * @param type $requestUrl
     * @param type $options
     * @return type
     */
    private function fsockopenApiMethod($requestUrl, $options = array())
    {
        $sslVerify = isset($options['ssl_verify']) ? $options['ssl_verify'] : false;
        $method = isset($options['method']) ? strtoupper($options['method']) : 'GET';
        $data = isset($options['post_data']) ? $options['post_data'] : array();
        $contentType = isset($options['content_type']) ? $options['content_type'] : 'form_params';
        $authAccessToken = isset($options['access-token']) ? trim($options['access-token']) : '';
        $sottHeaderContent = isset($options['X-LoginRadius-Sott']) ? trim($options['X-LoginRadius-Sott']) : '';
        $secretHeaderContent = isset($options['X-LoginRadius-ApiSecret']) ? trim($options['X-LoginRadius-ApiSecret']) : '';
        $expiryTime = isset($options['X-Request-Expires']) ? trim($options['X-Request-Expires']) : '';
        $digest = isset($options['digest']) ? trim($options['digest']) : '';
        $originIp = isset($options['ORIGIN_IP']) ? trim($options['ORIGIN_IP']) : '';
        $referer = isset($options['Referer']) ? trim($options['Referer']) : '';

        $optionsArray = array('http' =>
            array(
                'method' => strtoupper($method),
                'timeout' => 50,
                'ignore_errors' => true,
                'header' => 'Content-Type: application/' . $contentType
            ),
            "ssl" => array(
                "verify_peer" => $sslVerify
            )
        );
        if (!empty($data) || $data === true) {
            if (($contentType == 'json') && (is_array($data) || is_object($data))) {
                $data = json_encode($data);
            }
            $optionsArray['http']['header'] .= "\r\n" . 'Content-Length:' . (($data === true) ? '0' : strlen($data));
            $optionsArray['http']['header'] .= "\r\n" . 'Accept-Encoding: gzip';
            $optionsArray['http']['content'] = (($contentType == 'json') ? $data : Functions::queryBuild($data));
        }
        if ($authAccessToken != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'Authorization: ' . $authAccessToken;
        }
        if ($sottHeaderContent != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'X-LoginRadius-Sott: ' . $sottHeaderContent;
        }
        if ($secretHeaderContent != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'X-LoginRadius-ApiSecret: ' . $secretHeaderContent;
        }
        if ($expiryTime != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'X-Request-Expires: ' . $expiryTime;
        }
        if ($digest != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'digest: ' . $digest;
        }
        if($originIp != ''){
            $optionsArray['http']['header'] .= "\r\n" . 'X-Origin-IP: ' . $originIp;
        }
        if ($referer != '') {
            $optionsArray['http']['header'] .= "\r\n" . 'Referer: ' . $referer;
        }
        
        $context = stream_context_create($optionsArray);
        $jsonResponse['response'] = file_get_contents($requestUrl, false, $context);
        $parseHeaders = Functions::parseHeaders($http_response_header);
        if (isset($parseHeaders['Content-Encoding']) && $parseHeaders['Content-Encoding'] == 'gzip') {
            $jsonResponse['response'] = gzdecode($jsonResponse['response']);
        }
        $jsonResponse['statuscode'] = $parseHeaders['reponse_code'];
        
        if (!$jsonResponse) {
            throw new LoginRadiusException('file_get_contents error');
        }
        return $jsonResponse;
    }
}