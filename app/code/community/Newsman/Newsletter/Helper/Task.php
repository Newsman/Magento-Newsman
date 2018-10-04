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
		$csv = '"email","customerId", "groupId","source"' . PHP_EOL;

		$source = self::safeForCsv("magento newsman plugin");
		foreach ($data as $_dat)
		{
			$csv .= sprintf(
				"%s,%s,%s,%s",
				self::safeForCsv($_dat["email"]),
				self::safeForCsv($_dat["customerId"]),
				self::safeForCsv($_dat["groupId"]),
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

	public function _importDataC(&$data, $list, $segments = null, $storeId)
	{
		$csv = '"email","customerId","source"' . PHP_EOL;

		$source = self::safeForCsv("magento newsman plugin");
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

		$source = self::safeForCsv("magento newsman plugin");
		foreach ($data as $_dat)
		{
			$csv .= sprintf(
				"%s,%s,%s",
				self::safeForCsv($_dat["email"]),
				self::safeForCsv($_dat["subscriber_status"]),
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

	public function insertTasks()
	{
		$max = 9999;
		$batchSize = Mage::helper('newsman_newsletter')->getCustomerBatchSize();
		//Safe default
		$batchSize = 5000;

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

				$customers_to_import = array();

				foreach ($subscriberCollection as $sub)
				{
					$customers_to_import[] = array(
						"email" => $sub["subscriber_email"],
						"subscriber_status" => $sub["subscriber_status"]
					);

					if ((count($customers_to_import) % $batchSize) == 0)
					{
						$this->_importDataS($customers_to_import, $listId, null, $storeId);
					}
				}

				if (count($customers_to_import) > 0)
				{
					$this->_importDataS($customers_to_import, $listId, null, $storeId);
				}

				unset($customers_to_import);

				$this->_insertTasks($subscriberCollection, '_insertSubscriberTask');

				if ($importFilter == 2)
				{
					$customers_to_import = array();

					$ordercollection = Mage::getModel('sales/order')->getCollection()
						->addFieldToFilter('status', 'complete');
					foreach ($ordercollection as $_order)
					{
						$customerId[] = $_order->getCustomerId();
						$customerEmail[] = $_order->getCustomerEmail();

						$customers_to_import[] = array(
							"email" => $_order->getCustomerEmail(),
							"customerId" => $_order->getCustomerId()
						);

						if ((count($customers_to_import) % $batchSize) == 0)
						{
							$this->_importDataC($customers_to_import, $listId, null, $storeId);
						}
					}

					if (count($customers_to_import) > 0)
					{
						$this->_importDataC($customers_to_import, $listId, null, $storeId);
					}

					unset($customers_to_import);
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

					$customers_to_import = array();

					$subscriberCollection = $this->getSubscriberCollection();
					foreach ($subscriberCollection as $sub)
					{
						$customers_to_import[] = array(
							"email" => $sub["subscriber_email"],
							"subscriber_status" => $sub["subscriber_status"]
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

						$ordercollection = Mage::getModel('sales/order')->getCollection()
							->addFieldToFilter('status', 'complete');
						foreach ($ordercollection as $_order)
						{
							$customers_to_import[] = array(
								"email" => $_order->getCustomerEmail(),
								"customerId" => $_order->getCustomerId()
							);

							if ((count($customers_to_import) % $batchSize) == 0)
							{
								$this->_importDataC($customers_to_import, $listId, $segment, $storeId);
							}
						}

						if (count($customers_to_import) > 0)
						{
							$this->_importDataC($customers_to_import, $listId, $segment, $storeId);
						}

						unset($customers_to_import);
					}
				} else
				{
					$customers_to_import = array();

					$collection = $this->getCustomerCollection();
					$collection->addFieldToFilter('group_id', array('eq' => $mappedValue['customer_group_id']));

					$segment = array();
					$segment[] = $mappedValue["segment"];

					foreach ($collection as $col)
					{
						$tempEmail = $col->getData();
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
							"groupId" => $tempGroupId
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

					$subscriberCollection = $this->getSubscriberCollection();
					foreach ($subscriberCollection as $sub)
					{
						$customers_to_import[] = array(
							"email" => $sub["subscriber_email"],
							"subscriber_status" => $sub["subscriber_status"]
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