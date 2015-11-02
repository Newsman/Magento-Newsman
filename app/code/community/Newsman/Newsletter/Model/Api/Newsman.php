<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Api_Newsman
{
    protected $_client;

    public function getClient()
    {
        if (!$this->_client) {
            /** @var Newsman_Newsletter_Helper_Data $helper */
            $helper = Mage::helper('newsman_newsletter');
            $userId = $helper->getUserId();
            $apiKey = $helper->getApiKey();

            try {
                $this->_client = new Newsman_Client($userId, $apiKey);
                $this->_client->setCallType('rpc');
            } catch (Newsman_Client_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($helper->__($e->getMessage()));
                Mage::logException($e);
            } catch (Exception $e) {
                //Mage::getSingleton('adminhtml/session')->addError($helper->__('Connection to Newsman failed.'));
                Mage::logException($e);
            }
        }

        return $this->_client;
    }
}