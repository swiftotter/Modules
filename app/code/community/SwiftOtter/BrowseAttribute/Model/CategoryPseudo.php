<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/9/14
 * @package default
 **/


/**
 * This is a interesting way of making use of Magento's code. The layered navigation only works on categories.
 * To make this work, I have made a derivative of the base category class and redefined the product collection
 * call. This allows the system to still work with a "category", yet not have to rewrite much code.
 *
 * Class SwiftOtter_BrowseAttribute_Model_CategoryPseudo
 */
class SwiftOtter_BrowseAttribute_Model_CategoryPseudo extends Mage_Catalog_Model_Category
{
    protected $_attribute;
    protected $_optionId;

    /**
     * This is the key function to make layered navigation work on browse by attribute
     * @return Mage_Eav_Model_Entity_Collection_Abstract|Varien_Data_Collection_Db
     */
    public function getProductCollection()
    {
        $indexTable = $this->getResource()->getTable('SwiftOtter_BrowseAttribute/Index');

        $condition = array(
            'attribute_id' => array('eq' => $this->_attribute->getAttributeId()),
            'attribute_value' => array('eq' => $this->_optionId),
            'visibility' => array('in' => array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
            )),
        );



        /*
         * The cat_index_position is included to get past the fact that this column's necessity is hard-coded into
         * the _renderOrders method in the resource collection.
         */
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->joinTable(
                array('product' => $indexTable),
                'product_id = entity_id',
                array('visibility', 'attribute_id', 'attribute_value', 'cat_index_position' => 'position'),
                $condition,
                'inner'
            )
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
//            ->addFieldToFilter('product.attribute_id', array('eq' => $this->_attribute->getAttributeId()))
//            ->addFieldToFilter('product.attribute_value', array('eq' => $this->_optionId));

        return $collection;
    }

    public function getId()
    {
        return 99999;
    }

    /**
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * @param int $optionId
     * @return $this
     */
    public function setOptionId($optionId)
    {
        $this->_optionId = $optionId;
        return $this;
    }

    /**
     * @return int
     */
    public function getOptionId()
    {
        return $this->_optionId;
    }


}