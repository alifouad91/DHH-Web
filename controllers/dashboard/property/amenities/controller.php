<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyAmenitiesController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/amenities';
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

        $name = $th->sanitize($this->post('name'));

        if (!$name) {
            $e->add('Amenity name is required');
        }
        if (!$e->has()) {
            $amenity = Amenity::add($name);
            $_POST['amID'] = $amenity->getID();
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

        $amenityList = new AmenityList();


        if ($keywords) {
            $amenityList->filter('name', '%' . $keywords . '%', 'LIKE');
        }


        $amenityList->setItemsPerPage($items);
        $amenities = $amenityList->getPage();

        $this->set('amenityList', $amenityList);
        $this->set('amenities', $amenities);
        $this->set('task', 'overview');
    }


    public function detail($amenityID, $arg2 = false)
    {
        if (!$amenityID) {
            $this->redirect($this->configURL);
        }
        $amenity = Amenity::getByID($amenityID);

        $this->set('task', 'detail');
        $this->set('amenity', $amenity);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Amenity details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Amenity details saved!');
                    break;
            }
        }
    }


    public function edit_save()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th      = Loader::helper('text');
        $e       = Loader::helper('validation/error');
        $amenity = Amenity::getByID($this->post('amenityID'));

        if ($amenity === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name = $th->sanitize($this->post('name'));

            if (!$name) {
                $e->add('Amenity name is required');
            }

            if (!$e->has()) {
                $amenity->update($name);
                $_POST['amID'] = $amenity->getID();
                $this->image_save('updated');

            }
            $this->set('error', $e);
            $this->detail($amenity->getID());
        }

    }

    public function image_save($message)
    {
        $amID    = $this->post('amID');
        $amenity = Amenity::getByID($amID);

        if ($this->isPost()) {
            if (isset($_FILES['image'])) {
                $image = $amenity->saveImage('image');
                if ($image) {
                    $this->redirect($this->configURL . self::DETAIL_URL . $amenity->getID() . '/' . $message);
                }
            }
        }
        if ($amID) {
            $this->redirect($this->configURL . self::DETAIL_URL . $amID . '/error');
        }

        $this->redirect($this->getCollectionObject()->getCollectionPath());
    }

    public function image_remove()
    {
        if (!$this->isPost()) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        }
        $amID = $this->post('amID');

        $amenity = Amenity::getByID($amID);
        if ($amenity) {
            $amenity->removeImage();
            $this->redirect($this->configURL . self::DETAIL_URL . $amenity->getID() . '/' . 'updated');
        }
        $this->redirect($this->configURL . self::DETAIL_URL . $amID . '/error');
    }

    public function delete($id)
    {
        if (!$id) {
            $this->redirect($this->configURL);
        }
        $data = Amenity::getByID($id);


        UserLogs::add($data->getName(), 'deleted_amenity');

        $data->delete();

        $this->redirect($this->configURL);
    }
}
