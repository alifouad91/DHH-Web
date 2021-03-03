<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyAreaTypesController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/area-types';
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

        if (!$name) {
            $e->add('Area Type name is required');
        }
        if (!$caption) {
            $e->add('Caption is required');
        }

        if (!$e->has()) {
            $areaType = AreaType::add($name,$caption);
            $this->redirect($this->configURL . self::DETAIL_URL . $areaType->getID() . '/saved');
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

        $areaTypeList = new AreaTypeList();


        if ($keywords) {
            $areaTypeList->filter('name', '%' . $keywords . '%', 'LIKE');
        }


        $areaTypeList->setItemsPerPage($items);
        $areaTypes = $areaTypeList->getPage();

        $this->set('areaTypeList', $areaTypeList);
        $this->set('areaTypes', $areaTypes);
        $this->set('task', 'overview');
    }


    public function detail($areaTypeID, $arg2 = false)
    {
        if (!$areaTypeID)
        {
            $this->redirect($this->configURL);
        }
        $areaType = AreaType::getByID($areaTypeID);

        $this->set('task', 'detail');
        $this->set('areaType', $areaType);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Area Type details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Area Type details saved!');
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
        $areaType = AreaType::getByID($this->post('areaTypeID'));

        if ($areaType === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name            = $th->sanitize($this->post('name'));
            $caption            = $th->sanitize($this->post('caption'));

            if (!$name) {
                $e->add('Area Type name is required');
            }
            if (!$caption) {
                $e->add('Caption is required');
            }

            if (!$e->has()) {
                $areaType->update($name,$caption);
                $this->redirect($this->configURL . self::DETAIL_URL . $areaType->getID() . '/updated');
            }
            $this->set('error', $e);
            $this->detail($areaType->getID());
        }

    }

    public function delete($id)
    {
        if (!$id) {
            $this->redirect($this->configURL);
        }
        $data = AreaType::getByID($id);


        UserLogs::add($data->getName(), 'deleted_area_type');

        $data->delete();

        $this->redirect($this->configURL);
    }
}
