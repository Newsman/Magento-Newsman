<?php

/**
 * @author  Joel Hart
 */
class Mediotype_EnhancedEcommerce_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     *
     */
    public function indexAction()
    {
        $this->_redirectReferer();
    }

    public function getProductSkuByIdAction()
    {
        $productId = $this->getRequest()->getParam('productId');
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getId()) {
            $response = array(
                'productId'  => $product->getID(),
                'productSku' => $product->getSku(),
                'response'   => 'success'
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        } else {
            $response = array(
                'msg'      => 'failed to load product id: ' . $productId,
                'response' => 'error'
            );
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        }
    }

}
