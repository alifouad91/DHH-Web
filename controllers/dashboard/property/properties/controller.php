<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyPropertiesController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/properties';
    protected $pluginsPath = DIR_REL . JS_PLUGINS_DIR;

    const DETAIL_URL = '/detail/';

    public function view()
    {
        $this->overview();
        $this->set('configURL', $this->configURL);
    }

    public function add_save()
    {
        /** @var TextHelper $th */
        $th = Loader::helper('text');
        $e  = Loader::helper('validation/error');

        $name            = $th->sanitize($this->post('name'));
        $caption         = $th->sanitize($this->post('caption'));
        $description     = $th->sanitize($this->post('description'));
        $latitude        = $th->sanitize($this->post('latitude'));
        $longitude       = $th->sanitize($this->post('longitude'));
        $locationId      = $th->sanitize($this->post('locationID'));
        $noOfRooms       = $th->sanitize($this->post('noOfRooms'));
        $bedrooms        = $th->sanitize($this->post('bedrooms'));
        $bathrooms       = $th->sanitize($this->post('bathrooms'));
        $maxGuests       = $th->sanitize($this->post('maxGuests'));
        $beds            = $th->sanitize($this->post('beds'));
        $apartmentAreaID = $th->sanitize($this->post('apartmentAreaID'));
        $minNights       = $th->sanitize($this->post('minNights'));
        $noOfRooms       = $noOfRooms ? $noOfRooms : 1;
        $apartmentAreaID = $apartmentAreaID ? $apartmentAreaID : 0;
        $maxGuests       = $maxGuests ? $maxGuests : 0;
        $beds            = $beds ? $beds : 0;

        $areaTypeID      = $th->sanitize($this->post('areaTypeID'));
        $apartmentTypeID = $th->sanitize($this->post('apartmentTypeID'));
        $monthlyPrice    = $th->sanitize($this->post('monthlyPrice'));
        $weeklyPrice     = $th->sanitize($this->post('$weeklyPrice'));
        $perDayPrice     = $th->sanitize($this->post('perDayPrice'));
        $owner           = $th->sanitize($this->post('uID'));
        $tourismFee      = $th->sanitize($this->post('tourismFee'));
        $checkInTime     = $th->sanitize($this->post('checkInTime'));
        $checkOutTime    = $th->sanitize($this->post('checkOutTime'));
        $status          = $th->sanitize($this->post('status'));
        $monthlyPrice    = $monthlyPrice ? $monthlyPrice : 0;
        $weeklyPrice     = $weeklyPrice ? $weeklyPrice : 0;
        $perDayPrice     = $perDayPrice ? $perDayPrice : 0;

        $amenities       = $this->post('amenities');
        $facilities      = $this->post('facilities');
        $homePageFilters = $this->post('homePageFilters');

        if (!$name) {
            $e->add('Property name is required');
        }
        if (!$caption) {
            $e->add('Caption is required');
        }
        if (!$description) {
            $e->add('Description is required');
        }
        if (!$owner) {
            $e->add('Owner name is required');
        }
        if (!$tourismFee) {
            $e->add('Tourism fee is required');
        }
        if (!$checkInTime) {
            $e->add('CheckIn Time is required');
        }
        if (!$checkOutTime) {
            $e->add('CheckOut Time is required');
        }
        if (!$minNights) {
            $e->add('Minimum Nights is required');
        }

        if (!$e->has()) {


            $property = Property::add($name, $caption, $description, $latitude, $longitude, $locationId, $noOfRooms, $bedrooms, $bathrooms, $maxGuests, $beds, $apartmentAreaID, $areaTypeID, $apartmentTypeID, $monthlyPrice, $perDayPrice, $owner, $checkInTime, $checkOutTime, $weeklyPrice, $tourismFee, $minNights);

            UserLogs::add($property->getName(), 'added_property');

            if ($amenities) {
                $property->updateAmenities($amenities);
            }
            if ($facilities) {
                $property->updateFacilities($facilities);
            }
            if ($homePageFilters) {
                $property->updateHomePageFilters($homePageFilters);
            }


            $this->redirect($this->configURL . self::DETAIL_URL . $property->getID() . '/saved');
        }
        $this->set('error', $e);
        $this->add();

    }

    public function add()
    {
        $htmlHelper = Loader::helper('html');
        $this->loadFlatPickrPlugin();
        $this->loadSelect2Plugin();
        $token_helper = Loader::helper('validation/token');
        $token        = $token_helper->generate('properties.fetch_user');

        $locationOptions      = Location::getAll(true);
        $apartmentAreaOptions = ApartmentArea::getAll(true);
        $apartmentTypeOptions = ApartmentType::getAll(true);
        $areaTypeOptions      = AreaType::getAll(true);
        $statusOptions        = [
            '0' => 'In Active',
            '1' => 'Active'
        ];

        $this->set('bedroomOptions', Property::BEDROOM_OPTIONS);
        $this->set('statusOptions', $statusOptions);
        $this->set('locationOptions', ['' => 'Select Location'] + $locationOptions);
        $this->set('apartmentAreaOptions', ['' => 'Select Apartment Area'] + $apartmentAreaOptions);
        $this->set('apartmentTypeOptions', ['' => 'Select Apartment Type'] + $apartmentTypeOptions);
        $this->set('areaTypeOptions', ['' => 'Select Area type'] + $areaTypeOptions);
        $this->set('fetchUserToken', $token);
        $this->set('task', 'add');
        $this->set('configURL', $this->configURL);
        $this->addFooterItem($htmlHelper->javascript('populateUsers.js'));
    }

    protected function overview()
    {
        $filterOptions = [
            ''          => 'All',
            'active'    => 'Active',
            'in_active' => 'In Active',
            //            'exclusive' => 'Exclusive',
        ];

        $itemsOptions = [
            10  => 10,
            25  => 25,
            50  => 50,
            100 => 100,
            500 => 500,
        ];

        $locationOptions = ApartmentArea::getAll(true);

        $keywords = $this->request('keywords');
        $filter   = $this->request('filter');
        $locale   = $this->request('locale');
        $items    = intval($this->request('items'));
        $page     = intval($this->request('page')) ?: 1;
        $items    = in_array($items, $itemsOptions) ? $items : 10;

        $propertyList = new PropertyList();

        switch ($filter) {
            case 'active':
                $propertyList->filterByStatus(1);
                break;
            case 'in_active':
                $propertyList->filterByStatus(0);
                break;
            //            case 'exclusive':
            //                $propertyList->filterByExclusive();
            //                break;
            default:
                break;
        }


        if ($keywords) {
            $propertyList->filter(false, "(p.name like '%" . $keywords . "%' OR p.caption like '%" . $keywords . "%')");
        }

        $propertyList->populateByStatus(false);
        $propertyList->setItemsPerPage($items);
        $properties = $propertyList->getPage($page);

        $this->set('filterOptions', $filterOptions);
        $this->set('itemsOptions', $itemsOptions);
        $this->set('locationOptions', $locationOptions);
        $this->set('propertyList', $propertyList);
        $this->set('properties', $properties);
        $this->set('task', 'overview');
    }


    public function detail($propertyID, $arg2 = false)
    {
        $htmlHelper = Loader::helper('html');
        if (!$propertyID) {
            $this->redirect($this->configURL);
        }
        $property = Property::getByID($propertyID);
        $this->loadFlatPickrPlugin();
        $this->loadSelect2Plugin();

        $token_helper = Loader::helper('validation/token');
        $token        = $token_helper->generate('properties.fetch_user');

        $locationOptions      = Location::getAll(true);
        $apartmentAreaOptions = ApartmentArea::getAll(true);
        $apartmentTypeOptions = ApartmentType::getAll(true);
        $areaTypeOptions      = AreaType::getAll(true);
        $statusOptions        = [
            '1' => 'Active',
            '0' => 'In Active'
        ];

        $this->set('task', 'detail');
        $this->set('property', $property);
        $this->set('statusOptions', $statusOptions);
        $this->set('locationOptions', ['' => 'Select Location'] + $locationOptions);
        $this->set('apartmentAreaOptions', ['' => 'Select Apartment Area'] + $apartmentAreaOptions);
        $this->set('apartmentTypeOptions', ['' => 'Select Apartment Type'] + $apartmentTypeOptions);
        $this->set('areaTypeOptions', ['' => 'Select Area type'] + $areaTypeOptions);
        $this->set('fetchUserToken', $token);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Property details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Property details saved!');
                    break;
            }
        }
        $this->set('bedroomOptions', Property::BEDROOM_OPTIONS);
        $this->set('configURL', $this->configURL);
        $this->addFooterItem($htmlHelper->javascript('populateUsers.js'));
    }


    public function edit_save()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th            = Loader::helper('text');
        $e             = Loader::helper('validation/error');
        $canUpdatePath = false;
        $oldPath       = '';
        $property      = Property::getByID($this->post('propertyID'));

        if ($property === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name            = $th->sanitize($this->post('name'));
            $caption         = $th->sanitize($this->post('caption'));
            $description     = $th->sanitize($this->post('description'));
            $latitude        = $th->sanitize($this->post('latitude'));
            $longitude       = $th->sanitize($this->post('longitude'));
            $locationId      = $th->sanitize($this->post('locationID'));
            $noOfRooms       = $th->sanitize($this->post('noOfRooms'));
            $minNights       = $th->sanitize($this->post('minNights'));
            $bedrooms        = $th->sanitize($this->post('bedrooms'));
            $bathrooms       = $th->sanitize($this->post('bathrooms'));
            $maxGuests       = $th->sanitize($this->post('maxGuests'));
            $beds            = $th->sanitize($this->post('beds'));
            $apartmentAreaID = $th->sanitize($this->post('apartmentAreaID'));
            if (!$apartmentAreaID) {
                $apartmentAreaID = 0;
            }

            $areaTypeID = $th->sanitize($this->post('areaTypeID'));

            $apartmentTypeID = $th->sanitize($this->post('apartmentTypeID'));
            $monthlyPrice    = $th->sanitize($this->post('monthlyPrice'));
            $weeklyPrice     = $th->sanitize($this->post('weeklyPrice'));
            $perDayPrice     = $th->sanitize($this->post('perDayPrice'));
            $owner           = $th->sanitize($this->post('uID'));
            $checkInTime     = $th->sanitize($this->post('checkInTime'));
            $checkOutTime    = $th->sanitize($this->post('checkOutTime'));
            $status          = $th->sanitize($this->post('status'));
            $tourismFee      = $th->sanitize($this->post('tourismFee'));
            $pPath           = $th->sanitize($this->post('pPath'));

            $amenities = $this->post('amenities');

            $facilities      = $this->post('facilities');
            $homePageFilters = $this->post('homePageFilters');

            if (!$name) {
                $e->add('Property name is required');
            }
            if (!$caption) {
                $e->add('Caption is required');
            }
            if (!$description) {
                $e->add('Description is required');
            }
            if (!$owner) {
                $e->add('Owner name is required');
            }
            if (!$tourismFee) {
                $e->add('Tourism fee is required');
            }
            if (!$bedrooms) {
                $e->add('Bedroom is required');
            }
            if (!$minNights) {
                $e->add('Minimum Nights is required');
            }
            if (!$bathrooms) {
                $e->add('Bathroom is required');
            }
            if ($pPath) {
                if (!$this->validSlug($pPath)) {
                    $e->add('Invalid property URL');
                } else {
                    $oldPath = $property->getPath();
                    if ($oldPath !== $pPath) {
                        $tempPath = $property->getUniquePath($pPath);
                        if ($tempPath !== $pPath) {
                            $e->add('Property URL already exist');
                        } else {
                            $canUpdatePath = true;
                            $oldPath       = $property->getUniquePath($oldPath, Property::PATH_OLD);
                        }
                    }
                }
            }

            $fieldListArr = [
                'name'            => $name,
                'caption'         => $caption,
                'description'     => $description,
                'latitude'        => $latitude,
                'longitude'       => $longitude,
                'locationID'      => $locationId,
                'noOfRooms'       => $noOfRooms,
                'bedrooms'        => $bedrooms,
                'bathrooms'       => $bathrooms,
                'maxGuests'       => $maxGuests,
                'beds'            => $beds,
                'apartmentAreaID' => $apartmentAreaID,
                'areaTypeID'      => $areaTypeID,
                'apartmentTypeID' => $apartmentTypeID,
                'monthlyPrice'    => $monthlyPrice,
                'weeklyPrice'     => $weeklyPrice,
                'perDayPrice'     => $perDayPrice,
                'owner'           => $owner,
                'checkInTime'     => $checkInTime,
                'checkOutTime'    => $checkOutTime,
                'status'          => $status,
                'amenities'       => $amenities,
                'homePageFilters' => $homePageFilters,
                'minNights'       => $minNights,
            ];

            if (!$e->has()) {

                $property->update($name, $caption, $description, $latitude, $longitude, $locationId, $noOfRooms, $bedrooms, $bathrooms, $maxGuests, $beds, $apartmentAreaID, $areaTypeID, $apartmentTypeID, $monthlyPrice, $perDayPrice, $owner, $checkInTime, $checkOutTime, $status, $weeklyPrice, $tourismFee, $minNights);

                $fieldChanged = UserLogs::comparePropertyFieldsValue($property, $fieldListArr);
                if ($fieldChanged) {
                    UserLogs::add($fieldChanged, 'edited_property');
                }

                if ($amenities) {
                    $property->updateAmenities($amenities);
                }
                if ($facilities) {
                    $property->updateFacilities($facilities);
                }
                if ($homePageFilters || (!$homePageFilters && $property->getHomePageFilters())) {
                    $property->updateHomePageFilters($homePageFilters);
                }
                if ($canUpdatePath) {
                    $property->updatePath(Property::PATH, $pPath);
                    $property->updatePath(Property::PATH_OLD, $oldPath);
                }

                $this->redirect($this->configURL . self::DETAIL_URL . $property->getID() . '/updated');
            }
            $this->set('error', $e);
            $this->detail($property->getID());
        }

    }

    protected function validSlug($str)
    {
        return preg_match('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $str);
    }

    public function add_images($propertyID, $status = false)
    {
        if (!$propertyID) {
            $this->redirect($this->configURL);
        }
        $property       = Property::getByID($propertyID);
        $propertyImages = $property->getPropertyImages();
        $bgPosition     = [
            ''       => 'Select position',
            'top'    => 'Top',
            'center' => 'Center',
            'bottom' => 'Bottom',
        ];

        $this->set('propertyImages', $propertyImages);
        $this->set('property', $property);
        $this->set('bgPosition', $bgPosition);
        $this->set('task', 'add_images');
        $this->set('configURL', $this->configURL);
        switch ($status) {
            case 'saved':
                $this->set('message', 'Image successfully saved');
                break;
            case 'deleted':
                $this->set('message', 'Image successfully deleted');
                break;
            case 'error':
                $this->set('message', 'Error saving image! Please upload the images of maximun size of 500kb and not more than 2000*2000px ');
                break;
        }
    }

    public function delete($propertyID)
    {
        if (!$propertyID) {
            $this->redirect($this->configURL);
        }
        $property = Property::getByID($propertyID);


        UserLogs::add($property->getName(), 'deleted_property');

        $property->delete();

        $this->redirect($this->configURL);
    }

    public function add_property_rules($propertyID, $status = false)
    {
        if (!$propertyID) {
            $this->redirect($this->configURL);
        }
        $property = Property::getByID($propertyID);
        $this->set('property', $property);
        $this->set('task', 'add_property_rules');
        $this->set('configURL', $this->configURL);
        switch ($status) {
            case 'saved':
                $this->set('message', 'Property details saved');
                break;
            case 'error':
                $this->set('message', 'Error saving details');
                break;
        }
    }

    public function add_property_facilities($propertyID, $status = false)
    {
        if (!$propertyID) {
            $this->redirect($this->configURL);
        }
        $property = Property::getByID($propertyID);
        $this->set('property', $property);
        $this->set('task', 'add_property_facilities');
        $this->set('configURL', $this->configURL);
        switch ($status) {
            case 'saved':
                $this->set('message', 'Property details saved');
                break;
            case 'error':
                $this->set('message', 'Error saving details');
                break;
        }
    }

    public function save_property_rules()
    {
        $propertyID          = $this->post('propertyID');
        $property            = Property::getByID($propertyID);
        $propertyRules       = $this->post('propertyRules');
        $cancellationPolicy  = $this->post('cancellationPolicy');
        $locationDescription = $this->post('locationDescription');

        if ($this->isPost()) {
            $property->updatePropertyRules($propertyRules, $cancellationPolicy, $locationDescription);
            $this->redirect($this->configURL . '/add_property_rules/' . $propertyID . '/saved');
        }
        if ($propertyID) {
            $this->redirect($this->configURL . '/add_property_rules/' . $propertyID . '/error');
        }

        $this->redirect($this->getCollectionObject()->getCollectionPath());
    }

    public function save_property_facilities()
    {
        $propertyID         = $this->post('propertyID');
        $property           = Property::getByID($propertyID);
        $propertyFacilities = $this->post('facilities');

        if ($this->isPost()) {

            $fieldChanged = UserLogs::comparePropertyFacilities($propertyFacilities, $property);
            if ($fieldChanged) {
                UserLogs::add($fieldChanged, 'edited_facilities');
            }
            $property->updateFacilities($propertyFacilities);

            $this->redirect($this->configURL . '/add_property_facilities/' . $propertyID . '/saved');
        }
        if ($propertyID) {
            $this->redirect($this->configURL . '/add_property_facilities/' . $propertyID . '/error');
        }

        $this->redirect($this->getCollectionObject()->getCollectionPath());
    }

    public function images_save()
    {
        /** @var FileHelper $fh */
        $fh = Loader::helper('file');

        $propertyID = $this->post('propertyID');
        $property   = Property::getByID($propertyID);
        $bgPosition = $this->post('bgPosition');
        $caption    = $this->post('caption');

        if ($this->isPost()) {
            if (isset($_FILES['image'])) {

                $image = $property->saveImage('image', $caption, $bgPosition);
                if ($fh->getExtension($_FILES['image']['name']) == "webp") {
                    $this->set('message', 'Webp format is not supported');
                    $this->redirect($this->configURL . '/add_images/' . $propertyID . '/error');
                }
                if ($image) {
                    $this->redirect($this->configURL . '/add_images/' . $propertyID . '/saved');
                }
            }
            if (isset($_FILES['images'])) {
                foreach ($_FILES['images']['name'] as $image) {
                    if ($fh->getExtension($image) == "webp") {
                        $this->set('message', 'Webp format is not supported');
                        $this->redirect($this->configURL . '/add_images/' . $propertyID . '/error');
                    }
                }
                $image = $property->saveImages('images');
                if ($image) {
                    $this->redirect($this->configURL . '/add_images/' . $propertyID . '/saved');
                }
            }
        }
        if ($propertyID) {
            $this->redirect($this->configURL . '/add_images/' . $propertyID . '/error');
        }

        $this->redirect($this->getCollectionObject()->getCollectionPath());
    }

    public function thumbnail_save()
    {
        if (!$this->isPost()) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        }
        $propertyID = $this->post('propertyID');
        $imageID    = $this->post('imageID');

        $property = Property::getByID($propertyID);
        $image    = Images::getByID($imageID);
        if ($property && $image) {
            $property->setThumbnail($image->getID());
            $this->redirect($this->configURL . '/add_images/' . $propertyID . '/saved');
        }
        $this->redirect($this->configURL . '/add_images/' . $propertyID . '/error');
    }

    public function image_remove()
    {
        if (!$this->isPost()) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        }
        $propertyID = $this->post('propertyID');
        $imageID    = $this->post('imageID');

        $image = Images::getByID($imageID);
        if ($image) {
            $image->delete();
            $this->redirect($this->configURL . '/add_images/' . $propertyID . '/deleted');
        }
        $this->redirect($this->configURL . '/add_images/' . $propertyID . '/error');
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

    public function add_property_seasons($propertyID, $arg2 = false)
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');


        if (!$propertyID) {
            $this->redirect($this->configURL);
        }

        $filterOptions = [
            ''          => 'All',
            'active'    => 'Active',
            'in_active' => 'In Active',
            //            'exclusive' => 'Exclusive',
        ];

        $itemsOptions = [
            10  => 10,
            25  => 25,
            50  => 50,
            100 => 100,
            500 => 500,
        ];

        $seasonName      = $th->sanitize($this->request('seasonName'));
        $seasonStartDate = $th->sanitize($this->request('seasonStartDate'));
        $seasonEndDate   = $th->sanitize($this->request('seasonEndDate'));
        $filter          = $th->sanitize($this->request('filter'));
        $page            = intval($this->request('page')) ?: 1;
        $items           = 10;


        $seasonList = new PropertySeasonList();

        $seasonList->filterByPropertyID($propertyID);

        switch ($filter) {
            case 'active':
                $seasonList->filterByStatus(1);
                break;
            case 'in_active':
                $seasonList->filterByStatus(0);
                break;
            default:
                break;
        }

        if ($seasonName) {
            $seasonList->filter('seasonName', '%' . $seasonName . '%', 'LIKE');
        }

        if ($seasonStartDate) {
            $seasonStartDate = $dh->date('Y-m-d', strtotime($seasonStartDate));
            $seasonList->filterByStartDate($seasonStartDate);
        }
        if ($seasonEndDate) {
            $seasonEndDate = $dh->date('Y-m-d', strtotime($seasonEndDate));
            $seasonList->filterByEndDate($seasonEndDate);
        }

        $seasonList->populateByStatus(false);

        $seasonList->setItemsPerPage($items);

        $seasons = $seasonList->getPage();


        $this->set('filterOptions', $filterOptions);
        $this->set('itemsOptions', $itemsOptions);
        $this->set('seasonList', $seasonList);
        $this->set('seasons', $seasons);

        $property = Property::getByID($propertyID);
        $this->set('property', $property);
        $this->set('task', 'add_property_seasons');
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Season details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Season details saved!');
                    break;
                case 'deleted':
                    $this->set('message', 'Season deleted!');
                    break;
            }
        }
    }

    public function add_season($propertyID, $arg2 = false)
    {
        if (!$propertyID) {
            $this->redirect($this->configURL);
        }
        $property = Property::getByID($propertyID);

        $statusOptions = [
            '1' => 'Active',
            '0' => 'In Active'
        ];

        $this->set('task', 'add_season');
        $this->set('property', $property);
        $this->set('statusOptions', $statusOptions);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Season details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Season details saved!');
                    break;
            }
        }
    }

    public function edit_season($propertyID, $seasonID, $arg2 = false)
    {
        if (!$propertyID || !$seasonID) {
            $this->redirect($this->configURL);
        }


        $property = Property::getByID($propertyID);
        $season   = PropertySeason::getByID($seasonID);

        $statusOptions = [
            '1' => 'Active',
            '0' => 'In Active'
        ];

        $this->set('task', 'edit_season');
        $this->set('property', $property);
        $this->set('season', $season);
        $this->set('statusOptions', $statusOptions);
        $this->set('configURL', $this->configURL);


        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Season details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Season details saved!');
                    break;
            }
        }
    }

    public function save_season()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th = Loader::helper('text');
        /** @var DateHelper $dh */
        $dh       = Loader::helper('date');
        $e        = Loader::helper('validation/error');
        $property = Property::getByID($this->post('propertyID'));

        if ($property === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $seasonName      = $th->sanitize($this->post('seasonName'));
            $seasonPrice     = $th->sanitize($this->post('seasonPrice'));
            $seasonStartDate = $th->sanitize($this->post('seasonStartDate'));
            $seasonEndDate   = $th->sanitize($this->post('seasonEndDate'));
            $seasonStatus    = $th->sanitize($this->post('seasonStatus'));
            $minNightsSeason    = $th->sanitize($this->post('minNightsSeason'));

            $seasonStartDate = $dh->getFormattedDate($seasonStartDate, 'Y-m-d');
            $seasonEndDate   = $dh->getFormattedDate($seasonEndDate, 'Y-m-d');


            if (!$seasonName) {
                $e->add('Season name is required');
            }
            if (!$seasonPrice) {
                $e->add('Season price is required');
            }
            if (!$seasonStartDate) {
                $e->add('Season start date is required');
            }
            if (!$seasonEndDate) {
                $e->add('Season end date is required');
            }

            if ($seasonStartDate > $seasonEndDate && $seasonStartDate && $seasonEndDate) {
                $e->add('Start date cannot be greater than End date');
            }

            if ($seasonStartDate < $seasonEndDate) {
                $seasonList = new PropertySeasonList();

                $seasonList->filterByPropertyID($this->post('propertyID'));

                $seasonList->filterByStartEndDate($seasonStartDate, $seasonEndDate);

                $seasons = $seasonList->get();

                if ($seasons) {
                    $e->add('Date range overlapping with existing season dates');
                }
            }
            if($minNightsSeason && (!(is_numeric($minNightsSeason)) || $minNightsSeason < 0)) {
                $e->add('Season Minimum nights is invalid');
            }

            if (!$e->has()) {
                $season = PropertySeason::add($property->getID(), $seasonName, $seasonStartDate, $seasonEndDate, $seasonPrice, $seasonStatus, $minNightsSeason);

                $message = '<strong>' . $season->getSeasonName() . '</strong>' . ' to property ' . '<strong>' . $property->getName() . '</strong>';
                UserLogs::add($message, 'added_season');

                $this->redirect($this->configURL . '/edit_season/' . $this->post('propertyID') . '/' . $season->getID() . '/saved');
            }

            $this->set('error', $e);
            $this->add_season($property->getID());
        }
    }

    public function update_season()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th = Loader::helper('text');
        /** @var DateHelper $dh */
        $dh       = Loader::helper('date');
        $e        = Loader::helper('validation/error');
        $property = Property::getByID($this->post('propertyID'));
        $season   = PropertySeason::getByID($this->post('seasonID'));

        if ($property === null || $season === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $seasonName      = $th->sanitize($this->post('seasonName'));
            $seasonPrice     = $th->sanitize($this->post('seasonPrice'));
            $seasonStartDate = $th->sanitize($this->post('seasonStartDate'));
            $seasonEndDate   = $th->sanitize($this->post('seasonEndDate'));
            $seasonStatus    = $th->sanitize($this->post('seasonStatus'));
            $seasonStartDate = $dh->getFormattedDate($seasonStartDate, 'Y-m-d');
            $seasonEndDate   = $dh->getFormattedDate($seasonEndDate, 'Y-m-d');
            $minNightsSeason = $th->sanitize($this->post('minNightsSeason'));

            if (!$seasonName) {
                $e->add('Season name is required');
            }
            if (!$seasonPrice) {
                $e->add('Season price is required');
            }
            if (!$seasonStartDate) {
                $e->add('Season start date is required');
            }
            if (!$seasonEndDate) {
                $e->add('Season end date is required');
            }

            if ($seasonStartDate > $seasonEndDate && $seasonStartDate && $seasonEndDate) {
                $e->add('Start date cannot be greater than End date');
            }

            if ($seasonStartDate < $seasonEndDate) {
                $seasonList = new PropertySeasonList();

                $seasonList->filterByPropertyID($this->post('propertyID'));

                $seasonList->filterByStartEndDate($seasonStartDate, $seasonEndDate);

                $seasonList->filterByNotInID($season->getID());

                $seasons = $seasonList->get();

                if ($seasons) {
                    $e->add('Date range overlapping with existing season dates');
                }
            }

            if($minNightsSeason && (!(is_numeric($minNightsSeason)) || $minNightsSeason < 0)) {
                $e->add('Season Minimum nights is invalid');
            }

            if (!$e->has()) {

                $seasonStartDate = $dh->getFormattedDate($seasonStartDate, 'Y-m-d');
                $seasonEndDate   = $dh->getFormattedDate($seasonEndDate, 'Y-m-d');

                $fieldListArr = [
                    'seasonName'      => $seasonName,
                    'seasonPrice'     => $seasonPrice,
                    'seasonStartDate' => $seasonStartDate,
                    'seasonEndDate'   => $seasonEndDate,
                    'seasonStatus'    => $seasonStatus,
                    'minNightsSeason' => $minNightsSeason,
                ];

                PropertySeason::update($property->getID(), $season->getID(), $seasonName, $seasonStartDate, $seasonEndDate, $seasonPrice, $seasonStatus, $minNightsSeason);

                $fieldChanged = UserLogs::comparePropertySeasonFieldsValue($season, $fieldListArr, $property);
                if ($fieldChanged) {
                    UserLogs::add($fieldChanged, 'edited_season');
                }

                $this->edit_season($this->post('propertyID'), $this->post('seasonID'), 'updated');
            }
            $this->set('error', $e);
            $this->edit_season($property->getID(), $season->getID());

        }
    }

    public function delete_season($propertyID, $seasonID)
    {
        if (!$propertyID || !$seasonID) {
            $this->redirect($this->configURL);
        }
        $property = Property::getByID($propertyID);
        $season   = PropertySeason::getByID($seasonID);

        $message = '<strong>' . $season->getSeasonName() . '</strong>' . ' belonging to property ' . '<strong>' . $property->getName() . '</strong>';
        UserLogs::add($message, 'deleted_season');
        $season->delete();

        $this->redirect($this->configURL . '/add_property_seasons/' . $propertyID . '/deleted');
    }

    public function view_block_dates($propertyID, $arg2 = false)
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');

        if (!$propertyID) {
            $this->redirect($this->configURL);
        }

        $startDate = $th->sanitize($this->request('startDate'));
        $endDate   = $th->sanitize($this->request('endDate'));
        $items     = 5;

        $blockDatesList = new PropertyBlockDatesList();

        $blockDatesList->filterByPropertyID($propertyID);

        if ($startDate) {
            $startDate = $dh->date('Y-m-d', strtotime($startDate));
            $blockDatesList->filterByStartDate($startDate);
        }
        if ($endDate) {
            $endDate = $dh->date('Y-m-d', strtotime($endDate));
            $blockDatesList->filterByEndDate($endDate);
        }

        $blockDatesList->setItemsPerPage($items);

        $blockDates = $blockDatesList->getPage();


        $this->set('blockDatesList', $blockDatesList);
        $this->set('blockDates', $blockDates);

        $property = Property::getByID($propertyID);
        $this->set('property', $property);
        $this->set('task', 'view_block_dates');
        $this->set('configURL', $this->configURL);

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

    public function add_block_dates($propertyID, $arg2 = false)
    {
        $property = Property::getByID($propertyID);
        $this->set('property', $property);
        $this->set('task', 'add_block_dates');
        $this->set('configURL', $this->configURL);
    }

    public function edit_block_date($propertyID, $blockID, $arg2 = false)
    {
        if (!$propertyID || !$blockID) {
            $this->redirect($this->configURL);
        }

        $property  = Property::getByID($propertyID);
        $blockDate = PropertyBlockDates::getByID($blockID);

        $this->set('task', 'edit_block_date');
        $this->set('property', $property);
        $this->set('blockDate', $blockDate);
        $this->set('configURL', $this->configURL);

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

    public function save_block_date()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');
        /** @var ValidationErrorHelper $e */
        $e = Loader::helper('validation/error');

        $startDate = $th->sanitize($this->post('startDate'));
        $endDate   = $th->sanitize($this->post('endDate'));
        $desc      = $th->sanitize($this->post('blockDesc'));
        $price     = (float)$th->sanitize($this->post('blockPrice'));
        $startDate = $dh->getFormattedDate($startDate, 'Y-m-d');
        $endDate   = $dh->getFormattedDate($endDate, 'Y-m-d');

        $propertyID = $th->sanitize($this->post('propertyID'));
        $property   = Property::getByID($propertyID);

        if (!$propertyID) {
            $this->redirect($this->configURL);
        }

        if (!$startDate) {
            $e->add('Start date is required');
        }

        if (!$endDate) {
            $e->add('End date is required');
        }

        if ($startDate > $endDate && $startDate && $endDate) {
            $e->add('Start date cannot be greater than End date');
        }

        if ($startDate < $endDate) {
            $blockDatesList = new PropertyBlockDatesList();

            $blockDatesList->filterByPropertyID($propertyID);

            $blockDatesList->filterByStartEndDate($startDate, $endDate);

            $blockDates = $blockDatesList->get();

            if ($blockDates) {
                $e->add('Date range overlapping with existing block dates');
            } else {
                $blockDatesList = new PropertyBlockDatesList();

                $blockDatesList->filterByPropertyID($propertyID);

                $blockDatesList->filterByAvailability($startDate, $endDate);

                $blockDates = $blockDatesList->get();

                if ($blockDates) {
                    $e->add('Date range overlapping with existing booking dates');
                }
            }
        }


        if (!$e->has()) {

            $blockDate = PropertyBlockDates::add($property->getID(), $startDate, $endDate, $desc, $price);


            $message = '<strong>' . $blockDate->getStartDate() . '</strong>' . ' to ' . '<strong>' . $blockDate->getEndDate() . '</strong>' . ' to property ' . '<strong>' . $property->getName() . '</strong>';
            UserLogs::add($message, 'added_blockedDate');

            $this->redirect($this->configURL . '/view_block_dates/' . $this->post('propertyID') . '/saved');
        }

        $this->set('error', $e);
        $this->add_block_dates($property->getID());
    }

    public function update_block_date()
    {
        /** @var $th TextHelper */
        $th = Loader::helper('text');

        /** @var DateHelper $dh */
        $dh = Loader::helper('date');
        /** @var ValidationErrorHelper $e */
        $e = Loader::helper('validation/error');

        $startDate = $th->sanitize($this->post('startDate'));
        $endDate   = $th->sanitize($this->post('endDate'));
        $desc      = $th->sanitize($this->post('blockDesc'));
        $price     = (float)$th->sanitize($this->post('blockPrice'));
        $startDate = $dh->getFormattedDate($startDate, 'Y-m-d');
        $endDate   = $dh->getFormattedDate($endDate, 'Y-m-d');

        $propertyID = $th->sanitize($this->post('propertyID'));
        $blockID    = $th->sanitize($this->post('blockID'));

        $property = Property::getByID($propertyID);

        $blockDate = PropertyBlockDates::getByID($blockID);

        if (!$propertyID || !$blockID) {
            $this->redirect($this->configURL);
        }

        if (!$startDate) {
            $e->add('Start date is required');
        }

        if (!$endDate) {
            $e->add('End date is required');
        }

        if ($startDate > $endDate && $startDate && $endDate) {
            $e->add('Start date cannot be greater than End date');
        }

        if ($startDate < $endDate) {
            $blockDatesList = new PropertyBlockDatesList();

            $blockDatesList->filterByPropertyID($propertyID);

            $blockDatesList->filterByNotInID($blockID);

            $blockDatesList->filterByStartEndDate($startDate, $endDate);

            $blockDates = $blockDatesList->get();

            if ($blockDates) {
                $e->add('Date range overlapping with existing block dates');
            } else {
                $blockDatesList = new PropertyBlockDatesList();

                $blockDatesList->filterByPropertyID($propertyID);

                $blockDatesList->filterByAvailability($startDate, $endDate);

                $blockDates = $blockDatesList->get();

                if ($blockDates) {
                    $e->add('Date range overlapping with existing booking dates');
                }
            }
        }


        if (!$e->has()) {
            $fieldListArr = [
                'startDate' => $startDate,
                'endDate'   => $endDate
            ];
            $fieldChanged = UserLogs::comparePropertyBlockedDates($blockDate, $fieldListArr, $property);
            if ($fieldChanged) {
                UserLogs::add($fieldChanged, 'edited_blockedDate');
            }
            $blockDate = PropertyBlockDates::update($blockDate->getID(), $property->getID(), $startDate, $endDate, $desc, $price);

            $this->redirect($this->configURL . '/view_block_dates/' . $this->post('propertyID') . '/updated');
        }

        $this->set('error', $e);
        $this->edit_block_date($property->getID(), $blockDate->getID());
    }

    public function delete_block_date($propertyID, $blockID)
    {

        if (!$propertyID || !$blockID) {
            $this->redirect($this->configURL);
        }
        $blockDate = PropertyBlockDates::getByID($blockID);
        $property  = Property::getByID($propertyID);


        $message = '<strong>' . $blockDate->getStartDate() . '</strong>' . ' to ' . '<strong>' . $blockDate->getEndDate() . '</strong>' . ' belonging to property ' . '<strong>' . $property->getName() . '</strong>';
        UserLogs::add($message, 'deleted_blockedDate');
        $blockDate->delete();

        $this->redirect($this->configURL . '/view_block_dates/' . $propertyID . '/deleted');
    }
}
