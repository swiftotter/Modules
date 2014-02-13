<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/9/14
 * @package default
 **/

/**
 * Class SwiftOtter_BrowseAttribute_Model_Observer_Router
 * This class is acting as a hybrid router and observer class. This may not be the best practice, so I am open for ideas
 * to bring this to best practice standards.
 */

class SwiftOtter_BrowseAttribute_Model_Observer_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    public function controllerFrontInitRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('browse_attribute', $this);
    }

    public function match(Zend_Controller_Request_Http $request)
    {
        $identifier = explode('/', trim($request->getPathInfo(), '/'));

        if (count($identifier) <= 1) {
            return false;
        }

        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $identifier[1]);

        if ($attribute && $attribute->getId() && $attribute->getAllowBrowseBy()) {
            $action = 'viewParent';
            $optionId = -1;

            if (count($identifier) > 2) {
                $optionId = $this->_matchOptionId($attribute, urldecode($identifier[2]));
                $action = 'view';
            }

            $request->setModuleName('browse')
                ->setControllerName('attribute')
                ->setActionName($action)
                ->setParam('attribute_id', $attribute->getAttributeId())
                ->setParam('attribute', $attribute);

            if ($optionId > 0) {
                $request->setParam('option_id', $optionId);
            }


            $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, implode('/', $identifier));

            return true;
        }

        return false;

    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param $value
     * @return mixed
     */
    protected function _matchOptionId($attribute, $value)
    {
        foreach ($attribute->getSource()->getAllOptions() as $option) {
            if ($this->_formatValue($option['label']) == $this->_formatValue($value) || $option['value'] == $value) {
                return $option['value'];
            }
        }
    }

    protected function _formatValue($input)
    {
        return strtolower(preg_replace('/[^A-Z0-9]/ui', '', $input));
    }

}