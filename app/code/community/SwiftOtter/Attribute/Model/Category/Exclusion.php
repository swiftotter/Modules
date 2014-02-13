<?php
/**
 *
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