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

/**
 * Class SwiftOtter_Attribute_Model_Category_Exclusion
 * @method int getCategoryId()
 * @method int getAttributeId()
 */
class SwiftOtter_Attribute_Model_Category_Exclusion extends Mage_Core_Model_Abstract
{

	protected $_attribute;
	protected $_category;

    protected function _construct()
    {
        $this->_init('SwiftOtter_Attribute/Category_Exclusion');
    }

	/**
	 * @param mixed $attribute
	 * @return $this
	 */
	public function setAttribute($attribute)
	{
		$this->_attribute = $attribute;
		return $this;
	}

	/**
	 * @return Mage_Eav_Model_Entity_Attribute
	 */
	public function getAttribute()
	{
		if (!$this->_attribute) {
			$this->_attribute = Mage::getModel('eav/entity_attribute')->load($this->getAttributeId());
		}

		return $this->_attribute;
	}

	/**
	 * @param mixed $category
	 * @return $this
	 */
	public function setCategory($category)
	{
		$this->_category = $category;
		return $this;
	}

	/**
	 * @return Mage_Catalog_Model_Category
	 */
	public function getCategory()
	{
		if (!$this->_category)
		{
			$this->_category = Mage::getModel('catalog/category')
				->load($this->getCategoryId());
		}

		return $this->_category;
	}


}