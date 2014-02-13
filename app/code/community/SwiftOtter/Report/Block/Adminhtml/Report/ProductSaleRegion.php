<?php

class SwiftOtter_Report_Block_Adminhtml_Report_ProductSaleRegion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
    {
		parent::__construct();
		$this->_controller = 'Adminhtml_Report_ProductSaleRegion';
		$this->_blockGroup = 'SwiftOtter_Report';
		$this->_headerText = Mage::helper('SwiftOtter_Report')->__('Product Sales Report');

        $this->_removeButton('add');
    }

	protected function _prepareLayout()
    {
        $helper = Mage::helper('SwiftOtter_Report');
        $this->setChild('filter',
            $this->getLayout()->createBlock('SwiftOtter_Report/Adminhtml_Report_ProductSaleRegion_Filter')->setSaveParametersInSession(true)->setRegistryKey(
                $helper::REPORT_REGISTRY_NODE
            )
        );

		$this->setChild('grid', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_Grid', $this->_controller . '.Grid')->setSaveParametersInSession(true));
	}
}