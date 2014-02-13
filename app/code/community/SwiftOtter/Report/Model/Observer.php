<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Report_Model_Observer
{
    public function salesOrderPlaceAfter($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();

        Mage::helper('SwiftOtter_Report/ProductSale')->registerSaleForReports($order);
    }
}