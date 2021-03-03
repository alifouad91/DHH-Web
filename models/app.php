<?php
defined('C5_EXECUTE') or die('Access Denied.');

class App
{

    const LOCALE_EN = 'en_US';
    const LOCALE_AR = 'ar_AE';
    const LOCALE_KW = 'ar_KW';
    const LOCALE_SA = 'ar_SA';
    const LOCALE_RU = 'ru_RU';//Russia Ruble
    const LOCALE_DE = 'de_DE';//Germany EUR

    const CURRENCY = [
        App::LOCALE_EN => 'USD',
        App::LOCALE_DE => 'EUR',
        App::LOCALE_AR => 'AED',
        App::LOCALE_SA => 'SAR',
        App::LOCALE_RU => 'RUB',
        App::LOCALE_KW => 'KWD',
    ];

    const COUNTRY_CODE = [
        'US' => self::LOCALE_EN,
        'AE' => self::LOCALE_AR,
        'AD' => self::LOCALE_DE,
        'SA' => self::LOCALE_SA,
        'RU' => self::LOCALE_RU,
        'KW' => self::LOCALE_KW
    ];

    public static function getUserLocal()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        // Load the class
        $ipinfodb = new IPInfoDB();

        $results = $ipinfodb->getCountry($ip);
        return (self::COUNTRY_CODE[$results['countryCode']])?self::COUNTRY_CODE[$results['countryCode']]:CurrencyRates::DEFAULT_LOCALE;
    }

    public static function getTempLocale()
    {
        return isset($_REQUEST['_lcl']) && static::isLocaleValid($_REQUEST['_lcl']) ? $_REQUEST['_lcl'] : '';
    }

    public static function getSessionLocale()
    {
        if (!$_SESSION['ACTIVE_LOCALE']) {
            $_SESSION['ACTIVE_LOCALE'] = self::setSessionLocale(self::getUserLocal());
        }
        return isset($_SESSION['ACTIVE_LOCALE']) ? $_SESSION['ACTIVE_LOCALE'] : CurrencyRates::DEFAULT_LOCALE;
    }

    public static function setSessionLocale($locale)
    {
        $_SESSION['ACTIVE_LOCALE'] = $locale;
        return $locale;
    }

    public static function isLocaleValid($locale)
    {
        return in_array($locale, [self::LOCALE_EN, self::LOCALE_AR], true);
    }
}