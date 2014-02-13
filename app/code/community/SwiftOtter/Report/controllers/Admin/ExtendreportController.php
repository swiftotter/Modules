<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Report_Admin_ExtendreportController extends Mage_Adminhtml_Controller_Action
{
    public function productsalesregionAction()
    {
        Mage::helper('SwiftOtter_Base')->initDateFilterParams(
            $this->_getRegistryNode()
        );

        $this->loadLayout();
        $this->renderLayout();
    }

    public function productsalesregiongridAction()
    {
        Mage::helper('SwiftOtter_Base')->initDateFilterParams(
            $this->_getRegistryNode()
        );

        $this->loadLayout();
        $this->renderLayout();
    }

    public function salesummaryAction()
    {
        Mage::helper('SwiftOtter_Base')->initDateFilterParams(
            $this->_getRegistryNode()
        );

        $this->loadLayout();
        $this->renderLayout();
    }

    public function salesummarygridAction()
    {
        Mage::helper('SwiftOtter_Base')->initDateFilterParams(
            $this->_getRegistryNode()
        );

        $this->loadLayout();
        $this->renderLayout();
    }

    public function regenerateAction()
    {
        Mage::helper('SwiftOtter_Report/ProductSale')->regenerate();

        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _getRegistryNode()
    {
        $helper = Mage::helper('SwiftOtter_Report');

        return $helper::REPORT_REGISTRY_NODE;
    }

    protected function _isAllowed()
    {
        $actionList = explode('_', $this->getFullActionName('_'));
        $action = str_replace('grid', '', array_pop($actionList));

        return Mage::getSingleton('admin/session')->isAllowed('report/salesroot/' . $action);
    }


}