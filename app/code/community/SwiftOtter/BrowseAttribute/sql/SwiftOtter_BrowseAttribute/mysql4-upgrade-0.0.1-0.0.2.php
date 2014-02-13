<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('eav/attribute')}` ADD COLUMN allow_browse_by TINYINT DEFAULT 0 NOT NULL;
");

$installer->endSetup();


