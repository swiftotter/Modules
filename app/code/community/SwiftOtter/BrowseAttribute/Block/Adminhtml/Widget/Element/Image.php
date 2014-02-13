<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/14/14
 * @package default
 **/


class SwiftOtter_BrowseAttribute_Block_Adminhtml_Widget_Element_Image extends Mage_Adminhtml_Block_Widget
{

    protected function _toHtml()
    {
        $params = array(
            'value' => '{{image_file}}',
            'name' => $this->getName(),
            'label' => $this->getLabel()
        );

        $image = new Varien_Data_Form_Element_Image($params);
        $image->setForm(new Varien_Data_Form());
        $html = trim($image->getElementHtml());

        $html = str_replace("'", "\\'", $html);
        $html = str_replace("\n", '', $html);
        $html = str_replace(Mage::getBaseUrl('media'), '', $html);

        return $html;
    }
}