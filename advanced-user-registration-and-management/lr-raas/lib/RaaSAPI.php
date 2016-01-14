<?php

/**
 * @link                : http://www.loginradius.com
 * @category            : LoginRadius_RaaS
 * @package             : RaaSAPI
 * @author              : LoginRadius Team
 * @license             : http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
global $loginradius_api_settings;
define('API_DOMAIN', 'https://api.loginradius.com');

/**
 * Class RaaSAPI
 */
class RaaSAPI {

    /**
     * Raas API authentication
     * @param type $array
     * @return type
     */
    public static function authentication($array = array()) {
        $result = array(
            "appkey" => RAAS_API_KEY,
            "appsecret" => RAAS_SECRET_KEY
        );
        if (is_array($array) && sizeof($array) > 0) {
            $result = array_merge($result, $array);
        }
        return $result;
    }

    /**
     * build array to sting
     * 
     * @param type $data
     * @return type
     */
    public static function query_build($data = array()) {
        if (is_array($data) && sizeof($data) > 0) {
            return http_build_query($data);
        } else {
            return $data;
        }
    }

    /**
     * 
     * @param type $path
     * @param type $queryArray
     * @param type $post
     * @param type $contentType
     * @return type
     */
    public static function api_client($path, $queryArray = array(), $post = '', $contentType = 'application/x-www-form-urlencoded') {
        $parseurl = parse_url($path);
        if (isset($parseurl['scheme']) && !empty($parseurl['scheme'])) {
            $validateUrl = $path;
        } else {
            $validateUrl = API_DOMAIN . $path;
        }
        if ($queryArray !== false) {
            $validateUrl .= '?' . self::query_build(self::authentication($queryArray));
        }
        if (self::get_api_method()) {
            $response = self::curl_api_method($validateUrl, $post, $contentType);
        } else {
            $response = self::fsockopen_api_method($validateUrl, $post, $contentType);
        }
        return $response;
    }

    /**
     * 
     * @return type
     */
    private static function get_api_method() {
        return function_exists('curl_version');
    }

    /**
     * 
     * @param type $validateUrl
     * @param type $data
     * @param type $contentType
     * @return type
     */
    private static function curl_api_method($validateUrl, $data, $contentType) {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $validateUrl);
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 15);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($data) || $data === true) {
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-type: ' . $contentType));
            curl_setopt($curlHandle, CURLOPT_POST, 1);
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, self::query_build($data));
        }

        if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == 'Off' or ! ini_get('safe_mode'))) {
            curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        } else {
            curl_setopt($curlHandle, CURLOPT_HEADER, 1);
            $effactiveUrl = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);
            curl_close($curlHandle);
            $curlHandle = curl_init();
            $url = str_replace('?', '/?', $effactiveUrl);
            curl_setopt($curlHandle, CURLOPT_URL, $url);
            curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        }

        $jsonResponse = curl_exec($curlHandle);
        curl_close($curlHandle);
        return ($jsonResponse);
    }

    /**
     * 
     * @param type $validateUrl
     * @param type $data
     * @param type $contentType
     * @return type
     */
    private static function fsockopen_api_method($validateUrl, $data, $contentType) {
        if (!empty($data) || $data === true) {
            $options = array('http' =>
                array(
                    'method' => 'POST',
                    'timeout' => 15,
                    'header' => 'Content-type :' . $contentType,
                    'content' => self::query_build($data)
                )
            );
            $context = stream_context_create($options);
        } else {
            $context = NULL;
        }
        $JsonResponse = file_get_contents($validateUrl, false, $context);
        return json_decode($JsonResponse);
    }

}
