<?php
defined('C5_EXECUTE') or die('Access Denied.');

class Google
{

    public static function getGoogleClient()
    {
        $googleClient = new Google_Client([
            'client_id'     => GOOGLE_OAUTH_CLIENT_ID,
            'client_secret' => GOOGLE_OAUTH_CLIENT_SECRET,
            'redirect_uri'  => 'postmessage',
        ]);

        return $googleClient;
    }
}
