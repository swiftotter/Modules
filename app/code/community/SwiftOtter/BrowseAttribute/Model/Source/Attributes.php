<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/8/14
 * @package default
 **/

class SwiftOtter_BrowseAttribute_Model_Source_Attributes extends SwiftOtter_Base_Model_Source_Abstract
{
    /**
     * Returns a list of browsable attributes
     *
     * @return array
     */
    public function getBrowsableAttributes()
    {
        $attributes = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection()
            ->addFieldToFilter('allow_browse_by', 1);

        $output = array();

        foreach ($attributes as $attribute) {
            $output[] = $attribute->getAttributeCode();
        }

        return $output;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $attributes = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection()
            ->addFieldToFilter('allow_browse_by', 1);
        $output = array();

        /** @var Mage_Eav_Model_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $output[] = array(
                'label' => $attribute->getFrontend()->getLabel(),
                'value' => $attribute->getAttributeCode()
            );
        }

        return $output;
    }
}