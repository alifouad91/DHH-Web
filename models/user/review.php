<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class Review
{

	protected $id;
	protected $uId;
	protected $pId;
	protected $bId;
	protected $reviewRating;
	protected $reviewComment;
	protected $createdAt;
	protected $updatedAt;
	protected $updatedCnt;

	public $ui;
	public $booking;
	public $property;

	const REVIEW_EDITS = 2;

	/**
	 * Review constructor.
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->id            = $data['id'];
		$this->uId           = $data['uId'];
		$this->pId           = $data['pId'];
		$this->bId           = $data['bId'];
		$this->reviewRating  = $data['reviewRating'];
		$this->reviewComment = $data['reviewComment'];
		$this->createdAt     = $data['createdAt'];
		$this->updatedAt     = $data['updatedAt'];
		$this->updatedCnt    = $data['updatedCnt'];
		$this->ui            = null;
		$this->property      = null;
		$this->booking       = null;
	}

	/**
	 * @param int $id
	 * @return null|static
	 */
	public static function getById($id)
	{
		$query = 'SELECT * FROM Reviews WHERE id = ?';
		$db    = Loader::db();
		$row   = $db->GetRow($query, [$id]);

		if (isset($row['id'])) {
			return new static($row);
		}

		return null;
	}

	/**
	 * @param int $uId
	 * @param int $pId
	 * @param int $bId
	 * @return null|static
	 */
	public static function get($uId, $pId, $bId)
	{
		$query = 'SELECT * FROM Reviews WHERE uId = ? AND pId = ? AND bId = ?';
		$db    = Loader::db();
		$row   = $db->GetRow($query, [$uId, $pId, $bId]);

		if (isset($row['id'])) {
			return new static($row);
		}

		return null;
	}

	public static function getByBookingID($bID)
	{
		$db    = Loader::db();
		$query = "SELECT * FROM  Reviews where bId = ? ";
		$row   = $db->GetRow($query, [$bID]);

		if (isset($row['id'])) {
			return new Review($row);
		}
		return null;
	}

	public static function getByBookingIDAndUserID($bID, $userID)
	{
		$db    = Loader::db();
		$query = "SELECT * FROM  Reviews WHERE bId = ? AND uId = ?";
		$row   = $db->GetRow($query, [$bID, $userID]);

		if (isset($row['id'])) {
			return new Review($row);
		}
		return null;
	}

	/**
	 * @param int $uId
	 * @param int $pId
	 * @param int $bId
	 * @param int reviewRating
	 * @param string $reviewComment
	 * @return null|Review
	 */
	public static function add($uId, $pId, $bId, $reviewRating, $reviewComment)
	{
		$dh         = Loader::helper('date');
		$updatedCnt = 0;

		$date = $dh->date('Y-m-d H:i:s');

		$query = 'INSERT INTO Reviews (uId, pId, bId, reviewRating, reviewComment, updatedCnt, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
		$db    = Loader::db();
		$db->Execute($query, [$uId, $pId, $bId, $reviewRating, $reviewComment, $updatedCnt, $date, $date]);

		return self::getById($db->Insert_ID());
	}

	/**
	 * @param int $reviewRating
	 * @param string $reviewComment
	 * @param int $reviewCnt
	 * @return null|Review
	 */
	public function update($reviewRating, $reviewComment, $reviewCnt)
	{
		$dh = Loader::helper('date');

		$date = $dh->date('Y-m-d H:i:s');

		$query = 'UPDATE Reviews SET reviewRating = ?, reviewComment = ?, updatedAt = ?, updatedCnt = ? WHERE uId = ? AND pId = ? AND bId =?';
		$db    = Loader::db();
		$db->Execute($query, [$reviewRating, $reviewComment, $date, $reviewCnt, $this->getUserId(), $this->getPropertyId(), $this->getBookingId()]);

		return self::getById($db->Affected_Rows());
	}

	public function updateAdmin($uId, $pId, $bId, $reviewRating, $reviewComment)
	{
		$dh = Loader::helper('date');

		$date = $dh->date('Y-m-d H:i:s');

		$query = 'UPDATE Reviews SET uId = ?, pId = ?, bId = ?, reviewRating = ?, reviewComment = ?, updatedAt = ? WHERE id =?';
		$db    = Loader::db();
		$db->Execute($query, [$uId, $pId, $bId, $reviewRating, $reviewComment, $date, $this->getId()]);

		return self::getById($db->Affected_Rows());
	}

	/**
	 * @return int
	 */
	public static function getReviewEditCount($uId, $pId, $bId)
	{
		$db    = Loader::db();
		$query = "SELECT updatedCnt FROM Reviews WHERE uId = ? AND pId = ? AND bId =?";
		$row   = $db->GetRow($query, [$uId, $pId, $bId]);
		return $row['updatedCnt'];
	}

	/**
	 * @param int $pId
	 * @param int $bId
	 * @return int
	 */
	public static function getCount($pId, $bId)
	{
		return (int) Loader::db()->GetOne('SELECT COUNT(*) AS count FROM Reviews WHERE pId = ? AND bId = ?', [$pId, $bId]);
	}

	public function getId()
	{
		return $this->id;
	}

	public function getUserId()
	{
		return $this->uId;
	}

	public function getPropertyId()
	{
		return $this->pId;
	}

	public function getBookingId()
	{
		return $this->bId;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}


	public function delete()
	{
		$query = 'DELETE FROM Reviews WHERE id = ?';
		$db    = Loader::db();
		$db->Execute($query, [$this->getId()]);
	}

	/**
	 * @return null|UserInfo
	 */
	public function getUserInfo()
	{
		if (null === $this->ui) {
			$this->ui = UserInfo::getByID($this->getUserId());
		}

		return $this->ui;
	}

	/**
	 * @return null|Property
	 */
	public function getProperty()
	{
		if (null === $this->property) {
			$this->property = Property::getByID($this->getPropertyId());
		}

		return $this->property;
	}

	/**
	 * @return Booking|null
	 */
	public function getBooking()
	{
		if (null === $this->booking) {
			$this->booking = Booking::getByID($this->getBookingId());
		}

		return $this->booking;
	}

	/**
	 * @return mixed
	 */
	public function getReviewRating()
	{
		return $this->reviewRating;
	}

	/**
	 * @return mixed
	 */
	public function getReviewComment()
	{
		return $this->reviewComment;
	}

	/**
	 * @return mixed
	 */
	public function getUpdateCount()
	{
		return $this->updatedCnt;
	}

	/**
	 * @param mixed $updatedCnt
	 */
	public function setUpdateCount($updatedCnt)
	{
		$this->updatedCnt = $updatedCnt;
	}

	public function isEditable()
	{
		return ($this->getUpdateCount() >= Review::REVIEW_EDITS) ? false : true;
	}
}
