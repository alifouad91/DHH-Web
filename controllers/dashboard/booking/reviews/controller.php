<?php

class DashboardBookingReviewsController extends DashboardBaseController
{
    protected $configURL = 'dashboard/booking/reviews';
    protected $pluginsPath = DIR_REL . JS_PLUGINS_DIR;
    const RATINGS = [
            1=>1,
            2=>2,
            3=>3,
            4=>4,
            5=>5,
        ];

    public function view($arg = false)
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');

        $keywords  = $th->sanitize($this->request('keywords'));

        $reviewList = new ReviewList();
        $reviewList->populateUsers();
        $reviewList->populateBookings();
        $reviewList->populateProperties();
        $reviewList->filterByKeywords($keywords);
        $reviewList->setItemsPerPage(10 );

        $reviews = $reviewList->getPage();

        $this->set('task', 'overview');
        $this->set('reviewList', $reviewList);
        $this->set('reviews', $reviews);
        $this->set('configURL', $this->configURL);
        if ($arg) {
            switch ($arg) {
                case 'deleted':
                    $this->set('message', 'Successfully deleted!');
                    break;
            }
        }
    }

    public function detail($id, $arg2 = false)
    {
        $htmlHelper = Loader::helper('html');
        $review = Review::getByID($id);
        $this->set('task', 'detail');
        $this->set('ratings', self::RATINGS);
        $this->set('review', $review);
        $this->set('configURL', $this->configURL);
        $token_helper = Loader::helper('validation/token');
        $fetch_user_token = $token_helper->generate('properties.fetch_user');
        $fetch_booking_token = $token_helper->generate('properties.fetch_booking');
        $this->set('fetchUserToken',$fetch_user_token);
        $this->set('fetchBookingToken',$fetch_booking_token);



        $this->loadFlatPickrPlugin();
        $this->loadSelect2Plugin();
        $this->addFooterItem($htmlHelper->javascript('populateRegisteredUsers.js'));
        $this->addFooterItem($htmlHelper->javascript('populateBookings.js'));
        $this->addFooterItem($htmlHelper->javascript('bill.js'));
        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Successfully updated');
                    break;
                case 'saved':
                    $this->set('message', 'Successfully saved!');
                    break;
                case 'deleted':
                    $this->set('message', 'Successfully deleted!');
                    break;
            }
        }
    }

    public function add()
    {
        $htmlHelper = Loader::helper('html');
        $this->set('task', 'add');
        $this->set('ratings', self::RATINGS);
        $this->set('configURL', $this->configURL);
        $token_helper = Loader::helper('validation/token');
        $fetch_user_token = $token_helper->generate('properties.fetch_user');
        $fetch_booking_token = $token_helper->generate('properties.fetch_booking');
        $this->set('fetchUserToken',$fetch_user_token);
        $this->set('fetchBookingToken',$fetch_booking_token);



        $this->loadFlatPickrPlugin();
        $this->loadSelect2Plugin();
        $this->addFooterItem($htmlHelper->javascript('populateRegisteredUsers.js'));
        $this->addFooterItem($htmlHelper->javascript('populateBookings.js'));
        $this->addFooterItem($htmlHelper->javascript('bill.js'));
    }

    public function save()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');
        /** @var $eh ValidationErrorHelper */
        $eh = Loader::helper('validation/error');
        /** @var DateHelper $dh */
        $dh = Loader::helper('date');

        $uId            = $th->sanitize($this->post('uId'));
        $pId            = $th->sanitize($this->post('pId'));
        $bId            = $th->sanitize($this->post('bId'));
        $reviewRating   = $th->sanitize($this->post('reviewRating'));
        $reviewComment  = $th->sanitize($this->post('reviewComment'));

        if (!$uId) {
            $eh->add('Please enter User');
        }
        if (!$pId) {
            $eh->add('Please enter a Property');
        }
        if (!$bId) {
            $eh->add('Please enter a Booking');
        }

        if (!$reviewRating) {
            $eh->add('Please enter rating');
        }

        if (!$eh->has()) {
            $review = Review::add($uId, $pId, $bId, $reviewRating, $reviewComment);
            $this->redirect($this->configURL . '/detail/' . $review->getID() . '/saved');
        }

        $this->set('error', $eh);
        $this->add();
    }

    public function update()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');
        /** @var $eh ValidationErrorHelper */
        $eh = Loader::helper('validation/error');
        /** @var DateHelper $dh */
        $dh = Loader::helper('date');


        $reviewID   = $th->sanitize($this->post('reviewID'));
        $review     = Review::getByID($reviewID);

        if ($review === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $uId            = $th->sanitize($this->post('uId'));
            $pId            = $th->sanitize($this->post('pId'));
            $bId            = $th->sanitize($this->post('bId'));
            $reviewRating   = $th->sanitize($this->post('reviewRating'));
            $reviewComment  = $th->sanitize($this->post('reviewComment'));

            if (!$uId) {
                $eh->add('Please enter User');
            }
            if (!$pId) {
                $eh->add('Please enter a Property');
            }
            if (!$bId) {
                $eh->add('Please enter a Booking');
            }

            if (!$reviewRating) {
                $eh->add('Please enter rating');
            }

            if (!$eh->has()) {

                $review->updateAdmin($uId, $pId, $bId, $reviewRating, $reviewComment);
                $this->redirect($this->configURL . '/detail/' . $review->getID() . '/updated');
            }

            $this->set('error', $eh);
            $this->detail($review->getID());
        }
    }

    public function delete($rID)
    {
        if (!$rID) {
            $this->redirect($this->configURL);
        }
        $review = Review::getByID($rID);

        $review->delete();

        $this->redirect($this->configURL . '/deleted');
    }

    protected function loadFlatPickrPlugin()
    {
        /** @var HtmlHelper $html */
        $html = Loader::helper('html');

        $this->addHeaderItem($html->css($this->pluginsPath . "/flatpickr/flatpickr.min.css"));
        $this->addFooterItem($html->javascript($this->pluginsPath . "/flatpickr/flatpickr.min.js"));
    }

    protected function loadSelect2Plugin()
    {
        /** @var HtmlHelper $html */
        $html = Loader::helper('html');

        $this->addHeaderItem($html->css($this->pluginsPath . "/select2/select2.min.css"));
        $this->addFooterItem($html->javascript($this->pluginsPath . "/select2/select2.min.js"));
    }

}