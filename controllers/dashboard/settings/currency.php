<?php
defined('C5_EXECUTE') or die('Access Denied.');

class DashboardSettingsCurrencyController extends DashboardBaseController
{
    public function view()
    {
        $currency = new CurrencyRates();

        $this->set('currency', $currency);
        $this->set('success_message', '');
    }

    public function save()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th = Loader::helper('text');
        $e  = Loader::helper('validation/error');


        $eur      = $th->sanitize($this->post('eur'));
        $usd            = $th->sanitize($this->post('usd'));
        $sar     = $th->sanitize($this->post('sar'));
        $rub  = $th->sanitize($this->post('rub'));
        $kwd = $th->sanitize($this->post('kwd'));

        if (!$eur) {
            $e->add('Euro is required');
        }

        if (!$usd) {
            $e->add('American Dollar is required');
        }
        if (!$sar) {
            $e->add('Saudi Riyal is required');
        }
        if (!$rub) {
            $e->add('Russian Ruble is required');
        }
        if (!$kwd) {
            $e->add('Kuwaiti Dinar is required');
        }
        if (!$e->has()) {
            $data['eur'] = floatval($eur);
            $data['usd'] = floatval($usd);
            $data['sar'] = floatval($sar);
            $data['rub'] = floatval($rub);
            $data['kwd'] = floatval($kwd);

            CurrencyRates::updateRates($data);

            $this->set('success_message', 'Currency Rates saved successfully');
            $currency = new CurrencyRates();

            $this->set('currency', $currency);
        }
    }
}

?>