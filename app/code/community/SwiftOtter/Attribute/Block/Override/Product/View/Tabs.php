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

class SwiftOtter_Attribute_Block_Override_Product_View_Tabs extends Mage_Catalog_Block_Product_View_Tabs
{
    public function removeTab($alias)
    {
        foreach ($this->_tabs as $index => $tab) {
            if (isset($tab['alias']) && $tab['alias'] == $alias) {
                unset($this->_tabs[$index]);
            }
        }

        return $this;
    }
}