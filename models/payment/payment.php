<?php
defined('C5_EXECUTE') or die('Access Denied.');

class Payment
{
	protected $bpID;
	protected $uID;
	protected $bID;
	protected $bookingNo;
	protected $total;
	protected $discountReceived;
	protected $orderStatus;
	protected $orderID;
	protected $trackingID;
	protected $message;

	public function __construct($row)
	{
		$this->setPropertiesFromArray($row);
	}

	public function setPropertiesFromArray($arr)
	{
		foreach ($arr as $key => $prop) {
			$this->{$key} = $prop;
		}
	}

	public static function add($uID, $booking, $responseArr)
	{
		$db               = Loader::db();
		$bID              = $booking->getBID();
		$bookingNo        = $booking->getBookingNo();
		$total            = $booking->getTotal();
		$discountReceived = $booking->getDiscountReceived();
		$orderStatus      = ($responseArr['order_status']) ? $responseArr['order_status'] : '';
		$orderID          = $responseArr['order_id'];
		$trackingID       = $responseArr['tracking_id'];
		$message          = serialize($responseArr);

		$query = "INSERT INTO BookingPayment( uID, bID, bookingNo, total, discountReceived, orderStatus, orderID, trackingID, message ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ? )";
		$ret   = $db->Execute($query, [$uID, $bID, $bookingNo, $total, $discountReceived, $orderStatus, $orderID, $trackingID, $message]);
		if ($ret) {
			$payment = self::getByID($db->Insert_ID());
			return $payment;
		}
		return null;
	}

	/**
	 * @return mixed
	 */
	public function getID()
	{
		return $this->bpID;
	}

	/**
	 * @return mixed
	 */
	public function getBpID()
	{
		return $this->bpID;
	}

	/**
	 * @return mixed
	 */
	public function getUID()
	{
		return $this->uID;
	}

	/**
	 * @return mixed
	 */
	public function getBID()
	{
		return $this->bID;
	}

	/**
	 * @return mixed
	 */
	public function getBookingNo()
	{
		return $this->bookingNo;
	}

	/**
	 * @return mixed
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * @return mixed
	 */
	public function getDiscountReceived()
	{
		return $this->discountReceived;
	}

	/**
	 * @return mixed
	 */
	public function getOrderStatus()
	{
		return $this->orderStatus;
	}

	/**
	 * @return mixed
	 */
	public function getOrderID()
	{
		return $this->orderID;
	}

	/**
	 * @return mixed
	 */
	public function getTrackingID()
	{
		return $this->trackingID;
	}

	/**
	 * @return mixed
	 */
	public function getMessage()
	{
		return $this->message;
	}

	public static function getByID($bpID)
	{
		$db    = Loader::db();
		$query = "SELECT * FROM  BookingPayment where bpID = ? ";
		$row   = $db->GetRow($query, [$bpID]);

		if (isset($row['bpID'])) {
			return new CCAvenuePaymentSetup($row);
		}
		return null;
	}

	public static function getByBookingNo($bookingNo)
	{
		$db    = Loader::db();
		$query = "SELECT * FROM  BookingPayment where bookingNo = ? ";
		$row   = $db->GetRow($query, [$bookingNo]);

		if (isset($row['bpID'])) {
			return new CCAvenuePaymentSetup($row);
		}
		return null;
	}

	public static function getByBookingID($bID)
	{
		$db    = Loader::db();
		$query = "SELECT * FROM  BookingPayment where bID = ? ";
		$row   = $db->GetRow($query, [$bID]);

		if (isset($row['bpID'])) {
			return new CCAvenuePaymentSetup($row);
		}
		return null;
	}
}