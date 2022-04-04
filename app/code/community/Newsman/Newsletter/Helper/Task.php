<?php

/**
 * @category   Newsman
 * @package    Newsman_Newsletter
 */
class Newsman_Newsletter_Helper_Task extends Mage_Core_Helper_Abstract
{
	const XML_PATH_NEWSMAN_CLEAR_FAILED_TASKS = 'newsman_newsletter/subscribers_import/clear_failed_tasks';
	const XML_PATH_NEWSMAN_DELETE_PROCESSED_TASKS = 'newsman_newsletter/subscribers_import/delete_processed_tasks';
	const XML_PATH_NEWSMAN_IMPORTFILTER = 'newsman_newsletter/settings/importfilter';

	public function clearFailedTasks($store = null)
	{
		return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_CLEAR_FAILED_TASKS, $store);
	}

	public function deleteProcessedTasks($store = null)
	{
		return (bool)Mage::getStoreConfig(self::XML_PATH_NEWSMAN_DELETE_PROCESSED_TASKS, $store);
	}

	public static function safeForCsv($str)
	{
		return '"' . str_replace('"', '""', $str) . '"';
	}

	public function _importDataG(&$data, $list, $segments = null, $storeId)
	{
		$csv = '"email","customerId", "groupId","source", "firstname", "lastname"' . PHP_EOL;

		$source = self::safeForCsv("magento customer");
		foreach ($data as $_dat)
		{
			$csv .= sprintf(
				"%s,%s,%s,%s,%s,%s",
				self::safeForCsv($_dat["email"]),
				self::safeForCsv($_dat["customerId"]),
				self::safeForCsv($_dat["groupId"]),
				$source,
				self::safeForCsv($_dat["firstname"]),
				self::safeForCsv($_dat["lastname"])
			);
			$csv .= PHP_EOL;
		}

		if (is_array($segments) && count($segments) > 0)
		{
			$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($list, $segments, $csv, $storeId);
		} else
		{
			$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($list, null, $csv, $storeId);
		}

		$data = array();
	}

	public function _importDataC(&$data, $list, $segments = null, $storeId)
	{
		$csv = '"email","customerId","source"' . PHP_EOL;

		$source = self::safeForCsv("magento order customer");
		foreach ($data as $_dat)
		{
			$csv .= sprintf(
				"%s,%s,%s",
				self::safeForCsv($_dat["email"]),
				self::safeForCsv($_dat["customerId"]),
				$source
			);
			$csv .= PHP_EOL;
		}

		if (is_array($segments) && count($segments) > 0)
		{
			$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($list, $segments, $csv, $storeId);
		} else
		{
			$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($list, null, $csv, $storeId);
		}

		$data = array();
	}

	public function _importDataS(&$data, $list, $segments = null, $storeId)
	{
		$csv = '"email","subscriber_status","source"' . PHP_EOL;

		foreach ($data as $_dat)
		{
			$csv .= sprintf(
				"%s,%s,%s",
				self::safeForCsv($_dat["email"]),
				self::safeForCsv($_dat["subscriber_status"]),
				self::safeForCsv($_dat["subscriber_type"])
			);
			$csv .= PHP_EOL;
		}

		if (is_array($segments) && count($segments) > 0)
		{
			$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($list, $segments, $csv, $storeId);
		} else
		{
			$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($list, null, $csv, $storeId);
		}

		$data = array();
	}

	public function setFeed($store){

		$listId = Mage::getStoreConfig("newsman_newsletter/settings/list_id", $store);

		$helper = Mage::helper('newsman_newsletter');
		$apiKey = $helper->getApiKey($store);

		$url = Mage::getBaseUrl() . "newsmanfetch.php?newsman=products.json&apikey=" . $apiKey;
		$url = str_replace("index.php/", "", $url);
		$domain = Mage::getBaseUrl();
		$domain = str_replace("index.php/", "", $domain);

		Mage::getModel('newsman_newsletter/api_import')->setFeed($listId, $url, $domain, $store);	

		return $this;
	}

	public function insertTasks()
	{	
		$max = 9999;	
		$batchSize = 9000;

		$customerEmail[] = "";
		$customerId[] = "";
		$customerGroupId[] = "";
		$customerFirstName[] = "''";

		$subscriberEmail[] = "";
		$subscriberStatus[] = "";

		$customerEmailOrderCompleted[] = "";
		$customerIdOrderCompleted[] = "";

		$stores = Mage::app()->getStores();

		try{
		foreach ($stores as $storeId => $store)
		{									
			$website_id = $store->website_id;

			//overwrite
			$batchSize = Mage::helper('newsman_newsletter')->getCustomerBatchSize($storeId);

			Mage::app()->getLocale()->emulate($storeId);
			Mage::app()->setCurrentStore($storeId);			

			if (!Mage::helper('newsman_newsletter')->isEnabled())
			{
				return $this;
			}

			$listId = Mage::getStoreConfig("newsman_newsletter/settings/list_id", $store);

			$importFilter = Mage::getStoreConfig(self::XML_PATH_NEWSMAN_IMPORTFILTER, $store);

			$mappedValues = Mage::helper('newsman_newsletter')->getSegments($storeId);

			//Get Subscribers filter 1
	
			$subscriberCollection = $this->getSubscriberCollection($storeId);

			$customers_to_import = array();

			foreach ($subscriberCollection as $sub)
			{
				$type = "newsletter subscriber ";

				if($sub["customer_id"] == "0")
				{
					$type .= "visitor";
				}
				else{
					$type .= "customer";
				}

				$customers_to_import[] = array(
					"email" => $sub["subscriber_email"],
					"subscriber_status" => $sub["subscriber_status"],
					"subscriber_type" => $type
				);

				if ((count($customers_to_import) % $batchSize) == 0)
				{
					$this->_importDataS($customers_to_import, $listId, array(), $storeId);
				}
			}

			if (count($customers_to_import) > 0)
			{
				$this->_importDataS($customers_to_import, $listId, array(), $storeId);
			}		

			unset($customers_to_import);

			$this->_insertTasks($subscriberCollection, '_insertSubscriberTask');			

			if (!$mappedValues)
			{							
				//Get customers filter 2				

				if ($importFilter == 2)
				{
					$customers_to_import = array();
				
					$collection = $this->getCustomerCollection($website_id);						

					foreach ($collection as $col)
					{
						$tempEmail = $col->getData();

						$firstname = $tempEmail["firstname"];
						$lastname = $tempEmail["lastname"];

						$tempEmail = $tempEmail["email"];

						$customerEmail[] = $tempEmail;

						$tempEntityId = $col->getData();
						$tempEntityId = $tempEntityId["entity_id"];

						$customerId[] = $tempEntityId;

						$tempGroupId = $col->getData();
						$tempGroupId = $tempGroupId["group_id"];

						$customers_to_import[] = array(
							"email" => $tempEmail,
							"customerId" => $tempEntityId,
							"groupId" => $tempGroupId,
							"firstname" => $firstname,
							"lastname" => $lastname
						);

						if ((count($customers_to_import) % $batchSize) == 0)
						{						
							$this->_importDataG($customers_to_import, $listId, array(), $storeId);
						}
					}

					if (count($customers_to_import) > 0)
					{						
						$this->_importDataG($customers_to_import, $listId, array(), $storeId);
					}				

					unset($customers_to_import);

					$this->_insertTasks($collection, '_insertSubscriberTask');
				}			
			}

			//Mapped values

			$tempCustomerId[] = "";
			$tempCustomerEmail[] = "";

			$hasNonLoggedInIdGroup = false;
			foreach ($mappedValues as $mappedValue)
			{
				$insertMethod = '_insertCustomerTask';
				if ($mappedValue['customer_group_id'] == Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
				{
					$hasNonLoggedInIdGroup = true;
					$insertMethod = '_insertSubscriberTask';
					$collection = $this->getSubscriberCollection($storeId);

					$this->_insertTasks($collection, $insertMethod, $mappedValue['customer_group_id'], $mappedValue['segment']);

				} elseif ($mappedValue['customer_group_id'] == Mage_Customer_Model_Group::CUST_GROUP_ALL)
				{
					$segment = array();
					$segment[] = $mappedValue["segment"];

					$customers_to_import = array();

					$subscriberCollection = $this->getSubscriberCollection($storeId);

					foreach ($subscriberCollection as $sub)
					{
						$type = "newsletter subscriber ";

						if($sub["customer_id"] == "0")
						{
							$type .= "visitor";
						}
						else{
							$type .= "customer";
						}

						$customers_to_import[] = array(
							"email" => $sub["subscriber_email"],
							"subscriber_status" => $sub["subscriber_status"],
							"subscriber_type" => $type
						);

						if ((count($customers_to_import) % $batchSize) == 0)
						{
							$this->_importDataS($customers_to_import, $listId, $segment, $storeId);
						}
					}

					if (count($customers_to_import) > 0)
					{
						$this->_importDataS($customers_to_import, $listId, $segment, $storeId);
					}

					unset($customers_to_import);

					if ($importFilter == 2)
					{
						$customers_to_import = array();

						$collection = $this->getCustomerCollection($website_id);

						$segment = array();
						$segment[] = $mappedValue["segment"];

						foreach ($collection as $col)
						{
							$tempEmail = $col->getData();

							$firstname = $tempEmail["firstname"];
							$lastname = $tempEmail["lastname"];

							$tempEmail = $tempEmail["email"];

							$customerEmail[] = $tempEmail;

							$tempEntityId = $col->getData();
							$tempEntityId = $tempEntityId["entity_id"];

							$customerId[] = $tempEntityId;

							$tempGroupId = $col->getData();
							$tempGroupId = $tempGroupId["group_id"];

							$customers_to_import[] = array(
								"email" => $tempEmail,
								"customerId" => $tempEntityId,
								"groupId" => $tempGroupId,
								"firstname" => $firstname,
								"lastname" => $lastname
							);

							if ((count($customers_to_import) % $batchSize) == 0)
							{
								$this->_importDataG($customers_to_import, $listId, $segment, $storeId);
							}
						}

						if (count($customers_to_import) > 0)
						{
							$this->_importDataG($customers_to_import, $listId, $segment, $storeId);
						}


						unset($customers_to_import);

						$this->_insertTasks($collection, '_insertSubscriberTask');
					}
				} else
				{
					$customers_to_import = array();

					$collection = $this->getCustomerCollection($website_id);
		
					$collection->addFieldToFilter('group_id', array('eq' => $mappedValue['customer_group_id']));

					$segment = array();
					$segment[] = $mappedValue["segment"];

					foreach ($collection as $col)
					{
						$tempEmail = $col->getData();

						$firstname = $tempEmail["firstname"];
						$lastname = $tempEmail["lastname"];

						$tempEmail = $tempEmail["email"];

						$customerEmail[] = $tempEmail;

						$tempEntityId = $col->getData();
						$tempEntityId = $tempEntityId["entity_id"];

						$customerId[] = $tempEntityId;

						$tempGroupId = $col->getData();
						$tempGroupId = $tempGroupId["group_id"];

						$customers_to_import[] = array(
							"email" => $tempEmail,
							"customerId" => $tempEntityId,
							"groupId" => $tempGroupId,
							"firstname" => $firstname,
							"lastname" => $lastname
						);

						if ((count($customers_to_import) % $batchSize) == 0)
						{
							$this->_importDataG($customers_to_import, $listId, $segment, $storeId);
						}
					}

					if (count($customers_to_import) > 0)
					{
						$this->_importDataG($customers_to_import, $listId, $segment, $storeId);
					}

					unset($customers_to_import);

					$this->_insertTasks($collection, '_insertSubscriberTask');

					
					$customers_to_import = array();

					$subscriberCollection = $this->getSubscriberCollection($storeId);
					
					foreach ($subscriberCollection as $sub)
					{
						$type = "newsletter subscriber ";

						if($sub["customer_id"] == "0")
						{
							$type .= "visitor";
						}
						else{
							$type .= "customer";
						}

						$customers_to_import[] = array(
							"email" => $sub["subscriber_email"],
							"subscriber_status" => $sub["subscriber_status"],
							"subscriber_type" => $type
						);

						if ((count($customers_to_import) % $batchSize) == 0)
						{
							$this->_importDataS($customers_to_import, $listId, $segment, $storeId);
						}
					}

					if (count($customers_to_import) > 0)
					{
						$this->_importDataS($customers_to_import, $listId, $segment, $storeId);
					}

					unset($customers_to_import);

					$this->_insertTasks($subscriberCollection, '_insertSubscriberTask');
				}
			}

			Mage::app()->getLocale()->revert();
		}
	}
		catch(Exception $e)
		{
			
		}

		return $this;
	}

	public function getSubscriberCollection($storeId)
	{
		//$storeId = Mage::app()->getStore()->getStoreId();

		$collection = Mage::getModel('newsletter/subscriber')->getCollection()
			->addFieldToFilter('store_id', $storeId)
			->addFieldToFilter('subscriber_status', 1);

		return $collection;
	}

	public function getCustomerCollection($storeId)
	{
		//$storeId = Mage::app()->getStore()->getStoreId();

		$collection = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToSelect('firstname')
			->addAttributeToSelect('lastname')
			->addFieldToFilter('website_id', $storeId);
			->addFieldToFilter('is_active', 1);

		return $collection;
	}

	protected function _insertTasks($collection, $insertMethod, $groupId = null, $segment = null)
	{
		$totalSubscribers = $collection->getSize();
		$batchSize = Mage::helper('newsman_newsletter')->getCustomerBatchSize();

		$pageLimit = ceil($totalSubscribers / $batchSize);

		for ($page = 1; $page <= $pageLimit; $page++)
		{
			$data = array(
				'customer_group_id' => $groupId,
				'segment' => $segment,
				'page' => $page,
				'store_id' => Mage::app()->getStore()->getStoreId()
			);

			$this->$insertMethod($data);
		}
	}

	/**
	 * Insert a new customer task in the queue
	 *
	 * @param $data
	 */
	protected function _insertCustomerTask($data)
	{
		$task = Mage::getModel('newsman_newsletter/task');
		$task->setEntity(Newsman_Newsletter_Model_Task::TASK_ENTITY_CUSTOMER)
			->setInfo(serialize($data))
			->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
			->save();
	}

	/**
	 * Insert a new subscriber task in the queue
	 *
	 * @param $data
	 */
	protected function _insertSubscriberTask($data)
	{
		$task = Mage::getModel('newsman_newsletter/task');
		$task->setEntity(Newsman_Newsletter_Model_Task::TASK_ENTITY_SUBSCRIBER)
			->setInfo(serialize($data))
			->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
			->save();
	}
}