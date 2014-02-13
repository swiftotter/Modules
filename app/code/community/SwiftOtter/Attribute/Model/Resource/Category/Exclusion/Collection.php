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
 
class SwiftOtter_Attribute_Model_Resource_Category_Exclusion_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('SwiftOtter_Attribute/Category_Exclusion');
    }

	/**
	 * Adds an attribute id filter
	 *
	 * @param $attributeId
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	public function filterByAttributeId($attributeId)
	{
		return $this->addFieldToFilter('attribute_id', array('eq' => $attributeId));
	}

	/**
	 * Adds a category id filter
	 *
	 * @param $categoryId
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	public function filterByCategoryId($categoryId)
	{
		return $this->addFieldToFilter('category_id', array('eq' => $categoryId));
	}

}