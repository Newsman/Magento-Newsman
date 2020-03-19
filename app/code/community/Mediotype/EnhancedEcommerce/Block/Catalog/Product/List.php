<?php
/**
 * Product View Block For Google Enhanced Ecommerce Analytics
 *
 * @author  Joel Hart @mediotype
 */
class Mediotype_EnhancedEcommerce_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_List{

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
