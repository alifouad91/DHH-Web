<?php
defined('C5_EXECUTE') or die('Access Denied.');

class DashboardSettingsGeneralController extends DashboardBaseController
{
    public function view()
    {
        $this->set('enableSms', Config::get('ENABLE_SMS'));
        $this->set('smsApiKey', Config::get('SMS_API_KEY'));
        $this->set('smsApiEndpoint', Config::get('SMS_API_ENDPOINT'));
        $this->set('smsApiFromNumber', Config::get('SMS_API_FROM_NUMBER'));

      /*  define('SMS_API_KEY','');
        define('SMS_API_ENDPOINT','');
        define('SMS_API_FROM_NUMBER','5555566666');*/

    }

    public function save()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th = Loader::helper('text');
        $e  = Loader::helper('validation/error');

        $enableSms = $th->sanitize($this->post('enableSms'));
        $smsApiEndpoint = $th->sanitize($this->post('smsApiEndpoint'));
        $smsApiKey = $th->sanitize($this->post('smsApiKey'));
        $smsApiFromNumber = $th->sanitize($this->post('smsApiFromNumber'));

        $enableSms =  $enableSms ? true : false;


        if (!$smsApiEndpoint) {
            $e->add('Endpoint is required.');
        }
        if (!$smsApiKey) {
            $e->add('API key is required');
        }
        if (!$smsApiFromNumber) {
            $e->add('From Number is required');
        }
        if (!$e->has()) {
            Config::save('ENABLE_SMS', $enableSms);
            Config::save('SMS_API_KEY', $smsApiKey);
            Config::save('SMS_API_ENDPOINT', $smsApiEndpoint);
            Config::save('SMS_API_FROM_NUMBER', $smsApiFromNumber);
            $this->set('success_message', 'Setting saved successfully');
        }else
        {
            $this->set('errors',$e->getList());
        }


    }
}

?>