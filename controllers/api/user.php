<?php
defined('C5_EXECUTE') or die('Access Denied.');
Loader::controller('/api');

class ApiUserController extends ApiController
{
    const ITEMS_PER_PAGE      = 10;
    const REGULAR_USERS_GROUP = 'Registered Users';

    public function view()
    {
        $u = $this->validUser();

        $userID = $u->getUserID();
        $ui     = UserInfo::getByID($userID);
        if ($ui) {
            if ($u->isSuperUser()) {
                $additionalData = [
                    'userGroup'       => 'Admin',
                    'userBadge'       => $ui->getBadge(),
                    'bookingCount'    => $ui->getBookingCount(),
                    'reviewCount'     => $ui->getReviewCount(),
                    'favouriteCount'  => $ui->getFavouriteCount(),
                    'myProperties'    => $ui->getBookingCount(),
                    'propertyReviews' => $ui->getReviewCount(),
                ];
            } elseif ($u->isLandLord()) {
                $additionalData = [
                    'userGroup'       => 'Landlord',
                    'myProperties'    => $ui->getBookingCount(),
                    'propertyReviews' => $ui->getReviewCount(),
                    'userBadge'       => $ui->getBadge(),
                ];
            } else {
                $additionalData = [
                    'userGroup'      => 'Regular User',
                    'userBadge'      => $ui->getBadge(),
                    'bookingCount'   => $ui->getBookingCount(),
                    'reviewCount'    => $ui->getReviewCount(),
                    'favouriteCount' => $ui->getFavouriteCount(),
                ];
            }
            $userData = [
                'fullName'          => $ui->getFullName(),
                'avatar'            => $ui->getAvatar(),
                'dateOfBirth'       => $ui->getDateOfBirth(),
                'nationality'       => $ui->getNationality(),
                'uEmail'            => $ui->getUserEmail(),
                'phone'             => $ui->getPhone(),
                'passportNo'        => $ui->getPassportNo(),
                'passportValidTill' => $ui->getPassportValidTill(),
                'serviceNews'       => $ui->getServiceNews(),
                'dubaiAdvices'      => $ui->getDubaiAdvices(),
                'relatedProposal'   => $ui->getRelatedProposal(),
                'isSocialLogin'     => !!$ui->getFacebookID() || !!$ui->getGoogleID(),
            ];

            return array_merge($userData, $additionalData);
        }
        $this->addError('No User Found');
        return null;
    }

    public function edit()
    {
        $u = $this->validUser();

        $userID = $u->getUserID();
        $data   = $this->post();
        $ui     = UserInfo::getByID($userID);

        $th = Loader::helper('text');

        if ($data['uPassword']) {
            $u = new User($ui->getUserEmail(), $data['uPasswordOld']);
            if ($u->isError()) {
                return $this->addError('Invalid Password');
            }
            if ($data['uPassword'] != $data['uPasswordConfirm']) {
                return $this->addError('Two passwords do not match');
            }
        }
        if ($ui) {
            $ui->update($data);
            return $this->view();
        }
        $this->addError('No User Found');
        return null;
    }

    public function updateAvatar()
    {
        $u = $this->validUser();

        $userID            = $u->getUserID();
        $ui                = UserInfo::getByID($userID);
        $results           = [];
        $results['status'] = false;
        //Profile Avatar
        if (isset($_FILES) && !empty($_FILES['avatar'])) {
            if ($this->validateUploadedFile($_FILES['avatar'])) {
                $filename = $ui->getUserID() . '.png';
                $filepath = DIR_FILES_UPLOADED_STANDARD . '/avatars/' . $filename;
                $tmp_name = $_FILES['avatar']['tmp_name'];
                $result   = move_uploaded_file($tmp_name, $filepath);
                if ($result) {
                    $img = Loader::helper('image');
                    $img->create($filepath, $filepath, 1200, 1200);
                    $data['uHasAvatar'] = 1;
                    $ui->update($data);
                    $results['status'] = true;
                    $results['avatar'] = $ui->getAvatar();
                }
            }
        }

        return $results;
    }

    public function getAddresses($user_id)
    {
        $ual = new UserAddressList();
        $ual->filterByUserId($user_id);
        $user_addresses = $ual->get();
        return $user_addresses;

    }

    public function myFavourites()
    {
        /** @var TextHelper $th */
        /** @var Property $property */

        $th = Loader::helper('text');
        $u  = $this->validUser();

        $userID = $u->getUserID();
        $pageNo = (int)$th->sanitize($this->get('pageNo')) ?: 1;

        $u = User::getByUserID($userID);
        if (!$u) {
            return null;
        }
        $propertyList = new PropertyList();
        $propertyList->populateAverageAndTotalRatings();

        $propertyList->filterUserFavourites($userID);

        $properties = $propertyList->getPage($pageNo);
        $results    = [];

        foreach ($properties as $property) {

            $results[] = [
                'pID'          => $property->getID(),
                'path'         => $property->getPath(),
                'thumbnail'    => $property->getThumbnailPath(),
                'title'        => $property->getName(),
                'caption'      => $property->getCaption(),
                'location'     => $property->getLocation(),
                'perDayPrice'  => $property->getPerDayPrice(),
                'monthlyPrice' => $property->getMonthlyPrice(),
                'weeklyPrice'  => $property->getWeeklyPrice(),
                'isFavorite'   => true,
                'avgRating'    => $property->getAverageRating(),
                'reviews'      => $property->getTotalRatings()
            ];

        }

        return $results;
    }

    public function myBookings()
    {
        /** @var TextHelper $th */
        /** @var Property $property */

        $th = Loader::helper('text');
        $dh = Loader::helper('date');
        $ph = Loader::helper('price');
        $u  = $this->validUser();

        $userID = $u->getUserID();
        $pageNo = (int)$th->sanitize($this->get('pageNo')) ?: 1;

        $u = User::getByUserID($userID);
        if (!$u) {
            return null;
        }
        $bookingList = new BookingList();
        $bookingList->populatePropertyDetails();
        $bookingList->populateEventStatus();
        $bookingList->filterByBookingStatus(Booking::PAYMENT_COMPLETE);

        $bookingList->filterByUserID($userID);
        $bookings = $bookingList->getPage($pageNo);

        $upcoming = [];
        $past     = [];
        $progress = [];
        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $totalNights = $dh->getNoOfNights($booking->getBookingStartDate(), $booking->getbookingEndDate());
            /** @var Property $property */
            $property       = $booking->getProperty();
            $review         = Review::getByBookingIDAndUserID($booking->getBID(), $userID);
            $rID            = null;
            $reviewRating   = null;
            $reviewComment  = null;
            $reviewEditable = false;
            if ($review) {
                $rID            = $review->getId();
                $reviewRating   = $review->getReviewRating();
                $reviewComment  = $review->getReviewComment();
                $reviewEditable = $review->isEditable();
            }
            $temp = [
                'bID'         => $booking->getBID(),
                'pID'         => $booking->getPID(),
                'thumbnail'   => $property->getThumbnailPath(),
                'title'       => $property->getName(),
                'caption'     => $property->getCaption(),
                'location'    => $property->getLocation(),
                'perDayPrice' => $ph->format($property->getPerDayPrice()),
                'avgRating'   => $property->getAverageRating(),
                'reviews'     => $property->getTotalRatings(),
                'startDate'   => $booking->getBookingStartDate(),
                'endDate'     => $booking->getbookingEndDate(),
                'guests'      => $booking->getNoOfGuest(),
                'totalNights' => $totalNights,
                'total'       => $ph->format($booking->getTotal()),
                'path'        => $property->getPath(),
                'bookingNo'   => $booking->getBookingNo(),
                'rID'         => $rID,
                'myRatings'   => $reviewRating,
                'myComments'  => $reviewComment,
                'editable'    => $reviewEditable

            ];
            if ($booking->getEventStatus() == Booking::UPCOMING_HANDLE) {
                $upcoming[] = $temp;
            } else if ($booking->getEventStatus() == Booking::PAST_HANDLE) {
                $past[] = $temp;
            } else {
                $progress[] = $temp;
            }
        }

        $results = [
            'upcomingTotal'    => count($upcoming),
            'pastTotal'        => count($past),
            'upcomingBookings' => $upcoming,
            'pastBookings'     => $past,
            'inProgress'       => $progress,
        ];

        return $results;
    }

    public function myReviews()
    {
        /** @var TextHelper $th */
        /** @var Property $property */

        $th = Loader::helper('text');
        $dh = Loader::helper('date');
        $u  = $this->validUser();

        $userID       = $u->getUserID();
        $sortByRating = $th->sanitize($this->get('sortByRating')) ?: 'desc';
        $sortByDate   = $th->sanitize($this->get('sortByDate')) ?: 'desc';
        $pageNo       = (int)$th->sanitize($this->get('pageNo')) ?: 1;

        $u = User::getByUserID($userID);
        if (!$u) {
            return null;
        }

        $reviewList = new ReviewList();
        $reviewList->filterByUser($userID);
        $reviewList->sortByReviewRating($sortByRating);
        $reviewList->sortByReviewDate($sortByDate);
        $reviewList->populateBookings();
        $reviewList->populateProperties();

        $reviews = $reviewList->getPage($pageNo);
        $temp    = [];

        /** @var Review $review */
        foreach ($reviews as $review) {
            $property    = $review->getProperty();
            $booking     = $review->getBooking();
            $totalNights = $dh->getNoOfNights($booking->getBookingStartDate(), $booking->getbookingEndDate());
            $temp[]      = [
                'bID'         => $booking->getBID(),
                'pID'         => $booking->getPID(),
                'thumbnail'   => $property->getThumbnailPath(),
                'title'       => $property->getName(),
                'caption'     => $property->getCaption(),
                'location'    => $property->getLocation(),
                'perDayPrice' => $property->getPerDayPrice(),
                'startDate'   => $booking->getBookingStartDate(),
                'endDate'     => $booking->getbookingEndDate(),
                'guests'      => $booking->getNoOfGuest(),
                'totalNights' => $totalNights,
                'rID'         => $review->getId(),
                'myRatings'   => $review->getReviewRating(),
                'myComments'  => $review->getReviewComment(),
                'createdAt'   => $review->getCreatedAt(),
                'editable'    => $review->isEditable()
            ];
        }

        $results = [
            'totalReviews' => count($temp),
            'reviews'      => $temp
        ];
        return $results;
    }

    public function guestReviews()
    {
        /** @var TextHelper $th */
        /** @var Property $property */

        $th = Loader::helper('text');
        $dh = Loader::helper('date');
        $u  = $this->validateLandLordGroup();

        $userID       = $u->getUserID();
        $sortByRating = $th->sanitize($this->get('sortByRating')) ?: 'desc';
        $sortByDate   = $th->sanitize($this->get('sortByDate')) ?: 'desc';
        $pageNo       = (int)$th->sanitize($this->get('pageNo')) ?: 1;


        if (!$userID) {
            return $this->addError('Invalid user ID');
        }

        $reviewList = new ReviewList();
        $reviewList->filterGuestReviewsForUser($userID);
        $reviewList->sortByReviewRating($sortByRating);
        $reviewList->sortByReviewDate($sortByDate);
        $reviewList->populateBookings();
        $reviewList->populateProperties();

        $reviews = $reviewList->getPage($pageNo);
        $temp    = [];

        /** @var Review $review */
        foreach ($reviews as $review) {
            $property = $review->getProperty();
            $booking  = $review->getBooking();

            $totalNights = $dh->getNoOfNights($booking->getBookingStartDate(), $booking->getbookingEndDate());
            $temp[]      = [
                'bID'           => $booking->getBID(),
                'pID'           => $booking->getPID(),
                'thumbnail'     => $property->getThumbnailPath(),
                'title'         => $property->getName(),
                'path'          => $property->getPath(),
                'caption'       => $property->getCaption(),
                'location'      => $property->getLocation(),
                'perDayPrice'   => $property->getPerDayPrice(),
                'startDate'     => $booking->getBookingStartDate(),
                'endDate'       => $booking->getbookingEndDate(),
                'guests'        => $booking->getNoOfGuest(),
                'totalNights'   => $totalNights,
                'rID'           => $review->getId(),
                'reviewRating'  => $review->getReviewRating(),
                'reviewComment' => $review->getReviewComment(),
                'createdAt'     => $review->getCreatedAt(),
                'userName'      => $review->getUserInfo()->getUserName(),
                'fullName'      => $review->getUserInfo()->getFullName(),
                'avatar'        => $review->getUserInfo()->getAvatar(),
                'profilePic'    => '',
                'avgRating'     => $property->getAverageRating()
            ];
        }

        $results = [
            'totalReviews' => count($temp),
            'reviews'      => $temp
        ];
        return $results;
    }

    public function addReview()
    {
        /** @var TextHelper $th */
        /** @var Property $property */

        $th = Loader::helper('text');
        $u  = $this->validUser();

        $userID        = $u->getUserID();
        $bID           = (int)$th->sanitize($this->post('bID'));
        $pID           = (int)$th->sanitize($this->post('pID'));
        $reviewRating  = $th->sanitize($this->post('reviewRating'));
        $reviewComment = $th->sanitize($this->post('reviewComment'));

        $u        = User::getByUserID($userID);
        $booking  = Booking::getByID($bID);
        $property = Property::getByID($pID);

        if (!$booking) {
            return $this->addError('Invalid Booking ID');
        }
        if (!$property) {
            return $this->addError('Invalid property ID');
        }
        if (!$reviewRating) {
            return $this->addError('Invalid Rating');
        }
        if (!$u || $userID != $booking->getUID()) {
            return $this->addError('Invalid User ID');
        }

        $review = Review::add($userID, $pID, $bID, $reviewRating, $reviewComment);
        $result = [
            'rID'           => $review->getId(),
            'reviewRating'  => $review->getReviewRating(),
            'reviewComment' => $review->getReviewComment(),
        ];
        return $result;
    }

    public function updateReview()
    {
        /** @var TextHelper $th */
        /** @var Property $property */

        $th = Loader::helper('text');
        $u  = $this->validUser();

        $userID        = $u->getUserID();
        $rID           = (int)$th->sanitize($this->post('rID'));
        $reviewRating  = $th->sanitize($this->post('reviewRating'));
        $reviewComment = $th->sanitize($this->post('reviewComment'));

        $review = Review::getById($rID);

        $reviewCnt = $review->getUpdateCount();

        $reviewEditable = $review->isEditable();

        if (!$review) {
            return $this->addError('Invalid Review ID');
        }
        if (!$u || $userID != $review->getUserId()) {
            return $this->addError('Invalid User ID');
        }
        if (!$reviewRating) {
            return $this->addError('Invalid Rating');
        }
        if (!$reviewEditable) {
            return $this->addError('You cannot edit the review');
        }
        $reviewCnt++;
        $review->update($reviewRating, $reviewComment, $reviewCnt);
        $result = [
            'rID'           => $review->getId(),
            'reviewRating'  => $review->getReviewRating(),
            'reviewComment' => $review->getReviewComment(),
        ];
        return $result;
    }

    public function deleteReview()
    {
        /** @var TextHelper $th */
        /** @var Property $property */

        $th = Loader::helper('text');
        $u  = $this->validUser();

        $userID = $u->getUserID();
        $rID    = (int)$th->sanitize($this->post('rID'));

        $review = Review::getById($rID);
        $u      = User::getByUserID($userID);

        if (!$review) {
            $this->addError('Invalid Review ID');
        }
        if (!$u || $userID != $review->getUserId()) {
            $this->addError('Invalid User ID');
        }
        $review->delete();
        $result['status'] = 'deleted';
        return $result;
    }

    public function myProperties()
    {
        /** @var TextHelper $th */ /** @var DateHelper $dh */
        /** @var Property $property */

        $th = Loader::helper('text');
        $dh = Loader::helper('date');
        $u  = $this->validateLandLordGroup();

        $userID = $u->getUserID();
        $pageNo = (int)$th->sanitize($this->get('pageNo')) ?: 1;

        $propertyList = new PropertyList();
        $propertyList->populateAverageAndTotalRatings();
        $propertyList->populateAvailability();
        $propertyList->filterByOwner($userID);

        $properties = $propertyList->getPage($pageNo);
        $results    = [];

        foreach ($properties as $property) {

            $results[] = [
                'pID'           => $property->getID(),
                'path'          => $property->getPath(),
                'thumbnail'     => $property->getThumbnailPath(),
                'title'         => $property->getName(),
                'caption'       => $property->getCaption(),
                'location'      => $property->getLocation(),
                'perDayPrice'   => $property->getPerDayPrice(),
                'monthlyPrice'  => $property->getMonthlyPrice(),
                'weeklyPrice'   => $property->getWeeklyPrice(),
                'isFavorite'    => true,
                'avgRating'     => $property->getAverageRating(),
                'reviews'       => $property->getTotalRatings(),
                'blockedDates'  => $property->getBlockedDates(),
                'bookingStatus' => $property->getBookingStatus()
            ];

        }

        return $results;
    }

    public function statistics()
    {
        /** @var TextHelper $th */ /** @var DateHelper $dh */
        /** @var Property $property */

        $th = Loader::helper('text');
        $u  = $this->validateLandLordGroup();

        $userID     = $u->getUserID();
        $propertyID = (int)$th->sanitize($this->get('propertyID'));
        $startDate  = $th->sanitize($this->get('startDate'));
        $endDate    = $th->sanitize($this->get('endDate'));
        $p_year     = $th->sanitize($this->get('year'));
        $p_year     = is_numeric($p_year) && $p_year > 0 ? $p_year : date('Y');

        if (!$userID) {
            $this->addError('Invalid user ID');
        }
        if ($this->hasErrors()) {
            return null;
        }

        $staticsList = new StatisticList();

        if ($startDate && $endDate) {
            $staticsList->setDateRange($startDate, $endDate);
        }
        $staticsList->filterByUserID($userID);
        $staticsList->filterByYear($p_year);
        if ($propertyID) {
            $staticsList->filterByPropertyID($propertyID);
        }
        $statistics = $staticsList->get();

        $results      = [];
        $currentMonth = date('Y-m');

        /** @var Statistic $statistic */

        $dateHelper = new DateHelper();
        foreach ($statistics as $statistic) {
            $b_startDate  = strtotime($statistic->getBookingStartDate());
            $b_endDate    = strtotime($statistic->getBookingEndDate());
            $b_startMonth = date('Y-m', $b_startDate);


            if ($b_startMonth === date('Y-m', $b_endDate)) {
                if ($statistic->getBookingStatus() == 'paid') {
                    if ($b_startMonth < $currentMonth) {
                        $statistic->setPaidOut($statistic->getTotal());
                    } else {
                        $statistic->setExpected($statistic->getTotal());
                    }
                }
                ## Abbreviation change for API ##
                if ($statistic->getMonth() == 'SEP') {
                    $statistic->setMonth('SEPT');
                }

                $results[$statistic->getYear()]['statistic'][$statistic->getMonth()]['monthlyTotal'] += $statistic->getTotal();
                $results[$statistic->getYear()]['statistic'][$statistic->getMonth()]['paidOut']      += $statistic->getPaidOut();
                $results[$statistic->getYear()]['statistic'][$statistic->getMonth()]['expected']     += $statistic->getExpected();
                $results[$statistic->getYear()]['statistic'][$statistic->getMonth()]['nights']       += $statistic->getAvgNights();


                $results[$statistic->getYear()]['paidOut']     += $statistic->getPaidOut();
                $results[$statistic->getYear()]['expected']    += $statistic->getExpected();
                $results[$statistic->getYear()]['yearlyTotal'] += $statistic->getTotal();

                if ($b_startMonth < $currentMonth) {
                    $results[$statistic->getYear()]['avgNights'] += $statistic->getAvgNights();
                } else {
                    $results[$statistic->getYear()]['avgNights'] += 0;
                }

            } else {

                $date        = $b_startDate;
                $splitResult = [];
                $noOfDays    = ($dateHelper->getNoOfNights($statistic->getBookingStartDate(), $statistic->getBookingEndDate()));

                $perDayTotal = round($statistic->getTotal() / $noOfDays, 2);
                while ($date < $b_endDate) {
                    $year    = date('Y', $date);
                    $month   = strtoupper(date('M', $date));
                    $b_month = date('Y-m', $date);

                    ## Abbreviation change for API ##
                    if ($month == 'SEP') {
                        $month = 'SEPT';
                    }

                    $splitResult[$year][$month]['monthNights']  += 1;
                    $splitResult[$year][$month]['monthlyTotal'] += $perDayTotal;
                    if ($statistic->getBookingStatus() == 'paid') {
                        if ($b_month < $currentMonth) {
                            $splitResult[$year][$month]['paidOut'] += $perDayTotal;

                        } else {
                            $splitResult[$year][$month]['expected'] += $perDayTotal;
                        }
                    }

                    if ($b_month < $currentMonth) {
                        $splitResult[$year][$month]['avgNights'] += 1;

                    } else {
                        $splitResult[$year][$month]['avgNights'] += 0;
                    }


                    $date = strtotime("+1 day", $date);

                }


                foreach ($splitResult as $yKey => $yearly) {
                    foreach ($yearly as $mKey => $monthly) {
                        $results[$yKey]['statistic'][$mKey]['monthlyTotal'] += $monthly['monthlyTotal'];
                        $results[$yKey]['statistic'][$mKey]['paidOut']      += $monthly['paidOut'];
                        $results[$yKey]['statistic'][$mKey]['expected']     += $monthly['expected'];
                        $results[$yKey]['statistic'][$mKey]['nights']       += $monthly['monthNights'];

                        $results[$yKey]['paidOut']     += $monthly['paidOut'];
                        $results[$yKey]['expected']    += $monthly['expected'];
                        $results[$yKey]['yearlyTotal'] += $monthly['monthlyTotal'];
                        $results[$yKey]['avgNights']   += $monthly['avgNights'];
                    }

                }

            }
        }
        unset($statistics, $splitResult);

        $blockDateList = new PropertyBlockDatesList();
        $blockDateList->populateProperty();
        $blockDateList->setOwner($userID);
        $blockDateList->filterPrice();
        $blockDateList->filterByYear($p_year);
        $blockDateList->groupByCustom();
        if ($propertyID) {
            $blockDateList->filterByPropertyID($propertyID);
        }
        if ($startDate && $endDate) {
            $blockDateList->filterByStartDateBetween($startDate, $endDate);
        }
        $blocks = $blockDateList->get();

        /** @var PropertyBlockDates $block */
        if ($blocks) {
            foreach ($blocks as $block) {

                $b_startDate  = strtotime($block->getStartDate());
                $b_endDate    = strtotime($block->getEndDate());
                $b_startMonth = date('Y-m', $b_startDate);


                if ($b_startMonth === date('Y-m', $b_endDate)) {
                    ## Abbreviation change for API ##
                    if ($block->getMonth() == 'SEP') {
                        $block->setMonth('SEPT');
                    }

                    $results[$block->getYear()]['statistic'][$block->getMonth()]['monthlyTotal'] += $block->getTotal();
                    if ($b_startMonth < $currentMonth) {

                        $results[$block->getYear()]['statistic'][$block->getMonth()]['paidOut'] += $block->getTotal();
                        $results[$block->getYear()]['paidOut']                                  += $block->getTotal();

                        $results[$block->getYear()]['statistic'][$block->getMonth()]['expected'] += 0;
                        $results[$block->getYear()]['expected']                                  += 0;

                        $results[$block->getYear()]['avgNights'] += $block->getAvgNights();

                    } else {

                        $results[$block->getYear()]['statistic'][$block->getMonth()]['paidOut'] += 0;
                        $results[$block->getYear()]['paidOut']                                  += 0;


                        $results[$block->getYear()]['statistic'][$block->getMonth()]['expected'] += $block->getTotal();
                        $results[$block->getYear()]['expected']                                  += $block->getTotal();

                        $results[$block->getYear()]['avgNights'] += 0;
                    }


                    $results[$block->getYear()]['statistic'][$block->getMonth()]['nights'] += $block->getAvgNights();
                    $results[$block->getYear()]['yearlyTotal']                             += $block->getTotal();


                } else {
                    $date        = $b_startDate;
                    $splitResult = [];
                    $noOfDays    = ($dateHelper->getNoOfNights($block->getStartDate(), $block->getEndDate()));


                    $perDayTotal = round($block->getTotal() / $noOfDays, 2);
                    while ($date < $b_endDate) {
                        $year    = date('Y', $date);
                        $month   = strtoupper(date('M', $date));
                        $b_month = date('Y-m', $date);

                        ## Abbreviation change for API ##
                        if ($month == 'SEP') {
                            $month = 'SEPT';
                        }

                        $splitResult[$year][$month]['monthNights']  += 1;
                        $splitResult[$year][$month]['monthlyTotal'] += $perDayTotal;
                        if ($b_month < $currentMonth) {
                            $splitResult[$year][$month]['paidOut']   += $perDayTotal;
                            $splitResult[$year][$month]['avgNights'] += 1;

                        } else {
                            $splitResult[$year][$month]['expected']  += $perDayTotal;
                            $splitResult[$year][$month]['avgNights'] += 0;
                        }
                        $date = strtotime("+1 day", $date);

                    }

                    foreach ($splitResult as $yKey => $yearly) {
                        foreach ($yearly as $mKey => $monthly) {
                            $results[$yKey]['statistic'][$mKey]['monthlyTotal'] += $monthly['monthlyTotal'];
                            $results[$yKey]['statistic'][$mKey]['paidOut']      += $monthly['paidOut'];
                            $results[$yKey]['statistic'][$mKey]['expected']     += $monthly['expected'];
                            $results[$yKey]['statistic'][$mKey]['nights']       += $monthly['monthNights'];

                            $results[$yKey]['yearlyTotal'] += $monthly['monthlyTotal'];
                            $results[$yKey]['paidOut']     += $monthly['paidOut'];
                            $results[$yKey]['expected']    += $monthly['expected'];
                            $results[$yKey]['avgNights']   += $monthly['avgNights'];
                        }

                    }
                }
            }


        }

        foreach ($results as $key => $result) {

            $pastMonths = $this->getPastMonths($key, $result['statistic']);
            if (count($pastMonths) > 0) {
                $results[$key]['avgNights'] /= count($pastMonths);
            }
            $results[$key]['avgNights'] = round($results[$key]['avgNights'], 2);
        }

        if ($results[$p_year] == null) {
            $results[$p_year] = [
                'yearlyTotal' => 0,
                'paidOut'     => 0,
                'expected'    => 0,
                'avgNights'   => 0,
                'statistic'   => new stdClass()
            ];
        }
        return [$p_year => $results[$p_year]];
    }

    public function notifications()
    {
        /** @var TextHelper $th */

        $th = Loader::helper('text');
        $u  = $this->validUser();

        $userID = $u->getUserID();
        $pageNo = (int)$th->sanitize($this->get('pageNo')) ?: 1;
        $new    = $th->sanitize($this->get('new')) ?: 0;

        if (!$userID) {
            $this->addError('Invalid user ID');
        }
        if ($this->hasErrors()) {
            return null;
        }

        $notificationList = new NotificationList();
        $notificationList->sortBy('nID', 'desc');
        $notificationList->filterByUserID($userID);
        $notificationList->setItemsPerPage(self::ITEMS_PER_PAGE);
        if ($new) {
            $notificationList->filterByReadStatus(0);
        }

        $notifications = $notificationList->getPage($pageNo);
        $result        = [];

        /** @var Notification $notification */
        foreach ($notifications as $notification) {
            array_push($result, [
                'nID'        => $notification->getID(),
                'title'      => $notification->getTitle(),
                'body'       => $notification->getBody(),
                'readStatus' => $notification->getReadStatus(),
                'link'       => $notification->getLink(),
                'contentID'  => $notification->getContentID(),
                'createdAt'  => $notification->getCreatedAt(),
            ]);
        }

        return $result;
    }

    public function markNotificationAsRead()
    {
        /** @var TextHelper $th */

        $th = Loader::helper('text');
        $u  = $this->validUser();

        $userID = $u->getUserID();
        $nID    = (int)$th->sanitize($this->post('nID'));

        if (!$userID) {
            $this->addError('Invalid user ID');
        }
        if (!$nID) {
            $this->addError('Invalid Notification ID');
        }
        if ($this->hasErrors()) {
            return null;
        }

        $notification = Notification::getByID($nID);
        if (!$notification) {
            return $this->addError('Invalid Notification ID');
        }
        $notification = $notification->updateReadStatus();
        return [
            'nID'        => $notification->getID(),
            'title'      => $notification->getTitle(),
            'body'       => $notification->getBody(),
            'readStatus' => $notification->getReadStatus(),
            'link'       => $notification->getLink(),
            'contentID'  => $notification->getContentID(),
            'createdAt'  => $notification->getCreatedAt(),
        ];
    }

    public function clearAllNotifications()
    {
        /** @var TextHelper $th */

        $th = Loader::helper('text');
        $u  = $this->validUser();

        $userID = $u->getUserID();

        if (!$userID) {
            $this->addError('Invalid user ID');
        }
        if ($this->hasErrors()) {
            return null;
        }
        Notification::deleteAll($userID);
        return $result['success'] = true;
    }

    private function validateUploadedFile($uploadedFile)
    {
        if (!$uploadedFile) {
            return false;
        } else if (!isset($uploadedFile['tmp_name']) || empty($uploadedFile['tmp_name'])) {
            return false;
        } else if (!is_uploaded_file($uploadedFile['tmp_name'])) {
            return false;
        } else if ($uploadedFile['error'] != UPLOAD_ERR_OK) {
            return false;
        } else if (!Loader::helper('validation/file')->extension($uploadedFile['name'], array(
            'jpg',
            'jpeg',
            'png',
            'gif'
        ))) {
            return false;
        } else {
            return true;
        }
    }

    public function getTotalDays($year)
    {
        $dh = new DateHelper();
        $startDate = date($year.'-01-01');

        if($year == date('Y')) {
            $month = date('m');
            $month = $month - 1;
            if($month <= 0) {
                return false;
            }
            $endDate = date("Y-n-j", strtotime("last day of previous month"));
        } else if($year > date('Y')) {
            return false;
        }else {
            $endDate = date($year.'-12-31');
        }
        if($startDate && $endDate) {
            return $dh->getNoOfNights($startDate,$endDate)+1;
        }
        return false;
    }

    public function getPastMonths($year, $monthData)
    {
        $result = [];
        foreach ($monthData as $key => $value) {
            $month = $key;
            if ($month == 'SEPT') {
                $month = 'SEP';
            }
            if (date('Y-m', strtotime($year . " " . $month)) < date('Y-m')) {
                $result[] = $key;
            }
        }
        return $result;
    }
}