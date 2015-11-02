<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_NEWSMAN_ENABLED = 'newsman_newsletter/settings/enabled';
    const XML_PATH_NEWSMAN_USERID = 'newsman_newsletter/settings/userid';
    const XML_PATH_NEWSMAN_APIKEY = 'newsman_newsletter/settings/apikey';
    const XML_PATH_NEWSMAN_LISTID = 'newsman_newsletter/settings/list_id';
    const XML_PATH_NEWSMAN_AUTOSYNC = 'newsman_newsletter/settings/auto_sync';

    const XML_PATH_NEWSMAN_MAP_SEGMENTS = 'newsman_newsletter/mapping/segments';

    const XML_PATH_NEWSMAN_SYNC_ENABLED = 'newsman_newsletter/subscribers_synchronization/enabled';
    const XML_PATH_NEWSMAN_SYNC_TIME = 'newsman_newsletter/subscribers_synchronization/time';
    const XML_PATH_NEWSMAN_SYNC_FREQUENCY = 'newsman_newsletter/subscribers_synchronization/frequency';
    /**
     * 'Send error emails to' configuration
     */
    const XML_PATH_NEWSMAN_SYNC_ERROR_EMAIL = 'newsman_newsletter/subscribers_synchronization/error_email';
    /**
     * Error email identity configuration
     */
    const XML_PATH_NEWSMAN_SYNC_ERROR_EMAIL_IDENTITY = 'newsman_newsletter/subscribers_synchronization/error_email_identity';
    /**
     * Error email template configuration
     */
    const XML_PATH_NEWSMAN_SYNC_ERROR_EMAIL_TEMPLATE = 'newsman_newsletter/subscribers_synchronization/error_email_template';

    const XML_PATH_NEWSMAN_IMPORT_BATCH_SIZE = 'newsman_newsletter/subscribers_import/batch_size';
    const XML_PATH_NEWSMAN_IMPORT_QUEUE_BATCH_SIZE = 'newsman_newsletter/subscribers_import/queue_batch_size';


    public function isEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_ENABLED, $store);
    }

    public function getUserId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_USERID, $store);
    }

    public function getApiKey($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_APIKEY, $store);
    }

    public function getListId($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_LISTID, $store);
    }

    public function getAutoSync($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_AUTOSYNC, $store);
    }

    public function useAutoSync($store = null)
    {
        return $this->isEnabled($store) && $this->getAutoSync($store);
    }

    public function getSegments($store = null)
    {
        $configValueSerialized = Mage::getStoreConfig(self::XML_PATH_NEWSMAN_MAP_SEGMENTS, $store);

        if (!$configValueSerialized) {
            return false;
        }

        $segments = @unserialize($configValueSerialized);

        if (empty($segments)) {
            return false;
        }

        if (is_array($segments)) {
            $segments = array_values($segments);
        }

        return $segments;
    }

    public function isSubscribersSynchronizationEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SYNC_ENABLED, $store);
    }

    public function getSynchronizationTime($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SYNC_TIME, $store);
    }

    public function getSynchronizationFrequency($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SYNC_FREQUENCY, $store);
    }

    public function getSynchronizationErrorEmail($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SYNC_ERROR_EMAIL, $store);
    }

    public function getSynchronizationErrorEmailIdentity($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SYNC_ERROR_EMAIL_IDENTITY, $store);
    }

    public function getSynchronizationErrorEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SYNC_ERROR_EMAIL_TEMPLATE, $store);
    }

    public function getCustomerBatchSize($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_IMPORT_BATCH_SIZE, $store);
    }

    public function getQueueBatchSize($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_IMPORT_QUEUE_BATCH_SIZE, $store);
    }
}