<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
 * Manager class Todoyu for contact infos
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactInfoManager {

	/**
	 * Default table for database requests
	 */
	const TABLE = 'ext_contact_contactinfo';



	/**
	 * Get contactinfo object
	 *
	 * @param	Integer		$idContactInfo
	 * @return	TodoyuContactInfo
	 */
	public static function getContactinfo($idContactInfo)	{
		$idContactInfo	= intval($idContactInfo);

		return TodoyuRecordManager::getRecord('TodoyuContactInfo', $idContactInfo);
	}



	/**
	 * Get name of given contact info type
	 *
	 * @param	Integer	$idContactinfotype
	 * @return	String
	 */
	public function getContactInfoTypeName($idContactInfoType) {
		$idContactInfoType = intval($idContactInfoType);

		$label = Todoyu::db()->getFieldValue('title', 'ext_contact_contactinfotype', 'id = ' . $idContactInfoType);

		return TodoyuString::getLabel($label);
	}



	/**
	 * Saves contact infos
	 *
	 * @param	Array	$contactInfoData
	 * @return	Integer
	 */
	public static function saveContactInfos(array $data) {
		$idContactinfo	= intval($data['id']);

		if( $idContactinfo === 0 ) {
			$idContactinfo = self::addContactinfo();
		}

		// Add form save handler here

		self::updateContactinfo($idContactinfo, $data);

		self::removeFromCache($idContactinfo);

		return $idContactinfo;
	}



	public static function addContactinfo(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	public static function updateContactinfo($idContactinfo, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idContactinfo, $data);
	}



	/**
	 * Creates a new contact info record in table ext_contact_contactinfo
	 *
	 * @return	Integer
	 */
	protected static function createNewContactInfoRecord()	{
		$insertArray = array(
			'date_create'		=> NOW,
			'id_person_create'	=> personid(),
			'deleted'			=> 0
		);

		return Todoyu::db()->doInsert(self::TABLE, $insertArray);
	}



	/**
	 * Removes record from cache
	 *
	 * @param	Integer		$idContactInfo
	 */
	public static function removeFromCache($idContactInfo)	{
		$idContactInfo	= intval($idContactInfo);

		TodoyuRecordManager::removeRecordCache('TodoyuContactInfo', $idContactInfo);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idContactInfo);
	}



	/**
	 * Delete contact informations which are linked over an mm-table.
	 * Deletes all except the given IDs
	 *
	 * @param	String		$mmTable					MM table
	 * @param	Integer		$idRecord					Record ID which is linked to a contact info
	 * @param	Array		$currentContactInfoIDs		Contactinfo IDs which should stay linked with the record
	 * @param	String		$fieldRecord				Fieldname for the record ID
	 * @param	String		$fieldInfo					Fieldname for the contactinfo ID
	 * @return	Integer		Number of deleted records
	 */
	public static function deleteLinkedContactInfos($mmTable, $idRecord, array $currentContactInfoIDs,  $fieldRecord, $fieldInfo = 'id_contactinfo') {
		return TodoyuDbHelper::deleteOtherMmRecords($mmTable, 'ext_contact_contactinfo', $idRecord, $currentContactInfoIDs, $fieldRecord, $fieldInfo);
	}

}

?>