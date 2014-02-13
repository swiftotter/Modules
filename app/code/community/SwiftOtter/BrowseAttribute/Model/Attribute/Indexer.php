<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/8/14
 * @package default
 **/

/**
 * Browse by attribute products indexer model.
 * Responsibility for system actions:
 *  - Product save (changed assigned categories list)
 *  - Category save (changed assigned products list or category move)
 *  - Store save (new store creation, changed store group) - require reindex all data
 *  - Store group save (changed root category or group website) - require reindex all data
 *
 * @method Mage_Catalog_Model_Resource_Category_Indexer_Product _getResource()
 * @method Mage_Catalog_Model_Resource_Category_Indexer_Product getResource()
 * @method int getCategoryId()
 * @method Mage_Catalog_Model_Category_Indexer_Product setCategoryId(int $value)
 * @method int getProductId()
 * @method Mage_Catalog_Model_Category_Indexer_Product setProductId(int $value)
 * @method int getPosition()
 * @method Mage_Catalog_Model_Category_Indexer_Product setPosition(int $value)
 * @method int getIsParent()
 * @method Mage_Catalog_Model_Category_Indexer_Product setIsParent(int $value)
 * @method int getStoreId()
 * @method Mage_Catalog_Model_Category_Indexer_Product setStoreId(int $value)
 * @method int getVisibility()
 * @method Mage_Catalog_Model_Category_Indexer_Product setVisibility(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SwiftOtter_BrowseAttribute_Model_Attribute_Indexer extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'catalog_category_product_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
        ),
        Mage_Catalog_Model_Convert_Adapter_Product::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('SwiftOtter_BrowseAttribute/Attribute_Indexer');
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('catalog')->__('Browse By Attribute Products');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('catalog')->__('Indexed attribute value/products association');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data      = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Mage_Catalog_Model_Product::ENTITY) {
            $this->_registerProductEvent($event);
        } else if ($entity == Mage_Catalog_Model_Resource_Eav_Attribute::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_SAVE:
                    $this->_registerCatalogAttributeSaveEvent($event);
                    break;
            }
        } else if ($entity == Mage_Catalog_Model_Convert_Adapter_Product::ENTITY) {
            $event->addNewData('catalog_product_eav_reindex_all', true);
        }



        if ($entity == Mage_Core_Model_Store::ENTITY) {
            $store = $event->getDataObject();
            if ($store && ($store->isObjectNew() || $store->dataHasChangedFor('group_id'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                    || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }


    /**
     * Register data required by process in event object
     * Check if category ids was changed
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        $entity = $event->getEntity();
        switch ($entity) {
            case Mage_Catalog_Model_Product::ENTITY:
                $this->_registerProductEvent($event);
                break;

            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;

            case Mage_Catalog_Model_Convert_Adapter_Product::ENTITY:
                $event->addNewData('attribute_product_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        return $this;
    }

    /**
     * Register event data during product save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerProductEvent(Mage_Index_Model_Event $event)
    {
        $eventType = $event->getType();
        $attributes = Mage::getModel('SwiftOtter_BrowseAttribute/Source_Attributes')->getBrowsableAttributes();

        if ($eventType == Mage_Index_Model_Event::TYPE_SAVE) {
            $product = $event->getDataObject();
            /**
             * Check if product categories data was changed
             */

            $dataChanges = array();
            foreach ($attributes as $attributeCode) {
                if ($product->dataHasChangedFor($attributeCode)) {
                    $dataChanges[] = $attributeCode;
                }
            }

            if (count($dataChanges)) {
                $event->addNewData('attribute_codes', $dataChanges);
            }
        } else if ($eventType == Mage_Index_Model_Event::TYPE_MASS_ACTION) {
            /* @var $actionObject Varien_Object */
            $actionObject = $event->getDataObject();
            $rebuildIndex = false;

            // check if attributes changed
            $attrData = $actionObject->getAttributesData();
            $attributesChanged = array();

            if (is_array($attrData)) {
                foreach ($attributes as $attributeCode) {
                    if (array_key_exists($attributeCode, $attrData)) {
                        $rebuildIndex = true;
                        $attributesChanged[] = $attributeCode;
                    }
                }
            }

            // check changed websites
            if ($actionObject->getWebsiteIds()) {
                $rebuildIndex = true;
            }

            // register affected products
            if ($rebuildIndex) {
                $event->addNewData('attributes', $attributesChanged);
                $event->addNewData('product_ids', $actionObject->getProductIds());
            }
        }
    }

    /**
     * Register data required by process attribute save in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Catalog_Model_Product_Indexer_Eav
     */
    protected function _registerCatalogAttributeSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $event->getDataObject();
        if ($attribute->isIndexable()) {
            $before = $attribute->getOrigData('is_filterable')
                || $attribute->getOrigData('is_filterable_in_search')
                || $attribute->getOrigData('is_visible_in_advanced_search');
            $after  = $attribute->getData('is_filterable')
                || $attribute->getData('is_filterable_in_search')
                || $attribute->getData('is_visible_in_advanced_search');

            if (!$before && $after || $before && !$after) {
                $event->addNewData('attribute', $attribute->getAttributeId());
                $event->addNewData('reindex_attribute', 1);
                $event->addNewData('attribute_index_type', $attribute->getIndexType());
                $event->addNewData('is_indexable', $after);
            }
        }

        return $this;
    }

    /**
     * Process event data and save to index
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['attribute_product_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['catalog_category_product_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}

 