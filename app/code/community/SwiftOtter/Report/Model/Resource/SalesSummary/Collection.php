<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Report_Model_Resource_SalesSummary_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    const ROWS_DISPLAYED = 4;

    protected function _construct()
    {
        $this->_init('sales/order');
    }

    public function getReportCollection()
    {
        $this->_reset();
        $select = $this->getSelect();
        $select->reset($select::COLUMNS);

        $this->addFieldToSelect(array(
            new Zend_Db_Expr('SUM(base_grand_total) AS grand_total'),
            new Zend_Db_Expr('SUM(base_subtotal) AS subtotal'),
            new Zend_Db_Expr('SUM(base_tax_amount) AS tax'),
            new Zend_Db_Expr('SUM(base_shipping_amount) AS shipping'),
        ));

        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();

        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::WHERE);

        $countSelect->columns(new Zend_Db_Expr(self::ROWS_DISPLAYED))
            ->limit(1);

        return $countSelect;
    }

    public function addDateFilter(DateTime $from, DateTime $to)
    {
        $this->getSelect()
            ->where('created_at > ?', $from->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d'))
            ->where('created_at <= ?', $to->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d'));

        return $this;
    }

    public function convertToRows()
    {
        $output = array();
        foreach($this->getItems() as $total) {
            $output[] = new Varien_Object(array(
                'label' => Mage::helper('SwiftOtter_Report')->__('Subtotal'),
                'amount' => (float)$total->getSubtotal()
            ));
            $output[] = new Varien_Object(array(
                'label' => Mage::helper('SwiftOtter_Report')->__('Tax'),
                'amount' => (float)$total->getTax()
            ));
            $output[] = new Varien_Object(array(
                'label' => Mage::helper('SwiftOtter_Report')->__('Shipping'),
                'amount' => (float)$total->getShipping()
            ));
            $output[] = new Varien_Object(array(
                'label' => Mage::helper('SwiftOtter_Report')->__('Grand Total'),
                'amount' => (float)$total->getGrandTotal()
            ));
        }

        $this->_items = $output;

        return $this;
    }
}