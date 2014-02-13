<?php
/**
 * 
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