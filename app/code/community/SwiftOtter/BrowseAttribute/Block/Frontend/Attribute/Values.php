<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/13/14
 * @package default
 **/

class SwiftOtter_BrowseAttribute_Block_Frontend_Attribute_Values extends Mage_Core_Block_Template
{
    protected $_attribute;
    protected $_values;

    public function getAttribute()
    {
        if (!$this->_attribute) {
            $this->_attribute = Mage::registry('current_attribute');

            if (!$this->_attribute) {
                $this->_attribute = Mage::getModel('eav/entity_attribute');
            }
        }

        return $this->_attribute;
    }

    /**
     * Loads the values associated with the current attribute. Note that it will return an array if there is no source
     * attached with the attribute. Thus, foreach will still work, it will not put out any values.
     *
     * @return array|Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection
     */
    public function getValues()
    {
        if (!$this->_values) {
            $attribute = $this->getAttribute();
            if ($attribute->usesSource()) {
                $this->_values = Mage::helper('SwiftOtter_BrowseAttribute')->getOptionsForAttribute($attribute);
            } else {
                $this->_values = array();
            }
        }

        return $this->_values;
    }

    /**
     * Converts the system local path to a url
     *
     * @param $path
     * @return string
     */
    public function getImageUrl($path)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $path;
    }

    /**
     * Formats and returns the option value in a url
     *
     * @param $label
     * @return string
     */
    public function getOptionUrl($label)
    {
        $code = $this->getAttribute()->getAttributeCode();
        $urlized = Mage::helper('SwiftOtter_BrowseAttribute')->toUnderscoreUrl($label);

        $url = Mage::getUrl(sprintf('browse/%s/%s', $code, $urlized));

        return $url;
    }
}