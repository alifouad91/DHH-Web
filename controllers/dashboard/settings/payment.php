<?php
defined('C5_EXECUTE') or die('Access Denied.');

class DashboardSettingsPaymentController extends DashboardBaseController
{
    public function view()
    {
        $this->set('vatCharge', Config::get('VAT_PERCENT'));

        //$this->set('dirhamFee', Config::get('DIRHAM_FEE'));

        $this->set('mID', Config::get('MERCHANT_ID'));
        $this->set('accessCode', Config::get('ACCESS_CODE'));
        $this->set('encryptionKey', Config::get('ENCRYPTION_KEY'));
        $this->set('referralCredit', Config::get('REFERRAL_CREDIT'));

        $this->set('success_message', '');
    }

    public function save()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th = Loader::helper('text');
        $e  = Loader::helper('validation/error');


        $vatCharge      = $th->sanitize($this->post('vatCharge'));
        //$dirhamFee      = $th->sanitize($this->post('dirhamFee'));
        $mID            = $th->sanitize($this->post('mID'));
        $accessCode     = $th->sanitize($this->post('accessCode'));
        $encryptionKey  = $th->sanitize($this->post('encryptionKey'));
        $referralCredit = $th->sanitize($this->post('referralCredit'));

        if (!$vatCharge) {
            $e->add('Vat Charge is required');
        }
//        if (!$dirhamFee) {
//            $e->add('Dirham Fee is required');
//        }
        if (!$mID) {
            $e->add('Merchant ID is required');
        }
        if (!$accessCode) {
            $e->add('Access Code is required');
        }
        if (!$encryptionKey) {
            $e->add('Encryption Key is required');
        }
        if (!$referralCredit) {
            $e->add('Referral Credit is required');
        }
        if (!$e->has()) {
            if ($vatCharge) {
                Config::save('VAT_PERCENT', $vatCharge);
            }
//            if ($dirhamFee) {
//                Config::save('DIRHAM_FEE', $dirhamFee);
//            }
            if ($mID) {
                Config::save('MERCHANT_ID', $mID);
            }
            if ($accessCode) {
                Config::save('ACCESS_CODE', $accessCode);
            }
            if ($encryptionKey) {
                Config::save('ENCRYPTION_KEY', $encryptionKey);
            }
            if ($encryptionKey) {
                Config::save('REFERRAL_CREDIT', $referralCredit);
            }
            $this->set('success_message', 'Setting saved successfully');
        }
    }
}

?>