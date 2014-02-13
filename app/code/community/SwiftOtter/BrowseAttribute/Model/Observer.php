<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/8/14
 * @package default
 **/

class SwiftOtter_BrowseAttribute_Model_Observer
{
    /**
     * Inserting the allow browse by field into the attribute edit form
     *
     * @param $observer
     */
    public function adminhtmlCatalogProductAttributeEditPrepareForm($observer)
    {
        /* @var $form Varien_Data_Form */
        $form = $observer->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('front_fieldset');

        $fieldset->addField('allow_browse_by', 'select', array(
            'name' => 'allow_browse_by',
            'type' => 'options',
            'options' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
            'label' => Mage::helper('catalog')->__('Allow Browse By'),
            'title' => Mage::helper('catalog')->__('Allow Browse By')
        ));
    }
}