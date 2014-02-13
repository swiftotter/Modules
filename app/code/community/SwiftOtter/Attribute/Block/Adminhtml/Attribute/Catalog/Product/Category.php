<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/31/14
 * @package default
 **/

class SwiftOtter_Attribute_Block_Adminhtml_Attribute_Catalog_Product_Category extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return $this->__('Category Exclusion');
    }

    public function getTabTitle()
    {
        return $this->__('Category Exclusion');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getCategoryJson()
    {
		$attribute = Mage::registry('entity_attribute');

		if ($attribute) {
			$categories = Mage::getModel('SwiftOtter_Attribute/Source_Category')
				->setAttributeId($attribute->getAttributeId())
				->getAllOptions();

			return json_encode($categories);
		}
    }
}