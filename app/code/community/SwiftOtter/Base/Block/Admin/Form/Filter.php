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
 * @copyright Swift Otter Studios, 3/14/13
 * @package default
 **/

class SwiftOtter_Base_Block_Admin_Form_Filter extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = Mage::getSingleton('adminhtml/session')->getData('filter_data');
        $filter = Mage::helper('SwiftOtter_Base')->getDateRange($this->getRegistryKey());

        $form = new Varien_Data_Form(array(
            'id' => 'filter_form',
            'method' => 'get',
            'enctype' => 'multipart/form-data',
            'class' => 'fieldset-wide'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('filter_form', array('legend' => $this->__('Report Filtering')));
        $fieldset->addField('custom_start', 'date', array(
            'label' => $this->__('Custom Start Date'),
            'required' => false,
            'name' => 'custom_start',
            'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));

        $fieldset->addField('custom_end', 'date', array(
            'label' => $this->__('Custom End Date'),
            'required' => false,
            'name' => 'custom_end',
            'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));

        $fieldset->addField('date_range', 'select', array(
            'label' => $this->__('Date Range'),
            'name'  => 'Date Range',
            'type' => 'options'
        ));

        $fieldset->addField('form_filter', 'hidden', array(
            'name'  => 'form_filter',
            'id'    => 'form_filter'
        ));

        $this->_addAdditionalFields($fieldset, $filter);

        $fieldset->addType('submit_button', 'SwiftOtter_Base_Block_Admin_Form_SubmitButton');

        $fieldset->addField('submit', 'submit_button', array(
            'id'    => 'filter_submit',
            'name' => 'Submit',
            'label' => $this->__('Update Report'),
            'onclick' => 'assembleAndSubmitRequest();'
        ));

        $params = $filter->getData();
        $filter->unsetFormFilter();

        $start = $filter->getRange()->getStart();
        $end = $filter->getRange()->getEnd();

        $params['custom_start'] = $start->format('Y-m-d');
        $params['custom_end'] = $end->format('Y-m-d');
        $params['form_filter'] = json_encode($filter->getData());

        $params = $this->_formatInputParams($params);

        $form->setValues($params);

        return parent::_prepareForm();
    }

    /**
     * Provides the opportunity for inheritors to add additional fields
     *
     * @param $fieldset
     * @return $this
     */
    protected function _addAdditionalFields($fieldset, $filter)
    {
        return $this;
    }

    protected function _formatInputParams($params)
    {
        return $params;
    }
}