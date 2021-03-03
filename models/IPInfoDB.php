<?php

class IPInfoDB
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = '7c2368b4f6766be8d1663e0da4ff06673fb0804016f9790c0cdbecc8f64f757d';
    }

    public function getCountry($ip)
    {
        $response = @file_get_contents('http://api.ipinfodb.com/v3/ip-country?key=' . $this->apiKey . '&ip=' . $ip . '&format=json');

        if (($json = json_decode($response, true)) === null) {
            $json['statusCode'] = 'ERROR';
//            Log::addEntry(json_encode($json));
            return false;
        }

        $json['statusCode'] = 'OK';

        return $json;
    }

    public function getCity($ip)
    {
        $response = @file_get_contents('http://api.ipinfodb.com/v3/ip-city?key=' . $this->apiKey . '&ip=' . $ip . '&format=json');

        if (($json = json_decode($response, true)) === null) {
            return false;
        }

        $json['statusCode'] = 'OK';

        return $json;
    }
}