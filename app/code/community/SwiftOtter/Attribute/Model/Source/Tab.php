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