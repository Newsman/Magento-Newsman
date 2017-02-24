<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Api_Segment extends Newsman_Newsletter_Model_Api_Newsman
{
    /**
     * @param $listId
     * @return mixed
     * @throws Exception
     */
    public function getAll($listId, $storeId = null)
    {
        $segments = array();
        try {
            $segments = $this->getClient($storeId)->segment->all($listId);

            if (!is_array($segments)) {
                throw new Exception(Mage::helper('newsman_newsletter')->__('Error on method segment.clear'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $segments;
    }

    public function addSubscriber($segmentId, $subscriberId)
    {
        $result = false;
        try {
            $result = $this->getClient()->segment->addSubscriber(
                $segmentId,
                $subscriberId
            );

            if ($result !== true) {
                throw new Exception(Mage::helper('newsman_newsletter')->__('Error on method segment.addSubscriber'));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $result;
    }
}