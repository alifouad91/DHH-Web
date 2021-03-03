<?php
defined('C5_EXECUTE') or die('Access Denied.');

class Notification
{
    const CATEGORY_UPCOMING_BOOKING = 'upcoming_booking';
    const CATEGORY_PAST_BOOKING     = 'past_booking';
    const CATEGORY_PROPERTY_REVIEW  = 'property_review';
    const CATEGORY_PROPERTY_BOOKED  = 'property_booked';
    const CATEGORY_UTILITY          = 'utility';

    const TYPE_BOOKING  = 'booking';
    const TYPE_UTILITY  = 'utility';
    const TYPE_PROPERTY = 'property';
    const TYPE_REVIEW   = 'review';
    const TYPE_GENERAL  = 'custom';

    const TITLE    = 'title';
    const SUBTITLE = 'subtitle';
    const BODY     = 'body';

    protected $messages = [
        self::CATEGORY_UPCOMING_BOOKING => [
            self::TITLE    => 'Upcoming Booking',
            self::SUBTITLE => '',
            self::BODY     => 'You have upcoming booking in %d %s',
        ],
        self::CATEGORY_PAST_BOOKING     => [
            self::TITLE    => 'Booking Completed',
            self::SUBTITLE => '',
            self::BODY     => 'Booking Completed',
        ],
        self::CATEGORY_PROPERTY_REVIEW  => [
            self::TITLE    => 'Property Reviewed',
            self::SUBTITLE => '',
            self::BODY     => '%s reviewed your property!',
        ],
        self::CATEGORY_PROPERTY_BOOKED  => [
            self::TITLE    => 'Property Booked',
            self::SUBTITLE => '',
            self::BODY     => '%s booked your property!',
        ],
        self::CATEGORY_UTILITY          => [
            self::TITLE    => 'Utility Bill uploaded',
            self::SUBTITLE => '',
            self::BODY     => 'Utility Bill uploaded',
        ]
    ];

    protected $links = [
        self::CATEGORY_UPCOMING_BOOKING => '/profile/mybookings',
        self::CATEGORY_PAST_BOOKING     => '/profile/mybookings',
        self::CATEGORY_PROPERTY_BOOKED  => '/profile/mybookings',
        self::CATEGORY_PROPERTY_REVIEW  => '/profile/property-reviews',
        self::CATEGORY_UTILITY          => '/finances',
    ];

    protected $nID;
    protected $category;
    protected $uID;
    protected $type;
    protected $link;
    protected $title;
    protected $subtitle;
    protected $body;
    protected $contentID;
    protected $createdAt;
    protected $read;

    /**
     * @param $arr
     */
    public function setPropertiesFromArray($arr)
    {
        foreach ($arr as $key => $prop) {
            $this->{$key} = $prop;
        }
    }

    public static function add($uID = null, $category, $type, $contentID = null, $link = null, $title = null, $subtitle = null, $body = null)
    {
        $now = date('Y-m-d H:i:s');

        $db    = Loader::db();
        $uID   = self::populateUID($category, $type, $contentID, $uID);
        if (!$uID) {
        	return null;
        }
        $title = self::populateTitle($category, $title);
        $body  = self::formatMessage($category, $type, $body, $contentID);
        $link  = self::getLinkURL($category, $link);
        $query = "INSERT INTO Notifications(uID, category, type, link, title, subtitle, body, contentID, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $ret   = $db->Execute($query, [$uID, $category, $type, $link, $title, $subtitle, $body, $contentID, $now]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public static function addIfNew($uID = null, $category, $type, $contentID = null, $link = null, $title = null, $subtitle = null, $body = null)
    {
        $uID   = self::populateUID($category, $type, $contentID, $uID);
        $title = self::populateTitle($category, $title);
        $body  = self::formatMessage($category, $type, $body, $contentID);
        $link  = self::getLinkURL($category, $link);
        $isExist = static::where([
                                     'uID'       => $uID,
                                     'category'  => $category,
                                     'type'      => $type,
                                     'link'      => $link,
                                     'title'     => $title,
                                     'subtitle'  => $subtitle,
                                     'body'      => $body,
                                     'contentID' => $contentID,
                                 ]);

        if ($isExist) {
            return $isExist[0];
        }

        return static::add($uID, $category, $type, $contentID, $link, $title, $subtitle, $body);
    }

    public static function emptyTable()
    {
        $db    = Loader::db();
        $query = "TRUNCATE TABLE Notifications";
        $ret   = $db->Execute($query);

        return $ret;
    }

    public function update($category, $type, $link, $title, $subtitle, $body, $contentID)
    {
        $db    = Loader::db();
        $query = "UPDATE Notifications SET type = ?, link = ?, title = ?, subtitle = ?, body = ?  WHERE nID = ?";
        $ret   = $db->Execute($query, [$category, $type, $link, $title, $subtitle, $body, $contentID, $this->getID()]);

        if ($ret) {
            return $this;
        }

        return null;
    }

    public function updateReadStatus($status = 1)
    {
        $db    = Loader::db();
        $query = "UPDATE Notifications SET `read` = ? WHERE nID = ?";
        $ret   = $db->Execute($query, [$status, $this->getID()]);

        if ($ret) {
            return $this;
        }

        return null;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM Notifications WHERE nID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }

    public static function deleteAll($userID)
    {
        $db    = Loader::db();
        $query = "DELETE FROM Notifications WHERE uID = ?";
        $ret   = $db->Execute($query, [$userID]);

        return $ret;
    }


    public static function getByID($nID)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Notifications WHERE nID = ?";
        $row   = $db->GetRow($query, [$nID]);

        if (isset($row['nID'])) {
            $notification = new Notification();
            $notification->setPropertiesFromArray($row);
            return $notification;
        }

        return null;
    }

    public static function where($condition = null)
    {
        $condition_vars   = [];
        $condition_suffix = '1';

        if (!is_array($condition)) {
            if ($condition) {
                $condition_suffix = $condition;
            }
        } else {

            $condition_suffix = '';
            $last_key         = end(array_keys($condition));

            foreach ($condition as $key => $value) {

                if ($value === null) {
                    $condition_suffix .= $key . ' IS NULL';
                } else {
                    $condition_suffix .= $key . ' = ?';
                    $condition_vars[] = $value;
                }

                if ($key !== $last_key) {
                    $condition_suffix .= " AND ";
                }
            }
        }

        $db     = Loader::db();
        $query  = "SELECT * FROM Notifications WHERE {$condition_suffix}";
        $result = $db->Execute($query, $condition_vars);

        $links = [];

        while ($row = $result->FetchRow()) {
            $notification = new Notification();
            $notification->setPropertiesFromArray($row);
            $links[] = $notification;
        }

        return $links;
    }

    public static function getLastNotification()
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Notifications WHERE 1 ORDER BY createdAt LIMIT 1";
        $row   = $db->GetRow($query);

        if (isset($row['nID'])) {
            $notification = new Notification();
            $notification->setPropertiesFromArray($row);
            return $notification;
        }

        return null;
    }

    public static function getLastNotificationByType($type)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Notifications WHERE type = ? ORDER BY createdAt LIMIT 1";
        $row   = $db->GetRow($query, [$type]);

        if (isset($row['nID'])) {
            $notification = new Notification();
            $notification->setPropertiesFromArray($row);
            return $notification;
        }

        return null;
    }

    public static function getCategoryOptions()
    {
//        $result [self::CATEGORY_TOPPICKS] = self::CATEGORY_TOPPICKS;
//        $result [self::CATEGORY_GENERAL] = self::CATEGORY_GENERAL;
//        $result [self::CATEGORY_FREE]    = self::CATEGORY_FREE;
//        $result [self::CATEGORY_PROMOTE] = self::CATEGORY_PROMOTE;
//        $result [self::CATEGORY_UPCOMMING] = self::CATEGORY_UPCOMMING;

//        return $result;
    }

    public static function getTypeOptions()
    {
//        $result [self::TYPE_GENERAL] = self::TYPE_GENERAL;
//        $result [self::TYPE_NEWS]    = self::TYPE_NEWS;
//        $result [self::TYPE_EVENTS]  = self::TYPE_EVENTS;
//
//        return $result;
    }

    public static function populateTitle($category, $title = false)
    {
        if ($title) {
            return $title;
        }
        return (new self())->messages[$category][self::TITLE];
    }

    public static function populateUID($category, $type, $contentID, $uID = false)
    {
        if ($uID) {
            return $uID;
        }
        switch ($type) {
            case self::TYPE_BOOKING:
                $booking = Booking::getByID($contentID);
                switch ($category) {
                    case self::CATEGORY_UPCOMING_BOOKING:
                        $uID = $booking->getUID();
                        break;
                    case self::CATEGORY_PAST_BOOKING:
                        $uID = $booking->getUID();
                        break;
                    case self::CATEGORY_PROPERTY_BOOKED:
                        $uID = $booking->getProperty()->getOwnerID();
                        break;
                }
                break;
            case self::TYPE_UTILITY:
                $bill = Bill::getByID($contentID);
                $uID  = $bill->getProperty()->getOwnerID();
                break;
            case self::TYPE_PROPERTY:
                $property = Property::getByID($contentID);
                $uID      = $property->getOwnerID();
                break;
            case self::TYPE_REVIEW:
                $review = Review::getByID($contentID);
                $uID    = $review->getProperty()->getOwnerID();
                break;
        }
        return $uID;
    }

    public static function formatMessage($category, $type, $body = false, $contentID)
    {
        if ($body) {
            return $body;
        }
        switch ($type) {
            case self::TYPE_BOOKING:
                switch ($category) {
                    case self::CATEGORY_UPCOMING_BOOKING:
                        $booking = Booking::getByID($contentID);
                        $rd      = $booking->getRemainingDays();
                        $body    = sprintf((new self())->messages[$category][self::BODY], $rd, t2('day', 'days', $rd));
                        break;
                    case self::CATEGORY_PAST_BOOKING:
                        $body = (new self())->messages[$category][self::BODY];
                        break;
                    case self::CATEGORY_PROPERTY_BOOKED:
                        $booking = Booking::getByID($contentID);
                        $ui      = UserInfo::getByID($booking->getUID());
                        $body    = sprintf((new self())->messages[$category][self::BODY], $ui->getFullName());
                        break;
                }
                break;
            case self::TYPE_UTILITY:
                $body = (new self())->messages[$category][self::BODY];
                break;
            case self::TYPE_PROPERTY:
                $body = (new self())->messages[$category][self::BODY];
                break;
            case self::TYPE_REVIEW:
                $review = Review::getByID($contentID);
                $ui     = UserInfo::getByID($review->getUserId());
                $body   = sprintf((new self())->messages[$category][self::BODY], $ui->getFullName());
                break;
        }
        return $body;
    }

    public static function getLinkURL($category, $link = false)
    {
        if ($link) {
            return $link;
        }
        $link = (new self())->links[$category];
//        $link = BASE_URL . View::url($link);
        return $link;
    }

    public function getID()
    {
        return $this->nID;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSubTitle()
    {
        return $this->subtitle;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getContentID()
    {
        return $this->contentID;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
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
    public function getReadStatus()
    {
        return $this->read;
    }

    public function sendSocketNotification()
    {

    }
}
