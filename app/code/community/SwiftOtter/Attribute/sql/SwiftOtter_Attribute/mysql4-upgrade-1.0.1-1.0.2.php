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
    CREATE TABLE `{$installer->getTable('SwiftOtter_Attribute/Category_Exclusion')}` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `category_id` INT UNSIGNED NOT NULL,
        `attribute_id` SMALLINT(5) UNSIGNED NOT NULL,

        KEY `EXCLUSION_CATEGORY_ATTRIBUTE_CATEGORY_ID` (`category_id`),
			CONSTRAINT `FK_EXCLUSION_CATEGORY_ATTRIBUTE_CATEGORY_ID` FOREIGN KEY (`category_id`)
				REFERENCES `{$this->getTable('catalog/category')}` (`entity_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE,
		KEY `EXCLUSION_CATEGORY_ATTRIBUTE_ATTRIBUTE_ID` (`attribute_id`),
			CONSTRAINT `FK_EXCLUSION_CATEGORY_ATTRIBUTE_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`)
				REFERENCES `{$this->getTable('eav/attribute')}` (`attribute_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE
    );
");

$installer->endSetup();