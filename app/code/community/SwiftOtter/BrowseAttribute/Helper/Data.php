<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/13/14
 * @package default
 **/


class SwiftOtter_BrowseAttribute_Helper_Data extends SwiftOtter_Base_Helper_Data
{
    public function getOptionsForAttribute($attribute)
    {
        return Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setPositionOrder('asc')
            ->setAttributeFilter($attribute->getId())
            ->setStoreFilter($attribute->getStoreId());
    }
}