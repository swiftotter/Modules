<?php

class SwiftOtter_Report_Model_Resource_ProductSaleRegion_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected $_includeGroupedProducts;

	protected function _construct()
	{
		$this->_init('SwiftOtter_Report/ProductSaleRegion');
	}

    /**
     * Returns the joined tables for the custom product sale report
     *
     * @param array|int|null $productId
     * @return $this
     */
    public function getReportCollection($productId = null)
    {
        $this->_reset();

        $this->addFieldToSelect(array(
            new Zend_Db_Expr('MIN(`main_table`.sale_date) AS date_start'),
            new Zend_Db_Expr('MAX(`main_table`.sale_date) AS date_end'),
            new Zend_Db_Expr('SUM(`main_table`.quantity) AS quantity'),
            new Zend_Db_Expr('SUM(`main_table`.total) AS total'),
            'type',
            'product_id'
        ));

        $includeAttributes = array('name', 'price', 'vendor_id');
        $locale = Mage::app()->getLocale()->getLocaleCode();

        $select = $this->getSelect();

        $productTable = $this->getTable('catalog/product');
        $select->joinLeft(
            array('product' => $productTable),
            '`main_table`.product_id = `product`.entity_id',
            array('sku')
        );

        foreach ($includeAttributes as $attributeCode) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
            $joinTable = sprintf('product_%s_table', $attributeCode);

            $select->joinLeft(
                array($joinTable => $attribute->getBackendTable()),
                sprintf('`main_table`.product_id = `%s`.entity_id AND `%s`.attribute_id = %s', $joinTable, $joinTable, $attribute->getAttributeId()),
                array($attributeCode => new Zend_Db_Expr(sprintf('`%s`.value', $joinTable)))
            );
        }

//        // MAY NOT BE NECESSARY
//        $select->joinLeft(
//            array('region_name_table' => $this->getTable('directory/country_region_name')),
//            sprintf('`region_name_table`.region_id = `main_table`.region_id AND `region_name_table`.locale = \'%s\'', $locale),
//            array('region_name' => new Zend_Db_Expr('`region_name_table`.name'))
//        );

        $select->group(
            array(
                new Zend_Db_Expr('`main_table`.store_id'),
                new Zend_Db_Expr('`main_table`.product_id'),
                new Zend_Db_Expr('`main_table`.type')
            )
        );

        if ($productId) {
            if (is_array($productId)) {
                $productId = implode(',', $productId);
            }
            $select->where('`main_table`.product_id IN(?)', $productId);
        }

        if (!$this->getIncludeGroupedProducts()) {
            $select->where('`main_table`.type <> ?', Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE);
        }

        return $this;
    }

    /**
     * Due to the grouping of the main query, we need to subquery it so that we get an accurate count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $select = clone $this->getSelect();
        $query = (string)$select;

        $select->reset();

        $select->from(
            array('count_table' => new Zend_Db_Expr('(' . $query . ')')),
            array(new Zend_Db_Expr('COUNT(`count_table`.id)'))
        );

        return $select;
    }

    public function addRegionSelect()
    {
        $this->addFieldToSelect(array(
            'region_id',
            'country_id'
        ));

        $this->getSelect()
            ->group(array(
                    new Zend_Db_Expr('`main_table`.country_id'),
                    new Zend_Db_Expr('`main_table`.region_id')
            ));

        return $this;
    }

	/**
	 * Filters the menu items by the current store view id
	 *
	 * @param $storeId
	 * @return $this
	 */
	public function getByStoreView($storeId)
	{
		$this->getSelect()
			->order('sort_order ASC')
			->where('store_id = ? OR store_id = ""', $storeId);

		return $this;
	}

    /**
     * @param $includeGroupedProducts
     * @return $this
     */
    public function setIncludeGroupedProducts($includeGroupedProducts)
    {
        $this->_includeGroupedProducts = $includeGroupedProducts;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIncludeGroupedProducts()
    {
        return $this->_includeGroupedProducts;
    }


}