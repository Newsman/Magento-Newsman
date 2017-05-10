<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Helper_Task extends Mage_Core_Helper_Abstract
{
    const XML_PATH_NEWSMAN_CLEAR_FAILED_TASKS = 'newsman_newsletter/subscribers_import/clear_failed_tasks';
    const XML_PATH_NEWSMAN_DELETE_PROCESSED_TASKS = 'newsman_newsletter/subscribers_import/delete_processed_tasks';

    public function clearFailedTasks($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_CLEAR_FAILED_TASKS, $store);
    }

    public function deleteProcessedTasks($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_DELETE_PROCESSED_TASKS, $store);
    }

    public function insertTasks()
    {
        $stores = Mage::app()->getStores();
        foreach ($stores as $storeId => $store)
        {
            Mage::app()->getLocale()->emulate($storeId);
            Mage::app()->setCurrentStore($storeId);

            if (!Mage::helper('newsman_newsletter')->isEnabled()) {
                return $this;
            }
            $mappedValues = Mage::helper('newsman_newsletter')->getSegments();
            if (!$mappedValues) {
                $subscriberCollection = $this->getSubscriberCollection();
                $this->_insertTasks($subscriberCollection, '_insertSubscriberTask');

                $customerCollection = $this->getCustomerCollection();
                $this->_insertTasks($customerCollection, '_insertCustomerTask');

                return $this;
            }

            $hasNonLoggedInIdGroup = false;
            foreach ($mappedValues as $mappedValue) {
                $insertMethod = '_insertCustomerTask';
                if ($mappedValue['customer_group_id'] == Mage_Customer_Model_Group::NOT_LOGGED_IN_ID) {
                    $hasNonLoggedInIdGroup = true;
                    $insertMethod = '_insertSubscriberTask';
                    $collection = $this->getSubscriberCollection();
                } elseif ($mappedValue['customer_group_id'] == Mage_Customer_Model_Group::CUST_GROUP_ALL) {
                    $collection = $this->getCustomerCollection();
                } else {
                    $collection = $this->getCustomerCollection();
                    $collection->addFieldToFilter('group_id', array('eq' => $mappedValue['customer_group_id']));
                }
                $this->_insertTasks($collection, $insertMethod, $mappedValue['customer_group_id'], $mappedValue['segment']);
            }

            if (!$hasNonLoggedInIdGroup) {
                $collection = $this->getSubscriberCollection();
                $this->_insertTasks($collection, '_insertSubscriberTask');
            }
            Mage::app()->getLocale()->revert();
        }

        return $this;
    }

    public function getSubscriberCollection()
    {
        return Mage::getModel('newsletter/subscriber')->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getStoreId())
            ->useOnlyNonCustomers();
    }

    public function getCustomerCollection()
    {
        return Mage::getModel('customer/customer')->getCollection()
            ->addFieldToFilter('store_id', Mage::app()->getStore()->getStoreId());
    }

    protected function _insertTasks($collection, $insertMethod, $groupId = null, $segment = null)
    {
        $totalSubscribers = $collection->getSize();
        $batchSize = Mage::helper('newsman_newsletter')->getCustomerBatchSize();
        $pageLimit = ceil($totalSubscribers / $batchSize);

        for ($page = 1; $page <= $pageLimit; $page++) {
            $data = array(
                'customer_group_id' => $groupId,
                'segment' => $segment,
                'page' => $page,
                'store_id' => Mage::app()->getStore()->getStoreId()
            );
            $this->$insertMethod($data);
        }
    }

    /**
     * Insert a new customer task in the queue
     *
     * @param $data
     */
    protected function _insertCustomerTask($data)
    {
        $task = Mage::getModel('newsman_newsletter/task');
        $task->setEntity(Newsman_Newsletter_Model_Task::TASK_ENTITY_CUSTOMER)
            ->setInfo(serialize($data))
            ->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
            ->save();
    }

    /**
     * Insert a new subscriber task in the queue
     *
     * @param $data
     */
    protected function _insertSubscriberTask($data)
    {
        $task = Mage::getModel('newsman_newsletter/task');
        $task->setEntity(Newsman_Newsletter_Model_Task::TASK_ENTITY_SUBSCRIBER)
            ->setInfo(serialize($data))
            ->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
            ->save();
    }
}