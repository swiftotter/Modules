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
 * @copyright Swift Otter Studios, 1/31/14
 * @package default
 **/
 
class SwiftOtter_Attribute_Model_Resource_Category_Exclusion extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('SwiftOtter_Attribute/Category_Exclusion', 'id');
    }

	public function processInput($attributeId, $exclusions)
	{
		if (is_array($exclusions)) {
			$currentSelect = $this->_getReadAdapter()->select();
			$currentSelect->from($this->getMainTable())
				->where('attribute_id=?', $attributeId);

			$currentResults = $this->_getReadAdapter()->fetchAll($currentSelect);

			$delete = array();
			$insert = array();

			foreach($currentResults as $current) {
				// if the database entry is not found in the input
				if (!in_array($current['category_id'], $exclusions)) {
					$delete[] = $current['id'];
				}
			}

			foreach ($exclusions as $exclusion) {
				$exists = false;
				foreach ($currentResults as $current) {
					if ($current['category_id'] == $exclusion) {
						$exists = true;
					}
				}

				// adding in a new exclusion
				if (!$exists) {
					$insert[] = $exclusion;
				}
			}

			if (count($delete) || count($insert)) {
				$this->beginTransaction();

				foreach ($delete as $id) {
					$this->_getWriteAdapter()->delete($this->getMainTable(), $id);
				}

				foreach ($insert as $categoryId) {
					$this->_getWriteAdapter()->insert($this->getMainTable(), array(
						'attribute_id' => $attributeId,
						'category_id' => $categoryId
					));
				}

				$this->commit();
			}
		}
	}
}