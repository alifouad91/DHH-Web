<?php
defined('C5_EXECUTE') or die('Access Denied.');

class ReviewList extends DatabaseItemList
{

    protected $queryCreated;
    protected $populateUsers;
    protected $populateBookings;
    protected $populateProperties;

    /**
     * ReviewList constructor.
     */
    public function __construct()
    {
        $this->queryCreated       = false;
        $this->populateUsers      = false;
        $this->populateProperties = false;
        $this->populateBookings   = false;
    }

    public function setBaseQuery()
    {
        $this->setQuery("SELECT * FROM Reviews r");
    }

    public function createQuery()
    {
        if (!$this->queryCreated) {
            $this->setBaseQuery();
            $this->queryCreated = 1;
        }
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        $this->createQuery();

        return parent::getTotal();
    }

    /**
     * @param int $id
     */
    public function filterByProperty($id)
    {
        $this->filter('r.pId', $id);
    }

    /**
     * @param $rating
     * @param string $comparison
     */
    public function filterByRatings($rating,$comparison = '=')
    {
        $this->filter('r.reviewRating', $rating,$comparison);
    }

    /**
     * @param int $id
     */
    public function filterByUser($id)
    {
        $this->filter('r.uId', $id);
    }

    /**
     * @param int $id
     */
    public function filterGuestReviewsForUser($id)
    {
        $db = Loader::db();
        $id = $db->Quote($id);
        $this->filter(false, "r.pID IN (SELECT DISTINCT(p.pID) FROM Properties p WHERE p.owner = {$id})");
    }

    public function populateUsers()
    {
        $this->populateUsers = true;
    }

    public function populateBookings()
    {
        $this->populateBookings = true;
    }

    public function populateProperties()
    {
        $this->populateProperties = true;
    }

    public function filterByKeywords($keywords)
    {
        $db        = Loader::db();
        $qkeywords = $db->quote($keywords . '%');
        $this->filter(
            false,
            "( reviewComment LIKE  {$qkeywords}  )
          ");
    }

    public function sortByReviewRating($reviewRating = 'desc')
    {
        $this->sortBy('r.reviewRating', $reviewRating);
    }

    public function sortByReviewDate($reviewDate = 'desc')
    {
        $this->sortBy('r.createdAt', $reviewDate);
    }

    /**
     * @param int $itemsToGet
     * @param int $offset
     * @return array|bool
     */
    public function get($itemsToGet = 0, $offset = 0)
    {
        $this->createQuery();
        $rows        = parent::get($itemsToGet, $offset);
        $reviews     = [];
        $propertyIds = [];
        $bookingIds  = [];
        $userIds     = [];

        foreach ($rows as $row) {
            $review                    = new Review($row);
            $reviews[$review->getId()] = $review;
            $propertyIds[]             = $review->getPropertyId();
            $bookingIds[]              = $review->getBookingId();
            $userIds[]                 = $review->getUserId();
        }

        if ($this->populateProperties) {

            $propertyIds = array_filter(array_unique($propertyIds));

            $propertyList = new PropertyList();
            $propertyList->filterById($propertyIds);
            $properties = $propertyList->get();

            /** @var Review $review */
            foreach ($reviews as $review) {
                $review->property = $properties[$review->getPropertyId()];
            }
        }

        if ($this->populateBookings) {

            $bookingIds = array_filter(array_unique($bookingIds));

            $bookingList = new BookingList();
            $bookingList->filterByID($bookingIds);
            $bookings = $bookingList->get();

            /** @var Review $review */
            foreach ($reviews as $review) {
                $review->booking = $bookings[$review->getBookingId()];
            }

        }

        if ($this->populateUsers) {

            $userIds = array_filter(array_unique($userIds));

            $userList = new UserList();
            $userList->filterByUserIDs($userIds);
            $users = $userList->get();
            $newUsers = [];
            foreach ($users as $user){
                $newUsers[$user->getUserID()] = $user;
            }
            unset($users);

            /** @var Review $review */
            foreach ($reviews as $review) {
                $review->ui = $newUsers[$review->getUserId()];
            }

        }

        return $reviews;
    }


}