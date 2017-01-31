<?php

/**
 * Newsletter subscribe popup block
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Block_Popup extends Mage_Core_Block_Template
{
    const NEWSLETTER_POPUP_DISPLAY_COOKIE = 'newsletter_popup';
    const NEWSLETTER_POPUP_VALUE_COOKIE = 'shown';

    const XML_PATH_NEWSLETTER_POPUP_ENABLED = 'newsman_newsletter/popup_settings/enable';
    const XML_PATH_NEWSLETTER_POPUP_DESIGN = 'newsman_newsletter/popup_settings/design';
    const XML_PATH_COOKIE_LIFETIME = 'newsman_newsletter/popup_settings/cookie_lifetime';
    const XML_PATH_WINDOW_RESOLUTION = 'newsman_newsletter/popup_settings/window_resolution';
    const XML_PATH_DELAY_DISPLAY = 'newsman_newsletter/popup_settings/delay_display';

    public function getSuccessMessage()
    {
        $message = Mage::getSingleton('newsletter/session')->getSuccess();
        return $message;
    }

    public function getErrorMessage()
    {
        $message = Mage::getSingleton('newsletter/session')->getError();
        return $message;
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', array('_secure' => true));
    }

    public function getDisplayCookie()
    {
        $storeId = Mage::app()->getStore()->getId();
        $displayCookie = self::NEWSLETTER_POPUP_DISPLAY_COOKIE . '_' . $storeId;
        return $displayCookie;
    }

    public function getDisplayCookieValue()
    {
        return self::NEWSLETTER_POPUP_VALUE_COOKIE;
    }

    public function isEnabled($store = null)
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSLETTER_POPUP_ENABLED, $store);
    }

    public function getNewsletterPopupDesign($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NEWSLETTER_POPUP_DESIGN, $store);
    }

    public function getNewsletterCookieLifetime($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_COOKIE_LIFETIME, $store);
    }

    public function getWindowResolution($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_WINDOW_RESOLUTION, $store);
    }

    public function getNewsletterDelayDisplay($store = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_DELAY_DISPLAY, $store);
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }
        return parent::_toHtml();
    }
}
