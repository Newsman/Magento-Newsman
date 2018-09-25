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

	public function insertTasks()
	{
		$max = 9999;
		$batchSize = Mage::helper('newsman_newsletter')->getCustomerBatchSize();

		$customerEmail[] = "";
		$customerId[] = "";
		$customerGroupId[] = "";
		$customerFirstName[] = "''";

		$subscriberEmail[] = "";
		$subscriberStatus[] = "";

		$customerEmailOrderCompleted[] = "";
		$customerIdOrderCompleted[] = "";

		$stores = Mage::app()->getStores();
		foreach ($stores as $storeId => $store)
		{
			Mage::app()->getLocale()->emulate($storeId);
			Mage::app()->setCurrentStore($storeId);

			if (!Mage::helper('newsman_newsletter')->isEnabled())
			{
				return $this;
			}

			$listId = Mage::getStoreConfig("newsman_newsletter/settings/list_id", $storeId);

			$storeId = Mage::app()->getStore()->getStoreId();
			$importFilter = Mage::getStoreConfig(self::XML_PATH_NEWSMAN_IMPORTFILTER, $storeId);

			$mappedValues = Mage::helper('newsman_newsletter')->getSegments();
			if (!$mappedValues)
			{
				$subscriberCollection = $this->getSubscriberCollection();
				foreach ($subscriberCollection as $sub)
				{
					$subscriberEmail[] = $sub["subscriber_email"];
					$subscriberStatus[] = $sub["subscriber_status"];
				}

				$csvData = "email,subscriber_status,magento_source" . PHP_EOL;
				for ($int = 0; $int < count($subscriberEmail); ++$int)
				{
					$csvData .= $subscriberEmail[$int];
					$csvData .= ",";
					$csvData .= $subscriberStatus[$int];
					$csvData .= ",";
					$csvData .= "newsletterSubscribers";
					$csvData .= PHP_EOL;

					if ($int == $max)
					{
						$max += 9999;

						$csv = utf8_encode($csvData);

						$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, null, $csvData, $storeId);

						//start from scratch
						$csvData = "email,subscriber_status,magento_source" . PHP_EOL;
					}
				}

				$csv = utf8_encode($csvData);

				$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, null, $csvData, $storeId);

				$this->_insertTasks($subscriberCollection, '_insertSubscriberTask');

				if ($importFilter == 2)
				{
					$ordercollection = Mage::getModel('sales/order')->getCollection()
						->addFieldToFilter('status', 'complete');
					foreach ($ordercollection as $_order)
					{
						$customerId[] = $_order->getCustomerId();
						$customerEmail[] = $_order->getCustomerEmail();
					}

					$max = 9999;

					$csvData = "email, customerId, magento_source" . PHP_EOL;
					for ($int = 0; $int < count($customerEmail); ++$int)
					{
						$csvData .= $customerEmail[$int];
						$csvData .= ",";
						$csvData .= $customerId[$int];
						$csvData .= ",";
						$csvData .= "orderCustomer";
						$csvData .= PHP_EOL;

						if ($int == $max)
						{
							$max += 9999;

							$csv = utf8_encode($csvData);

							$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, null, $csv, $storeId);

							$csvData = "email, customerId, magento_source" . PHP_EOL;
						}

					}

					$csv = utf8_encode($csvData);

					$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, null, $csv, $storeId);
				}

				return $this;
			}

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
					$collection = $this->getSubscriberCollection();

					$this->_insertTasks($collection, $insertMethod, $mappedValue['customer_group_id'], $mappedValue['segment']);

				} elseif ($mappedValue['customer_group_id'] == Mage_Customer_Model_Group::CUST_GROUP_ALL)
				{
					$segment = array();
					$segment[] = $mappedValue["segment"];

					$subscriberCollection = $this->getSubscriberCollection();
					foreach ($subscriberCollection as $sub)
					{
						$subscriberEmail[] = $sub["subscriber_email"];
						$subscriberStatus[] = $sub["subscriber_status"];
					}

					$max = 9999;

					$csvData = "email,subscriber_status,magento_source" . PHP_EOL;
					for ($int = 1; $int < count($subscriberEmail); ++$int)
					{
						$csvData .= $subscriberEmail[$int];
						$csvData .= ",";
						$csvData .= $subscriberStatus[$int];
						$csvData .= ",";
						$csvData .= "newsletterSubscribers";
						$csvData .= PHP_EOL;

						if ($int == $max)
						{
							$max += 9999;

							$csv = utf8_encode($csvData);

							$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csvData, $storeId);

							$csvData = "email,subscriber_status,magento_source" . PHP_EOL;
						}
					}

					$csv = utf8_encode($csvData);

					$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csvData, $storeId);

					if ($importFilter == 2)
					{
						$ordercollection = Mage::getModel('sales/order')->getCollection()
							->addFieldToFilter('status', 'complete');
						foreach ($ordercollection as $_order)
						{
							$customerId[] = $_order->getCustomerId();
							$customerEmail[] = $_order->getCustomerEmail();
						}

						$max = 9999;

						$csvData = "email, customerId, magento_source" . PHP_EOL;
						for ($int = 1; $int < count($customerEmail); ++$int)
						{
							$csvData .= $customerEmail[$int];
							$csvData .= ",";
							$csvData .= $customerId[$int];
							$csvData .= ",";
							$csvData .= "orderCustomer";
							$csvData .= PHP_EOL;

							if ($int == $max)
							{
								$max += 9999;

								$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csvData, $storeId);

								$csvData = "email, customerId, magento_source" . PHP_EOL;
							}
						}

						$csv = utf8_encode($csvData);

						$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csv, $storeId);
					}
				} else
				{
					$collection = $this->getCustomerCollection();
					$collection->addFieldToFilter('group_id', array('eq' => $mappedValue['customer_group_id']));

					foreach ($collection as $col)
					{
						$tempEmail = $col->getData();
						$tempEmail = $tempEmail["email"];

						$customerEmail[] = $tempEmail;

						$tempEntityId = $col->getData();
						$tempEntityId = $tempEntityId["entity_id"];

						$customerId[] = $tempEntityId;

						$tempCustomerId = $col->getData();
						$tempCustomerId = $tempCustomerId["group_id"];
					}

					$ordercollection = Mage::getModel('sales/order')->getCollection()
						->addFieldToFilter('status', 'complete');
					foreach ($ordercollection as $_order)
					{
						$customerIdOrderCompleted[] = $_order->getCustomerId();
						$customerEmailOrderCompleted[] = $_order->getCustomerEmail();
					}

					for ($int = 0; $int < count($customerEmailOrderCompleted); $int++)
					{
						if (in_array($customerEmailOrderCompleted[$int], $customerEmail))
						{
							$tempCustomerId[] = $customerIdOrderCompleted[$int];
							$tempCustomerEmail[] = $customerEmailOrderCompleted[$int];
						}
					}

					for ($int = 0; $int < count($tempCustomerEmail); $int++)
					{
						if (empty($tempCustomerEmail[$int]))
						{
							unset($tempCustomerEmail[$int]);
							unset($tempCustomerId[$int]);
						}
					}

					$segment = array();
					$segment[] = $mappedValue["segment"];

					$max = 9999;

					$csvData = "email, customerId, magento_source" . PHP_EOL;
					for ($int = 2; $int < count($tempCustomerEmail) + 2; $int++)
					{
						if ($tempCustomerEmail[$int] != null)
						{
							$csvData .= $tempCustomerEmail[$int];
							$csvData .= ",";
							$csvData .= $tempCustomerId[$int];
							$csvData .= ",";
							$csvData .= "orderCustomer";
							$csvData .= PHP_EOL;

							if ($int == $max)
							{
								$max += 9999;

								$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csvData, $storeId);

								$csvData = "email, customerId, magento_source" . PHP_EOL;
							}
						}
					}

					$csv = utf8_encode($csvData);

					$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csv, $storeId);

					$subscriberCollection = $this->getSubscriberCollection();
					foreach ($subscriberCollection as $sub)
					{
						$subscriberEmail[] = $sub["subscriber_email"];
						$subscriberStatus[] = $sub["subscriber_status"];
					}

					$max = 9999;

					$csvData = "email,subscriber_status,magento_source" . PHP_EOL;
					for ($int = 1; $int < count($subscriberEmail); ++$int)
					{
						$csvData .= $subscriberEmail[$int];
						$csvData .= ",";
						$csvData .= $subscriberStatus[$int];
						$csvData .= ",";
						$csvData .= "newsletterSubscriber";
						$csvData .= PHP_EOL;

						if ($int == $max)
						{
							$max += 9999;

							$csv = utf8_encode($csvData);

							$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csv, $storeId);

							$csvData = "email,subscriber_status,magento_source" . PHP_EOL;
						}
					}
					$csv = utf8_encode($csvData);

					$importResult = Mage::getModel('newsman_newsletter/api_import')->csv($listId, $segment, $csv, $storeId);

					$this->_insertTasks($subscriberCollection, '_insertSubscriberTask');
				}
			}

			Mage::app()->getLocale()->revert();
		}

		return $this;
	}

	public function getSubscriberCollection()
	{
		$storeId = Mage::app()->getStore()->getStoreId();

		$collection = Mage::getModel('newsletter/subscriber')->getCollection()
			->addStoreFilter($storeId)
			->addFieldToFilter('subscriber_status', 1);

		return $collection;
	}

	public function getCustomerCollection()
	{
		$storeId = Mage::app()->getStore()->getStoreId();

		$collection = Mage::getModel('customer/customer')->getCollection()
			->addFieldToFilter('store_id', $storeId);

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