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
 * @copyright Swift Otter Studios, 10/24/2013
 * @package default
 **/

class SwiftOtter_Base_Helper_Db extends Mage_Core_Helper_Abstract
{
    protected $_cachedAttributes = array();

    /**
     * @param array|string $attributes
     * @param Varien_Db_Select $select
     * @param string $mainCorrelation
     * @param string $mainIdColumn
     * @return Varien_Db_Select
     */
    public function includeProductAttribute($attributes, $select, $mainCorrelation = 'main_table', $mainIdColumn = 'entity_id')
    {
        $attributes = $this->_loadAttributes($attributes);

        foreach ($attributes as $attributeCode) {
            $attribute = $this->_getCachedAttribute($attributeCode);
            if ($attribute->getId()) {
                $joinTable = sprintf('product_%s_table', $attributeCode);

                $select->joinLeft(
                    array($joinTable => $attribute->getBackendTable()),
                    sprintf('`%s`.%s = `%s`.entity_id AND `%s`.attribute_id = %s', $mainCorrelation, $mainIdColumn, $joinTable, $joinTable, $attribute->getAttributeId()),
                    array($attributeCode => new Zend_Db_Expr(sprintf('`%s`.value', $joinTable)))
                );
            }
        }

        return $select;
    }

    protected function _loadAttributes($attributes)
    {
        if (!is_array($attributes)) {
            $attributes = array($attributes);
        }

        $returnAttributes = array();
        $loadAttributes = array();
        foreach ($attributes as $attributeCode) {
            if ($this->_isCached($attributeCode)) {
                $returnAttributes[$attributeCode] = $this->_getCachedAttribute($attributeCode);
            } else {
                $loadAttributes[] = $attributeCode;
            }
        }

        if (count($loadAttributes) > 0) {
            $attributeCollection = Mage::getResourceModel('eav/entity_attribute_collection');
            $attributeCollection->addFieldToFilter('attribute_code', array('in', $attributes));

            foreach ($attributeCollection as $attribute) {
                $returnAttributes[$attribute->getAttributeCode()] = $attribute;
                $this->_cacheAttribute($attribute);
            }
        }

        return $returnAttributes;
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return $this
     */
    protected function _cacheAttribute($attribute)
    {
        $this->_cachedAttributes[$attribute->getAttributeCode()] = $attribute;

        return $this;
    }

    protected function _getCachedAttribute($attributeCode)
    {
        if (isset($this->_cachedAttributes[$attributeCode])) {
            return $this->_cachedAttributes[$attributeCode];
        } else {
            return false;
        }
    }

    /**
     * @param $attribute
     * @return bool
     */
    protected function _isCached($attribute)
    {
        if (is_string($attribute)) {
            return isset($this->_cachedAttributes[$attribute]);
        } else if (is_object($attribute)) {
            return isset($this->_cachedAttributes[$attribute->getAttributeCode()]);
        }
    }
}