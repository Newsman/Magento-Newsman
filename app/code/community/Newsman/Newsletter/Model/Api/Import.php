<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Api_Import extends Newsman_Newsletter_Model_Api_Newsman
{
    /**
     * Import subscribers using csv
     *
     * @param $listId
     * @param $segments
     * @param $csvData
     * @param null $storeId
     * @return null
     */
    public function csv($listId, $segments, $csvData, $storeId = null)
    {
        $result = null;
        try {
            $result = $this->getClient($storeId)->import->csv($listId, $segments, $csvData);

            if ($result == '') {
                Mage::throwException(Mage::helper('newsman_newsletter')->__('Error on method import.csv'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $result;
    }
}