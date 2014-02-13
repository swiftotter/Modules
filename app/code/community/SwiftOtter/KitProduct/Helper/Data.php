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
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_KitProduct_Helper_Data extends SwiftOtter_Base_Helper_Data
{
    public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        return array_merge(
            $this->getKitOptions($item),
            Mage::helper('catalog/product_configuration')->getCustomOptions($item)
        );
    }

    public function getKitOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $options = array();

        /** @var Mage_Catalog_Model_Product $product */
        $product = $item->getProduct();
        /** @var SwiftOtter_KitProduct_Model_Product_Type_Kit $typeInstance */
        $typeInstance = $product->getTypeInstance(true);
        $serializedKitProducts = $item->getOptionByCode('kit_product_options')->getValue();

        if ($serializedKitProducts) {
            $kitProducts = unserialize($serializedKitProducts);
            $option = array(
                'label' => $this->__('Included Products'),
                'value' => array()
            );

            foreach ($kitProducts as $kitProduct) {
                $value = '';

                if ($kitProduct['qty'] > 1) {
                    $value .= sprintf('%u', $kitProduct['qty']) . ' x ';
                }

                $value .= $kitProduct['name'];
                $option['value'][] = $value;
            }

            if ($option['value']) {
                $options[] = $option;
            }
        }

        return array(); //$options;
    }

}