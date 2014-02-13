<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Report_Helper_ProductSale extends Mage_Core_Helper_Abstract
{
    /**
     * Registers the products orders in proper report tables
     *
     * @param Mage_Sales_Model_Order $order
     */
    public function registerSaleForReports (Mage_Sales_Model_Order $order)
    {
        Mage::getModel('SwiftOtter_Report/ProductSaleRegion');

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            /** @var SwiftOtter_Report_Model_ProductSaleRegion $productSale */
            $productSale = Mage::getModel('SwiftOtter_Report/ProductSaleRegion')->loadByOrderItem($order, $item);

            $productSale
                ->addSaleItem($item)
                ->save();
        }
    }

    public function regenerate()
    {
        $resource = Mage::getModel('core/resource');
        $read = $resource->getConnection('core_read');

        $read->truncateTable($resource->getTableName('SwiftOtter_Report/ProductSaleRegion'));

        $select = $read->select()
            ->from(array('item' => $resource->getTableName('sales/order_item'))
            )
            ->joinInner(array('order' => $resource->getTableName('sales/order')),
                '`order`.entity_id = `item`.order_id'
            )->joinLeft(array('address' => $resource->getTableName('sales/order_address')),
                '`address`.parent_id = `order`.entity_id'
            );

        $select->reset($select::COLUMNS)
            ->columns(new Zend_Db_Expr('MAX(`order`.store_id) AS store_id'))
            ->columns(new Zend_Db_Expr('MAX(`order`.created_at) AS sale_date'))
            ->columns(new Zend_Db_Expr('`item`.product_id AS product_id'))
            ->columns(new Zend_Db_Expr('MAX(`address`.country_id) AS country_id'))
            ->columns(new Zend_Db_Expr('MAX(`address`.region_id) AS region_id'))
            ->columns(new Zend_Db_Expr('SUM(`item`.qty_ordered) AS quantity'))
            ->columns(new Zend_Db_Expr('MAX(`item`.product_type) AS type'))
            ->columns(new Zend_Db_Expr('IF (SUM(`item`.base_row_total) > 0, SUM(`item`.base_row_total), 0) AS total'))
            ->columns(new Zend_Db_Expr('`order`.entity_id AS order_id'));

        $select->group(array(
            new Zend_Db_Expr('`item`.product_id'),
            new Zend_Db_Expr('`item`.order_id')
        ));

        $select->order(new Zend_Db_Expr('MAX(`order`.created_at)'));

        echo (string)$select;

        $query = $read->insertFromSelect($select, $resource->getTableName('SwiftOtter_Report/ProductSaleRegion'),
            array('store_id', 'sale_date', 'product_id', 'country_id', 'region_id', 'quantity', 'type', 'total', 'order_id')
        ); //, array(), 1

        $read->query($query);

//        $productId = $read->frontend($select);
    }
}