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
 * @copyright Swift Otter Studios, 10/21/2013
 * @package default
 **/

class SwiftOtter_SimpleConfigurable_Model_Product_Type_SimpleConfigurable_Price extends Mage_Catalog_Model_Product_Type_Configurable_Price
{
    public function getMinimalPrice ($product)
    {
        return $this->getPrice($product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function getMaxPossibleFinalPrice($product)
    {
        // Indexer calculates max_price, so if this value's been loaded, use it
        $price = $product->getMaxPrice();
        if ($price !== null) {
            return $price;
        }

        $childProduct = $this->getChildProductForRangeExtent($product, "finalPrice", false);
        // If there aren't any salable child products we return the highest price
        // of all child products, including any ones not currently salable.

        if (!$childProduct) {
            $childProduct = $this->getChildProductForRangeExtent($product, "finalPrice", false, false);
        }

        if ($childProduct) {
            return $childProduct->getFinalPrice();
        }
        return false;
    }

    public function getFinalPrice($qty=null, $product)
    {
        $childProduct = $this->getChildProductForRangeExtent($product, "finalPrice");
        if (!$childProduct) {
            $childProduct = $this->getChildProductForRangeExtent($product, "finalPrice", true, false);
        }

        if ($childProduct) {
            $price = $childProduct->getFinalPrice();
        } else {
            return false;
        }

        $product->setFinalPrice($price);
        return $price;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return decimal
     */
    public function getPrice($product)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return 0;
        }

        // Just return indexed_price, if it's been fetched already
        // (which it will have been for collections, but not on product page)
        $price = $product->getIndexedPrice();
        if ($price !== null) {
            return $price;
        }

        $childProduct = $this->getChildProductForRangeExtent($product, "finalPrice");
        // If there aren't any salable child products we return the lowest price
        // of all child products, including any ones not currently salable.
        if (!$childProduct) {
            $childProduct = $this->getChildProductForRangeExtent($product, "finalPrice", true, false);
        }

        if ($childProduct) {
            return $childProduct->getPrice();
        }

        return false;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $priceType
     * @param bool $lowRange
     * @param bool $checkSalable
     * @return Mage_Catalog_Model_Product
     */
    public function getChildProductForRangeExtent($product, $priceType, $lowRange = true, $checkSalable = true)
    {
        $childProducts = $product->getTypeInstance(true)->getChildProducts($product, $checkSalable);
        if (count($childProducts) == 0) { // If config product has no children
            return false;
        }

        $rangePrice = 0;
        if ($lowRange) {
            $rangePrice = PHP_INT_MAX;
        }
        $rangeProduct = false;
        foreach($childProducts as $childProduct) {
            if ($priceType == "finalPrice") {
                $thisPrice = $childProduct->getFinalPrice();
            } else {
                $thisPrice = $childProduct->getPrice();
            }


            if (($lowRange && $thisPrice < $rangePrice) || (!$lowRange && $thisPrice > $rangePrice)) {
                $rangePrice = $thisPrice;
                $rangeProduct = $childProduct;
            }
        }
        return $rangeProduct;
    }
}