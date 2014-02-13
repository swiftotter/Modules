<?php

class SwiftOtter_Report_Block_Adminhtml_Report_ProductSaleRegion_Grid extends SwiftOtter_Base_Block_Admin_Form_Filter_Grid
{
    const ENTITY_TYPE = 'catalog_product';
    const CONFIG_PATH_REPORT_COUNTRY_LIST = 'reports/product_sales/regions_for_countries';
    const CONFIG_PATH_REPORT_INCLUDE_GROUPED_PRODUCTS = 'reports/product_sales/include_grouped_products';

    protected $_filter;
    protected $_helper = 'SwiftOtter_Report';

	public function __construct()
	{
		parent::__construct();

		$this->setId('product_sale_report')
			 ->setDefaultSort('id')
			 ->setDefaultDir('desc')
             ->setUseAjax(true)
			 ->setSaveParametersInSession(true)
             ->setCountTotals(true);
	}

    protected function getSelectedProducts() {

    }



	
	protected function _prepareCollection()
	{
        /** @var SwiftOtter_Report_Model_Resource_ProductSale_Collection $collection */
        $collection = Mage::getResourceModel('SwiftOtter_Report/ProductSaleRegion_Collection')->getReportCollection();
        $collection->setIncludeGroupedProducts(Mage::getStoreConfigFlag(self::CONFIG_PATH_REPORT_INCLUDE_GROUPED_PRODUCTS));

        if ($this->getFilter()) {
            $filter = $this->getFilter();

            if ($filter->getShowRegions()) {
                $collection->addRegionSelect();
            }

            /** @var SwiftOtter_Base_Model_DateRange $dateRange */
            $dateRange = $filter->getRange();

            $select = $collection->getSelect();
            $select->where('`main_table`.sale_date >= ?', $dateRange->getStart()->format('Y-m-d'));
            $select->where('`main_table`.sale_date < ?', $dateRange->getEnd()->format('Y-m-d'));
        }
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}



    protected function _afterLoadCollection()
    {
        $collection = $this->getCollection();

        $total = 0;
        $quantity = 0;
        foreach ($collection as $row) {
            $total += $row->getTotal();
            $quantity += $row->getQuantity();
        }

        $this->setTotals(new Varien_Object(
            array (
                'total' => $total,
                'quantity' => $quantity
            )
        ));

        return parent::_afterLoadCollection();
    }
	
	protected function _prepareColumns() {
        $this->addColumn('sku', array(
            'header'	=> $this->__('SKU'),
            'align'		=> 'center',
            'index'		=> 'sku',
        ));

        $this->addColumn('name', array(
            'header'	=> $this->__('Name'),
            'align'		=> 'center',
            'index'		=> 'name',
        ));

        $this->addColumn('vendor', array(
            'header'	=> $this->__('Vendor'),
            'align'		=> 'center',
            'index'		=> 'vendor',
            'type'      => 'options',
            'options'   => Mage::getModel('SwiftOtter_Inventory/Source_Vendor')->getAllOptions()
        ));

        if ($this->getFilter()->getShowRegions()) {
            $this->addColumn('region_id', array(
                'header'	=> $this->__('Region'),
                'align'		=> 'center',
                'index'		=> 'region_id',
                'type'      => 'options',
                'options'   => $this->_getRegions()
            ));

            $this->addColumn('country_id', array(
                'header'	=> $this->__('Country'),
                'align'		=> 'center',
                'index'		=> 'country_id',
                'type'      => 'options',
                'options'   => $this->_getCountries()
            ));
        }

        $this->addColumn('quantity', array(
            'header'	=> $this->__('QTY Sold'),
            'align'		=> 'center',
            'index'		=> 'quantity',
        ));

        $this->addColumn('total', array(
            'header'	=> $this->__('Dollars Sold'),
            'align'		=> 'center',
            'index'		=> 'total',
            'type'      => 'price',
            'currency_code' => Mage::app()->getStore(0)->getBaseCurrency()->getCode()
        ));

		return parent::_prepareColumns();
	}

    protected function _getCountries()
    {
        $countries = $this->_convertToAllOptions(Mage::getModel('adminhtml/system_config_source_country')->toOptionArray());
        array_shift($countries);

        return $countries;
    }

    protected function _getRegions()
    {
        $countries = Mage::getStoreConfig(self::CONFIG_PATH_REPORT_COUNTRY_LIST);
        if (!is_array($countries)) {
            $countries = explode(',', $countries);
        }

        $regions = array();
        $regionsCollection = Mage::getResourceModel('directory/region_collection')->load();
        /** @var Mage_Directory_Model_Region $region */
        foreach ($regionsCollection as $region) {
            if (in_array($region->getCountryId(), $countries)) {
                $regions[$region->getId()] = $region->getDefaultName();
            }
        }

        return $regions;
    }

    protected function _convertToAllOptions($input) {
        $output = array();
        foreach ($input as $value) {
            $output[$value['value']] = $value['label'];
        }

        return $output;
    }

    public function reorderLeftFormat ($renderedValue, $row, $column, $isExport)
    {
        if ($renderedValue < 0) {
            $renderedValue = "<strong>0 - Reorder Now</strong>";
        }
        return $renderedValue;
    }

    public function getRowUrl($item)
    {
        return $this->getUrl('catalog/product/view/', array('id' => $item->getProductId()));
    }


    public function getGridUrl($params = array())
    {
        return $this->getUrl('*/*/productsalesregiongrid', array('_current'=>true));
    }


}