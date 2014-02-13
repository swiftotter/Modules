<?php
/**
 * 
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

/**
 * Class SwiftOtter_Report_Model_ProductSaleRegion
 *
 * @method int getStoreId()
 * @method DateTime getSaleDate()
 * @method int getProductId()
 * @method int getCountryId()
 * @method int getRegionId()
 * @method int getQuantity()
 * @method string getType()
 * @method float getTotal()
 */

class SwiftOtter_Report_Model_ProductSaleRegion extends Mage_Core_Model_Abstract
{
	protected $_store;
    protected $_product;
    protected $_orderItem;

	public function __construct()
	{
		return $this->_init('SwiftOtter_Report/ProductSaleRegion');
	}

    public function loadByOrderItem (Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Item $orderItem)
    {
        /** @var SwiftOtter_Report_Model_Resource_ProductSale $resource */
        $resource = $this->getResource();

        $address = $order->getShippingAddress();
        if ($order->getIsVirtual()) {
            $address = $order->getBillingAddress();
        }

        $resource->loadProductSaleItem(
            $this,
            $order->getStoreId(),
            $orderItem->getProductId(),
            $orderItem->getProductType(),
            $address->getCountryId(),
            $address->getRegionId()
        );

        $this->_orderItem = $orderItem;

        return $this;
    }

    /**
     * Adds the order item
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return $this
     */
    public function addSaleItem (Mage_Sales_Model_Order_Item $item = null)
    {
        if (!$item && $this->_orderItem) {
            $item = $this->_orderItem;
        }

        if (is_object($item)) {
            $quantity = $item->getQtyOrdered();
            $amount = $item->getBaseRowTotal();

            $this->setOrderId($item->getOrderId());
            $this->setQuantity($this->getQuantity() + $quantity);
            $this->setTotal($this->getTotal() + $amount);
        }

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     */
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product && $this->getProductId()) {
            $this->_product = Mage::getModel('catalog/product')->load($this->getProductId());
        }
        return $this->_product;
    }

    /**
     * @param Mage_Core_Model_Store $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (!$this->_store && $this->getStoreId()) {
            $this->_store = Mage::getModel('core/store')->load($this->getStoreId());
        }
        return $this->_store;
    }
}