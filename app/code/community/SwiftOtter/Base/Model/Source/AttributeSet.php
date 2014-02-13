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
 * @copyright Swift Otter Studios, 2/26/13
 * @package default
 **/


class SwiftOtter_Base_Model_Source_AttributeSet extends SwiftOtter_Base_Model_Source_Abstract
{
	public function getAllOptions()
	{
		$output = array();

		/** @var $sets Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection */
		$sets = Mage::getModel('eav/entity_attribute_set')->getCollection();

        $output[] = array('value' => '0', 'label' => 'All Attribute Sets');

		/** @var $block Mage_Eav_Model_Entity_Attribute_Set */
		foreach ($sets as $set) {
			$output[] = array('value' => $set->getAttributeSetId(), 'label' => $set->getAttributeSetName());
		}

		return $output;
	}
}