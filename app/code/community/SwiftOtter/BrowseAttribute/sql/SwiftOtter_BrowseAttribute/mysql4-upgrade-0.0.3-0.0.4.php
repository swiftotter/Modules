<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/14/14
 * @package default
 **/

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('eav/attribute_option')}` ADD COLUMN image_path VARCHAR(100);
");

$installer->endSetup();