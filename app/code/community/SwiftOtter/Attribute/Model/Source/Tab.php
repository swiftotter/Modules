<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 08/14/2013
 * @package default
 **/

class SwiftOtter_Attribute_Model_Source_Tab extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options;
    const EVENT_ATTRIBUTE_SOURCE_TAB_COLLECTION = 'swiftotter_attribute_source_tab';

    public function toOptionArray()
    {
        $options = array();
        foreach ($this->getAllOptions() as $optionValue => $optionLabel) {
            $options[] = array(
                'value' => $optionValue,
                'label' => $optionLabel
            );
        }

        return $options;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $transport = new Varien_Object(array('options' => array()));
            Mage::dispatchEvent(self::EVENT_ATTRIBUTE_SOURCE_TAB_COLLECTION, array('transport' => $transport));

            $this->_options = $transport->getOptions();
        }

        return $this->_options;
    }
}