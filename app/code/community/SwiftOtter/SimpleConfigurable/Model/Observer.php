<?php
/**
 * SwiftOtter_Base is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SwiftOtter_Base is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with SwiftOtter_Base. If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright: 2013 (c) SwiftOtter Studios
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 10/25/2013
 * @package default
 **/

class SwiftOtter_SimpleConfigurable_Model_Observer
{
    /**
     * This function is executed for the admin context only. It temporarily changes the product into a Configurable
     * product so we can extend the Configurable product functionality, while wrapping that with our own.
     *
     * @param $observer
     */
    public function catalogProductLoadAfter($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getProduct();

        if ($product->getTypeId() == SwiftOtter_SimpleConfigurable_Model_Product_Type_SimpleConfigurable::SIMPLE_TYPE &&
            Mage::app()->getFrontController()->getRequest()->getControllerName() == 'catalog_product') {
            $product->setData('type_id', Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE);
            $product->setSimpleConfigurable(true);
        }
    }

    /**
     * Changing back the Configurable status to SimpleConfigurable (if necessary).
     *
     * @param $observer
     */
    public function catalogProductSaveBefore($observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getProduct();

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE &&
            $product->getSimpleConfigurable()) {
            $product->setData('type_id', SwiftOtter_SimpleConfigurable_Model_Product_Type_SimpleConfigurable::SIMPLE_TYPE);
        }
    }


    /**
     * When we have a collection loaded, we need to erase the price for the product.
     *
     * @param $observer
     */
    public function catalogProductCollectionLoadAfter($observer)
    {
        if (Mage::app()->getFrontController()->getRequest()->getControllerName() == 'sales_order_create') {
            $collection = $observer->getCollection();

            /** @var Mage_Catalog_Model_Product $product */
            foreach ($collection as $product) {
                if ($product->getTypeId() == SwiftOtter_SimpleConfigurable_Model_Product_Type_SimpleConfigurable::SIMPLE_TYPE) {
                    $product->setPrice(0);
                }
            }
        }
    }

    /**
     * Adding means to select attributes for a SimpleConfigurable product
     *
     * @param $observer
     */
    public function controllerActionLayoutLoadBefore($observer)
    {
        $type = SwiftOtter_SimpleConfigurable_Model_Product_Type_SimpleConfigurable::SIMPLE_TYPE;

        /** @var Mage_Adminhtml_Controller_Action $controller */
        $controller = $observer->getAction();
        /** @var Mage_Core_Model_Layout $layout */
        $layout = $observer->getLayout();

        if ($controller->getFullActionName() == 'adminhtml_catalog_product_new' && $controller->getRequest()->getParam('type') == $type &&
            !$controller->getRequest()->getParam('attributes')) {
            $layout->getUpdate()->addHandle('adminhtml_catalog_product_' . $type . '_new');
        }
    }

}