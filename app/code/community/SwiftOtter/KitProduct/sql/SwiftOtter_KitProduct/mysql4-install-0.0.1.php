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

$updateAttributes = array('price', 'tier_price', 'special_price', 'special_from_date', 'special_to_date');

foreach($updateAttributes as $attributeName) {
    $attribute = $installer->getAttribute('catalog_product', $attributeName);
    $applyTo = $attribute['apply_to'];
    $typeCode = SwiftOtter_KitProduct_Model_Product_Type_Kit::KIT_TYPE_CODE;

    if (strstr($applyTo, $typeCode) === false) {
        $value = $applyTo . ',' . $typeCode;

        $installer->updateAttribute('catalog_product', $attributeName, 'apply_to', $value);
    }
}



$installer->endSetup();