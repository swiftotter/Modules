<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/14/14
 * @package default
 **/

/**
 * This was a very unfortunate rewrite. _prepareForm is called during _beforeToHtml, so there seems to me no other way to
 * insert the multipart encoding data into the form. The only possible thing would be to watch for the beforeToHtml
 * event that is triggered (but that happens for every single block that is about to be sent to html, and that seems
 * burdensome on the system).
 *
 * Class SwiftOtter_BrowseAttribute_Block_Adminhtml_Override_Catalog_Product_Attribute_Edit_Form
 */
class SwiftOtter_BrowseAttribute_Block_Adminhtml_Override_Catalog_Product_Attribute_Edit_Form extends
    Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Form
{
    protected function _prepareForm()
    {
        $output = parent::_prepareForm();

        $this->getForm()->setEnctype('multipart/form-data');

        return $output;
    }
}