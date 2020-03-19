<?php
/**
 *
 *
 * @author  Joel Hart
 */
class Mediotype_EnhancedEcommerce_Block_Page_AfterFooter extends Mage_Core_Block_Template{

    public function _construct(){
        parent::_construct();
    }

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