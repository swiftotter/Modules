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
    CREATE TABLE `{$installer->getTable('SwiftOtter_BrowseAttribute/Index')}` (
        `attribute_id` SMALLINT UNSIGNED NOT NULL,
        `attribute_value` INT UNSIGNED NOT NULL,
        `product_id` INT UNSIGNED NOT NULL,
        `position` INT UNSIGNED,
        `store_id` SMALLINT UNSIGNED,
        `visibility` INT,
        KEY `BROWSEATTRIBUTE_ATTRIBUTE_ID` (`attribute_id`),
		CONSTRAINT `FK_BROWSEATTRIBUTE_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`)
			REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
        KEY `BROWSEATTRIBUTE_ATTRIBUTE_OPTION_VALUE_ID` (`attribute_value`),
		CONSTRAINT `FK_BROWSEATTRIBUTE_ATTRIBUTE_OPTION_VALUE_ID` FOREIGN KEY (`attribute_value`)
			REFERENCES `{$this->getTable('eav/attribute_option')}` (`option_id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
        KEY `BROWSEATTRIBUTE_PRODUCT_ID` (`product_id`),
		CONSTRAINT `FK_BROWSEATTRIBUTE_PRODUCT_ID` FOREIGN KEY (`product_id`)
			REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE,
        KEY `BROWSEATTRIBUTE_STORE_ID` (`store_id`),
		CONSTRAINT `FK_BROWSEATTRIBUTE_STORE_ID` FOREIGN KEY (`store_id`)
			REFERENCES `{$this->getTable('core/store')}` (`store_id`)
			ON DELETE CASCADE
			ON UPDATE CASCADE
    );
");

$installer->endSetup();


