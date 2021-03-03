<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyFacilitiesController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/facilities';
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
            $e->add('Facility name is required');
        }
        if (!$e->has()) {
            $facility = Facility::add($name);
            $_POST['fID'] = $facility->getID();
            $this->image_save('saved');
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

        $facilityList = new FacilityList();


        if ($keywords) {
            $facilityList->filter('name', '%' . $keywords . '%', 'LIKE');
        }


        $facilityList->setItemsPerPage($items);
        $facilities = $facilityList->getPage();

        $this->set('facilityList', $facilityList);
        $this->set('facilities', $facilities);
        $this->set('task', 'overview');
    }


    public function detail($facilityID, $arg2 = false)
    {
        if (!$facilityID)
        {
            $this->redirect($this->configURL);
        }
        $facility = Facility::getByID($facilityID);

        $this->set('task', 'detail');
        $this->set('facility', $facility);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Facility details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Facility details saved!');
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
        $facility = Facility::getByID($this->post('facilityID'));

        if ($facility === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name            = $th->sanitize($this->post('name'));

            if (!$name) {
                $e->add('Facility name is required');
            }

            if (!$e->has()) {
                $facility->update($name);
                $_POST['fID'] = $facility->getID();
                $this->image_save('updated');
            }
            $this->set('error', $e);
            $this->detail($facility->getID());
        }

    }

    public function image_save($message)
    {
        $fID    = $this->post('fID');
        $facility = Facility::getByID($fID);

        if ($this->isPost()) {
            if (isset($_FILES['image'])) {
                $image = $facility->saveImage('image');
                if ($image) {
                    $this->redirect($this->configURL . self::DETAIL_URL . $facility->getID() . '/' . $message);
                }
            }
        }
        if ($fID) {
            $this->redirect($this->configURL . self::DETAIL_URL . $fID . '/error');
        }

        $this->redirect($this->getCollectionObject()->getCollectionPath());
    }

    public function image_remove()
    {
        if (!$this->isPost()) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        }
        $fID = $this->post('fID');

        $facility = Facility::getByID($fID);
        if ($facility) {
            $facility->removeImage();
            $this->redirect($this->configURL . self::DETAIL_URL . $facility->getID() . '/' . 'updated');
        }
        $this->redirect($this->configURL . self::DETAIL_URL . $fID . '/error');
    }

    public function delete($id)
    {
        if (!$id) {
            $this->redirect($this->configURL);
        }
        $data = Facility::getByID($id);


        UserLogs::add($data->getName(), 'deleted_facility');

        $data->delete();

        $this->redirect($this->configURL);
    }
}
