<?php
defined('C5_EXECUTE') or die('Access Denied.');

class HttpRequestHelper {

    /**
     * Gets Authorization from HTTP header
     *
     * @return null|string
     */
    public function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }


    /**
     * Gets Bearer Token
     *
     * @return null|string
     */
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        $bearer_token = null;

        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $bearer_token = $matches[1];
            }
        }
        return $bearer_token;
    }

    /**
     * Gets Api Key
     *
     * @return null|string
     */
    public function getAPIToken() {
        $headers = $this->getAPITokenHeader();
        $api_key = null;
        // HEADER: Get the API Key from the header
        if (!empty($headers)) {
            $api_key = $headers;
        }
        return $api_key;
    }

    /**
     * Gets token from HTTP header
     *
     * @return null|string
     */
    public function getAPITokenHeader(){
        $headers = null;;
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Token'])) {
                $headers = trim($requestHeaders['Token']);
            }
        }
        return $headers;
    }
}