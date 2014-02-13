<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 2/18/13
 * @package default
 **/

/** @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'hide_included_products',
    array(
        'type' => 'int',
        'grid' => true,
        'required' => false,
        'source' => 'eav/entity_attribute_source_boolean',
        'input' => 'select',
        'group' => 'General',
        'label' => 'Hide Included Products',
        'apply_to'  => 'kit'
    ));


$installer->endSetup();