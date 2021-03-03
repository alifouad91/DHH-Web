<?php

/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 10/3/19
 * Time: 3:35 PM
 */
class Bill
{
    protected $billID;
    protected $billNo;
    protected $pID;
    protected $amount;
    protected $type;
    protected $description;
    protected $fixedBy;
    protected $billImage;
    protected $date;
    protected $property;

    const THUMB_PATH  = BASE_URL . DIR_REL . '/files/bills/';

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
    public function getID()
    {
        return $this->billID;
    }

    /**
     * @param mixed $billID
     */
    public function setID($billID)
    {
        $this->billID = $billID;
    }

    /**
     * @return mixed
     */
    public function getBillNo()
    {
        return $this->billNo;
    }

    /**
     * @param mixed $billNo
     */
    public function setBillNo($billNo)
    {
        $this->billNo = $billNo;
    }

    /**
     * @return mixed
     */
    public function getPID()
    {
        return $this->pID;
    }

    /**
     * @return null|Property
     */
    public function getProperty()
    {
        if (!$this->property) {
            $this->property = Property::getByID($this->getPID());
        }
        return $this->property;
    }

    /**
     * @param mixed $pID
     */
    public function setPID($pID)
    {
        $this->pID = $pID;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getFixedBy()
    {
        return $this->fixedBy;
    }

    /**
     * @param mixed $fixedBy
     */
    public function setFixedBy($fixedBy)
    {
        $this->fixedBy = $fixedBy;
    }

    /**
     * @return mixed
     */
    public function getBillImage()
    {
        return $this->billImage;
    }

    /**
     * @param mixed $billImage
     */
    public function setBillImage($billImage)
    {
        $this->billImage = $billImage;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        /** @var DateHelper $dh */
        $dh = Loader::helper('date');
        return $dh->getFormattedDate($this->date, 'Y-m-d');
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param mixed $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    public static function add($pID, $amount, $type, $description, $fixedBy, $billImage, $date)
    {
        $db = Loader::db();
        /** @var DateHelper $dh */
        $dh    = Loader::helper('date');
        $date  = $dh->getFormattedDate($date, 'Y-m-d H:i:s');

        $query = "INSERT INTO Bills(pID, amount, type, description, fixedBy, billImage, date) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $ret   = $db->Execute($query, [$pID, $amount, $type, $description, $fixedBy, $billImage, $date]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public function update($pID, $amount, $type, $description, $fixedBy, $billImage, $date)
    {
        $db = Loader::db();
        /** @var DateHelper $dh */
        $dh    = Loader::helper('date');
        $date  = $dh->getFormattedDate($date, 'Y-m-d H:i:s');
        $query = "UPDATE Bills SET pID = ?, amount = ?, type = ?, description = ?, fixedBy = ?, billImage = ?, date = ? WHERE billID = ?";
        $ret   = $db->Execute($query, [$pID, $amount, $type, $description, $fixedBy, $billImage, $date, $this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public function updateBillImage($billImage)
    {

        $db    = Loader::db();
        $query = "UPDATE Bills SET billImage = ? WHERE billID = ?";
        $ret   = $db->Execute($query, [$billImage, $this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public static function getByID($id)
    {

        $db    = Loader::db();
        $query = "SELECT * FROM Bills WHERE billID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['billID'])) {
            return new Bill($row);
        }

        return null;
    }

    /**
     * @param        $inputName
     * @param string $caption
     * @param string $bgPosition
     * @return boolean
     */
    public function saveImage($inputName, $caption = '', $bgPosition = '')
    {
        /** @var FileHelper $fh */
        $fh = Loader::helper('file');

        $path = DIR_FILES_UPLOADED_STANDARD . '/bills';
        $file = $fh->uploadFile($inputName, $path, $this->getType() . '_' . $this->getID());

        if ($file) {
            $this->updateBillImage(basename($file));
        }
        return true;
    }

    public function removeImage()
    {
        $path = DIR_FILES_UPLOADED_STANDARD . '/bills/'.$this->getBillImage();
        unlink($path);
        $this->updateBillImage(null);
    }

    /**
     * Generates the image object for the article
     * using the image helper and returns the
     * thumbnail path.
     *
     * @return string
     */
    public function getPDFPath($justPath = false)
    {
        $imageFilename    = $this->getBillImage() ? : null;
        if($justPath) {
            return $imageFilename;
        }
        $imagePath        = self::THUMB_PATH . $imageFilename;

        return $imageFilename ? $imagePath : null;
    }
}