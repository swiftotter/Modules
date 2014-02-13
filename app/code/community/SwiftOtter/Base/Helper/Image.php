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
 * @copyright Swift Otter Studios, 09/18/2013
 * @package default
 **/

class SwiftOtter_Base_Helper_Image extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @param null $file
     * @param int $width
     * @param int $height
     * @param int $minHeight
     * @param bool $showZoom
     * @param bool $keepFrame
     * @return SwiftOtter_Base_Model_Image
     */
    public function createImage (Mage_Catalog_Model_Product $product, $attribute = 'image',  $file = null, $width = 370, $height = 370, $minHeight = 150, $showZoom = false, $keepFrame = true){
        $output = Mage::getModel('SwiftOtter_Base/Image');

        $output->setProduct($product)
            ->setAttribute($attribute)
            ->setFile($file)
            ->setKeepFrame($keepFrame)
            ->setShowZoom($showZoom)
            ->setWidth((int)$width)
            ->setHeight((int)$height)
            ->calculateSize($minHeight, (int)$width, (int)$height);

        return $output;
    }
}