<?php
/**
 * Create task table
 *
 * @category  Newsman
 * @package   Newsman_Newsletter
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('newsman_task')}`;");
$installer->run(
    "CREATE TABLE IF NOT EXISTS `{$installer->getTable('newsman_task')}` (
        `task_id` int(11) unsigned NOT NULL auto_increment,
        `entity` varchar(30) default NULL,
        `info` varchar(100) default NULL,
        `created_at` datetime default NULL,
        `processed_at` datetime default NULL,
        `failed_at` datetime default NULL,
        `message` varchar(255) default NULL,
        PRIMARY KEY (`task_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsman Task';"
);
$installer->endSetup();
