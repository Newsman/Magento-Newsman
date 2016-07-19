<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Helper_Smtp extends Mage_Core_Helper_Abstract
{
    const XML_PATH_NEWSMAN_SMTP_ENABLED = 'newsman_newsletter/smtp/enabled';
    const XML_PATH_NEWSMAN_SMTP_HOST = 'newsman_newsletter/smtp/host';
    const XML_PATH_NEWSMAN_SMTP_PORT = 'newsman_newsletter/smtp/port';
    const XML_PATH_NEWSMAN_SMTP_SSL_TYPE = 'newsman_newsletter/smtp/ssl_type';
    const XML_PATH_NEWSMAN_SMTP_AUTH = 'newsman_newsletter/smtp/auth';
    const XML_PATH_NEWSMAN_SMTP_USERNAME = 'newsman_newsletter/smtp/username';
    const XML_PATH_NEWSMAN_SMTP_PASSWORD = 'newsman_newsletter/smtp/password';


    public function isEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SMTP_ENABLED, $store);
    }

    public function getHost($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SMTP_HOST, $store);
    }

    public function getPort($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SMTP_PORT, $store);
    }

    public function getSslType($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SMTP_SSL_TYPE, $store);
    }

    public function getAuth($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SMTP_AUTH, $store);
    }

    public function getUsername($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SMTP_USERNAME, $store);
    }

    public function getPassword($store = null)
    {
        $password = Mage::getStoreConfig(self::XML_PATH_NEWSMAN_SMTP_PASSWORD, $store);
        return Mage::helper('core')->decrypt($password);
    }
}