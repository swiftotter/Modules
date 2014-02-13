<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 2/26/13
 * @package default
 **/

class SwiftOtter_Attribute_Model_Observer
{
	public function controllerActionPredispatchAdminhtmlCatalogProductAttributeSave($observer)
	{
		$request = Mage::app()->getRequest();
		$rawExclusions = $request->getParam('category_exclusions');

		$attributeId = $request->getParam('attribute_id');

		$exclusions = json_decode($rawExclusions);

		if ($attributeId && $rawExclusions && is_array($exclusions)) {
			Mage::getResourceModel('SwiftOtter_Attribute/Category_Exclusion')->processInput($attributeId, $exclusions);
		}
	}

	public function adminhtmlCatalogProductAttributeEditPrepareForm($observer)
	{
		/* @var $form Varien_Data_Form */
		$form = $observer->getForm();
		/* @var $fieldset Varien_Data_Form_Element_Fieldset */
		$fieldset = $form->getElement('front_fieldset');

		$fieldset->addField('explanatory_note', 'text', array(
			'name' => 'explanatory_note',
			'label' => Mage::helper('catalog')->__('Explanatory Note'),
			'title' => Mage::helper('catalog')->__('Explanatory Note'),
			'note' => Mage::helper('catalog')->__('Put any extra text you want in here in reference to sizing. Urls are to be relative to the domain name.')
		));

        $fieldset->addField('tab', 'select', array(
            'name' => 'tab',
            'type' => 'options',
            'options' => Mage::getSingleton('SwiftOtter_Attribute/Source_Tab')->getAllOptions(),
            'label' => Mage::helper('catalog')->__('Display on Tab'),
            'title' => Mage::helper('catalog')->__('Display on Tab'),
            'note' => Mage::helper('catalog')->__('(requires that show on frontend is enabled)')
        ));
	}
}