<?php

/**
 * Task model
 *
 * @category  Newsman
 * @package   Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Task extends Mage_Core_Model_Abstract
{
    const TASK_ENTITY_CUSTOMER = 'customer';
    const TASK_ENTITY_SUBSCRIBER = 'subscriber';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('newsman_newsletter/task');
    }

    /**
     * Set the task as processed
     *
     * @return $this Newsman_Newsletter_Model_Task
     */
    public function setProcessed()
    {
        $data = array(
            'processed_at' => Mage::getSingleton('core/date')->gmtDate(),
            'failed_at' => null,
            'message' => null
        );
        $this->addData($data)
            ->save();

        return $this;
    }

    /**
     * Set the task as failed
     *
     * @param string $message Error message
     * @return $this Newsman_Newsletter_Model_Task
     */
    public function setFailed($message = '')
    {
        if (!$this->getFailedAt()) {
            $data = array(
                'failed_at' => Mage::getSingleton('core/date')->gmtDate(),
                'message' => $message
            );
            $this->addData($data)
                ->save();
        }
        return $this;
    }

    /**
     * Clear failed task
     *
     * @return $this Newsman_Newsletter_Model_Task
     */
    public function clearFailedTask()
    {
        $data = array(
            'failed_at' => null,
            'message' => null

        );
        $this->addData($data)
            ->save();

        return $this;
    }
}