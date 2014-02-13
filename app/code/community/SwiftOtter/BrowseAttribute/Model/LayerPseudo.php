<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/14/14
 * @package default
 **/

/**
 * Another way of keeping from using a rewrite.
 *
 * Class SwiftOtter_BrowseAttribute_Model_LayerPseudo
 */
class SwiftOtter_BrowseAttribute_Model_LayerPseudo extends Mage_Catalog_Model_Layer
{
    /**
     * Initialize product collection
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId());

//        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
//        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }
}