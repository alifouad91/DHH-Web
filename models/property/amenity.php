<?php
/**
 * Created by PhpStorm.
 * User: backend1
 * Date: 6/2/19
 * Time: 12:11 PM
 */

class Amenity
{
    protected $amID;
    protected $name;
    protected $icon;

    const THUMB_PATH  = DIR_REL . '/files/amenities/';

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

    public static function add($name)
    {

        $db    = Loader::db();
        $query = "INSERT INTO Amenities(name) VALUES ( ? ) ";
        $ret   = $db->Execute($query, [$name]);

        if ($ret) {
            return self::getByID($db->Insert_ID());
        }

        return null;
    }

    public function update($name)
    {

        $db    = Loader::db();
        $query = "UPDATE Amenities SET name = ? WHERE amID = ?";
        $ret   = $db->Execute($query, [$name, $this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    public static function getByID($id)
    {
        $db    = Loader::db();
        $query = "SELECT * FROM Amenities WHERE amID = ?";
        $row   = $db->GetRow($query, [$id]);

        if (isset($row['amID'])) {
            return new Amenity($row);
        }

        return null;
    }

    public static function getAll()
    {
        $db     = Loader::db();
        $query  = "SELECT * FROM Amenities";
        $result = $db->Execute($query);

        $amenities = [];

        while ($row = $result->FetchRow()) {
            $amenities[$row['amID']] = new static($row);
        }

        return $amenities;
    }

    public function getID()
    {
        return $this->amID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function updateIcon($icon)
    {

        $db    = Loader::db();
        $query = "UPDATE Amenities SET icon = ? WHERE amID = ?";
        $ret   = $db->Execute($query, [$icon, $this->getID()]);

        if ($ret) {
            return self::getByID($this->getID());
        }

        return null;
    }

    /**
     * @param        $inputName
     * @return boolean
     */
    public function saveImage($inputName)
    {
        /** @var FileHelper $fh */
        $fh = Loader::helper('file');

        $path = DIR_FILES_UPLOADED_STANDARD . '/amenities';
        $file = $fh->uploadFile($inputName, $path, 'amenity' . '_' . $this->getID());

        if ($file) {
            $this->updateIcon(basename($file));
        }
        return true;
    }

    public function removeImage()
    {
        $path = DIR_FILES_UPLOADED_STANDARD . '/amenities/'.$this->getIcon();
        unlink($path);
        $this->updateIcon(null);
    }

    /**
     * Generates the image object for the article
     * using the image helper and returns the
     * thumbnail path.
     *
     * @param int $maxWidth
     * @param int $maxHeight
     * @param bool $crop
     *
     * @return string
     */
    public function getImagePath($maxWidth = 100, $maxHeight = 100, $crop = false)
    {
        /** @var ImageHelper $ih */
        $ih = Loader::helper('image');

        $imageFilename    = $this->getIcon() ? : null;
        $imagePath        = self::THUMB_PATH . $imageFilename;
        $defaultImagePath = self::THUMB_PATH . 'default.png';
        $img              = null;

        if ($imageFilename) {
            $img = $ih->getThumbnail($imagePath, $maxWidth, $maxHeight, $crop);
            $img = $img->src ? $img->src : $imagePath;
        }

        if (!$img) {
            $img = $ih->getThumbnail($defaultImagePath, $maxWidth, $maxHeight, $crop);
            $img = $img->src ? $img->src : $defaultImagePath;
        }

        return BASE_URL . $img;
    }

    public function delete()
    {
        $db    = Loader::db();
        $query = "DELETE FROM Amenities WHERE amID = ?";
        $ret   = $db->Execute($query, [$this->getID()]);

        return $ret;
    }
}