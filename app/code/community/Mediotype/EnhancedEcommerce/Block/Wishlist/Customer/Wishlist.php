<?php
/**
 * Wishlist Product Block For Google Enhanced Ecommerce Analytics
 *
 * @author  Joel Hart @mediotype
 */
class Mediotype_EnhancedEcommerce_Block_Wishlist_Customer_Wishlist extends Mage_Wishlist_Block_Customer_Wishlist{

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
