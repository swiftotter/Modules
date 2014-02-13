<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/31/14
 * @package default
 **/

class SwiftOtter_Attribute_Model_Source_Category
{
    protected $_options;

	protected $_exclusions;
	protected $_attributeId;

    const DEFAULT_INITIAL_CATEGORY_LEVEL = 1;

	/**
	 * @param int $attributeId
	 * @return $this
	 */
	public function setAttributeId($attributeId)
	{
		$this->_attributeId = $attributeId;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAttributeId()
	{
		return $this->_attributeId;
	}

	public function getExclusions()
	{
		if (!$this->_exclusions) {
			$this->_exclusions = Mage::getResourceModel('SwiftOtter_Attribute/Category_Exclusion_Collection')
				->filterByAttributeId($this->getAttributeId());
		}

		return $this->_exclusions;
	}

	/**
	 * Determines whether the category is excluded
	 *
	 * @param $id
	 * @return bool
	 */
	protected function _categoryExcluded($id)
	{
		/** @var SwiftOtter_Attribute_Model_Category_Exclusion $exclusion */
		foreach ($this->getExclusions() as $exclusion)
		{
			if ($exclusion->getCategoryId() == $id) {
				return true;
			}
		}

		return false;
	}


    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array();

            $categories = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('name')
                ->addFieldToFilter('level', array('eq' => self::DEFAULT_INITIAL_CATEGORY_LEVEL));

            foreach ($categories as $category) {
                $this->_options[] = $this->getCategories($category);
            }
        }
        return $this->_options;
    }

    /**
     * @param Mage_Catalog_Model_Category $parentCategory
     * @param string $prefix
	 * @return array
     */
    public function getCategories($parentCategory, $prefix = '')
    {
        if (!$parentCategory) {
            $parentCategory = Mage::getModel('catalog/category')->load($parentCategory);
        }

		// Setting up the main configuration for jsTree plugin
        $option = array(
            'text' => $parentCategory->getName(),
            'id' => $parentCategory->getId(),
			'state' => array(
				'opened' => true
			)
		);

		$selected = $this->_categoryExcluded($parentCategory->getId());

		if ($selected) {
			$option['state']['selected'] = true;
		}

        if ($parentCategory->hasChildren()) {
            $categories = Mage::getResourceModel('catalog/category_collection')
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('parent_id', array('eq' => $parentCategory->getId()));

            foreach ($categories as $category) {
				// Recursively iterating through child categories
                $option['children'][] = $this->getCategories($category, $prefix);
            }
        }

		return $option;
    }
}