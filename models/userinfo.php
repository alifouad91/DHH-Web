<?php

defined('C5_EXECUTE') or die('Access Denied.');

class UserInfo extends UserDetails
{

    /**
     * returns the UserInfo object for a give user's uID.
     *
     * @param int $uID
     *
     * @return UserInfo
     */
    public static function getByID($uID)
    {
        return UserInfo::get('where u.uID = ?', $uID);
    }

    /**
     * returns the UserInfo object for a give user's username.
     *
     * @param string $uName
     *
     * @return UserInfo
     */
    public static function getByUserName($uName)
    {
        return UserInfo::get('where u.uName = ?', $uName);
    }

    /**
     * returns the UserInfo object for a give user's email address.
     *
     * @param string $uEmail
     *
     * @return UserInfo
     */
    public static function getByEmail($uEmail)
    {
        return UserInfo::get('where u.uEmail = ?', $uEmail);
    }

    public function get($where, $var)
    {
        $db = Loader::db();
        $q  = "SELECT u.uID,u.uLastLogin,u.uLastIP,u.uIsValidated,u.uPreviousLogin,u.uIsFullRecord,u.uNumLogins,u.uDateAdded,
                u.uIsActive,u.uLastOnline,u.uHasAvatar,u.uName,u.uEmail,u.uTimezone,u.uDefaultLanguage,ud.fullName,
                ud.dateOfBirth,ud.nationality,ud.phone,ud.passportNo,ud.passportValidTill,ud.facebookID,ud.googleID,
                ud.serviceNews,ud.dubaiAdvices,ud.relatedProposal,
                (SELECT COUNT(*) FROM Booking b INNER JOIN Properties AS p ON p.pID = b.pID  WHERE b.uID = u.uID AND b.bookingStatus IN ('paid')) as bookingCount,
                (SELECT COUNT(*) FROM Reviews r INNER JOIN Properties AS p ON p.pID = r.pID WHERE r.uID = u.uID) as reviewCount,
                (SELECT COUNT(*) FROM UserFavourites uf INNER JOIN Properties AS p ON p.pID = uf.pID WHERE uf.uID = u.uID) as favouriteCount,
                ud.billingFirstName,ud.billingLastName,ud.billingEmail,ud.billingPhone,ud.billingAddress,ud.billingCity,ud.billingCountry,ud.creditAmount,ud.referredBy,ud.uniqueToken
                FROM Users u left join UserDetails ud on ud.uID = u.uID " . $where;

        $r = $db->query($q, array($var));
        if ($r && $r->numRows() > 0) {
            $ui  = new UserInfo();
            $row = $r->fetchRow();
            $ui->setPropertiesFromArray($row);
            $r->free();
        }

        if (is_object($ui)) {
            return $ui;
        }
    }

    /**
     * @param array $data
     * @param array | false $options
     *
     * @return UserInfo
     */
    public static function add($data, $options = false)
    {
        $ui = parent::add($data, $options);
        /** @var ValidationIdentifierHelper $vih */
        $vih        = Loader::helper('validation/identifier');
        $uniqueToken = $vih->generate('UserDetails', 'uniqueToken', 8);
        if ($ui) {
            $db    = Loader::db();
            $query = "INSERT INTO UserDetails(uID,fullName,uniqueToken) VALUES (?,?,?)";
            $ret   = $db->Execute($query, [$ui->getUserID(),$data['fullName'],$uniqueToken]);

            if ($ret) {
                return static::getByID($ui->getUserID());
            }

        }
    }

    public function update($data)
    {
        /** @var DateHelper $dh */
        parent::update($data);

        $th                = Loader::helper('text');
        $dh                = Loader::helper('date');
        $fullName          = $this->getFullName();
        $dateOfBirth       = $this->getDateOfBirth();
        $nationality       = $this->getNationality();
        $phone             = $this->getPhone();
        $passportNo        = $this->getPassportNo();
        $passportValidTill = $this->getPassportValidTill();
        $serviceNews       = $this->getServiceNews();
        $dubaiAdvices      = $this->getDubaiAdvices();
        $relatedProposal   = $this->getRelatedProposal();

        $fullName =$this->getFullName();
        if (isset($data['fullName'])) {
            $fullName = $th->sanitize($data['fullName']);
        }

        $dateOfBirth = $this->getDateOfBirth();

        if (isset($data['dateOfBirth'])) {
            $dateOfBirth = $th->sanitize($data['dateOfBirth']);
            $dateOfBirth = $dh->getFormattedDate($dateOfBirth, 'Y-m-d');
        }
        if(!$dateOfBirth) {
            $dateOfBirth = '0000-00-00';
        }

        $nationality = $this->getNationality();
        if (isset($data['nationality'])) {
            $nationality = $th->sanitize($data['nationality']);
        }

        $phone = $this->getPhone();
        if (isset($data['phone'])) {
            $phone = $th->sanitize($data['phone']);
        }

        $passportNo = $this->getPassportNo();
        if (isset($data['passportNo'])) {
            $passportNo = $th->sanitize($data['passportNo']);
        }

        $passportValidTill = $this->getPassportValidTill();
        if (isset($data['passportValidTill'])) {
            $passportValidTill = $th->sanitize($data['passportValidTill']);
        }
        if(!$passportValidTill) {
            $passportValidTill = '0000-00-00';
        }

        $serviceNews = $this->getServiceNews();
        if (isset($data['serviceNews'])) {
            $serviceNews = $th->sanitize($data['serviceNews']);
        }

        $dubaiAdvices = $this->getDubaiAdvices();
        if (isset($data['dubaiAdvices'])) {
            $dubaiAdvices = $th->sanitize($data['dubaiAdvices']);
        }

        $relatedProposal = $this->getRelatedProposal();
        if (isset($data['relatedProposal'])) {
            $relatedProposal = $th->sanitize($data['relatedProposal']);
        }

        $db    = Loader::db();
        $query = "SELECT uID FROM UserDetails WHERE uID = ?";
        $res   = $db->GetRow($query, [$this->getUserID()]);
        if (!$res['uID']) {
            $query = "INSERT INTO UserDetails(uID) VALUES (?)";
            $db->Execute($query, [$this->getUserID()]);
        }

        $query = "UPDATE UserDetails SET fullName = ?, dateOfBirth = ?, nationality = ?, phone = ?, passportNo = ?, passportValidTill = ?,
                  serviceNews = ?, dubaiAdvices = ?, relatedProposal = ? WHERE uID  = ?";


        $ret = $db->Execute($query, [$fullName, $dateOfBirth, $nationality, $phone, $passportNo, $passportValidTill,
                                     $serviceNews, $dubaiAdvices, $relatedProposal, $this->getUserID()]);

        if ($ret) {
            return self::getByID($this->getUserID());
        }
        return null;
    }

    public function getAvatar()
    {
        if (!$this->avatar) {
            /** @var ConcreteAvatarHelper $av */
            $av = Loader::helper('concrete/avatar');

            $avatar = $av->getImagePath($this);
            if (!$avatar) {
//                $avatar = $this->generateLetterAvatar();
                return null;
            }

            $this->avatar = $avatar;
            return BASE_URL.$this->avatar;
        }

        return BASE_URL.$this->avatar;
    }

    public function generateLetterAvatar()
    {
        if (!$this->getFullName())
        {
            $avatar = REL_DIR_FILES_AVATARS.'/default.png';
            return $avatar;
        }
        $avatar = new YoHang88\LetterAvatar\LetterAvatar($this->getFullName(), 'circle', AVATAR_HEIGHT);
        $av     = $avatar->saveAs(DIR_FILES_AVATARS . '/' . $this->getUserID() . '.png', 'image/png');

        /** @var ConcreteAvatarHelper $av */
        $av     = Loader::helper('concrete/avatar');
        $avatar = $av->getImagePath($this);
        return $avatar;
    }
}
