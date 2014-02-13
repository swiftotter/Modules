<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Resource model for category product indexer
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SwiftOtter_BrowseAttribute_Model_Resource_Attribute_Indexer extends Mage_Index_Model_Resource_Abstract
{
    const EXCLUDE_ATTRIBUTE = 'exclude_browse_attribute';
    const WEIGHT_ATTRIBUTE = 'browse_attribute_weight';

    /**
     * Product table
     *
     * @var string
     */
    protected $_productTable;

    /**
     * Attribute table
     *
     * @var string
     */
    protected $_attributeTable;

    /**
     * Attribute option table
     *
     * @var string
     */
    protected $_attributeOptionTable;

    /**
     * Product website table
     *
     * @var string
     */
    protected $_productWebsiteTable;

    /**
     * Store table
     *
     * @var string
     */
    protected $_storeTable;

    /**
     * Group table
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * Array of info about stores
     *
     * @var array
     */
    protected $_storesInfo;

    /**
     * Preprocessing table for multi-select attributes
     *
     * @var string
     */
    protected $_preprocessTable;

    protected $_hasPreprocess = false;

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('SwiftOtter_BrowseAttribute/Index', 'attribute_id');

        $this->_productTable         = $this->getTable('catalog/product');
        $this->_attributeTable       = $this->getTable('eav/attribute');
        $this->_attributeOptionTable = $this->getTable('eav/attribute_option');
        $this->_productWebsiteTable  = $this->getTable('catalog/product_website');
        $this->_storeTable           = $this->getTable('core/store');
        $this->_groupTable           = $this->getTable('core/store_group');

        $this->_preprocessTable      = $this->getTable('SwiftOtter_BrowseAttribute/Index_Tmp');
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data      = $event->getNewData();

        if (isset($data['attribute_codes'])) {
            $attributes = $data['attribute_codes'];
            $products = array($productId);

            $this->_updateProducts($products, $attributes);
            $this->_completePreProcess();
        }

        return $this;
    }

    /**
     * Process Catalog Product mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     */
    public function catalogProductMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        /**
         * check is product ids were updated
         */
        if (!isset($data['product_ids']) || !isset($data['attributes'])) {
            return $this;
        }

        $products = $data['product_ids'];
        $attributes = $data['attributes'];

        $this->_hasPreprocess = false;

        $this->_updateProducts($products, $attributes);
        $this->_completePreProcess();

        return $this;
    }

    protected function _updateProducts($products, $attributes)
    {
        $visibility = $this->_getVisibilityAttributeInfo();

        $excludeAttribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, self::EXCLUDE_ATTRIBUTE);
        $weightAttribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, self::WEIGHT_ATTRIBUTE);

        foreach ($attributes as $attributeCode) {
            $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
            $multiSelect = $attribute->getFrontend()->getInputType() == 'multiselect';

            $select = $this->_getWriteAdapter()->select();

            $select->from(array('product' => $this->_productTable))
                ->joinLeft(array('attribute' => $attribute->getBackendTable()), 'product.entity_id = attribute.entity_id AND attribute.attribute_id = ' . $attribute->getAttributeId())
                ->joinLeft(array('visibility' => $visibility['table']), 'product.entity_id = visibility.entity_id AND visibility.attribute_id = ' . $visibility['id'])
                ->joinLeft(array('exclude' => $excludeAttribute->getBackendTable()), sprintf('product.entity_id = exclude.entity_id AND exclude.attribute_id = %s', $excludeAttribute->getAttributeId()))
                ->joinLeft(array('weight' => $weightAttribute->getBackendTable()), sprintf('product.entity_id = weight.entity_id AND weight.attribute_id = %s', $weightAttribute->getAttributeId()))
                ->where('product.entity_id IN (?)', implode(',', $products))
                ->where(new Zend_Db_Expr('exclude.value NOT LIKE ? OR exclude.value IS NULL'), "%" . $attribute->getAttributeCode() . "%")
                ->where('attribute.value > 0')
                ->reset($select::COLUMNS)
                ->columns(array(
                    new Zend_Db_Expr($attribute->getAttributeId() . ' AS attribute_id'),
                    'attribute.value as attribute_value',
                    'product.entity_id AS product_id',
                    new Zend_Db_Expr('IF(weight.value IS NOT NULL, weight.value, 0) AS position'),
                    'attribute.store_id AS store_id',
                    'visibility.value AS visibility'
                ));

            $table = $this->getMainTable();
            if ($multiSelect) {
                $table = $this->_preprocessTable;
                $this->_hasPreprocess = true;
            }

            $this->_getWriteAdapter()->delete($this->getMainTable(), array('product_id IN (?)' => implode(',', $products)));
            $insert = $this->_getWriteAdapter()->insertFromSelect($select, $table);
            $this->_getWriteAdapter()->query($insert);
        }

        return $this;
    }

    /**
     * This is the preprocessing function that takes the comma-delimited array values for a multi-select, explodes them
     * re-inserts them, deletes the comma-delimited values. Then, it moves these values over to the master table.
     *
     * Because the MySQL -> PHP translation is expensive for large operations, we are trying to mitigate as much as
     * possible by select ONLY the comma-delimited values. All values from a multiselect input are being inserted into
     * this table.
     */
    protected function _completePreprocess()
    {
        if (!$this->_hasPreprocess) {
            return $this;
        }

        try {
            $select = $this->_getWriteAdapter()->select();
            $select->from(array('table' => $this->_preprocessTable))
                ->where('attribute_value LIKE ?', '%,%');

            $results = $this->_getWriteAdapter()->fetchAll($select);

            $this->beginTransaction();
            foreach ($results as $result) {
                if (isset($result['attribute_value'])) {
                    $values = explode(',', $result['attribute_value']);
                    foreach ($values as $value) {
                        $insertValues = $result;
                        $insertValues['attribute_value'] = $value;

                        $this->_getWriteAdapter()->insert($this->getMainTable(), $insertValues);
                    }
                }
            }

            $this->commit();

            /**
             * Moving the non-comma'd values over to the main index table (that work was already done for the comma'd
             * ones in the previous foreach. We filter out the comma'd values and then truncate the table.
             */
            $select = $this->_getWriteAdapter()->select();
            $select->from($this->_preprocessTable)
                ->where('attribute_value NOT LIKE ?', '%,%');

            $insert = $this->_getWriteAdapter()->insertFromSelect($select, $this->getMainTable());
            $this->_getWriteAdapter()->query($insert);
            $this->_getWriteAdapter()->truncateTable($this->_preprocessTable);


        } catch(Exception $ex) {
            $this->rollBack();
        }

        return $this;
    }


    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Category_Indexer_Product
     * @throws Exception
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            // Truncating current table
            $this->_getWriteAdapter()->delete($this->getMainTable());

            $attributes = Mage::getModel('SwiftOtter_BrowseAttribute/Source_Attributes')->getBrowsableAttributes();
            $excludeAttribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, self::EXCLUDE_ATTRIBUTE);
            $weightAttribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, self::WEIGHT_ATTRIBUTE);

            $visibility = $this->_getVisibilityAttributeInfo();

            foreach ($attributes as $attributeCode) {
                $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
                $multiSelect = $attribute->getFrontend()->getInputType() == 'multiselect';

                $select = $this->_getWriteAdapter()->select();

                $select->from(array('attribute' => $attribute->getBackendTable()))
                    ->joinLeft(array('visibility' => $visibility['table']), 'attribute.entity_id = visibility.entity_id AND visibility.attribute_id = ' . $visibility['id'])
                    ->joinLeft(array('exclude' => $excludeAttribute->getBackendTable()), sprintf('attribute.entity_id = exclude.entity_id AND exclude.attribute_id = %s', $excludeAttribute->getAttributeId()))
                    ->joinLeft(array('weight' => $weightAttribute->getBackendTable()), sprintf('attribute.entity_id = weight.entity_id AND weight.attribute_id = %s', $weightAttribute->getAttributeId()))
                    ->where('attribute.attribute_id = ' . $attribute->getAttributeId())
                    ->where('attribute.value > 0')
                    ->where(new Zend_Db_Expr('exclude.value NOT LIKE ? OR exclude.value IS NULL'), "%" . $attribute->getAttributeCode() . "%")
                    ->reset($select::COLUMNS)
                    ->columns(array(
                        new Zend_Db_Expr($attribute->getAttributeId() . ' AS attribute_id'),
                        'attribute.value as attribute_value',
                        'attribute.entity_id AS product_id',
                        new Zend_Db_Expr('IF(weight.value IS NOT NULL, weight.value, 0) AS position'),
                        'attribute.store_id AS store_id',
                        'visibility.value AS visibility'
                    ));

                $table = $this->getMainTable();
                if ($multiSelect) {
                    $table = $this->_preprocessTable;
                    $this->_hasPreprocess = true;
                }

                $insert = $this->_getWriteAdapter()->insertFromSelect($select, $table);
                $this->_getWriteAdapter()->query($insert);
            }
            $this->commit();
            $this->_completePreprocess();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Get visibility product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getVisibilityAttributeInfo()
    {
        $visibilityAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'visibility');
        $info = array(
            'id'    => $visibilityAttribute->getId() ,
            'table' => $visibilityAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get status product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getStatusAttributeInfo()
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'status');
        $info = array(
            'id'    => $statusAttribute->getId() ,
            'table' => $statusAttribute->getBackend()->getTable()
        );
        return $info;
    }
}
