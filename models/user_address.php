<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

class UserAddress
{

    public $uaID;
    public $uID;
    public $address;
    public $street;
    public $city;
    public $country;
    public $state;
    public $phone;
    public $phone2;
    public $pin;

    function __construct($row)
    {
        $this->uaID    = $row['uaID'];
        $this->uID     = $row['uID'];
        $this->address = $row['address'];
        $this->city    = $row['city'];
        $this->country = $row['country'];
        $this->state   = $row['state'];
        $this->phone   = $row['phone'];
        $this->phone2  = $row['phone2'];
        $this->pin     = $row['pin'];

    }

    /**
     * @return string
     */
    public function getuaID()
    {
        return $this->uaID;
    }

    /**
     * @return string
     */
    public function getuID()
    {
        return $this->uID;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * @return string
     */
    public function getPin()
    {
        return $this->pin;
    }

    public static function add($uID, $address, $street, $city, $country, $state, $phone, $phone2, $pin)
    {
        $db    = Loader::db();
        $query = "INSERT INTO UserAddress( uID , address ,street , city ,country ,state ,phone ,phone2 ,pin  ) VALUES ( ?, ? ,? ,  ?, ? ,? ,  ?, ? ,?) ";
        $ret   = $db->Execute($query, [$uID, $address, $street, $city, $country, $state, $phone, $phone2, $pin]);
        return $ret;
    }

    public static function update($ua_id, $uID, $address, $street, $city, $country, $state, $phone, $phone2, $pin)
    {
        $db    = Loader::db();
        $query = "UPDATE UserAddress SET uID = ? , address = ?  ,street = ?  , city = ?  ,country = ?  ,state = ? ,phone = ? ,phone2 = ?  ,pin = ? WHERE ua_id = ?";
        $ret   = $db->Execute($query, [$uID, $address, $street, $city, $country, $state, $phone, $phone2, $pin, $ua_id]);
        return $ret;
    }

    public function delete($ua_id)
    {
        $db    = Loader::db();
        $query = "DELETE FROM UserAddress WHERE ua_id = ?";
        $ret   = $db->Execute($query, [$ua_id]);

        return $ret;
    }

    public static function getByUa_id($ua_id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM UserAddress WHERE id = ?";
        $row   = $db->GetRow($query, [$ua_id]);

        if (isset($row['ua_id'])) {
            return new UserAddress($row);
        }

        return null;
    }


}