<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/8/14
 * @package default
 **/

/**
 * As much as I greatly dislike having to rewrite classes, this one was necessary IMO. We have to use our own template,
 * add an image uploader (which could easily be done in a helper), and add our new columns into the attribute
 * output setup.
 *
 * Class SwiftOtter_BrowseAttribute_Block_Adminhtml_Override_Catalog_Product_Attribute_Edit_Tab_Options
 */
class SwiftOtter_BrowseAttribute_Block_Adminhtml_Override_Catalog_Product_Attribute_Edit_Tab_Options
    extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('SwiftOtter/BrowseAttribute/catalog_product_attribute_options.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('image_uploader',
            $this->getLayout()->createBlock('SwiftOtter_BrowseAttribute/Adminhtml_Widget_Element_Image')
                ->setData(array(
                    'label' => Mage::helper('SwiftOtter_BrowseAttribute')->__('Add Image'),
                    'name'  => 'option[image_{{id}}]'
                )));

        return parent::_prepareLayout();
    }

    public function getImageUploaderHtml()
    {
        return $this->getChild('image_uploader')->toHtml();
    }

    /**
     * Retrieve attribute option values if attribute input type select or multiselect
     *
     * @return array
     */
    public function getOptionValues()
    {
        $attributeType = $this->getAttributeObject()->getFrontendInput();
        $defaultValues = $this->getAttributeObject()->getDefaultValue();
        if ($attributeType == 'select' || $attributeType == 'multiselect') {
            $defaultValues = explode(',', $defaultValues);
        } else {
            $defaultValues = array();
        }

        switch ($attributeType) {
            case 'select':
                $inputType = 'radio';
                break;
            case 'multiselect':
                $inputType = 'checkbox';
                break;
            default:
                $inputType = '';
                break;
        }

        $values = $this->getData('option_values');
        if (is_null($values)) {
            $values = array();
            $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttributeObject()->getId())
//                ->addFieldToSelect('exclude_browse_by')
                ->setPositionOrder('desc', true)
                ->load();

            foreach ($optionCollection as $option) {
                $value = array();
                if (in_array($option->getId(), $defaultValues)) {
                    $value['checked'] = 'checked="checked"';
                } else {
                    $value['checked'] = '';
                }

                //* BEGIN INSERTED CODE *//

                if ($option->getExcludeBrowseBy()) {
                    $value['exclude_browse_by'] = 'checked="checked"';
                } else {
                    $value['exclude_browse_by'] = '';
                }

                $value['image_file'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $option->getImagePath();

                //* END INSERTED CODE *//

                $value['intype'] = $inputType;
                $value['id'] = $option->getId();
                $value['sort_order'] = $option->getSortOrder();
                foreach ($this->getStores() as $store) {
                    $storeValues = $this->getStoreOptionValues($store->getId());
                    if (isset($storeValues[$option->getId()])) {
                        $value['store'.$store->getId()] = htmlspecialchars($storeValues[$option->getId()]);
                    }
                    else {
                        $value['store'.$store->getId()] = '';
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->setData('option_values', $values);
        }

        return $values;
    }
}