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

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'exclude_browse_attribute', array(
    'type'      => 'varchar',
    'input'     => 'multiselect',
    'source_model' => 'SwiftOtter_BrowseAttribute/Source_Attributes',
    'backend_model' => 'eav/entity_attribute_backend_array',
    'label'     => 'Exclude From Shop By Attribute List',
    'required'  => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->run("
    ALTER TABLE `{$installer->getTable('eav/attribute_option')}` ADD COLUMN exclude_browse_by TINYINT DEFAULT 0 NOT NULL;
");

$installer->endSetup();


