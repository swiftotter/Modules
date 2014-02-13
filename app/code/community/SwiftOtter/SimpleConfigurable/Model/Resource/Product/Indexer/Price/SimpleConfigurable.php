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

class SwiftOtter_SimpleConfigurable_Model_Resource_Product_Indexer_Price_SimpleConfigurable extends Mage_Catalog_Model_Resource_Product_Indexer_Price_Configurable
{
    /**
     * Calculate minimal and maximal prices for configurable product options
     * and apply it to final price
     *
     * @return Mage_Catalog_Model_Resource_Product_Indexer_Price_Configurable
     */
    protected function _applyConfigurableOption()
    {
        $write      = $this->_getWriteAdapter();
        $coaTable   = $this->_getConfigurableOptionAggregateTable();
        $copTable   = $this->_getConfigurableOptionPriceTable();

        $this->_prepareConfigurableOptionAggregateTable();
        $this->_prepareConfigurableOptionPriceTable();

        $select = $write->select()
            ->from(array('i' => $this->_getDefaultFinalPriceTable()), array())
            ->join(
                array('l' => $this->getTable('catalog/product_super_link')),
                'l.parent_id = i.entity_id',
                array('parent_id', 'product_id'))
            ->columns(array('customer_group_id', 'website_id'), 'i')
            ->join(
                array('a' => $this->getTable('catalog/product_super_attribute')),
                'l.parent_id = a.product_id',
                array())
			->join(
				array('ip' => $this->getTable('catalog/product_index_price')),
				'ip.entity_id = l.product_id',
				array(
					'price' => 'ip.final_price',
					'tier_price' => 'ip.tier_price',
					'group_price' => 'ip.group_price'
				)
			)
            ->join(
                array('le' => $this->getTable('catalog/product')),
                'le.entity_id = l.product_id',
                array())

            ->where('le.required_options=0')
            ->group(array('l.parent_id', 'i.customer_group_id', 'i.website_id', 'l.product_id'));

        $query = $select->insertFromSelect($coaTable);
        $write->query($query);

        $select = $write->select()
            ->from(
                array($coaTable),
                array(
                    'parent_id', 'customer_group_id', 'website_id',
                    'MIN(price)', 'MAX(price)', 'MIN(tier_price)', 'MIN(group_price)'
                ))
            ->group(array('parent_id', 'customer_group_id', 'website_id'));

        $query = $select->insertFromSelect($copTable);
        $write->query($query);

        $table  = array('i' => $this->_getDefaultFinalPriceTable());
        $select = $write->select()
            ->join(
                array('io' => $copTable),
                'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id'
                .' AND i.website_id = io.website_id',
                array());
        $select->columns(array(
            'min_price'   => new Zend_Db_Expr('io.min_price'),
            'max_price'   => new Zend_Db_Expr('io.max_price'),
            'tier_price'  => $write->getCheckSql('i.tier_price IS NOT NULL', 'i.tier_price + io.tier_price', 'NULL'),
            'group_price' => $write->getCheckSql(
                    'i.group_price IS NOT NULL',
                    'i.group_price + io.group_price', 'NULL'
                ),
        ));

        $query = $select->crossUpdateFromSelect($table);
        $write->query($query);

        $write->delete($coaTable);
        $write->delete($copTable);

        return $this;
    }
}