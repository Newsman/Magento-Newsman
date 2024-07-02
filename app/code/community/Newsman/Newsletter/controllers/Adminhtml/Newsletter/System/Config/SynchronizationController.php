<?php

/**
 * Subscriber admin controller
 *
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Adminhtml_Newsletter_System_Config_SynchronizationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check whether the manual sync has been executed without errors
     *
     * @return void
     */
    public function manualsyncAction()
    {
        $isSynced = true;
        try {
            Mage::helper('newsman_newsletter/task')->insertTasks();
        } catch (Exception $e) {
            Mage::logException($e);
            $isSynced = false;
        }
        
        $code = $this->getRequest()->getPost('code');
        $response = array();

        if ($code) {

            $authUrl = "https://newsman.app/admin/oauth/token";

			$redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			$body = array(
				"grant_type" => "authorization_code",
				"code" => $code,
				"client_id" => "nzmplugin",
				"redirect_uri" => $redirect
			);

			$ch = curl_init($authUrl);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

			$response = curl_exec($ch);

			if (curl_errno($ch)) {
				$error .= 'cURL error: ' . curl_error($ch);
			}

			curl_close($ch);

			if ($response !== false) {

				$response = json_decode($response);

				$data["creds"] = json_encode(array(
					"newsman_userid" => $response->user_id,
					"newsman_apikey" => $response->access_token
					)
				);

				foreach($response->lists_data as $list => $l){
					$dataLists[] = array(
						"id" => $l->list_id,
						"name" => $l->name
					);
				}	

				$data["dataLists"] = $dataLists;

				$data["oauthStep"] = 2;
			} else {
				$error .= "Error sending cURL request.";
			} 
        } else {
            $response['message'] = 'Invalid code parameter.';
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));

        //$this->getResponse()->setBody((int)$isSynced);
    }

    public function setfeedAction()
    {
        $isSynced = true;
        try {
           
            $storeId = Mage::app()->getStore();
          
            $params = $this->getRequest()->getParams();

            $store = 0;

            if (isset($params['store']) && $params['store']) {
                $store = Mage::getModel('core/store')->load($params['store']);          
            } elseif (isset($params['website']) && $params['website']) {
                $store = Mage::getModel('core/website')->load($params['website']);        
            }                                 

            if(!is_numeric($store))
            {
                try{
                    $store = $store->getWebsiteId();        
                }
                catch(Exception $e)
                {
                    $store = $store->getStoreId();
                }
            }               
            
            Mage::helper('newsman_newsletter/task')->setFeed($store);
        } catch (Exception $e) {
            Mage::logException($e);
            $isSynced = false;
        }
        $this->getResponse()->setBody((int)$isSynced);
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/newsman_newsletter');
    }
}
