<?php

class SwiftOtter_Report_Block_Adminhtml_Report_SaleSummary_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    const ENTITY_TYPE = 'catalog_product';
    const CONFIG_PATH_REPORT_COUNTRY_LIST = 'reports/product_sales/regions_for_countries';

	public function __construct()
	{
		parent::__construct();

		$this->setId('product_sale_report')
			 ->setDefaultSort('id')
			 ->setDefaultDir('desc')
             ->setUseAjax(true)
			 ->setSaveParametersInSession(true);
             //->setCountTotals(true);
	}

    protected function getSelectedProducts() {

    }
	
	protected function _prepareCollection()
	{
        $helper = Mage::helper('SwiftOtter_Report');
        $registryKey = $helper::REPORT_REGISTRY_NODE;

        /** @var SwiftOtter_Report_Model_Resource_SalesSummary_Collection $collection */
        $collection = Mage::getResourceModel('SwiftOtter_Report/SalesSummary_Collection')->getReportCollection();
        if (Mage::helper('SwiftOtter_Base')->getDateRange($registryKey)) {
            /** @var SwiftOtter_Base_Model_DateRange $dateRange */
            $dateRange = Mage::helper('SwiftOtter_Base')
                ->getDateRange($registryKey)
                ->getRange();

            $collection->addDateFilter($dateRange->getStart(), $dateRange->getEnd());
        }
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}



    protected function _afterLoadCollection()
    {
        $collection = $this->getCollection();
        $collection->convertToRows();

        return parent::_afterLoadCollection();
    }
	
	protected function _prepareColumns() {

        $this->addColumn('amount', array(
            'header'	=> $this->__('Amount'),
            'align'		=> 'right',
            'index'		=> 'amount',
            'type'      => 'price',
            'width'     => '50%',
            'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode()
        ));

        $this->addColumn('label', array(
            'header'	=> $this->__('Label'),
            'align'		=> 'left',
            'index'		=> 'label',
            'width'     => '50%'
        ));

		return parent::_prepareColumns();
	}


    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/productsalesregiongrid', array('_current'=>true));
    }


}