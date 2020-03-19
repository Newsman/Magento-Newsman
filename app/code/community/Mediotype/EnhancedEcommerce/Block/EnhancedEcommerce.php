<?php
/**
 * Base Block For Google Enhanced Ecommerce Analytics
 *
 * @author  Joel Hart @mediotype
 */
class Mediotype_EnhancedEcommerce_Block_EnhancedEcommerce extends Mage_Core_Block_Template{

    protected function _toHtml()
    {
        //check to make sure ee is enabled
        if($this->getMediotypeEEHelper()->getEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    public function getMediotypeEEHelper()
    {
        return Mage::helper('mediotype_ee');
    }

}
