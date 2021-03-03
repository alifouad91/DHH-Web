<?php

class BookingPropertyFacilities
{

    protected $bpfID;
    protected $pfID;
    protected $bID;
    protected $fID;
    protected $name;
    protected $price;
    protected $bpfPrice;

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

    /**
     * @return mixed
     */
    public function getBpfID()
    {
        return $this->bpfID;
    }

    /**
     * @return mixed
     */
    public function getPfID()
    {
        return $this->pfID;
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
    public function getFID()
    {
        return $this->fID;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        if ($this->bpfPrice) {
            return $this->bpfPrice;
        }
        return $this->price;
    }


    public static function getByID($bpfID)
    {
        $db    = Loader::db();
        $query = "select bpf.bpfID ,bpf.pfID ,bpf.bID ,f.fID ,f.name ,pf.price,bpf.price as bpfPrice  from BookingPropertyFacilities bpf
                          inner join PropertyFacilities pf on bpf.pfID = pf.pfID
                          inner join Facilities f on  f.fID = pf.fID where bpf.bpfID = ?";

        $row = $db->GetRow($query, [$bpfID]);

        if (isset($row['bpfID'])) {
            return new BookingPropertyFacilities($row);
        }
        return null;
    }


    public static function add($pfID, $bID, $price = 0)
    {
        $db    = Loader::db();
        /** @var DateHelper $dh */
        $dh          = Loader::helper('date');
        $currentTime = $dh->getSystemDateTime('now', 'Y-m-d H:i:s');
        $query = "INSERT INTO BookingPropertyFacilities( pfID, bID, price, createdAt  ) VALUES ( ?, ?, ?, ? )";
        $ret   = $db->Execute($query, [$pfID, $bID, $price, $currentTime]);

        if ($ret) {
            $bpf = self::getByID($db->Insert_ID());
            $booking = Booking::getByID($bpf->getBID());
            $booking->updateBookingTotal();
            return $bpf;
        }
        return null;
    }

    public static function update($bpfID, $pfID, $bID)
    {
        $db    = Loader::db();
        $query = "UPDATE BookingPropertyFacilities SET pfID = ? , bID = ?  WHERE bpfID = ?";
        $ret   = $db->Execute($query, [$pfID, $bID, $bpfID]);
        if ($ret) {
            return self::getByID($db->Insert_ID());
        }
        return null;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM BookingPropertyFacilities WHERE bpfID = ?";
        $db->Execute($query, [$this->getBpfID()]);

        $booking = Booking::getByID($this->getBID());
        $booking->updateBookingTotal();

        return null;
    }


    public static function getAdditionalFacilityBIDAndPFID($bID, $fID)
    {
        $db    = Loader::db();
        $query = "SELECT bpf.bpfID ,bpf.pfID ,bpf.bID ,f.fID ,f.name ,pf.price,bpf.price as bpfPrice FROM BookingPropertyFacilities bpf inner join PropertyFacilities pf on bpf.pfID = pf.pfID inner join Facilities f on  f.fID = pf.fID where bpf.bID = ? AND pf.pfID = ? LIMIT 1";
        $result = $db->GetRow($query, [$bID, $fID]);
        if ($result)
        {
            return new BookingPropertyFacilities($result);
        }
        return null;
    }


}

