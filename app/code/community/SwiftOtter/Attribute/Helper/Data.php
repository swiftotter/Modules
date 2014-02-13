<?php
/**
 *
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