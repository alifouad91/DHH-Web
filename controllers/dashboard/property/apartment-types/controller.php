<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyApartmentTypesController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/apartment-types';
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

        if (!$name) {
            $e->add('Apartment Type name is required');
        }
        if (!$e->has()) {
            $apartmentType = ApartmentType::add($name);
            $this->redirect($this->configURL . self::DETAIL_URL . $apartmentType->getID() . '/saved');
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

        $apartmentTypeList = new ApartmentTypeList();


        if ($keywords) {
            $apartmentTypeList->filter('name', '%' . $keywords . '%', 'LIKE');
        }


        $apartmentTypeList->setItemsPerPage($items);
        $apartmentTypes = $apartmentTypeList->getPage();

        $this->set('apartmentTypeList', $apartmentTypeList);
        $this->set('apartmentTypes', $apartmentTypes);
        $this->set('task', 'overview');
    }


    public function detail($apartmentTypeID, $arg2 = false)
    {
        if (!$apartmentTypeID)
        {
            $this->redirect($this->configURL);
        }
        $apartmentType = ApartmentType::getByID($apartmentTypeID);

        $this->set('task', 'detail');
        $this->set('apartmentType', $apartmentType);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Apartment Type details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Apartment Type details saved!');
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
        $apartmentType = ApartmentType::getByID($this->post('apartmentTypeID'));

        if ($apartmentType === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name            = $th->sanitize($this->post('name'));

            if (!$name) {
                $e->add('Apartment Type name is required');
            }

            if (!$e->has()) {
                $apartmentType->update($name);
                $this->redirect($this->configURL . self::DETAIL_URL . $apartmentType->getID() . '/updated');
            }
            $this->set('error', $e);
            $this->detail($apartmentType->getID());
        }

    }

    public function delete($id)
    {
        if (!$id) {
            $this->redirect($this->configURL);
        }
        $data = ApartmentType::getByID($id);


        UserLogs::add($data->getName(), 'deleted_apartment_type');

        $data->delete();

        $this->redirect($this->configURL);
    }
}
