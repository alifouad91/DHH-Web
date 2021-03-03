<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardPropertyHomepageFiltersController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/property/homepage-filters';
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
            $e->add('Filter name is required');
        }
        if (!$e->has()) {
            $filter = HomePageFilters::add($name);
            $this->redirect($this->configURL . self::DETAIL_URL . $filter->getID() . '/saved');
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

        $filterList = new HomePageFiltersList();


        if ($keywords) {
            $filterList->filter('name', '%' . $keywords . '%', 'LIKE');
        }


        $filterList->setItemsPerPage($items);
        $filters = $filterList->getPage();

        $this->set('filterList', $filterList);
        $this->set('filters', $filters);
        $this->set('task', 'overview');
    }


    public function detail($filterID, $arg2 = false)
    {
        if (!$filterID)
        {
            $this->redirect($this->configURL);
        }
        $filter = HomePageFilters::getByID($filterID);

        $this->set('task', 'detail');
        $this->set('filter', $filter);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'Homepage Filters details updated');
                    break;
                case 'saved':
                    $this->set('message', 'Homepage Filters details saved!');
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
        $filter = HomePageFilters::getByID($this->post('filterID'));

        if ($filter === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $name            = $th->sanitize($this->post('name'));

            if (!$name) {
                $e->add('Homepage Filters name is required');
            }

            if (!$e->has()) {
                $filter->update($name);
                $this->redirect($this->configURL . self::DETAIL_URL . $filter->getID() . '/updated');
            }
            $this->set('error', $e);
            $this->detail($filter->getID());
        }

    }

    public function delete($id)
    {
        if (!$id) {
            $this->redirect($this->configURL);
        }
        $data = HomePageFilters::getByID($id);


        UserLogs::add($data->getName(), 'deleted_home_page_filters');

        $data->delete();

        $this->redirect($this->configURL);
    }
}
