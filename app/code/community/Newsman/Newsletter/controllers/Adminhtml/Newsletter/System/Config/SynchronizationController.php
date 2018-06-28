<?php

/**
 * Subscriber admin controller
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Adminhtml_Newsletter_System_Config_SynchronizationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check whether the manual sync has been executed without errors
     *
     * @return void
     */
    public function manualsyncAction()
    {
        $isSynced = true;
        try {
            Mage::helper('newsman_newsletter/task')->insertTasks();
        } catch (Exception $e) {
            Mage::logException($e);
            $isSynced = false;
        }
        $this->getResponse()->setBody((int)$isSynced);
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/newsman_newsletter');
    }
}
