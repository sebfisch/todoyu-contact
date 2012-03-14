<?php
/****************************************************************************
 * todoyu is published under the BSD License:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * Copyright (c) 2012, snowflake productions GmbH, Switzerland
 * All rights reserved.
 *
 * This script is part of the todoyu project.
 * The todoyu project is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
 * for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script.
 *****************************************************************************/

/**
 * Person implementation for record selector
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactFormElement_RecordsStaff extends TodoyuFormElement_Records {

	/**
	 * Init the object with special person config
	 */
	protected function init() {
		$this->initRecords('staff', 'contact', 'person', 'staffList');
	}



	/**
	 * Get record data
	 *
	 * @return	Array
	 */
	protected function getRecords() {
		$personIDs	= $this->getValue();
		$records	= array();

		foreach($personIDs as $idPerson) {
			$records[] = array(
				'id'	=> $idPerson,
				'label'	=> TodoyuContactPersonManager::getLabel($idPerson)
			);
		}

		return $records;
	}



	/**
	 * Set value
	 * Fix data if value contains whole user records instead of IDs
	 *
	 * @param	Array		$value
	 */
	public function setValue($value) {
		$first	= reset($value);
		if( is_array($first) ) {
			$value	= TodoyuArray::getColumn($value, 'id');
		}

		parent::setValue($value);
	}

}

?>