<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 18/2/19
 * Time: 11:26 AM
 */

class SMS
{
    const ACTION   = 'send-sms';
    const RESPONSE = 'json';

    /**
     * @input $to int/number
     * $form int/number
     */
    public static function send($to, $form, $body)
    {
        $params = [
            'action'   => self::ACTION,
            'api_key'  => Config::get('SMS_API_KEY'),
            'to'       => $to,
            'from'     => $form,
            'sms'      => $body,
            'response' => self::RESPONSE
        ];

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get(Config::get('SMS_API_ENDPOINT'), [
                'query' => $params
            ]);

            $response = json_decode($response->getBody()->getContents());
            if ($response->code == 'ok') {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }

    }

}