<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 2/19/13
 * @package default
 **/

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
	ALTER TABLE `{$installer->getTable('catalog/eav_attribute')}` ADD COLUMN `explanatory_note` VARCHAR(255);
");

$installer->endSetup();