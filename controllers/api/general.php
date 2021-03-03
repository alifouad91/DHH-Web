<?php
defined('C5_EXECUTE') or die('Access Denied.');
Loader::controller('/api');

class ApiGeneralController extends ApiController {
	const ITEM_PER_PAGE = 10;

	const FORM_TYPE_AMEND = 'amend';
	const FORM_TYPE_CANCEL = 'cancel';

	public function getStack() {
		/** @var TextHelper $th */
		$th = Loader::helper('text');
		$stackName = $this->get('stackName');
		if ($stackName) {
			$stack = Stack::getByName($stackName);
			global $a;
			ob_start();
			$blocks = $stack->getBlocks();
			foreach ($blocks as $b) {
				$bv = new BlockView();
				$bv->setAreaObject($a);
				$p = new Permissions($b);
				if ($p->canViewBlock()) {
					$bv->render($b);
				}
			}
			$result = ob_get_clean();
			$result = $th->decodeEntities($result);
			return $result;
		}
	}

	public function getPageList() {

		$th = Loader::helper('text');
		$nh = Loader::helper('navigation');
		$handle = $th->sanitize($this->get('pageHandle'));
		$maxWidth = (int) $th->sanitize($this->get('maxWidth'));
		$maxHeight = (int) $th->sanitize($this->get('maxHeight'));
		$pageNo = (int) $th->sanitize($this->get('pageNo')) ?: 1;
		$results = [];

		$pl = new PageList();
		$pl->setItemsPerPage(self::ITEM_PER_PAGE);
		$pl->filterByCollectionTypeHandle($handle);

		$pages = $pl->getPage($pageNo);
		if ($pages) {
			switch ($handle) {
			case 'blog_page':
				/** @var Page $blog */
				foreach ($pages as $blog) {
					$results[] = [
						'title' => $blog->getCollectionName(),
						'url' => $nh->getCollectionURL($blog),
						'date' => $blog->getCollectionDatePublic(),
						'author' => $blog->getAttribute('author'),
						'category' => (string) $blog->getAttribute('category'),
						'pageImage' => $this->getImagePath($blog->getAttribute('page_image')),
					];
				}
			}
		}
		return $results;

	}

	protected function getImagePath($image, $maxWidth = 300, $maxHeight = 300, $crop = false) {
		/** @var File $image */
		/** @var ImageHelper $ih */
		$ih = Loader::helper('image');
		if ($image) {
			$image = $ih->getThumbnail($image, $maxWidth, $maxHeight, $crop);
			if ($image->src) {
				return $image->src;
			}
		}
		return null;
	}

	public function checkStatus() {
		return User::isLoggedIn();
	}

	public function getLoggedInUser() {
		$u = new User();
		if (!$u || !$u->getUserID()) {
			return $this->addError('No LoggedIn User');
		}
		$userID = $u->getUserID();
		$u->setUserForeverCookie();

		// Generate a token
		$JWT = new JWT();
		$JWT->setClaim('user_id', $userID);
		$token = (string) $JWT->generateToken();
		$result['token'] = $token;
		return $result;
	}

	public function getLoggedInUserFromHash() {
		$hash = hex2bin($this->post('userHash'));
		$userID = openssl_decrypt($hash, MCRYPT_BLOWFISH, MCRYPT_KEY, OPENSSL_RAW_DATA, MCRYPT_IV);
		$u = User::getByUserID($userID);
		if (!$u || !$u->getUserID()) {
			return $this->addError('No LoggedIn User');
		}
		$u->setUserForeverCookie();

		// Generate a token
		$JWT = new JWT();
		$JWT->setClaim('user_id', $userID);
		$token = (string) $JWT->generateToken();
		$result['token'] = $token;
		return $result;
	}

	public function getCountries() {
		$co = Loader::helper('lists/countries');

		$countriesTmp = $co->getCountries();
		$countries = [];
		foreach ($countriesTmp as $_key => $_value) {
			array_push($countries, [
				'value' => $_value,
			]);
		}
		return $countries;
	}

	public function getRandomGuestReviews() {
		/** @var DateHelper $dh */
		$dh = Loader::helper('date');

		$reviewList = new ReviewList();
		$reviewList->populateBookings();
		$reviewList->populateProperties();
		$reviewList->filterByRatings(4, '>=');
		$reviewList->setItemsPerPage(3);

		$reviews = $reviewList->getPage(1);
		$result = [];

		/** @var Review $review */
		foreach ($reviews as $review) {
			$property = $review->getProperty();
			$booking = $review->getBooking();

			$totalNights = $dh->getNoOfNights($booking->getBookingStartDate(), $booking->getbookingEndDate());
			$result[] = [
				'bID' => $booking->getBID(),
				'pID' => $booking->getPID(),
				'thumbnail' => $property->getThumbnailPath(),
				'title' => $property->getName(),
				'caption' => $property->getCaption(),
				'location' => $property->getLocation(),
				'perDayPrice' => $property->getPerDayPrice(),
				'startDate' => $booking->getBookingStartDate(),
				'endDate' => $booking->getbookingEndDate(),
				'guests' => $booking->getNoOfGuest(),
				'totalNights' => $totalNights,
				'rID' => $review->getId(),
				'reviewRating' => $review->getReviewRating(),
				'reviewComment' => $review->getReviewComment(),
				'createdAt' => $review->getCreatedAt(),
				'userName' => $review->getUserInfo()->getFullName(),
				'profilePic' => $review->getUserInfo()->getAvatar(),
				'fullName' => $review->getUserInfo()->getFullName(),
				'avatar' => $review->getUserInfo()->getAvatar(),
                'path'          => $property->getPath(),
			];
		}
		return $result;
	}

	public function submitForm() {
		$bt = BlockType::getByHandle('formidable');
        $bID = $_REQUEST['bID'];
		if (intval($bID) != 0) {
			$bt = Block::getByID(intval($bID));
		}

		if (!is_object($bt)) {
			return false;
		}

		$cnt = $bt->getController();

		switch ($_REQUEST['action']) {
		case 'submit':
		case 'reviewed_back':
		case 'reviewed_submit':
			$r = $cnt->submit();
			break;

		case 'reset':
			$r = $cnt->reset();
			break;

		case 'upload':
			$r = $cnt->upload_file();
			break;
		}
		if (is_array($r)) {
			return $r;
		}
		$message = strip_tags($r);
		$result["message"] = $message == 'Thank you!' ? 'success' : $message;

		//Send SMS
        if(Config::get('ENABLE_SMS'))
        {
			$bookingNo = $_REQUEST['bookingNo'];
            $booking   = Booking::getByBookingNo($bookingNo);
            if ($booking) {
				$user = UserDetails::getByID($booking->getUID());
				switch ($_REQUEST['form_type']){
					case self::FORM_TYPE_CANCEL:
					case self::FORM_TYPE_AMEND:
						$body = 'We have received your request to cancel your booking. Our Agent will get back to you soon. Reference No :' . $booking->getBID().'.';
						SMS::send($user->getPhone(),Config::get('SMS_API_FROM_NUMBER'),$body);
						break;
				}
			}

        }

		return $result;
	}

	public function getCCMToken() {

		$valt = Loader::helper('validation/token');
		$result["ccm_token"] = $valt->generate('formidable_form');
		return $result;
	}

    public function sendInvite() {

        /** @var TextHelper $th */
        $th = Loader::helper('text');
        $u  = $this->validUser();

        $valc       = Loader::helper('concrete/validation');

        $emails = $th->sanitize($this->post('email'));
        if ($emails)
        {
            $emails = explode(',',$emails);
            $emailsArr = [];
            foreach($emails as $email){
                if($valc->isUniqueEmail($email)){
                    Referral::add($u->getUserID(),$email);
                    $emailsArr[] = $email;
                }
            }
            if($emailsArr){
                Events::fire('on_send_invite',$emailsArr, $u);
            }
            else {
            	$this->addError("This email is already registered  with Driven Holiday Homes.");
            }
        }
    }

	public function getHomepageFilters() {
		/** @var PriceHelper $ph */
		$ph = Loader::helper('price');
		$th = Loader::helper('text');

		$propertyList = new PropertyList();
		$propertyList->populateAverageAndTotalRatings();
		$hpf = $th->sanitize($this->get('filter'));
		$keywords = $th->sanitize($this->get('keywords'));
		$count = (int) $th->sanitize($this->get('count')) ?: 8;

		if ($hpf) {
			$propertyList->filterByHomePageFilters($hpf);
		}
		if ($keywords) {
			$propertyList->filterByKeywords($keywords);
		}
		$properties = $propertyList->get($count);

		$results = [];
		foreach ($properties as $property) {

			$results[] = [
				'pID' => $property->getID(),
				'path' => $property->getPath(),
				'thumbnail' => $property->getThumbnailViaHelper(),
				'title' => $property->getName(),
				'caption' => $property->getCaption(),
				'location' => $property->getLocation(),
				'perDayPrice' => $ph->format($property->getPerDayPrice()),
				'monthlyPrice' => ($property->getMonthlyPrice() > 0)?$ph->format($property->getMonthlyPrice()):$property->getMonthlyPrice(),
				'weeklyPrice' => ($property->getWeeklyPrice() > 0)?$ph->format($property->getWeeklyPrice()):$property->getWeeklyPrice(),
				'isFavorite' => $property->getFavorite(),
				'avgRating' => $property->getAverageRating(),
				'reviews' => $property->getTotalRatings(),
				'maxGuests' => $property->getMaxGuests(),
				'apartmentType' => $property->getApartmentType(),
				'beds' => $property->getBeds(),
			];

		}
		return $results;
	}

	public function getCurrencies() {
		$result = [
			App::LOCALE_EN => 'USD',
			App::LOCALE_DE => 'EUR',
			App::LOCALE_AR => 'AED',
			App::LOCALE_SA => 'SAR',
			App::LOCALE_RU => 'RUB',
			App::LOCALE_KW => 'KWD',
		];
		// $result[App::LOCALE_EN] = [
		//     'abbr' => 'USD',
		//     'name' => 'US Dollar',
		// ];
		// $result[App::LOCALE_DE] = [
		//     'abbr' => 'EUR',
		//     'abbr' => 'US Dollar',
		// ];
		return $result;
	}

	public function setCurrency() {
		$th = Loader::helper('text');
		$locale = $th->sanitize($this->post('locale'));
		$result['currentCurrency'] = App::getSessionLocale();

		if (in_array($locale, array_keys($this->getCurrencies()))) {
			App::setSessionLocale($locale);
			$result['currentCurrency'] = $locale;
		}

		return $result;
	}
}
