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
 * @copyright Swift Otter Studios, 11/12/2013
 * @package default
 **/

class SwiftOtter_Attribute_Block_Override_Product_View_Attributes extends Mage_Catalog_Block_Product_View_Attributes
{
    protected $_availableAttributes = array();

    /**
     * Whether the tab has attributes to display
     *
     * @return bool
     */
    public function hasAttributesForDisplay()
    {
        return count($this->_getAvailableAttributes($this->_getTab())) > 0;
    }

    /**
     * Whether to hide the tab (currently only based on whether there are attributes in the tab)
     *
     * @return bool
     */
    public function hideDisplay()
    {
        return !$this->hasAttributesForDisplay();
    }

    /**
     * Retrieves the attribute information for the tab specified (otherwise defaulting to the additional tab)
     *
     * @param string $tab
     * @param array $excludeAttr
     * @return array
     */
    public function getAdditionalDataByTab($tab = '', array $excludeAttr = array())
    {
        $product = $this->getProduct();
        $data = array();
        if (!$tab) {
            $tab = $this->_getTab();
        }

        /** @var Mage_Eav_Model_Attribute $attribute */
        foreach ($this->_getAvailableAttributes($tab) as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);
                if (!$product->getData($attribute->getAttributeCode())) {
                    $value = '';
                }

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = null;
                } elseif ((string)$value == '') {
                    $value = null;
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = Mage::app()->getStore()->convertPrice($value, true);
                }

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = array(
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code'  => $attribute->getAttributeCode()
                    );
                }
            }
        }

        return $data;
    }

    protected function _getAvailableAttributes($tab = '')
    {
        if (!$tab) {
            $tab = $this->_getTab();
        }

        if (!$this->_availableAttributes) {
            $product = $this->getProduct();
            $attributes = $product->getAttributes();

            /** @var Mage_Catalog_Model_Resource_Attribute $attribute */
            foreach ($attributes as $attribute) {
                if ($attribute->getIsVisibleOnFront() && ($attribute->getTab() == $tab || (!$attribute->getTab() && $tab == 'additional'))) {
                    $this->_availableAttributes[] = $attribute;
                }
            }
        }

        return $this->_availableAttributes;
    }



    protected function _getTab()
    {
        $tab = $this->getBlockAlias();
//        if ($tab == 'additional') {
//            $tab = 'general';
//        }

        return $tab;
    }
}