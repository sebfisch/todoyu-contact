<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 snowflake productions gmbh
*  All rights reserved
*
*  This script is part of the todoyu project.
*  The todoyu project is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License, version 2,
*  (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html) as published by
*  the Free Software Foundation;
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Contactinfotype manager
 *
 * @package		Todoyu
 * @subpackage	User
 */
class TodoyuContactInfoTypeManager {

	const TABLE = 'ext_contact_contactinfotype';


	/**
	 * Get contact info type
	 *
	 * @param	Integer		$idContactInfoType
	 * @return	TodoyuContactInfoType
	 */
	public static function getContactInfoType($idContactInfoType) {
		$idContactInfoType	= intval($idContactInfoType);

		return TodoyuCache::getRecord('TodoyuContactInfoType', $idContactInfoType);
	}


	/**
	 * Get all contactinfotypes
	 *
	 * @param	Bool		$parseLabels
	 * @return	Array
	 */
	public static function getContactInfoTypes($parseLabels = true) {
		$fields	= '*';
		$table	= self::TABLE;
		$where	= 'deleted = 0';

		$types	= Todoyu::db()->getArray($fields, $table, $where);

		if( $parseLabels ) {
			foreach($types as $index => $type) {
				$types[$index]['title'] = TodoyuDiv::getLabel($type['title']);
			}
		}

		return $types;
	}



	/**
	 * Get list of existing contactinfotype records
	 *
	 * @return	Array
	 */
	public static function getRecords() {
		$contactInfoTypes = self::getContactInfoTypes(true);
		$reform		= array(
			'id'	=> 'id',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($contactInfoTypes, $reform);
	}



	public static function addContactInfoType(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}


	public static function updateContactInfoType($idContactTypeInfo, array $data) {
		TodoyuRecordManager::updateRecord(self::TABLE, $idContactTypeInfo, $data);
	}

	public static function deleteContactTypeInfo($idContactTypeInfo) {
		TodoyuRecordManager::deleteRecord(self::TABLE, $idContactTypeInfo);
	}


	public static function saveContactInfoType(array $data) {
		$idContactInfoType	= intval($data['id']);
		$xmlPath			= 'ext/contact/config/form/admin/contactinfotype.xml';

		if( $idContactInfoType === 0 ) {
			$idContactInfoType = self::addContactInfoType();
		}

		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idContactInfoType);

		self::updateContactInfoType($idContactInfoType, $data);

		return $idContactInfoType;
	}

}

?>