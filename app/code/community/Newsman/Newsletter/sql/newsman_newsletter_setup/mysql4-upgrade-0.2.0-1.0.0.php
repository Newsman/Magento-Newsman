<?php
/**
 * Update task table
 *
 * @category  Newsman
 * @package   Newsman_Newsletter
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();


$installer->run("
    ALTER TABLE `{$installer->getTable('newsman_task')}`
        CHANGE `info` `info` VARCHAR(500) DEFAULT NULL;
");
$installer->endSetup();
