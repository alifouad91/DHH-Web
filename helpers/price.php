<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::library('3rdparty/Zend/Currency/CurrencyInterface');
Loader::library('3rdparty/Zend/Currency');

class PriceHelper implements Zend_Currency_CurrencyInterface
{

    static $symbol        = '';
    static $thousands     = '';
    static $decimal       = '';
    static $leftPlacement = '';

    public function getRate($from, $to)
    {
//        if ($from !== CurrencyRates::DEFAULT_CURRENCY) {
//            throw new Exception('We can only exchange '.CurrencyRates::DEFAULT_CURRENCY);
//        }
        $cr = new CurrencyRates();

        switch ($to) {
            case 'EUR':
                return $cr->getEur();
            case 'USD':
                return $cr->getUsd();
            case 'SAR':
                return $cr->getSar();
            case 'RUB':
                return $cr->getRub();
            case 'KWD':
                return $cr->getKwd();
            default :
                return 1;
        }

        throw new Exception("Unable to exchange {$to}");
    }

    public static function getThousandsSeparator()
    {
        if (empty(self::$thousands)) {
            self::$thousands = CURRENCY_THOUSANDS_SEPARATOR;
            if (empty(self::$thousands)) {
                self::$thousands = ',';
            }
        }
        return self::$thousands;
    }

    public static function getDecimalPoint()
    {
        if (empty(self::$decimal)) {
            self::$decimal = CURRENCY_DECIMAL_POINT;
            if (empty(self::$decimal)) {
                self::$decimal = '.';
            }
        }
        return self::$decimal;
    }

    /**
     * @param $number
     * @param string $locale
     * @param bool $symbol
     * @return float|string
     * @throws Zend_Currency_Exception
     */
    public static function format($number, $locale = false, $symbol = true)
    {
		/** @var Zend_Currency $currency */

		$locale = $locale ? $locale : App::getSessionLocale();

		if ($locale == CurrencyRates::DEFAULT_LOCALE) {
			$data     = [
				'value'    => $number,
				'currency' => DEFAULT_CURRENCY
			];
			$currency = new Zend_Currency($data,$locale);
			return $symbol ? self::toCurrency(SELF::getCurrency($locale), $currency->getValue()) : $currency->getValue();
		}

		$data     = [
			'value'    => $number,
			'currency' => DEFAULT_CURRENCY
		];
		$currency = new Zend_Currency($locale);
		$service  = new PriceHelper();
		$currency->setService($service);
		$currency2 = new Zend_Currency($data);

		$currency->setValue($currency2);
		return $symbol ? self::toCurrency(SELF::getCurrency($locale), $currency->getValue()) : $currency->getValue();
    }

    public static function toCurrency($symbol, $number) {
		return ($number < 0 ? '- ' : '') . $symbol . number_format(
				abs($number),
				2,
				'.',
				',');
	}

    public static function getCurrency($locale)
    {
        switch ($locale) {
            case APP::LOCALE_EN :
                return 'USD';
            case APP::LOCALE_AR :
                return 'AED';
            case APP::LOCALE_KW :
                return 'KWD';
            case APP::LOCALE_SA :
                return 'SAR';
            case APP::LOCALE_RU :
                return 'RUB';//Russia Ruble
            case APP::LOCALE_DE :
            default :
                return 'EUR';
        }
    }
}
