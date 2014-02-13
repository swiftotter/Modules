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

class SwiftOtter_Base_Block_Admin_Form_Filter_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_filter;
    protected $_helper = 'SwiftOtter_Base';

    /**
     * @param $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }

    /**
     * @return Varien_Object
     */
    public function getFilter()
    {
        if (!$this->_filter) {
            $helper = Mage::helper($this->_helper);
            $registryNode = $helper::REPORT_REGISTRY_NODE;

            $this->_filter = Mage::helper('SwiftOtter_Base')
                ->getDateRange($registryNode);

            if (!is_object($this->_filter)) {
                $this->_filter = new Varien_Object();
            }
        }

        return $this->_filter;
    }
}