<?php
defined('C5_EXECUTE') or die('Access Denied.');
Loader::controller('/api');

class ApiPropertyController extends ApiController
{
	// TODO: Change this later to 10
	const ITEMS_PER_PAGE = 12;

	public function on_start()
    {
        parent::on_start(); // TODO: Change the autogenerated stub
        $this->verifyAPIKey();
    }

    public function view()
	{
		/** @var TextHelper $th */
		/** @var DateHelper $dh */
		/** @var Property $property */
		/** @var PriceHelper $ph */
		$ph            = Loader::helper('price');
		$th            = Loader::helper('text');
		$dh            = Loader::helper('date');
		$pageNo        = (int) $th->sanitize($this->get('pageNo')) ? : 1;
		$areaType      = $th->sanitize($this->get('areaType'));
		$apartmentArea = $th->sanitize($this->get('apartmentArea'));
		$apartmentType = $th->sanitize($this->get('apartmentType'));
		$location      = $th->sanitize(urldecode($this->get('locations')));
		$amenities     = $th->sanitize($this->get('amenities'));
		$moreFilters   = $th->sanitize($this->get('otherFilters'));
		$bedrooms      = $th->sanitize($this->get('bedrooms'));
		$guests        = $th->sanitize($this->get('guests'));
		$minPrice      = $th->sanitize($this->get('minPrice'));
		$maxPrice      = $th->sanitize($this->get('maxPrice'));
		$startDate     = $th->sanitize($this->get('startDate'));
		$endDate       = $th->sanitize($this->get('endDate'));
		$excludeIDs    = $th->sanitize($this->get('excludeIDs'));
		$duration      = $th->sanitize($this->get('duration'));
		$monthly       = $th->sanitize($this->get('monthly')) ? : false;
		$weekly        = $th->sanitize($this->get('weekly')) ? : false;
		$keywords      = $th->sanitize($this->get('keywords'));
		$priceSort     = $th->sanitize($this->get('priceSort'));
        $ratingSort    = $th->sanitize($this->get('ratingSort'));
		$locale        = $th->sanitize($this->get('locale')) ? : null;

		try {
			$u = $this->validateToken();
		} catch (Exception $exception) {

		}
		if ($u) {
			$userID = $u->getUserID();
		}
		if ($startDate) {
			$currentDate = $dh->getSystemDateTime('now', 'Y-m-d');
			if (strtotime($startDate) < strtotime($currentDate)) {
				return $this->addError('Invalid start date');
			}
			if (!$endDate) {
				$endDate = $dh->getSystemDateTime($startDate . ' + 1 day', 'Y-m-d');
			}
			$startDate = $dh->getSystemDateTime($startDate, 'Y-m-d');
		}
		if ($endDate) {
			$tomDate = $dh->getSystemDateTime('now + 1 day', 'Y-m-d');
			if (strtotime($endDate) < strtotime($tomDate)) {
				return $this->addError('Invalid end date');
			}
		}

		$propertyList = new PropertyList();
		$propertyList->populateAverageAndTotalRatings();

		if ($guests) {
			$propertyList->filterByGuests($guests);
		}
		if ($bedrooms) {
			$propertyList->filterByBedrooms($bedrooms);
		}
		if ($minPrice) {
			$propertyList->filterByPrice($minPrice, '>=');
		}
		if ($maxPrice) {
			$propertyList->filterByPrice($maxPrice, '<=');
		}
		if ($areaType) {
			$propertyList->filterByAreaType($areaType);
		}
		if ($apartmentArea) {
			$propertyList->filterByApartmentArea($apartmentArea);
		}
		if ($apartmentType) {
			$propertyList->filterByApartmentType($apartmentType);
		}
		if ($amenities) {
			$propertyList->filterByAmenities($amenities);
		}
		if ($moreFilters) {
			$propertyList->filterByFacilities($moreFilters);
		}
		if ($location) {
			$propertyList->filterByLocation($location);
		}
		if ($monthly) {
			$propertyList->filterByMonthlyAvailability();
		}
		if ($weekly) {
			$propertyList->filterByWeeklyAvailability();
		}
		if ($keywords) {
			$propertyList->filterByKeywords($keywords);
		}
		if ($excludeIDs) {
			$excludeIDs = str_contains($excludeIDs, ',') ? explode(',', $excludeIDs) : $excludeIDs;
			$propertyList->filterById($excludeIDs, '!=');
		}
		if ($startDate && $endDate) {
			$propertyList->filterByAvailability($startDate, $endDate);
		}
		switch ($duration) {
			case 'Weekly':
				$propertyList->filterByWeeklyAvailability();
				break;
			case 'Monthly':
				$propertyList->filterByMonthlyAvailability();
				break;
		}
		if ($userID) {
			$propertyList->populateFavourites($userID);
		}

		if($priceSort) {
		    $propertyList->sortByPrice($priceSort);
        }

        if($ratingSort) {
            $propertyList->sortByRating($ratingSort);
        }

		$propertyList->setItemsPerPage(self::ITEMS_PER_PAGE);
		$properties = $propertyList->getPage($pageNo);
		$results    = [];

		foreach ($properties as $property) {

			$propertyPrice = $property->getMinPropertyPrice();

			$results[] = [
				'pID'           => $property->getID(),
				'path'          => $property->getPath(),
				'thumbnail'     => $property->getThumbnailViaHelper(),
				'title'         => $property->getName(),
				'caption'       => $property->getCaption(),
				'location'      => $property->getLocation(),
				'perDayPrice'   => $ph->format($propertyPrice, $locale, true),
				'propertyPrice'   => $propertyPrice,
				'monthlyPrice'  => ($property->getMonthlyPrice() > 0) ? $ph->format($property->getMonthlyPrice(), $locale, true) : $property->getMonthlyPrice(),
				'weeklyPrice'   => ($property->getWeeklyPrice() > 0) ? $ph->format($property->getWeeklyPrice(), $locale, true) : $property->getWeeklyPrice(),
				'vatPercent'    => Config::get('VAT_PERCENT'),
				'dihramFee'     => $ph->format($property->getTourismFee()),
				'vatAmount'     => $ph->format($property->getVatAmount(), $locale, true),
				'isFavorite'    => $property->getFavorite(),
				'avgRating'     => $property->getAverageRating(),
				'reviews'       => $property->getTotalRatings(),
				'maxGuests'     => $property->getMaxGuests(),
				'apartmentType' => $property->getApartmentType(),
				'beds'          => $property->getBeds()
			];
		}

		return $results;
	}

	public function detail()
	{
		/** @var PriceHelper $ph */
		$ph           = Loader::helper('price');
		$th           = Loader::helper('text');
		$propertyID   = (int) $th->sanitize($this->get('propertyID'));
		$propertyPath = $th->sanitize($this->get('propertyPath'));
		$locale       = $th->sanitize($this->get('locale')) ? : null;

		$property = null;

		if ($propertyID) {
			$property = Property::getByID($propertyID);
		}
		if ($propertyPath) {
			$property = Property::getByPath($propertyPath);
		}
		if (!$property) {
			return $this->addError('Invalid Property ID or Path');
		}

		$location = $property->getLocation();

		$_GET['locations']  = $location;
		$_GET['excludeIDs'] = $property->getID();
		$similarProperties  = $this->view();
		$_GET['bedrooms']   = $property->getBedrooms();
		$sCount             = count($similarProperties);
		$similarProperties  = array_slice($similarProperties, 0, 4);

		$result = [
			'id'                   => $property->getID(),
			'path'                 => $property->getPath(),
			'thumbnail'            => $property->getThumbnailPath(),
			'images'               => $property->getPropertyImages(true),
			'title'                => $property->getName(),
			'caption'              => $property->getCaption(),
			'location'             => $property->getLocation(),
			'perDayPrice'          => $ph->format($property->getPerDayPrice(), $locale, true),
			'monthlyPrice'         => ($property->getMonthlyPrice() > 0) ? $ph->format($property->getMonthlyPrice(), $locale, true) : $property->getMonthlyPrice(),
			'weeklyPrice'          => ($property->getWeeklyPrice() > 0) ? $ph->format($property->getWeeklyPrice(), $locale, true) : $property->getWeeklyPrice(),
			'vatPercent'           => Config::get('VAT_PERCENT'),
			'dihramFee'            => $ph->format($property->getTourismFee()),
			'vatAmount'            => $ph->format($property->getVatAmount(), $locale, true),
			'isFavorite'           => $property->getFavorite(),
			'avgRating'            => $property->getAverageRating(),
			'reviewCount'          => count($property->getReviews()),
			'reviews'              => $property->getReviews(),
			'amenities'            => $property->getAmenities(true),
			'facilities'           => $property->getPropertyFacilities(true),
			'apartmentType'        => $property->getApartmentType(),
			'numberOfRooms'        => $property->getNoOfRooms(),
			'beds'                 => $property->getBeds(),
			'bathrooms'            => $property->getBathrooms(),
			'guests'               => $property->getMaxGuests(),
			'description'          => $property->getDescription(),
			'propertyRules'        => $property->getPropertyRules(true),
			'cancellationPolicy'   => $property->getCancellationPolicy(true),
			'locationDescription'  => $property->getLocationDescription(true),
			'blockedDates'         => $property->getBlockedDates(),
			'similarProperties'    => $similarProperties,
			'similarPropertyCount' => $sCount,
			'lat'                  => (float) $property->getLatitude(),
			'long'                 => (float) $property->getLongitude(),
            'minNights'            => $property->getMinNights(),

		];

		return $result;
	}

	public function getReviews()
	{
		$th           = Loader::helper('text');
		$propertyID   = (int) $th->sanitize($this->get('propertyID'));
		$propertyPath = $th->sanitize($this->get('propertyPath'));
		$pageNo       = (int) $th->sanitize($this->get('pageNo'));
		$rating       = (int) $th->sanitize($this->get('rating'));
		$property     = null;

		if ($propertyID) {
			$property = Property::getByID($propertyID);
		}
		if ($propertyPath) {
			$property = Property::getByPath($propertyPath);
		}
		if (!$property) {
			return $this->addError('Invalid Property ID or Path');
		}


		/** @var DateHelper $dh */
		$dh         = Loader::helper('date');
		$reviewList = new ReviewList();
		$reviewList->filterByProperty($property->getID());
		$reviewList->populateUsers();
		$reviewList->populateBookings();
		if ($rating) {
			$reviewList->filterByRatings($rating);
		}
		$reviewList->setItemsPerPage(self::ITEMS_PER_PAGE);

		$reviews = $reviewList->getPage($pageNo);
		$temp    = [];

		/** @var Review $review */
		foreach ($reviews as $review) {
			$booking = $review->getBooking();
			$ui      = $review->getUserInfo();

			$totalNights = $dh->getNoOfNights($booking->getBookingStartDate(), $booking->getbookingEndDate());
			$temp[]      = [
				'rID'           => $review->getId(),
				'fullName'      => $ui->getFullName(),
				'avatar'        => $ui->getAvatar(),
				'totalNights'   => $totalNights,
				'reviewRating'  => $review->getReviewRating(),
				'reviewComment' => $review->getReviewComment(),
				'createdAt'     => $review->getCreatedAt()
			];
		}
		return $temp;
	}

	public function favourite()
	{
		/** @var TextHelper $th */
//        $u                    = $this->validUser();
		$th                   = Loader::helper('text');
		$u                    = $this->validUser();
		$result['favourited'] = false;
		$propertyID           = (int) $th->sanitize($this->post('propertyID'));
		if (!$propertyID) {
			return $this->addError('Invalid Property ID');
		}
		$property = Property::getByID($propertyID);
		if (!$property) {
			return $this->addError('Invalid Property ID');
		}
		if ($u) {
			$uID       = (int) $u->getUserID();
			$favourite = UserFavourite::getByUserIdAndPropertyID($uID, $propertyID);
			if ($favourite) {
				$favourite->delete();
			} else {
				UserFavourite::add($uID, $propertyID);
				$result['favourited'] = true;
			}
		}


		return $result;
	}

	public function filters()
	{
		$filter = new Filters();
		return $result = [
			'minPrice'      => $filter->getMinPrice(),
			'maxPrice'      => $filter->getMaxPrice(),
			'maxGuests'     => $filter->getMaxGuests(),
			'propertyTypes' => $filter->getPropertyTypes(),
			'maxBedrooms'   => $filter->getMaxBedrooms(),
			'locations'     => $filter->getLocations(),
			'otherFilters'  => $filter->getMoreFilters()
		];
	}

	public function pricePerDay()
	{
		$u          = $this->getLoggedInUser();
		$th         = Loader::helper('text');
		$propertyID = (int) $th->sanitize($this->get('propertyID'));
		$startDate  = $th->sanitize($this->get('startDate'));
		$endDate    = $th->sanitize($this->get('endDate'));
		$property   = null;
		$locale     = false;
		if ($u) {
			$locale = $u->getUserDefaultLanguage();
		}

		if ($propertyID) {
			$property = Property::getByID($propertyID);
		}

		if (!$property) {
			return $this->addError('Invalid Property ID');
		}

		$returnList = SeasonHelper::getPricePerDay($propertyID, $startDate, $endDate, $locale);

		return $returnList['pricePerDay'];
	}

	public function getSubTotal()
	{
		$u          = $this->getLoggedInUser();
		$th         = Loader::helper('text');
		$ph         = Loader::helper('price');
		$dh         = Loader::helper('date');
		$propertyID = (int) $th->sanitize($this->get('propertyID'));
		$startDate  = $th->sanitize($this->get('startDate'));
		$endDate    = $th->sanitize($this->get('endDate'));
		$property   = null;
		$locale     = false;
        $creditAmount = 0;
		if ($u) {

            // Clear Pending bookings
            $bookingStatus = [Booking::PAYMENT_UNPAID, Booking::PAYMENT_FAILED, Booking::PAYMENT_CANCELLED];
            $bookingList   = new BookingList();
            $bookingList->filterByBookingStatus($bookingStatus);
            $bookingList->filterByUserID($u->getUserID());
            $bookings = $bookingList->get(0);
            /** @var Booking $booking */
            foreach ($bookings as $booking) {
                BookingHelper::clearPendingBooking($booking, true);
            }


			$locale = $u->getUserDefaultLanguage();

            $creditAmount = $u->getUserInfoObject()->getCreditAmount();
		}

		if ($propertyID) {
			$property = Property::getByID($propertyID);
		}

		if (!$property) {
			return $this->addError('Invalid Property ID');
		}

        $minNights = $property->getMinNights();
        $nights    = $dh->getNoOfNights($startDate, $endDate);

        if($nights < $minNights) {
            return $this->addError('Minimum booking nights need to be '.$minNights);
        }


        $endDate = $dh->subtractNight($endDate, date_interval_create_from_date_string("1 day"));

		$returnList = SeasonHelper::getPricePerDay($propertyID, $startDate, $endDate, $locale, $creditAmount);


        if($nights < $returnList['minSeasonNight']){
            return $this->addError('Minimum booking nights for the season need to be '.$returnList['minSeasonNight']);
        }


        $total                      = $returnList['totalVal'];
        $creditAmount               = $returnList['creditAmount'];
		$returnList['subtotal']     = $ph->format($returnList['subtotal'], $locale, true);
		$returnList['total']        = $ph->format($total, $locale, true);
		$returnList['creditAmount'] = ($creditAmount)?$ph->format($creditAmount, $locale, true):0;
		return $returnList;
	}
}
