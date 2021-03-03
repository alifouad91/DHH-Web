<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyLocationsController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/locations';
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
            $e->add('Location name is required');
        }
        if (!$e->has()) {
            $location = Location::add($name);
            $this->redirect($this->configURL . self::DETAIL_URL . $location->getID() . '/saved');
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

        $locationList = new LocationList();


        if ($keywords) {
            $locationList->filter('name', '%' . $keywords . '%', 'LIKE');
        }


        $locationList->setItemsPerPage($items);
        $locations = $locationList->getPage();

        $this->set('locationList', $locationList);
        $this->set('locations', $locations);
        $this->set('task', 'overview');
    }


    public function detail($locationID, $arg2 = false)
    {
        if (!$locationID)
        {
            $this->redirect($this->configURL);
        }
        $location = Location::getByID($locationID);

        $this->set('task', 'detail');
        $this->set('location', $location);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Location details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Location details saved!');
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
        $location = Location::getByID($this->post('locationID'));

        if ($location === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name            = $th->sanitize($this->post('name'));

            if (!$name) {
                $e->add('Location name is required');
            }

            if (!$e->has()) {
                $location->update($name);
                $this->redirect($this->configURL . self::DETAIL_URL . $location->getID() . '/updated');
            }
            $this->set('error', $e);
            $this->detail($location->getID());
        }

    }

    public function delete($id)
    {
        if (!$id) {
            $this->redirect($this->configURL);
        }
        $data = Location::getByID($id);


        UserLogs::add($data->getName(), 'deleted_location');

        $data->delete();

        $this->redirect($this->configURL);
    }
}
