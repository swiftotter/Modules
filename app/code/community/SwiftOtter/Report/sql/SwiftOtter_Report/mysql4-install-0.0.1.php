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
    CREATE TABLE `{$this->getTable('SwiftOtter_Report/ProductSaleRegion')}`
    (
      `id` INT UNSIGNED AUTO_INCREMENT,
      `store_id` SMALLINT(5) UNSIGNED NOT NULL,
      `sale_date` DATE,
      `product_id` INT UNSIGNED NOT NULL,
      `country_id` VARCHAR(2) NOT NULL DEFAULT '',
      `region_id` INT UNSIGNED,
      `quantity` FLOAT UNSIGNED NOT NULL,
      `type` VARCHAR(10) NOT NULL,
      `total` FLOAT UNSIGNED NOT NULL,
      PRIMARY KEY (`id`),
      INDEX `column_registry` (`store_id`, `sale_date`, `product_id`, `region_id`),
      KEY `CATALOG_PRODUCT_ID` (`product_id`),
        CONSTRAINT `FK_CATALOG_PRODUCT_ID` FOREIGN KEY (`product_id`)
            REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
      KEY `DIRECTORY_REGION_ID` (`region_id`),
        CONSTRAINT `FK_DIRECTORY_REGION_ID` FOREIGN KEY (`region_id`)
            REFERENCES `{$this->getTable('directory/country_region')}` (`region_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
      KEY `CORE_STORE_ID` (`store_id`),
        CONSTRAINT `FK_CORE_STORE_ID` FOREIGN KEY (`store_id`)
            REFERENCES `{$this->getTable('core/store')}` (`store_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    );
");

$installer->endSetup();