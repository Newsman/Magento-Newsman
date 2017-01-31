<?php

/**
 * Subscribers Synchronization entity model
 *
 * @category    Newsman
 * @package     Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Synchronization
{
    protected $_listId;
    protected $_columns = array(
        'email',
        'firstname',
        'lastname'
    );

    public function getListId()
    {
        if (!$this->_listId) {
            $this->_listId = Mage::helper('newsman_newsletter')->getListId();
        }
        return $this->_listId;
    }

    public function subscribersSync($page, $segment = null, $storeId = null)
    {
        $collection = $this->getSubscribersOnly($storeId);
        return $this->_import($collection, $page, $segment, $storeId);
    }

    public function customersSync($pages, $customerGroupId = null, $segment = null, $storeId = null)
    {
        $collection = $this->getCustomersByGroup($customerGroupId, $storeId);
        return $this->_import($collection, $pages, $segment, $storeId);
    }

    protected function _import($collection, $page, $segment, $storeId)
    {
        $listId = Mage::helper('newsman_newsletter')->getListId($storeId);
        $batchSize = Mage::helper('newsman_newsletter')->getCustomerBatchSize();
        $subscriberCollection = $collection->setPageSize($batchSize)
            ->setCurPage($page);

        $csvData = $this->getCsv($subscriberCollection, $listId);
        $segments = null;

        if ($segment) {
            $segments = array($segment);
        }
        $importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segments, $csvData);

        return $importResult;
    }

    public function getCsv($subscriberCollection, $listId)
    {
        $csv = '';
        $data = array('"list_id"');
        foreach ($this->_columns as $column) {
            $data[] = '"' . $column . '"';
        }
        $csv .= implode(',', $data) . "\n";

        foreach ($subscriberCollection as $subscriber) {

            $data = array('"' . $listId . '"');
            foreach ($this->_columns as $column) {
                $data[] = '"' . str_replace(
                        array('"', '\\'),
                        array('""', '\\\\'),
                        $subscriber->getData($column)
                    ) . '"';
            }
            $csv .= implode(',', $data) . "\n";
        }
        return $csv;
    }

    public function getSubscribersOnly($storeId)
    {
        $collection = Mage::getModel('newsletter/subscriber')->getCollection()
            ->showAdditionalCustomerInfo()
            ->useOnlyNonCustomers();

        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }

        return $collection;
    }

    public function getCustomersByGroup($groupId, $storeId)
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addNameToSelect();
        if ($groupId && $groupId != Mage_Customer_Model_Group::CUST_GROUP_ALL) {
            $collection->addFieldToFilter('group_id', array('eq' => $groupId));
        }

        if ($storeId) {
            $collection->addFieldToFilter('store_id', array('eq' => $storeId));
        }

        return $collection;
    }
}