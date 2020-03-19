<?php
/**
 * Magento / Mediotype Module
 *
 * @author      Joel Hart @mediotype
 */
class Mediotype_EnhancedEcommerce_Block_Page_BeforeBody extends Mage_Core_Block_Template {

    protected $helper;

    /**
     * Get module helper for block
     *
     */
    public function _construct(){
        /** @var $helper Mediotype_EnhancedEcommerce_Helper_Data */
        $this->helper = Mage::helper('mediotype_ee');
        parent::_construct();
    }

    /**
     * Get Google Analytics UA Code
     *
     * @return string
     */
    public function getGoogleAnalyticsUA(){
        return $this->helper->getGoogleAnalyticsUA();
    }

    /**
     * Get domain.com for GA tracking block
     *
     * @return string
     */
    public function getHost(){
        $url    = $this->getUrl();
        $parse  = parse_url($url);
        Mediotype_Core_Helper_Debugger::log($parse);
        $host = $parse['host'];
        $hostArr = explode('.',$host);
        $dotDomain = array_pop($hostArr);
        $string = array_pop($hostArr) . '.' . $dotDomain;
        return $string;
    }

    /**
     * Render GA tracking scripts
     *
     * @return string
     */
    protected function _toHtml()
    {

        if (!$this->getIsGoogleAnalyticsAvailable() || !$this->helper->getEnabled() ) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    protected function getIsGoogleAnalyticsAvailable($store = null){
        $accountId = Mage::getStoreConfig(Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACCOUNT, $store);
        $availableCheck = $accountId && Mage::getStoreConfigFlag(Mage_GoogleAnalytics_Helper_Data::XML_PATH_ACTIVE, $store);
        return $availableCheck;
    }

}
