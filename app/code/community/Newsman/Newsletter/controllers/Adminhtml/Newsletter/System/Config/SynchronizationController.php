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

    public function setfeedAction()
    {
        $isSynced = true;
        try {
           
            $storeId = Mage::app()->getStore();
          
            $params = $this->getRequest()->getParams();

            $store = 0;

            if (isset($params['store']) && $params['store']) {
                $store = Mage::getModel('core/store')->load($params['store']);          
            } elseif (isset($params['website']) && $params['website']) {
                $store = Mage::getModel('core/website')->load($params['website']);        
            }                                 

            if(!is_numeric($store))
            {
                try{
                    $store = $store->getWebsiteId();        
                }
                catch(Exception $e)
                {
                    $store = $store->getStoreId();
                }
            }               
            
            Mage::helper('newsman_newsletter/task')->setFeed($store);
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
