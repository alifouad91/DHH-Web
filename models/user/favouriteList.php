<?php
defined('C5_EXECUTE') or die('Access Denied.');

class UserFavouriteList extends DatabaseItemList
{

    protected $queryCreated;
    protected $populateProperties;

    public function __construct()
    {
        $this->queryCreated       = false;
        $this->populateProperties = false;
    }

    public function populateProperties($value = true)
    {
        $this->populateProperties = (bool)$value;
    }

    public function setBaseQuery()
    {
        $this->setQuery("SELECT * FROM UserFavourites");
    }

    public function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }

    }

    public function getTotal()
    {
        $this->createQuery();

        return parent::getTotal();
    }

    public function filterById($id)
    {
        $this->filter('id', $id);
    }

    public function filterByPropertyID($id)
    {
        $this->filter('pID', $id);
    }

    public function filterByUserId($user_id)
    {
        $this->filter('uID', $user_id);
    }

    public function sortByAddedAt()
    {
        $this->sortBy('added_at');
    }

    public function sortByAddedAtDesc()
    {
        $this->sortBy('added_at', 'desc');
    }

    public function get($itemsToGet = 0, $offset = 0)
    {
        /** @var UserFavourite $user_favourite */
        $this->createQuery();
        $rows            = parent::get($itemsToGet, $offset);
        $user_favourites = [];
        $propertyIDs      = [];

        foreach ($rows as $row) {
            $user_favourite                            = new UserFavourite($row);
            $user_favourites[$user_favourite->getId()] = $user_favourite;
            if ($this->populateProperties) {
                $propertyIDs[] = $user_favourite->getPropertyID();
            }
        }

        if ($propertyIDs) {

            $propertyList = new PropertyList();
            $propertyList->filterById($propertyIDs);
            $properties = $propertyList->get();

            foreach ($user_favourites as $user_favourite) {
                $property = array_key_exists($user_favourite->getPropertyID(), $properties) ?
                    $properties[$user_favourite->getPropertyID()] : null;
                $user_favourite->setProperty($property);
            }
        }

        return $user_favourites;
    }


}