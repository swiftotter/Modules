<?php
/**
 *
 *
 * @author Joseph Maxwell
 * @copyright Swift Otter Studios, 1/8/14
 * @package default
 **/

class SwiftOtter_BrowseAttribute_Model_Resource_Override_Catalog_Attribute extends Mage_Catalog_Model_Resource_Attribute
{
    /**
     *  Save attribute options
     *
     * @param Mage_Eav_Model_Entity_Attribute $object
     * @return Mage_Eav_Model_Resource_Entity_Attribute
     */
    protected function _saveOption(Mage_Core_Model_Abstract $object)
    {


        $option = $object->getOption();
        if (is_array($option)) {
            $adapter            = $this->_getWriteAdapter();
            $optionTable        = $this->getTable('eav/attribute_option');
            $optionValueTable   = $this->getTable('eav/attribute_option_value');

            $stores = Mage::app()->getStores(true);
            if (isset($option['value'])) {
                $attributeDefaultValue = array();
                if (!is_array($object->getDefault())) {
                    $object->setDefault(array());
                }

                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $adapter->delete($optionTable, array('option_id = ?' => $intOptionId));
                        }
                        continue;
                    }

                    $sortOrder = !empty($option['order'][$optionId]) ? $option['order'][$optionId] : 0;
                    $excludeBrowseBy = !empty($option['exclude_browse_by'][$optionId]);
                    $imagePath = '';
                    $updateImagePath = isset($option['image_'.$optionId]['delete']);

                    $fileOptionId = 'image_' . $optionId;

                    if (isset($_FILES) && isset($_FILES['option']['name'][$fileOptionId])
                        && $_FILES['option']['name'][$fileOptionId]) {
                        try {
                            $fileName = $_FILES['option']['name'][$fileOptionId];
                            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                            $uploader = new Varien_File_Uploader('option[' . $fileOptionId . ']');
                            $uploader->setAllowRenameFiles(false);
                            $uploader->setFilesDispersion(false);

                            $path = 'catalog'.DS.'attribute';
                            $outputFileName = $optionId.'.'.$fileExtension;

                            $uploader->save(Mage::getBaseDir('media').DS.$path, $outputFileName);

                            $imagePath = $path .DS. $outputFileName;

                            $updateImagePath = true;
                        } catch (Exception $e) {
                            Mage::getSingleton('admin/session')->addError($e->getMessage());
                        }
                    }

                    if (!$intOptionId) {
                        $data = array(
                            'attribute_id'  => $object->getId(),
                            'sort_order'    => $sortOrder,
                            'exclude_browse_by' => $excludeBrowseBy
                        );

                        if ($updateImagePath) {
                            $data['image_path'] = $imagePath;
                        }

                        $adapter->insert($optionTable, $data);
                        $intOptionId = $adapter->lastInsertId($optionTable);
                    } else {
                        $data  = array(
                            'sort_order' => $sortOrder,
                            'exclude_browse_by' => $excludeBrowseBy
                        );

                        if ($updateImagePath) {
                            $data['image_path'] = $imagePath;
                        }

                        $where = array('option_id =?' => $intOptionId);
                        $adapter->update($optionTable, $data, $where);
                    }

                    if (in_array($optionId, $object->getDefault())) {
                        if ($object->getFrontendInput() == 'multiselect') {
                            $attributeDefaultValue[] = $intOptionId;
                        } elseif ($object->getFrontendInput() == 'select') {
                            $attributeDefaultValue = array($intOptionId);
                        }
                    }

                    // Default value
                    if (!isset($values[0])) {
                        Mage::throwException(Mage::helper('eav')->__('Default option value is not defined'));
                    }

                    $adapter->delete($optionValueTable, array('option_id =?' => $intOptionId));
                    foreach ($stores as $store) {
                        if (isset($values[$store->getId()])
                            && (!empty($values[$store->getId()])
                                || $values[$store->getId()] == "0")
                        ) {
                            $data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()]
                            );
                            $adapter->insert($optionValueTable, $data);
                        }
                    }
                }
                $bind  = array('default_value' => implode(',', $attributeDefaultValue));
                $where = array('attribute_id =?' => $object->getId());
                $adapter->update($this->getMainTable(), $bind, $where);
            }
        }

        return $this;
    }
}