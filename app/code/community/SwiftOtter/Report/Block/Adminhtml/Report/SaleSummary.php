<?php

class SwiftOtter_Report_Block_Adminhtml_Report_SaleSummary extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct(){
		parent::__construct();
		$this->_controller = 'Adminhtml_Report_SaleSummary';
		$this->_blockGroup = 'SwiftOtter_Report';
		$this->_headerText = Mage::helper('SwiftOtter_Report')->__('Sale Summary');

        $this->_removeButton('add');

	}
	
	protected function _prepareLayout() {
        $helper = Mage::helper('SwiftOtter_Report');
        $this->setChild('filter',
            $this->getLayout()->createBlock('SwiftOtter_Base/Admin_Form_Filter')->setSaveParametersInSession(true)->setRegistryKey(
                $helper::REPORT_REGISTRY_NODE
            )
        );

		$this->setChild('grid', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_Grid', $this->_controller . '.Grid')->setSaveParametersInSession(true));
	}
}