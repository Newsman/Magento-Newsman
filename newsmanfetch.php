<?php

$magentoRootDir = getcwd();
$bootstrapFilename = $magentoRootDir . '/app/bootstrap.php';
$mageFilename = $magentoRootDir . '/app/Mage.php';

if (!file_exists($bootstrapFilename)) {
    //echo 'Bootstrap file not found';
    //exit;
}
if (!file_exists($mageFilename)) {
    echo 'Mage file not found';
    exit;
}
//require $bootstrapFilename;
require $mageFilename;

class NewsmanFetch extends Mage_Core_Helper_Abstract
{
    private $storeId;

    public function __construct()
    {
        $this->storeId = Mage::app()->getStore()->getStoreId();

        $apiKey = Mage::getStoreConfig("newsman_newsletter/settings/apikey", $this->storeId);

        $this->NewsmanFetch($apiKey);
    }

    public function getSubscriberCollection()
    {
        $collection = Mage::getModel('newsletter/subscriber')->getCollection()
            ->addFieldToFilter('subscriber_status', 1);

        return $collection;
    }

    public function getCustomerCollection()
    {
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname');

        return $collection;
    }

    public function NewsmanFetch($_apikey)
    {
        $apikey = (empty($_GET["apikey"])) ? "" : $_GET["apikey"];
        $newsman = (empty($_GET["newsman"])) ? "" : $_GET["newsman"];
        $start = (!empty($_GET["start"]) && $_GET["start"] >= 0) ? $_GET["start"] : 0;
        $limit = (empty($_GET["limit"])) ? 1000 : $_GET["limit"];
	    $product_id = (empty($_GET["product_id"])) ? "" : $_GET["product_id"];

        if (!empty($newsman) && !empty($apikey) || strpos($_GET["newsman"], 'getCart.json') !== false) {
            $apikey = $_GET["apikey"];
            $currApiKey = $_apikey;

            if(strpos($_GET["newsman"], 'getCart.json') !== false)
            {
                Mage::app('default');
                Mage::getSingleton('core/session', array('name' => 'frontend'));

                $cart = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();                                                

                $prod = array();

                foreach ( $cart as $cart_item ) {                                 

                        $prod[] = array(
                            "id" => $cart_item->getId(),
                            "name" => $cart_item->getName(),
                            "price" => $cart_item->getPrice(),						
                            "quantity" => $cart_item->getQty()
                        );							
                                            
                    }		                        

                    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                    header("Cache-Control: post-check=0, pre-check=0", false);
                    header("Pragma: no-cache");
                    header('Content-Type:application/json');
                    echo json_encode($prod, JSON_PRETTY_PRINT);
                    exit;
            }
            else{
                if ($apikey != $currApiKey) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode(403, JSON_PRETTY_PRINT);
                    return;
                }
            }

            switch ($_GET["newsman"]) {
                case "orders.json":

                    $ordersObj = array();

                    $orders = Mage::getModel('sales/order')->getCollection();
                    $orders->getSelect()->limit($limit, $start);   
                    //->addFieldToFilter('status', 'complete')

                    foreach ($orders as $item) {

                        $colOrder = $item->getData();   

                        $products = Mage::getModel('sales/order_item')->getCollection();
                        $productsJson = array();

                        foreach ($products as $prod) {

                            $prod = Mage::getModel('catalog/product')->load($prod->getId());

                            $prodObj = $prod;
                            $prod = $prod->getData();

                            $price = $prod["price"];
                            $priceOld = (!empty($prod["special_price"])) ? $prod["special_price"] : 0;

                            if($priceOld > 0)
                            {
                                $priceOld = $price;
                                $price = $prod["special_price"];
                            }

                            $productsJson[] = array(
                                "id" => $prod['entity_id'],
                                "name" => $prod['name'],
                                "quantity" => $prod['qty_ordered'],
                                "price" => $price,
                                "price_old" => $priceOld,
                                "image_url" => $prodObj->getImageUrl(),
                                "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $prodObj->getUrlPath()
                            );
                        }

                        $ordersObj[] = array(
                            "order_no" => $colOrder["entity_id"],
                            "date" => "",
                            "status" => "",
                            "lastname" => $colOrder["customer_lastname"],
                            "firstname" => $colOrder["customer_firstname"],
                            "email" => $colOrder["email"],
                            "phone" => "",
                            "state" => "",
                            "city" => "",
                            "address" => "",
                            "discount" => $colOrder["discount_amount"],
                            "discount_code" => "",
                            "shipping" => "",
                            "fees" => 0,
                            "rebates" => 0,
                            "total" => $colOrder["base_grand_total"],
                            "products" => $productsJson
                        );
                    }

                    header('Content-Type: application/json');
                    echo json_encode($ordersObj, JSON_PRETTY_PRINT);
                    return;

                    break;

                    case "products.json":

                        if(empty($product_id))
                        {
                                
                            $products = Mage::getModel('catalog/product')->getCollection();
                                    $products->getSelect()->limit($limit, $start);         
    
                                    $productsJson = array();
    
                                    foreach ($products as $prod) {                                    
    
                                        $prod = Mage::getModel('catalog/product')->load($prod->getId());
    
                                        $prodObj = $prod;
                                        $prod = $prod->getData();
    
                                        if($prod["entity_id"] == 2)
                                        {
                                        var_dump($prod);die('');
                                        }
    
                                        $price = $prod["price"];
                                        $priceOld = (!empty($prod["special_price"])) ? $prod["special_price"] : 0;
                                        
                                        if($priceOld > 0)
                                        {
                                            $priceOld = $price;
                                            $price = $prod["special_price"];
                                        }
    
                                        $productsJson[] = array(
                                            "id" => $prod["entity_id"],
                                            "name" => $prod["name"],
                                            "stock_quantity" => $prod["is_in_stock"],
                                            "price" => $price,
                                            "price_old" => $priceOld,
                                            "image_url" => $prodObj->getImageUrl(),
                                            "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $prodObj->getUrlPath()
                                        );
                                        
                                    }
    
                        }
                        else{
    
                            $prod = Mage::getModel('catalog/product')->load($product_id);
                        
                            $prodObj = $prod;
                            $prod = $prod->getData();
    
                            $price = $prod["price"];
                            $priceOld = (!empty($prod["special_price"])) ? $prod["special_price"] : 0;
                            
                            if($priceOld > 0)
                            {
                                $priceOld = $price;
                                $price = $prod["special_price"];
                            }
    
                            $productsJson[] = array(
                                "id" => $prod["entity_id"],
                                "name" => $prod["name"],
                                "stock_quantity" => $prod["is_in_stock"],
                                "price" => $price,
                                "price_old" => $priceOld,
                                "image_url" => $prodObj->getImageUrl(),
                                "url" => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $prodObj->getUrlPath()
                            );
                    
                        }
    
                        header('Content-Type: application/json');
                        echo json_encode($productsJson, JSON_PRETTY_PRINT);
                        return;
    
                        break;

                case "customers.json":

                    $wp_cust = $this->getCustomerCollection();
                    $wp_cust->getSelect()->limit($limit, $start);  

                    $custs = array();

                    foreach ($wp_cust as $users) {

                        $col = $users->getData();

                        $custs[] = array(
                            "email" => $col["email"],
                            "firstname" => $col["firstname"],
                            "lastname" => $col["lastname"]
                        );
                    }

                    header('Content-Type: application/json');
                    echo json_encode($custs, JSON_PRETTY_PRINT);
                    return;

                    break;

                case "subscribers.json":

                    $wp_subscribers = $this->getSubscriberCollection();
                    $wp_subscribers->getSelect()->limit($limit, $start); 

                    $subs = array();

                    foreach ($wp_subscribers as $users) {

                        $col = $users->getData();

                        $subs[] = array(
                            "email" => $col["subscriber_email"],
                            "firstname" => "",
                            "lastname" => ""
                        );
                    }

                    header('Content-Type: application/json');
                    echo json_encode($subs, JSON_PRETTY_PRINT);
                    return;

                    break;
                case "version.json":

                        $version = array(
                            "version" => "Magento 1.9.x"
                        );

                        header('Content-Type: application/json');
                        echo json_encode($version, JSON_PRETTY_PRINT);
                        return;

                    break;
            }
        } else {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(403, JSON_PRETTY_PRINT);
        }
    }
}

$fetch = new NewsmanFetch();
