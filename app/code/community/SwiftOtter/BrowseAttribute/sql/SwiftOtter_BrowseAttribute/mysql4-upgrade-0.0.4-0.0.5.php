<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/21/14
 * @package default
 **/

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('SwiftOtter_BrowseAttribute/Index_Tmp')}` (
        `attribute_id` SMALLINT UNSIGNED NOT NULL,
        `attribute_value` VARCHAR(25) NOT NULL,
        `product_id` INT UNSIGNED NOT NULL,
        `position` INT UNSIGNED,
        `store_id` SMALLINT UNSIGNED,
        `visibility` INT
    );
");

$installer->endSetup();
