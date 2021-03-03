<?php
defined('C5_EXECUTE') or die('Access Denied.');

class PaymentController extends Concrete5_Controller_Profile
{
    public function __construct()
    {
        $u = new User();
        if (User::isLoggedIn()) {
            $profile = UserInfo::getByID($u->getUserID());
            if (!is_object($profile)) {
                throw new Exception('Invalid User ID.');
            }
        } else {
            $this->set('intro_msg', t('You must sign in order to access this page!'));
            Loader::controller('/login');
            $this->render('/login');
        }

        $this->set('profile', $profile);
        $this->set('u', $u);

    }

    public function view($task = '')
    {
        $this->set('t', Loader::helper('text'));
        if($task){
            $this->set('task', $task);
        } else {
            $this->set('task', 'payment');
        }
    }

    public function submit()
    {
        $th        = Loader::helper('text');
        $htmlHelper = Loader::helper('html');
        $bookingNo = $th->sanitize($this->post('bID'));
        $billing_name = $th->sanitize($this->post('billing_first_name')).' '.$th->sanitize($this->post('billing_last_name'));
        $billing_firstName = $th->sanitize($this->post('billing_first_name'));
        $billing_lastName = $th->sanitize($this->post('billing_last_name'));
        $billing_no = $th->sanitize($this->post('billing_phone'));
        $billing_email = $th->sanitize($this->post('billing_email'));
        $billing_address = $th->sanitize($this->post('billing_address'));
        $billing_city = $th->sanitize($this->post('billing_city'));
        $billing_country = $th->sanitize($this->post('billing_country'));


        $u = new User();

        UserInfo::updateBillingDetails($u->getUserID(),$billing_firstName, $billing_lastName, $billing_no, $billing_email, $billing_address, $billing_city, $billing_country);

        $booking = Booking::getByID($bookingNo);

        $working_key   = Config::get('ENCRYPTION_KEY');
        $access_code   = Config::get('ACCESS_CODE');
        $merchantID    = Config::get('MERCHANT_ID');
        $merchant_data = '';


        $postFields = [
            'merchant_id'         => $merchantID,
            'order_id'            => $booking->getBookingNo(),
            'amount'              => $booking->getTotal(),
            'currency'            => CurrencyRates::DEFAULT_CURRENCY,
            'redirect_url'        => BASE_URL.View::url('payment/makePayment/'.$bookingNo),
            'cancel_url'          => BASE_URL.View::url('payment/cancelPayment'),
            'language'            => 'EN',
            'billing_name'        => $billing_name,
            'billing_address'     => $billing_address,
            'billing_city'        => $billing_city,
            'billing_state'       => '',
            'billing_zip'         => '',
            'billing_country'     => $billing_country,
            'billing_tel'         => $billing_no,
            'billing_email'       => $billing_email,
            'delivery_name'       => '',
            'delivery_address'    => '',
            'delivery_city'       => '',
            'delivery_state'      => '',
            'delivery_zip'        => '',
            'delivery_country'    => '',
            'delivery_tel'        => '',
            'merchant_param1'     => '',
            'merchant_param2'     => '',
            'merchant_param3'     => '',
            'merchant_param4'     => '',
            'merchant_param5'     => '',
            'promo_code'          => '',
            'customer_identifier' => '',
            'submit'              => 'Payment',
            'integration_type'    => 'iframe_normal',
        ];


        foreach ($postFields as $key => $value) {
            $merchant_data .= $key . '=' . $value . '&';
        }

        $encrypted_data= CCAvenuePaymentSetup::encrypt($merchant_data,$working_key); // Method for encrypting the data.

        $production_url='https://secure.ccavenue.ae/transaction/transaction.do?command=initiateTransaction&encRequest='.$encrypted_data.'&access_code='.$access_code;

        $this->set('task', 'submit');
        $this->set('production_url', $production_url);
        $this->addFooterItem($htmlHelper->javascript('ccAvenuePayment.js'));
    }

    public function makePayment($bookingNo)
    {
        $working_key   = Config::get('ENCRYPTION_KEY');
        $encResponse=$this->post('encResp');			//This is the response sent by the CCAvenue Server

        $rcvdString=CCAvenuePaymentSetup::decrypt($encResponse,$working_key);		//Crypto Decryption used as per the specified working key.
        $order_status="";
        $decryptValues=explode('&', $rcvdString);
        $dataSize=sizeof($decryptValues);

        $booking = Booking::getByID($bookingNo);
        $u = new User();
        $uID = $u->getUserID();

        $responseArr = [];
        for($i = 0; $i < $dataSize; $i++)
        {
            $information=explode('=',$decryptValues[$i]);
            $responseArr[$information[0]] = $information[1];
            if($i==3){
                $order_status=$information[1];
            }
        }

        if($order_status == 'Success'){
            $booking->updatePaymentStatus('paid');
        }
        else{
            $booking->updatePaymentStatus('payment_failed');
        }

        Payment::add($uID,$booking,$responseArr);

        $this->set('status', $order_status);
        $this->view('afterSubmit');
    }

    public function cancelPayment(){
        $this->view('cancelled');
    }
}
