<?php
class SwiftOtter_Report_Model_Resource_ProductSaleRegion extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct(){
		$this->_init('SwiftOtter_Report/ProductSaleRegion', 'id');
	}


    public function loadProductSaleItem($object, $storeId, $productId, $type, $countryId, $regionId, $date = null) {
        if (!$date) {
            $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
            $date = date('Y-m-d', $currentTimestamp);
        }

        $read = $this->_getReadAdapter();
        if ($read) {
            $select = $this->_getLoadSpecificSelect($storeId, $productId, $type, $countryId, $regionId, $date);
            $data = $read->fetchRow($select);

            if ($data) {
                $object->setData($data);
            } else {
                $data = array(
                    'store_id'      => $storeId,
                    'product_id'    => $productId,
                    'country_id'    => $countryId,
                    'region_id'     => $regionId,
                    'sale_date'     => $date,
                    'type'          => $type
                );

                $object->setData($data);
            }
        }

        $this->unserializeFields($object);
        $this->_afterLoad($object);

        return $this;
    }

    protected function _getLoadSpecificSelect($storeId, $productId, $type, $countryId, $regionId, $date)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('store_id=?', $storeId)
            ->where('product_id=?', $productId)
            ->where('type=?', $type)
            ->where('country_id=?', $countryId)
            ->where('sale_date=?', $date);

        if ($regionId) {
            $select->where('region_id=?', $regionId);
        }

        return $select;
    }

	
}