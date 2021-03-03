<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 25/2/19
 * Time: 12:45 PM
 */
class CurrencyRates
{
    protected $eur;
    protected $aed;
    protected $sar;
    protected $rub;
    protected $kwd;
    protected $usd;

    const DEFAULT_CURRENCY = 'AED';
    const DEFAULT_LOCALE = 'ar_AE';

    public function __construct()
    {
        $db = Loader::db();
        $q = "SELECT * FROM CurrencyRates";
        $res = $db->GetRow($q);

        if (is_array($res) && count($res))
        {
            $this->setEur($res['EUR']);
            $this->setUsd($res['USD']);
            $this->setSar($res['SAR']);
            $this->setRub($res['RUB']);
            $this->setKwd($res['KWD']);
        }
    }

    public static function updateRates($data)
    {
        $db = Loader::db();

        $query = "DELETE FROM CurrencyRates WHERE 1";
        $db->Execute($query);

        $q = "INSERT INTO CurrencyRates  (EUR, USD, SAR, RUB, KWD) VALUES (?, ?, ?, ?, ?)";
        $db->Execute($q,[$data['eur'],$data['usd'],$data['sar'],$data['rub'],$data['kwd']]);


        return null;
    }
    /**
     * @return mixed
     */
    public function getEur()
    {
        return $this->eur;
    }

    /**
     * @param mixed $eur
     */
    public function setEur($eur)
    {
        $this->eur = $eur;
    }

    /**
     * @return mixed
     */
    public function getAed()
    {
        return $this->aed;
    }

    /**
     * @param mixed $aed
     */
    public function setAed($aed)
    {
        $this->aed = $aed;
    }

    /**
     * @return mixed
     */
    public function getUsd()
    {
        return $this->usd;
    }

    /**
     * @param mixed $aed
     */
    public function setUsd($usd)
    {
        $this->usd = $usd;
    }

    /**
     * @return mixed
     */
    public function getSar()
    {
        return $this->sar;
    }

    /**
     * @param mixed $sar
     */
    public function setSar($sar)
    {
        $this->sar = $sar;
    }

    /**
     * @return mixed
     */
    public function getRub()
    {
        return $this->rub;
    }

    /**
     * @param mixed $rub
     */
    public function setRub($rub)
    {
        $this->rub = $rub;
    }

    /**
     * @return mixed
     */
    public function getKwd()
    {
        return $this->kwd;
    }

    /**
     * @param mixed $kwd
     */
    public function setKwd($kwd)
    {
        $this->kwd = $kwd;
    }

}