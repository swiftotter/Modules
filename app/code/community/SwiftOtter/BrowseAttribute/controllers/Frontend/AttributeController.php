<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/9/14
 * @package default
 **/

require 'Mage/Catalog/controllers/CategoryController.php';
class SwiftOtter_BrowseAttribute_Frontend_AttributeController extends Mage_Catalog_CategoryController
{
    protected $_attribute;
    protected $_optionId = -1;
    protected $_optionLabel;

    public function viewParentAction()
    {
        $this->_loadData();

        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->_loadData();

        $category = Mage::getModel('SwiftOtter_BrowseAttribute/CategoryPseudo')
            ->setAttribute($this->_attribute)
            ->setOptionId($this->_optionId)
            ->setIsAnchor(true)
            ->setName($this->_getAttribute()->getFrontendLabel() . ': ' . $this->_getOptionLabel());

        $this->_title($this->_getAttribute()->getFrontendLabel() . ': ' . $this->_getOptionLabel());

        Mage::register('current_category', $category);

        // A interesting way to rewrite the layer singleton, without formally doing so.
        Mage::register('_singleton/catalog/layer', Mage::getModel('SwiftOtter_BrowseAttribute/LayerPseudo'));

        $this->loadLayout();

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('attribute_browse_list', array(
                    'label' => $this->_getAttribute()->getFrontendLabel(),
                    'title' => $this->_getAttribute()->getFrontendLabel(),
                    'link'  => Mage::getUrl('browse/' . $this->_getAttribute()->getAttributeCode()))
            );

            $breadcrumbs->addCrumb('attribute_browse_items', array(
                    'label' => $this->_getOptionLabel(),
                    'title' => $this->_getOptionLabel()
            ));
        }

        $this->renderLayout();
    }

    protected function _getOptionLabel()
    {
        if (!$this->_optionLabel && $this->_attribute && $this->_optionId) {
            $options = $this->_attribute->getSource()->getAllOptions(true, true);
            foreach ($options as $option) {
                if ($option['value'] == $this->_optionId && trim($option['label'])) {
                    $this->_optionLabel = $option['label'];
                }
            }
        }

        return $this->_optionLabel;
    }

    protected function _getAttribute()
    {
        if (!$this->_attribute) {
            $this->_attribute = Mage::getModel('eav/entity_attribute');
        }

        return $this->_attribute;
    }

    protected function _loadData()
    {
        $attribute = $this->getRequest()->getParam('attribute');
        if (!is_object($attribute)) {
            $attribute = Mage::getModel('eav/entity_attribute')->load($this->getRequest()->getParam('attribute_id'));
        }

        if (!$attribute->getAllowBrowseBy()) {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $pageId = Mage::getStoreConfig('web/default/cms_no_route');
            if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
                $this->_forward('defaultNoRoute');
            }
        }

        $this->_attribute = $attribute;
        Mage::register('current_attribute', $this->_attribute);

        if ($this->getRequest()->getParam('option_id')) {
            $this->_optionId = $this->getRequest()->getParam('option_id');
        }
        Mage::register('current_attribute_value', $this->_optionId);

        return $this;
    }
}