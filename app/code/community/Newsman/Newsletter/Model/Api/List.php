<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Api_List extends Newsman_Newsletter_Model_Api_Newsman
{
    /**
     * Retrieve all the lists
     *
     * @throws Exception
     */
    public function getAll()
    {
        $lists = array();
        try {
            $lists = $this->getClient()->list->all();

            if (!is_array($lists)) {
                Mage::throwException(Mage::helper('newsman_newsletter')->__('Error on method list.all'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $lists;
    }
}