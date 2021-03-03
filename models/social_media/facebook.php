<?php
defined('C5_EXECUTE') or die('Access Denied.');

class Facebook
{

    public static function getFacebookService()
    {
        return new Facebook\Facebook([
            'app_id'                => FACEBOOK_APP_ID,
            'app_secret'            => FACEBOOK_APP_SECRET,
            'default_graph_version' => FACEBOOK_GRAPH_VERSION,
        ]);
    }

    public static function getURL()
    {
        $fb     = self::getFacebookService();
        $helper = $fb->getRedirectLoginHelper();
        return $helper->getLoginUrl(BASE_URL . DIR_REL . FB_REDIRECT_URL);
    }
}
