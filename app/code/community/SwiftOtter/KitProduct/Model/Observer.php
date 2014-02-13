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

class SwiftOtter_KitProduct_Model_Observer
{
    public function adminhtmlSalesOrderItemCollectionLoadAfter($observer)
    {
        $collection = $observer->getOrderItemCollection();

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        foreach ($collection as $orderItem) {
            if (!$orderItem->getParentItemId() && $orderItem->getProductType() == SwiftOtter_KitProduct_Model_Product_Type_Kit::KIT_TYPE_CODE) {
                //$orderItem->setIsVirtual(true);
            }

            if ($orderItem->getParentItemId() && $orderItem->getProductType() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                $orderItem->setParentItemId(null);
            }
        }
    }

    public function salesConvertQuoteItemToOrderItem($observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getItem();

        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getOrderItem();

        // If this is a parent kit item
        if (!$orderItem->getParentItemId() && $orderItem->getProductType() == SwiftOtter_KitProduct_Model_Product_Type_Kit::KIT_TYPE_CODE) {
            $orderItem->setIsVirtual(true);

            $options = $orderItem->getProductOptions();
            foreach ($options as $name => $option) {
                $unserialized = $option;
                if (!is_array($unserialized)) {
					try {
                    	$unserialized = unserialize($option);
					} catch (Exception $ex) {}
                }
                if (is_array($unserialized)) {
                    $options[$name] = $unserialized;
                }
            }

            $orderItem->setProductOptions($options);
        }

        // If this is a child kit item
        if ($orderItem->getQuoteParentItemId() && $orderItem->getProductType() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
            $options = $orderItem->getProductOptions();
            if (isset($options['super_product_config'])) {
                $parentProduct = Mage::getModel('catalog/product')->load($options['super_product_config']['product_id']);

                $association = array(
                    'label' => Mage::helper('SwiftOtter_KitProduct')->__('Parent Product'),
                    'value' => sprintf('%s (%s)', $parentProduct->getName(), $parentProduct->getSku())
                );

                if (!isset($options['options'])) {
                    $options['options'] = array();
                }
                $options['options'][] = $association;
            }

            $orderItem->setProductOptions($options);
        }
    }

}