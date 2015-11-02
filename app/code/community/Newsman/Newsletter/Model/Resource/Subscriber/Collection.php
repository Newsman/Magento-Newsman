<?php

/**
 * Newsletter subscribers collection
 *
 * @category    Newsman
 * @package     Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Resource_Subscriber_Collection extends Mage_Newsletter_Model_Resource_Subscriber_Collection
{
    /**
     * Adds customer info to select
     *
     * @return Mage_Newsletter_Model_Resource_Subscriber_Collection
     */
    public function showAdditionalCustomerInfo()
    {
        $adapter = $this->getConnection();
        $customer = Mage::getModel('customer/customer');
        $firstname = $customer->getAttribute('firstname');
        $lastname = $customer->getAttribute('lastname');

        $this->addFieldToSelect('subscriber_email', 'email');
        $this->getSelect()
            ->joinLeft(
                array('customer_lastname_table' => $lastname->getBackend()->getTable()),
                $adapter->quoteInto('customer_lastname_table.entity_id=main_table.customer_id
                    AND customer_lastname_table.attribute_id = ?', (int)$lastname->getAttributeId()
                ),
                array('lastname' => 'value')
            )
            ->joinLeft(
                array('customer_firstname_table' => $firstname->getBackend()->getTable()),
                $adapter->quoteInto('customer_firstname_table.entity_id=main_table.customer_id
                    AND customer_firstname_table.attribute_id = ?', (int)$firstname->getAttributeId()
                ),
                array('firstname' => 'value')
            );

        return $this;
    }

    /**
     * Load only subscribers that are not customers
     *
     * @return Newsman_Newsletter_Model_Resource_Subscriber_Collection
     */
    public function useOnlyNonCustomers()
    {
        $this->addFieldToFilter('main_table.customer_id', array('eq' => 0));

        return $this;
    }
}
