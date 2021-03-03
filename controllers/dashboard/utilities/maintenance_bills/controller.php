<?php defined('C5_EXECUTE') or die('Access Denied.');

class DashboardUtilitiesMaintenanceBillsController extends DashboardBaseController
{

    protected $configURL   = 'dashboard/utilities/maintenance_bills';
    protected $pluginsPath = DIR_REL . JS_PLUGINS_DIR;

    const DETAIL_URL = '/detail/';
    const TYPE       = 'maintenance';

    public function view()
    {
        $this->overview();
        $this->set('configURL', $this->configURL);
    }

    public function add_save()
    {
        /** @var TextHelper $th */
        /** @var DateHelper $dh */
        $th = Loader::helper('text');
        $dh = Loader::helper('date');
        $e  = Loader::helper('validation/error');

        $pID         = $th->sanitize($this->post('pID'));
        $amount      = (double) $th->sanitize($this->post('amount'));
        $description = $th->sanitize($this->post('description'));
        $fixedBy = $th->sanitize($this->post('fixedBy'));
        $date        = $th->sanitize($this->post('date'));

        if (!$pID) {
            $e->add('Please select a bill');
        }
        if (!$amount) {
            $e->add('Amount is required');
        }
        if (!$description) {
            $e->add('Description is required');
        }
        if (!$fixedBy) {
            $e->add('Maintenance person/team name required');
        }
        if (!$date) {
            $e->add('Date is required');
        }
        if (!isset($_FILES['image'])) {
//            $e->add('Image is required');
        }

        $date      = $dh->getFormattedDate($date, 'Y-m-d H:i:s');
        if (!$e->has()) {
            $bill            = Bill::add($pID, $amount, self::TYPE, $description, $fixedBy, null, $date);
            $_POST['billID'] = $bill->getID();
            $this->images_save('saved');
        }
        $this->set('error', $e);
        $this->add();

    }

    public function add()
    {
        $htmlHelper = Loader::helper('html');
        $this->loadFlatPickrPlugin();
        $this->loadSelect2Plugin();
        $this->set('task', 'add');
        $this->addFooterItem($htmlHelper->javascript('bill.js'));
    }

    protected function overview()
    {
        $itemsOptions = [
            10  => 10,
            25  => 25,
            50  => 50,
            100 => 100,
            500 => 500,
        ];


        $keywords = $this->request('keywords');
        $filter   = $this->request('filter');
        $items    = intval($this->request('items'));
        $items    = in_array($items, $itemsOptions) ? $items : 10;

        $billList = new BillList();

        switch ($filter) {
            case 'active':
                $billList->filterByStatus(1);
                break;
            case 'in_active':
                $billList->filterByStatus(0);
                break;
//            case 'exclusive':
//                $billList->filterByExclusive();
//                break;
            default:
                break;
        }


        if ($keywords) {
            $billList->filter('name', '%' . $keywords . '%', 'LIKE');
        }

//        $billList->sortByDateTimeDescending();
        $billList->setItemsPerPage($items);
        $billList->filterByType(self::TYPE);
        $bills = $billList->getPage();

        $this->set('itemsOptions', $itemsOptions);
        $this->set('billList', $billList);
        $this->set('bills', $bills);
        $this->set('task', 'overview');
    }


    public function detail($billID, $arg2 = false)
    {
        $htmlHelper = Loader::helper('html');
        if (!$billID) {
            $this->redirect($this->configURL);
        }
        $bill = Bill::getByID($billID);
        $this->loadFlatPickrPlugin();
        $this->loadSelect2Plugin();

        $this->set('task', 'detail');
        $this->set('bill', $bill);
        $this->set('configURL', $this->configURL);

        if ($arg2) {
            switch ($arg2) {
                case 'updated':
                    $this->set('message', 'bill details updated');
                    break;
                case 'saved':
                    $this->set('message', 'bill details saved!');
                    break;
            }
        }
        $this->addFooterItem($htmlHelper->javascript('bill.js'));
    }


    public function edit_save()
    {
        /** @var TextHelper $th */
        /** @var ValidationErrorHelper $e */
        $th   = Loader::helper('text');
        $dh   = Loader::helper('date');
        $e    = Loader::helper('validation/error');
        $bill = Bill::getByID($this->post('billID'));

        if ($bill === null) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        } else {
            $pID         = $th->sanitize($this->post('pID'));
            $amount      = (double) $th->sanitize($this->post('amount'));
            $description = $th->sanitize($this->post('description'));
        $fixedBy = $th->sanitize($this->post('fixedBy'));
            $date        = $th->sanitize($this->post('date'));

            if (!$pID) {
                $e->add('Please select a bill');
            }
            if (!$amount) {
                $e->add('Amount is required');
            }
            if (!$description) {
                $e->add('Description is required');
            }
            if (!$fixedBy) {
                $e->add('Maintenance person/team name required');
            }
            if (!$date) {
                $e->add('Date is required');
            }
            if (!isset($_FILES['image'])) {
//            $e->add('Image is required');
            }
            $date = $dh->getFormattedDate($date, 'Y-m-d H:i:s');
            if (!$e->has()) {
                $type  = $bill->getType();
                $image = $bill->getBillImage();
                $bill->update($pID, $amount, $type, $description, $fixedBy, $image, $date);

                if (isset($_FILES['image'])) {
                    $this->images_save('updated');
                }
            }
            $this->set('error', $e);
            $this->detail($bill->getID());
        }

    }

    public function images_save($message)
    {
        $billID = $this->post('billID');
        $bill   = Bill::getByID($billID);

        if ($this->isPost()) {
            if (isset($_FILES['image'])) {
                $image = $bill->saveImage('image');
                if ($image) {
                    $this->redirect($this->configURL . self::DETAIL_URL . $bill->getID() . '/' . $message);
                }
            }
        }
        if ($billID) {
            $this->redirect($this->configURL . self::DETAIL_URL . $billID . '/error');
        }

        $this->redirect($this->getCollectionObject()->getCollectionPath());
    }

    public function image_remove()
    {
        if (!$this->isPost()) {
            $this->redirect($this->getCollectionObject()->getCollectionPath());
        }
        $billID = $this->post('billID');

        $bill = Bill::getByID($billID);
        if ($bill) {
            $bill->removeImage();
            $this->redirect($this->configURL . self::DETAIL_URL . $bill->getID() . '/' . 'updated');
        }
        $this->redirect($this->configURL . self::DETAIL_URL . $billID . '/error');
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
