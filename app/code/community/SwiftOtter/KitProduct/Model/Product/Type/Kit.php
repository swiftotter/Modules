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

class SwiftOtter_KitProduct_Model_Product_Type_Kit extends Mage_Catalog_Model_Product_Type_Grouped
{
    const KIT_TYPE_CODE = 'kit';
    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);
		$requestGroup = $buyRequest->getSuperGroup();

        if (!$requestGroup) {
            $requestGroup = $this->_getAssociatedProducts($product, array());
            $buyRequest->setSuperGroup($requestGroup);
        }

        $products = parent::_prepareProduct($buyRequest, $product, $processMode);
        if (is_string($products)) { //Unfortunately, there is a kit without physical products in it
            $products = array($product);
        }

        $hasMainProduct = false;
        $mainProductIndex = -1;
        $i = 0;

        foreach($products as $productIterate) {
            if ($productIterate->getId() == $product->getId()) {
                $hasMainProduct = true;
                $mainProductIndex = $i;
            }
            $i++;
        }

        // Making sure that the main product is not in the list of products to process.
        if ($hasMainProduct) {
            unset($products[$mainProductIndex]);
        }

        // Configuring the product and then adding it to the beginning of the list to add
        /** @var Mage_Catalog_Model_Product $mainProduct */
        $mainProduct = $product;
        $mainProductId = $mainProduct->getId();
        $mainProduct->setCartQty($buyRequest->getQty());
        array_unshift($products, $mainProduct);

        $subProducts = array();
        $optionDisplay = array();
        $optionOutput = array();
        $printOutput = array();

        foreach($products as $productIterate) {
            if ($productIterate !== $mainProduct && isset($requestGroup)) {
                $productIterate->setParentProductId($mainProductId);
                $qty = $requestGroup[$productIterate->getId()];

                $product->addCustomOption('product_qty_' . $productIterate->getId(), $qty, $productIterate);

                $subProducts[] = $productIterate->getId();
                $optionDisplay[] = array(
                    'sku' => $productIterate->getSku(),
                    'name' => $productIterate->getName(),
                    'qty' => $qty,
                    'id' => $productIterate->getId()
                );

                $value = '';
                if ($qty) {
                    $value .= sprintf('%d', $qty) . ' x ';
                }
                $value .= $productIterate->getName();

                $printOutput[] = $value;

                if ($qty > 0) {
                    $optionOutput[] = $value;
                }
            }
        }

        $optionText = array(array(
            'label' => Mage::helper('SwiftOtter_KitProduct')->__('Included Products'),
            'value' => $optionOutput,
            'print_value' => implode(', ', $printOutput)
        ));
        $product->addCustomOption('additional_options', serialize($optionText));

        $product->addCustomOption('kit_products_ids', serialize($subProducts));
        $product->addCustomOption('kit_product_options', serialize($optionDisplay));

        return $products;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array|void
     */
    public function getOrderOptions($product = null)
    {
        $customOptions = array();

        /** @var Mage_Sales_Model_Quote_Item_Option $option */
        foreach ($product->getCustomOptions() as $option) {
            $customOptions[$option->getCode()] = $option->getValue();
        }

        $options = array_merge($customOptions, parent::getOrderOptions($product));

        return $options;
    }

    /**
     * @param $product
     * @param array $requestGroup
     * @return array
     */
    protected function _getAssociatedProducts($product, $requestGroup = array())
    {
        $associatedProducts = $this->getAssociatedProducts($product);

        /** @var Mage_Catalog_Model_Product $associatedProduct */
        foreach ($associatedProducts as $associatedProduct) {
            $requestGroup[$associatedProduct->getId()] = $associatedProduct->getQty();
        }

        return $requestGroup;
    }



    /**
     * Retrieve array of associated products
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getAssociatedProducts($product = null, $associatedProducts = array())
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProducts)) {
            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($product)));

            /** @var Mage_Catalog_Model_Product $item */
            foreach ($collection as $item) {
                if (isset($associatedProducts[$item->getId()])) {
                    $this->_mergeProducts($associatedProducts[$item->getId()], $item);
                } else {
                    $associatedProducts[$item->getId()] = $item;
                }

                if ($item->getTypeId() == self::KIT_TYPE_CODE) {
                    $qty = $item->getQty();
                    if(!$qty) {
                        $qty = 1;
                    }

                    $merge = $this->getAssociatedProducts($item);

                    foreach ($merge as $mergeItem) {
                        $mergeItem->setQty($mergeItem->getQty() * $qty);

                        if (isset($associatedProducts[$mergeItem->getId()])) {
                            $this->_mergeProducts($associatedProducts[$mergeItem->getId()], $item);
                        } else {
                            $associatedProducts[$mergeItem->getId()] = $mergeItem;
                        }
                    }
                }
            }

            $associatedProducts = array_values($associatedProducts);

            $this->getProduct($product)->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $this->getProduct($product)->getData($this->_keyAssociatedProducts);
    }

    protected function _mergeProducts ($keepProduct, $discardProduct)
    {
        $keepProduct->setQty($keepProduct->getQty() + $discardProduct->getQty());

        return $keepProduct;
    }
}