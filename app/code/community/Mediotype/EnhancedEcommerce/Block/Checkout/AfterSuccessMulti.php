<?php

/**
 *
 * @author  Joel Hart @mediotype
 */
class Mediotype_EnhancedEcommerce_Block_Checkout_AfterSuccessMulti extends Mage_Core_Block_Template
{

    /**
     * Returns customer's placed orders in a multi shipping senario
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrders()
    {
        $ids = Mage::getSingleton('core/session')->getOrderIds(true);

        $orders = array();
        if ($ids && is_array($ids)) {
            foreach ($ids as $id => $incrementalId) {
                /** @var $orders[] Mage_Sales_Model_Order */
                $orders[] = Mage::getModel('sales/order')->load($id);
            }
            return $orders;
        }
        return false;
    }

    /**
     * Returns data to send to Google Analytics
     *
     * @param $order Mage_Sales_Model_Order
     *
     * @return Varien_Object
     */
    public function getTotals($order)
    {

        $totals = new Varien_Object();
        $totals->setData('amount', number_format($order->getGrandTotal(), 2,'.',''));
        $totals->setData('tax', number_format($order->getBaseTaxAmount(), 2,'.',''));
        $totals->setData('shipping', number_format($order->getShippingAmount(), 2,'.',''));

        return $totals;
    }

    /**
     * Returns all items in the order
     *
     * @param $order Mage_Sales_Model_Order
     *
     * @return array
     */
    public function getItems($order)
    {
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
