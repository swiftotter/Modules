<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

$installer = $this;

$installer->startSetup();
$installer->run("
    ALTER TABLE `{$this->getTable('SwiftOtter_Report/ProductSaleRegion')}`
      ADD COLUMN `order_id` INT UNSIGNED NOT NULL,
      ADD KEY `ORDER_REFERENCE_ID` (`order_id`),
      ADD CONSTRAINT `FK_ORDER_ID` FOREIGN KEY (`order_id`)
            REFERENCES `{$this->getTable('sales/order')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE;
");

$installer->endSetup();