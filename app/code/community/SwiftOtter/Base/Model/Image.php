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

/**
 * Class SwiftOtter_Base_Model_Image
 *
 * This is a wrapper class for Mage_Catalog_Helper_Image to ease it's configuration and use.
 */
class SwiftOtter_Base_Model_Image
{
    protected $_largeUrl;
    protected $_sourceUrl;
    protected $_width;
    protected $_height;
    protected $_showZoom;

    protected $_product;
    protected $_file;
    protected $_attribute;
    protected $_image;

    const IMAGE_WATERMARK_RATIO = 0.25;

    protected $_keepFrame;

    public function __toString()
    {
        return $this->getSourceUrl();
    }

    /**
     * Refigures the size of the image based on size constraints.
     *
     * @param $minHeight
     * @param null $width
     * @param null $height
     * @return $this
     */
    public function calculateSize($minHeight, $width = null, $height = null)
    {
        $image = $this->getImage();
        $showZoom = $this->getShowZoom();

        if ($height === null) {
            $height = $this->getHeight();
        }

        if ($width === null) {
            $width = $this->getWidth();
        }

        list($sourceWidth, $sourceHeight) = $image->getOriginalSizeArray();

        if ($width && !$height) {
            $difference = $width / $sourceWidth;
            $height = round($sourceHeight * $difference);
        }

        if ($height && !$width) {
            $difference = $height / $sourceHeight;
            $width = round($sourceWidth * $difference);
        }

        if ($sourceWidth > $width || $sourceHeight > $height){
            $showZoom = true;
        }

        if ($sourceHeight < $sourceWidth && !$showZoom) { //
            $height = round(($sourceHeight / $sourceWidth) * $width); //Convert to a percentage and multiply out again.
            if ($height < $minHeight) {
                $numericalDifference = $minHeight - $height;
                $difference = ($numericalDifference / $height) + 1;

                $height = $minHeight; // Set a minimum height
                $width = $width * $difference;
            }
        }

        $this->setHeight((int)$height)
            ->setWidth((int)$width);

        return $this;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->_image = $image;
    }

    /**
     * Loads the image up from the base class. Please note that the reflection is only used if we are going to use
     * a custom image, and not one associated with a product.
     *
     * @return Mage_Catalog_Helper_Image
     */
    public function getImage()
    {
        if (!$this->_image) {
            $this->_image = Mage::helper('catalog/image')->init($this->getProduct(), $this->getAttribute(), $this->getFile())
                ->constrainOnly(true)
                ->keepFrame($this->getKeepFrame());

            if ($this->getFile()) {
				/**
				 * We are using \Reflection to gain access to the `_model` property that we wouldn't otherwise
				 * have access to, and would probably have to create an override. We are doing this if we have a custom
                 * image to work with.
				 */
				$reflection = new ReflectionObject($this->_image);
                $property = $reflection->getProperty('_model');
                $property->setAccessible(true);

                $model = $property->getValue($this->_image);

                if (method_exists($model, 'setBaseFile')) {
                    $model->setBaseFile($this->getFile());
                }
            }
        }
        return $this->_image;
    }

    /**
     * Resizes the watermark to be proportionate to the size of the final image.
     *
     * @return $this
     */
    public function resizeWatermark()
    {
        $image = $this->getImage();

        $watermarkSize = explode('x', Mage::getStoreConfig(sprintf("design/watermark/%s_size", $this->getAttribute())));

        if (count($watermarkSize) >= 2) {
            $watermarkWidth = $watermarkSize[0];
            $watermarkHeight = $watermarkSize[1];
            $differenceWidth = 1;
            $differenceHeight = 1;

            $width = min($this->getWidth(), $image->getOriginalWidth());
            $height = min($this->getHeight(), $image->getOriginalHeight());

            if ($width && $watermarkWidth > ($width * self::IMAGE_WATERMARK_RATIO)) {
                $differenceWidth = ($width * self::IMAGE_WATERMARK_RATIO) / $watermarkWidth;
            }

            if ($height && $watermarkHeight > ($height * self::IMAGE_WATERMARK_RATIO)) {
                $differenceHeight = ($height * self::IMAGE_WATERMARK_RATIO) / $watermarkHeight;
            }

            $difference = $differenceWidth;
            if ($differenceHeight < $difference) {
                $difference = $differenceHeight;
            }

            $image->setWatermarkSize(sprintf('%dx%d', $watermarkWidth*$difference, $watermarkHeight*$difference));
        }

        return $this;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->_height = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * @param string $largeUrl
     * @return $this
     */
    public function setLargeUrl($largeUrl)
    {
        $this->_largeUrl = $largeUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getLargeUrl()
    {
        if (!$this->_largeUrl) {
            $this->resizeWatermark();
            $this->_largeUrl = (string)Mage::helper('catalog/image')->init($this->getProduct(), $this->getAttribute(), $this->getFile());
        }
        return $this->_largeUrl;
    }

    /**
     * @param bool $showZoom
     * @return $this
     */
    public function setShowZoom($showZoom)
    {
        $this->_showZoom = $showZoom;
        return $this;
    }

    /**
     * @return bool
     */
    public function getShowZoom()
    {
        return $this->_showZoom;
    }

    /**
     * @param string $sourceUrl
     * @return $this
     */
    public function setSourceUrl($sourceUrl)
    {
        $this->_sourceUrl = $sourceUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceUrl()
    {
        if (!$this->_sourceUrl) {
            $this->resizeWatermark();

            $this->_sourceUrl = (string)$this->getImage()
                ->keepAspectRatio(true)
                ->constrainOnly(true)
                ->backgroundColor(array(255,255,255))
                ->resize($this->getWidth(), $this->getHeight());
        }

        return $this->_sourceUrl;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->_width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * @param string $file
     * @return $this;
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @param string $attribute
     * @return $this;
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * @param bool $keepFrame
     * @return $this
     */
    public function setKeepFrame($keepFrame)
    {
        $this->_keepFrame = $keepFrame;
        return $this;
    }

    /**
     * @return bool
     */
    public function getKeepFrame()
    {
        return $this->_keepFrame;
    }




}