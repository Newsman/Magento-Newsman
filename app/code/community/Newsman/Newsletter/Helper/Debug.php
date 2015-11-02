<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Helper_Debug extends Mage_Core_Helper_Abstract
{
    const XML_PATH_NEWSMAN_DEBUG_ENABLED = 'newsman_newsletter/debug/enabled';

    public static function isEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_DEBUG_ENABLED, $store);
    }

}