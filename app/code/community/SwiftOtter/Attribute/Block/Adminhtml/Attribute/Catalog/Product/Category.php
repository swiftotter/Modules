<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright: 2013 (c) SwiftOtter Studios
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