<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
abstract class Concrete5_Model_Workflow extends Object
{

    protected $wfID                             = 0;
    protected $allowedTasks                     = array('cancel', 'approve');
    protected $restrictedToPermissionKeyHandles = array();

    public function getAllowedTasks()
    {
        return $this->allowedTasks;
    }

    public function getWorkflowID()
    {
        return $this->wfID;
    }

    public function getWorkflowName()
    {
        return $this->wfName;
    }

    public function getWorkflowTypeObject()
    {
        return WorkflowType::getByID($this->wftID);
    }

    public function getRestrictedToPermissionKeyHandles()
    {
        return $this->restrictedToPermissionKeyHandles;
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getWorkflowID();
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('DELETE FROM Workflows WHERE wfID = ?', array($this->wfID));

        foreach (
            $db->GetArray(
                'SELECT wpID FROM WorkflowProgress WHERE wfID = ?',
                array($this->wfID)
            )
            as $row
        ) {
            $wfp = WorkflowProgress::getByID($row['wpID']);
            if ($wfp) {
                $wfp->delete();
            }
        }
    }

    // by default the basic workflow just passes the status num from the request
    // we do this so that we can order things by most important, etc...
    public function getWorkflowProgressCurrentStatusNum(WorkflowProgress $wp)
    {
        $req = $wp->getWorkflowRequestObject();
        if (is_object($req)) {
            return $req->getWorkflowRequestStatusNum();
        }
    }


    public static function getList()
    {
        $workflows = array();
        $db        = Loader::db();
        $r         = $db->Execute('SELECT wfID FROM Workflows ORDER BY wfName ASC');
        while ($row = $r->FetchRow()) {
            $wf = Workflow::getByID($row['wfID']);
            if (is_object($wf)) {
                $workflows[] = $wf;
            }
        }

        return $workflows;
    }

    public static function add(WorkflowType $wt, $name)
    {
        $db   = Loader::db();
        $wfID = $db->getOne('SELECT wfID FROM Workflows WHERE wfName=?', array($name));
        if (!$wfID) {
            $db->Execute('INSERT INTO Workflows (wftID, wfName) VALUES (?, ?)', array($wt->getWorkflowTypeID(), $name));
            $wfID = $db->Insert_ID();
        }

        return self::getByID($wfID);
    }

    protected function load($wfID)
    {
        $db = Loader::db();
        $r  = $db->GetRow('SELECT Workflows.* FROM Workflows WHERE Workflows.wfID = ?', array($wfID));
        $this->setPropertiesFromArray($r);
    }

    public static function getByID($wfID)
    {
        $db = Loader::db();
        $r  = $db->GetRow('SELECT WorkflowTypes.wftHandle, WorkflowTypes.pkgID FROM Workflows INNER JOIN WorkflowTypes ON Workflows.wftID = WorkflowTypes.wftID WHERE Workflows.wfID = ?', array($wfID));
        if ($r['wftHandle']) {
            $class = Loader::helper('text')->camelcase($r['wftHandle']) . 'Workflow';
            if (!class_exists($class)) {
                exit;
            }
            $obj = new $class();
            $obj->load($wfID);
            if ($obj->getWorkflowID() > 0) {
                $obj->loadDetails();

                return $obj;
            }
        }
    }

    public function getWorkflowToolsURL($task)
    {
        $type = $this->getWorkflowTypeObject();
        $uh   = Loader::helper('concrete/urls');
        $url  = $uh->getToolsURL('workflow/types/' . $type->getWorkflowTypeHandle(), $type->getPackageHandle());
        $url .= '?wfID=' . $this->getWorkflowID() . '&task=' . $task . '&' . Loader::helper('validation/token')->getParameter($task);

        return $url;
    }

    public function updateName($wfName)
    {
        $db = Loader::db();
        $db->Execute('UPDATE Workflows SET wfName = ? WHERE wfID = ?', array($wfName, $this->wfID));
    }

    abstract public function canApproveWorkflow();

    abstract public function start(WorkflowProgress $wp);

    abstract public function getWorkflowProgressActions(WorkflowProgress $wp);

    abstract public function getWorkflowProgressCurrentDescription(WorkflowProgress $wp);

    abstract public function getWorkflowProgressStatusDescription(WorkflowProgress $wp);

    abstract public function canApproveWorkflowProgressObject(WorkflowProgress $wp);

    abstract public function updateDetails($vars);

    abstract public function loadDetails();

    public function getPermissionAccessObject()
    {
        return false;
    }

    public function validateTrigger(WorkflowRequest $req)
    {
        return true;
    }
}
