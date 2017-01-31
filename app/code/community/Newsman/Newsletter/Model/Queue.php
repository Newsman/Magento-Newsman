<?php

/**
 * Newsman Queue Model
 *
 * @category    Newsman
 * @package     Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Queue
{
    const MAX_FAILED_TASKS = 5;
    protected $_failedTasks = array();

    public function process()
    {
        if (Mage::helper('newsman_newsletter/task')->clearFailedTasks()) {
            $this->clearFailedTasks();
        }

        $processedTasks = 0;
        $taskCollection = $this->getTasksToProcess();
        if ($taskCollection->getSize() == 0) {
            return $processedTasks;
        }

        $syncStatus = false;
        foreach ($taskCollection as $task) {
            if (Newsman_Newsletter_Helper_Debug::isEnabled()) {
                Mage::log('[Newsman] Task #' . $task->getId());
                echo "[Newsman] Task #{$task->getId()}" . PHP_EOL;
            }
            $data = unserialize($task->getInfo());
            if ($task->getEntity() == 'customer') {
                if (Newsman_Newsletter_Helper_Debug::isEnabled()) {
                    Mage::log('[Newsman] - Syncing Customers');
                    echo '[Newsman] - Syncing Customers' . PHP_EOL;
                }
                $syncStatus = Mage::getModel('newsman_newsletter/synchronization')->customersSync($data['page'], $data['customer_group_id'], $data['segment'], $data['store_id']);
            }

            if ($task->getEntity() == 'subscriber') {
                if (Newsman_Newsletter_Helper_Debug::isEnabled()) {
                    Mage::log('[Newsman] - Syncing Subscribers');
                    echo '[Newsman] - Syncing Subscribers';
                }
                $syncStatus = Mage::getModel('newsman_newsletter/synchronization')->subscribersSync($data['page'], $data['segment'], $data['store_id']);
            }
            if ($syncStatus) {
                $task->setProcessed();
                if ($this->_getHelper('newsman_newsletter/task')->deleteProcessedTasks()) {
                    $task->delete();
                }
                $processedTasks++;
            } else {
                $task->setFailed($this->_getHelper('newsman_newsletter')->__('Unknown error'));
            }
        }

        if (Newsman_Newsletter_Helper_Debug::isEnabled()) {
            Mage::log("[Newsman] - {$processedTasks} tasks were processed.");
            echo "[Newsman] - {$processedTasks} tasks were processed." . PHP_EOL;
        }

        if (Mage::helper('newsman_newsletter')->isSubscribersSynchronizationEnabled() && $this->getTotalFailedTasks()) {
            $this->_sendErrorEmail();
        }

        return $processedTasks;
    }

    /**
     * Get task from the queue to process during this execution
     *
     * @return array
     */
    public function getTasksToProcess()
    {
        $collection = Mage::getModel('newsman_newsletter/task')->getCollection()
            ->addFieldToFilter('processed_at', array('null' => true))
            ->addFieldToFilter('failed_at', array('null' => true))
            ->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_ASC)
            ->setPageSize($this->_getHelper('newsman_newsletter')->getQueueBatchSize());

        return $collection;
    }

    /**
     * Clear failed tasks
     *
     * @return int
     */
    public function clearFailedTasks()
    {
        $cleared = 0;
        $failedTaskCollection = Mage::getModel('newsman_newsletter/task')->getCollection()
            ->addFieldToFilter('failed_at', array('notnull' => true))
            ->addFieldToFilter('message', array('notnull' => true))
            ->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_ASC)
            ->setPageSize(self::MAX_FAILED_TASKS);

        foreach ($failedTaskCollection as $failedTask) {
            if ($failedTask->clearFailedTask()) {
                $cleared++;
            }
        }

        if (Newsman_Newsletter_Helper_Debug::isEnabled()) {
            Mage::log("[Newsman] - {$cleared} failed tasks were cleared.");
            echo "[Newsman] - {$cleared} failed tasks were cleared." . PHP_EOL;
        }
        return $cleared;
    }

    public function getTotalFailedTasks()
    {
        if (!$this->_failedTasks) {
            $this->_failedTasks = Mage::getModel('newsman_newsletter/task')->getCollection()
                ->addFieldToFilter('failed_at', array('notnull' => true))
                ->addFieldToFilter('message', array('notnull' => true))
                ->getSize();
        }

        return $this->_failedTasks;
    }

    /**
     * Returns helper instance
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Send email to administrator if error
     *
     * @return Newsman_Newsletter_Model_Queue
     */
    protected function _sendErrorEmail()
    {
        /** @var Newsman_Newsletter_Helper_Data $helper */
        $helper = Mage::helper('newsman_newsletter');
        if (!$helper->getSynchronizationErrorEmailTemplate()) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $emailTemplate = Mage::getModel('core/email_template');
        /* @var $emailTemplate Mage_Core_Model_Email_Template */
        $emailTemplate->setDesignConfig(array('area' => 'backend'))
            ->sendTransactional(
                $helper->getSynchronizationErrorEmailTemplate(),
                $helper->getSynchronizationErrorEmailIdentity(),
                $helper->getSynchronizationErrorEmail(),
                null,
                array('warnings' => Mage::helper('newsman_newsletter')->__("There are {$this->_failedTasks} failed task(s)."))
            );

        $translate->setTranslateInline(true);
        $this->_failedTasks = array();

        return $this;
    }
}