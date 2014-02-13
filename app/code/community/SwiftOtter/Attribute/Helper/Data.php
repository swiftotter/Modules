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
 * @copyright Swift Otter Studios, 2/4/14
 * @package default
 **/


class SwiftOtter_Attribute_Helper_Data extends Mage_Core_Helper_Data
{
	protected $_exclusions;

	protected function _getExclusions($categoryId = null)
	{
		if (!$this->_exclusions) {
			$this->_exclusions = Mage::getResourceModel('SwiftOtter_Attribute/Category_Exclusion_Collection')
				->filterByCategoryId($categoryId);
		}

		return $this->_exclusions;
	}

	public function getAttributeExcluded($categoryId, $attributeId)
	{
		$exclusions = $this->_getExclusions($categoryId);

		/** @var SwiftOtter_Attribute_Model_Category_Exclusion $exclusion */
		foreach ($exclusions as $exclusion) {
			if ($exclusion->getAttributeId() == $attributeId) {
				return true;
			}
		}

		return false;
	}
}