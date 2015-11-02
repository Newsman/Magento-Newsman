<?php

/**
 * Newsman Task collection
 *
 * @category  Newsman
 * @package   Newsman_Newsletter
 */
class Newsman_Newsletter_Model_Resource_Task_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('newsman_newsletter/task');
    }
}