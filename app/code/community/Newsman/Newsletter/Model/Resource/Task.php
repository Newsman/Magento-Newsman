<?php

/**
 * Task resource model
 *
 * @category  Newsman
 * @package   Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Resource_Task extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('newsman_newsletter/task', 'task_id');
    }
}