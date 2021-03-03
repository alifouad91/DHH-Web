<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyApartmentAreasController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/apartment-areas';
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

        $caption            = $th->sanitize($this->post('caption'));
        $description            = $th->sanitize($this->post('description'));

        if (!$name) {
            $e->add('Apartment Area name is required');
        }
        if (!$caption) {
            $e->add('Caption is required');
        }
        if (!$description) {
            $e->add('Description is required');
        }

        if (!$e->has()) {
            $apartmentArea = ApartmentArea::add($name,$caption,$description);
            $this->redirect($this->configURL . self::DETAIL_URL . $apartmentArea->getID() . '/saved');
        }
        $this->set('error', $e);
        $this->add();

    }

    public function add()
    {
        $this->set('task', 'add');
        $this->set('configURL', $this->configURL);
    }

    protected function overview()
    {

        $itemsOptions = [
            10  => 10,
            25  => 25,
            50  => 50,
            100 => 100,
        ];

        $keywords = $this->request('keywords');
        $items    = intval($this->request('items'));
        $items    = in_array($items, $itemsOptions) ? $items : 10;

        $apartmentAreaList = new ApartmentAreaList();


        if ($keywords) {
            $apartmentAreaList->filter('name', '%' . $keywords . '%', 'LIKE');
        }


        $apartmentAreaList->setItemsPerPage($items);
        $apartmentAreas = $apartmentAreaList->getPage();

        $this->set('apartmentAreaList', $apartmentAreaList);
        $this->set('apartmentAreas', $apartmentAreas);
        $this->set('task', 'overview');
    }


    public function detail($apartmentAreaID, $arg2 = false)
    {
        if (!$apartmentAreaID)
        {
            $this->redirect($this->configURL);
        }
        $apartmentArea = ApartmentArea::getByID($apartmentAreaID);

        $this->set('task', 'detail');
        $this->set('apartmentArea', $apartmentArea);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Apartment Area details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Apartment Area details saved!');
                    break;
            }
        }
    }


    public function edit_save()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th       = Loader::helper('text');
        $e        = Loader::helper('validation/error');
        $apartmentArea = ApartmentArea::getByID($this->post('apartmentAreaID'));

        if ($apartmentArea === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name            = $th->sanitize($this->post('name'));
            $caption            = $th->sanitize($this->post('caption'));
            $description            = $th->sanitize($this->post('description'));

            if (!$name) {
                $e->add('Apartment Area name is required');
            }
            if (!$caption) {
                $e->add('Caption is required');
            }
            if (!$description) {
                $e->add('Description is required');
            }

            if (!$e->has()) {
                $apartmentArea->update($name,$caption,$description);
                $this->redirect($this->configURL . self::DETAIL_URL . $apartmentArea->getID() . '/updated');
            }
            $this->set('error', $e);
            $this->detail($apartmentArea->getID());
        }

    }

    public function delete($id)
    {
        if (!$id) {
            $this->redirect($this->configURL);
        }
        $data = ApartmentArea::getByID($id);


        UserLogs::add($data->getName(), 'deleted_apartment_area');

        $data->delete();

        $this->redirect($this->configURL);
    }
}
