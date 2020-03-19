<?php
/**
 *
 * @author  Joel Hart @mediotype
 */
class Mediotype_EnhancedEcommerce_Block_Checkout_AfterSuccess extends Mage_Core_Block_Template{

    /**
     * Returns customer's placed order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder(){
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
        return $order;
    }

    /**
     * Returns data to send to Google Analytics
     *
     * @return Varien_Object
     */
    public function getTotals(){
        $order  = $this->getOrder();

        $totals = new Varien_Object();
        $totals->setData('amount', number_format($order->getGrandTotal(), 2,'.',''));
        $totals->setData('tax', number_format($order->getBaseTaxAmount(), 2,'.',''));
        $totals->setData('shipping', number_format($order->getShippingAmount(), 2,'.',''));

        return $totals;
    }

    /**
     * Returns all items in the order
     *
     * @return array
     */
    public function getItems(){
        $order = $this->getOrder();
        $items = $order->getAllItems();
        return $items;
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
