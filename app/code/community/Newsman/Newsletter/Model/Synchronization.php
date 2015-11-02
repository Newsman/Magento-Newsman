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

    public function subscribersSync($page, $segment = null)
    {
        $collection = $this->getSubscribersOnly();
        return $this->_import($collection, $page, $segment);
    }

    public function customersSync($pages, $customerGroupId = null, $segment = null)
    {
        $collection = $this->getCustomersByGroup($customerGroupId);
        return $this->_import($collection, $pages, $segment);
    }

    protected function _import($collection, $page, $segment)
    {
        $listId = Mage::helper('newsman_newsletter')->getListId();
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

    public function getSubscribersOnly()
    {
        $collection = Mage::getModel('newsletter/subscriber')->getCollection()
            ->showAdditionalCustomerInfo()
            ->useOnlyNonCustomers();

        return $collection;
    }

    public function getCustomersByGroup($groupId)
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addNameToSelect();
        if ($groupId && $groupId != Mage_Customer_Model_Group::CUST_GROUP_ALL) {
            $collection->addFieldToFilter('group_id', array('eq' => $groupId));
        }
        return $collection;
    }
}