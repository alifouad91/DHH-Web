<?php
defined('C5_EXECUTE') or die('Access Denied.');
Loader::controller('/api');

class ApiUtilityController extends ApiController
{
    const ITEMS_PER_PAGE   = 10;
    const TYPE_PAYMENT     = 'payment';
    const TYPE_MAINTENANCE = 'maintenance';

    public function view()
    {
        $u      = $this->validateLandLordGroup();
        $userID = $u->getUserID();

        $propertyList = new PropertyList();
        $propertyList->filterByOwner($userID);
        $properties = $propertyList->get();

        $prop = reset($properties);
        if ($prop && $prop->getOwnerID() != $userID) {
            return $this->addError('Unauthorised User');
        }

        $result = [];
        $temp   = [];

        foreach ($properties as $property) {
            array_push($temp, [
                'pID'          => $property->getID(),
                'propertyName' => $property->getName()
            ]);
        }
        $result['properties']        = $temp;
        $result['payment_bills']     = $this->payment_bills();
        $result['maintenance_bills'] = $this->maintenance_bills();

        return $result;
    }

    public function payment_bills()
    {
        /** @var TextHelper $th */
        $th      = Loader::helper('text');
        $page    = (int) $th->sanitize($this->get('pageNo')) ? : 1;
        $pID     = (int) $th->sanitize($this->get('pID'));
        $results = [];

        $u      = $this->validateLandLordGroup();
        $userID = $u->getUserID();

        $billList = new BillList();
        $billList->filterByOwner($userID);
        $billList->filterByType(self::TYPE_PAYMENT);
        if ($pID) {
            $billList->filterByPropertyID($pID);
        }
        $billList->populatePropertyDetails();
        $billList->setItemsPerPage(self::ITEMS_PER_PAGE);

        $bills = $billList->getPage($page);

        /** @var Bill $bill */
        $bill = reset($bills);
        if ($bill) {
            $property = $bill->getProperty();
            if ($property->getOwnerID() != $userID) {
                return $this->addError('Unauthorised User');
            }
        }

        /** @var Bill $bill */
        foreach ($bills as $bill) {
            $property = $bill->getProperty();
            array_push($results, [
                'billID'       => $bill->getID(),
                'date'         => $bill->getDate(),
                'amount'       => $bill->getAmount(),
                'billImage'    => $bill->getPDFPath(),
                'pID'          => $property->getID(),
                'propertyName' => $property->getName(),
            ]);
        }

        return $results;
    }

    public function maintenance_bills()
    {
        /** @var TextHelper $th */
        $th      = Loader::helper('text');
        $page    = (int) $th->sanitize($this->get('pageNo')) ? : 1;
        $pID     = (int) $th->sanitize($this->get('pID'));
        $results = [];

        $u      = $this->validateLandLordGroup();
        $userID = $u->getUserID();

        $billList = new BillList();
        $billList->filterByOwner($userID);
        $billList->filterByType(self::TYPE_MAINTENANCE);
        if ($pID) {
            $billList->filterByPropertyID($pID);
        }
        $billList->populatePropertyDetails();
        $billList->setItemsPerPage(self::ITEMS_PER_PAGE);

        $bills = $billList->getPage($page);

        /** @var Bill $bill */
        $bill = reset($bills);
        if ($bill) {
            $property = $bill->getProperty();
            if ($property->getOwnerID() != $userID) {
                return $this->addError('Unauthorised User');
            }
        }

        /** @var Bill $bill */
        foreach ($bills as $bill) {
            $property = $bill->getProperty();
            array_push($results, [
                'billID'       => $bill->getID(),
                'date'         => $bill->getDate(),
                'amount'       => $bill->getAmount(),
                'description'  => $bill->getDescription(),
                'fixedBy'      => $bill->getFixedBy(),
                'billImage'    => $bill->getPDFPath(),
                'pID'          => $property->getID(),
                'propertyName' => $property->getName(),
            ]);
        }

        return $results;
    }

    public function sendAsEmail()
    {
        /** @var TextHelper $th */
        $th               = Loader::helper('text');
        $billID           = (int) $th->sanitize($this->post('billID'));
        $recipientEmail   = $th->sanitize($this->post('recipientEmail'));
        $result['status'] = false;

        $u      = $this->validateLandLordGroup();
        $userID = $u->getUserID();

        if (!$billID) {
            $this->addError('Invalid Bill ID');
        }
        $bill = Bill::getByID($billID);

        if ($bill) {
            $property = $bill->getProperty();

            if ($property->getOwnerID() != $userID) {
                return $this->addError('Unauthorised User');
            }
            if (!$recipientEmail)
            {
                $recipientEmail = $property->getOwnerInfo()->getUserEmail();
            }
            Events::fire('send_bill_as_email',$billID,$recipientEmail);
            $result['status'] = true;
        }

        return $result;
    }
}