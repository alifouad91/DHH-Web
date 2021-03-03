<?php


defined('C5_EXECUTE') or die('Access Denied.');

class UserDetails extends Concrete5_Model_UserInfo
{
    protected $nationality;
    protected $phone;
    protected $passportNo;
    protected $passportValidTill;
    protected $serviceNews;
    protected $dubaiAdvices;
    protected $relatedProposal;
    protected $dateOfBirth;
    protected $facebookID;
    protected $googleID;
    protected $fullName;
    protected $avatar;
    protected $badge;
    protected $bookingCount;
    protected $reviewCount;
    protected $favouriteCount;
    protected $myProperties;
    protected $propertyReviews;
    protected $billingFirstName;
    protected $billingLastName;
    protected $billingEmail;
    protected $billingAddress;
    protected $billingCity;
    protected $billingCountry;
    protected $billingPhone;
    protected $creditAmount;
    protected $referredBy;
    protected $uniqueToken;

    /**
     * @return mixed
     */
    public function getMyProperties()
    {

//        (SELECT COUNT(*) FROM Properties p WHERE p.owner = u.uID) as myProperties,
//                (SELECT COUNT(*) FROM Reviews p WHERE p.owner = u.uID) as myProperties,
        return $this->myProperties;
    }

    /**
     * @param mixed $myProperties
     */
    public function setMyProperties($myProperties)
    {
        $this->myProperties = $myProperties;
    }

    /**
     * @return mixed
     */
    public function getPropertyReviews()
    {
        return $this->propertyReviews;
    }

    /**
     * @param mixed $propertyReviews
     */
    public function setPropertyReviews($propertyReviews)
    {
        $this->propertyReviews = $propertyReviews;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return (string)$this->fullName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        $fullName =  (string)$this->fullName;
        $fullNameArr = explode(' ',$fullName);
        return $fullNameArr[0];
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        $fullName =  (string)$this->fullName;
        $fullNameArr = explode(' ',$fullName);
        return $fullNameArr[1];
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return mixed
     */
    public function getRelatedProposal()
    {
        return (bool)$this->relatedProposal;
    }

    /**
     * @param mixed $badge
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;
    }

    /**
     * @return mixed
     */
    public function getBadge()
    {
        if (!$this->badge) {
            $bookingCount = $this->getBookingCount();
            switch ($bookingCount) {
                case 0:
                    $this->badge = "The Beginner";
                    break;
                case ($bookingCount < 6):
                    $this->badge = "Explorer";
                    break;
                case ($bookingCount < 16):
                    $this->badge = "Senior Traveler";
                    break;
                case ($bookingCount < 31):
                    $this->badge = "Experienced Traveler";
                    break;
                default:
                    $this->badge = "Travel Guru";
                    break;
            }
        }
        return $this->badge;
    }

    /**
     * @param mixed $relatedProposal
     */
    public function setRelatedProposal($relatedProposal)
    {
        $this->relatedProposal = $relatedProposal;
    }

    /**
     * @return mixed
     */
    public function getFacebookID()
    {
        return (string)$this->facebookID;
    }

    /**
     * @param mixed $facebookID
     */
    public function setFacebookID($facebookID)
    {
        $db    = Loader::db();
        $query = "UPDATE UserDetails SET facebookID = ? WHERE uID = ?";
        $db->Execute($query, [$facebookID, $this->getUserID()]);

        $this->facebookID = $facebookID;
    }

    /**
     * @return mixed
     */
    public function getGoogleID()
    {
        return (string)$this->googleID;
    }

    /**
     * @param mixed $googleID
     */
    public function setGoogleID($googleID)
    {
        $db    = Loader::db();
        $query = "UPDATE UserDetails SET googleID = ? WHERE uID = ?";
        $db->Execute($query, [$googleID, $this->getUserID()]);
        $this->googleID = $googleID;
    }

    /**
     * @return string
     */
    public function getDateOfBirth()
    {
        return (string)$this->dateOfBirth;
    }


    /**
     * @return string
     */
    public function getNationality()
    {
        return (string)$this->nationality;
    }

    /**
     * @return string
     */
    public function getPassportNo()
    {
        return (string)$this->passportNo;
    }

    /**
     * @return string
     */
    public function getPassportValidTill()
    {
        return $this->passportValidTill != '0000-00-00' ? $this->passportValidTill : '';
    }

    /**
     * @return string
     */
    public function getServiceNews()
    {
        return (bool)$this->serviceNews;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return (string)$this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getBookingCount()
    {
        return (int)$this->bookingCount;
    }

    /**
     * @param mixed $bookingCount
     */
    public function setBookingCount($bookingCount)
    {
        $this->bookingCount = $bookingCount;
    }

    /**
     * @return mixed
     */
    public function getReviewCount()
    {
        return (int)$this->reviewCount;
    }

    /**
     * @param mixed $reviewCount
     */
    public function setReviewCount($reviewCount)
    {
        $this->reviewCount = $reviewCount;
    }

    /**
     * @return mixed
     */
    public function getFavouriteCount()
    {
        return (int)$this->favouriteCount;
    }

    /**
     * @param mixed $favouriteCount
     */
    public function setFavouriteCount($favouriteCount)
    {
        $this->favouriteCount = $favouriteCount;
    }

    /**
     * @return string
     */
    public function getDubaiAdvices()
    {
        return (bool)$this->dubaiAdvices;
    }

    /**
     * @return string
     */
    public function getBillingFirstName()
    {
        return (string)$this->billingFirstName;
    }

    /**
     * @return string
     */
    public function getBillingLastName()
    {
        return (string)$this->billingLastName;
    }

    /**
     * @return string
     */
    public function getBillingEmail()
    {
        return (string)$this->billingEmail;
    }

    /**
     * @return string
     */
    public function getBillingAddress()
    {
        return (string)$this->billingAddress;
    }

    /**
     * @return string
     */
    public function getBillingCity()
    {
        return (string)$this->billingCity;
    }

    /**
     * @return string
     */
    public function getBillingCountry()
    {
        return (string)$this->billingCountry;
    }

    /**
     * @return string
     */
    public function getUniqueToken()
    {
        return (string)$this->uniqueToken;
    }

    /**
     * @return double
     */
    public function getCreditAmount()
    {
        return $this->creditAmount;
    }

    /**
     * @return string
     */
    public function getReferredBy()
    {
        return (string)$this->referredBy;
    }

    /**
     * @return string
     */
    public function getBillingPhone()
    {
        return (string)$this->billingPhone;
    }

    /*  public function getAddresses()
      {
          if(!$this->addresses) {
              $ual = new UserAddressList();
              $ual->filterByUserId($this->uID);
              $this->addresses = $ual->get();
          }
          return  $this->addresses;
      }*/


}
