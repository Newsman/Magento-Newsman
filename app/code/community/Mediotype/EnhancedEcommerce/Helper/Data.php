<?php
/**
 * @author  Joel Hart @mediotype
 */
class Mediotype_EnhancedEcommerce_Helper_Data extends Mage_Core_Helper_Abstract{

    /**
     * Config paths for using throughout the code
     */
    const EE_ENABLED  = 'mediotype_ee/mediotype_ee_settings/ee_enabled';

    /**
     * Get Google Analytics UA Code
     *
     * @return string
     */
    public function getGoogleAnalyticsUA(){
        return Mage::getStoreConfig(Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACCOUNT);
    }

    public function getEnabled() {
        return Mage::getStoreConfig('mediotype_ee/mediotype_ee_settings/ee_enabled');
    }

}
