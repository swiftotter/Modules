<?php
/**
 *
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