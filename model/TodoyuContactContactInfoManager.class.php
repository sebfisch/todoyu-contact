<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
class TodoyuContactContactInfoManager {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_contact_contactinfo';



	/**
	 * Get contactinfo object
	 *
	 * @param	Integer		$idContactInfo
	 * @return	TodoyuContactContactInfo
	 */
	public static function getContactinfo($idContactInfo) {
		$idContactInfo	= intval($idContactInfo);

		return TodoyuRecordManager::getRecord('TodoyuContactContactInfo', $idContactInfo);
	}



	/**
	 * Get name of given contact info type
	 *
	 * @param	Integer	$idContactInfoType
	 * @return	String
	 */
	public static function getContactInfoTypeName($idContactInfoType) {
		$idContactInfoType = intval($idContactInfoType);

		$label = Todoyu::db()->getFieldValue('title', 'ext_contact_contactinfotype', 'id = ' . $idContactInfoType);

		return Todoyu::Label($label);
	}



	/**
	 * Saves contact infos
	 *
	 * @param	Array	$data
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



	/**
	 * Add contactinfo record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addContactinfo(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update contactinfo record
	 *
	 * @param	Integer		$idContactinfo
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateContactinfo($idContactinfo, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idContactinfo, $data);
	}



	/**
	 * Creates a new contact info record in table ext_contact_contactinfo
	 *
	 * @return	Integer
	 */
	protected static function createNewContactInfoRecord() {
		$insertArray = array(
			'date_create'		=> NOW,
			'id_person_create'	=> Todoyu::personid(),
			'deleted'			=> 0
		);

		return Todoyu::db()->doInsert(self::TABLE, $insertArray);
	}



	/**
	 * Removes record from cache
	 *
	 * @param	Integer		$idContactInfo
	 */
	public static function removeFromCache($idContactInfo) {
		$idContactInfo	= intval($idContactInfo);

		TodoyuRecordManager::removeRecordCache('TodoyuContactContactInfo', $idContactInfo);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idContactInfo);
	}



	/**
	 * Delete contact informations which are linked over an mm-table.
	 * Deletes all except the given IDs
	 *
	 * @param	String		$mmTable					MM table
	 * @param	Integer		$idRecord					Record ID which is linked to a contact info
	 * @param	Array		$currentContactInfoIDs		Contact info IDs which should stay linked with the record
	 * @param	String		$fieldRecord				Field name for the record ID
	 * @param	String		$fieldInfo					Field name for the contact info ID
	 * @return	Integer		Number of deleted records
	 */
	public static function deleteLinkedContactInfos($mmTable, $idRecord, array $currentContactInfoIDs,  $fieldRecord, $fieldInfo = 'id_contactinfo') {
		return TodoyuDbHelper::deleteOtherMmRecords($mmTable, 'ext_contact_contactinfo', $idRecord, $currentContactInfoIDs, $fieldRecord, $fieldInfo);
	}



	/**
	 * Get contact infos of given person
	 *
	 * @param	Integer		$idPerson
	 * @param	Integer		$category
	 * @param	String		$type
	 * @param	Boolean		$onlyPreferred
	 * @return	Array
	 */
	public static function getContactInfos($idPerson, $category = null, $type = null, $onlyPreferred = false) {
		$idPerson	= intval($idPerson);

		$fields	= '	ci.*,
					cit.key,
					cit.title';
		$tables	= '	ext_contact_contactinfo ci,
					ext_contact_contactinfotype cit,
					ext_contact_mm_person_contactinfo mm';
		$where	= '		mm.id_person			= ' . $idPerson .
				  ' AND	mm.id_contactinfo	= ci.id
				  	AND	ci.id_contactinfotype = cit.id';
		$order	= '	ci.is_preferred DESC,
					ci.id_contactinfotype ASC';

		if( $onlyPreferred ) {
			$where .= ' AND ci.is_preferred = 1';
		}

		if( ! is_null($category) ) {
			$where .= ' AND cit.category = ' . intval($category);
		}

		if( ! is_null($type) ) {
			$where .= ' AND cit.key LIKE \'%' . Todoyu::db()->escape($type) . '%\'';
		}

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get email addresses of given types of given person
	 *
	 * @param	Integer			$idPerson
	 * @param	String|Null		$type
	 * @param	Boolean|Null	$onlyPreferred
	 * @return	Array
	 */
	public static function getEmails($idPerson, $type = null, $onlyPreferred = false) {
		return self::getContactInfos($idPerson, CONTACT_INFOTYPE_CATEGORY_EMAIL, $type, $onlyPreferred);
	}



	/**
	 * Get phone numbers of given types of given person
	 *
	 * @param	Integer			$idPerson
	 * @param	String|Null		$type
	 * @param	Boolean|Null	$onlyPreferred
	 * @return	Array
	 */
	public static function getPhones($idPerson, $type = null, $onlyPreferred = false) {
		return self::getContactInfos($idPerson, CONTACT_INFOTYPE_CATEGORY_PHONE, $type, $onlyPreferred);
	}



	/**
	 * Get preferred email of a person
	 * First check system email, than check "contactinfo" records. Look for preferred emails
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPreferredEmail($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		$email		= $person->getEmails(true);

		$email		= $person->getEmail();

		if( empty($email) ) {
			$contactEmails	= TodoyuContactContactInfoManager::getContactInfos($idPerson, CONTACT_INFOTYPE_CATEGORY_EMAIL);

			if( sizeof($contactEmails) > 0 ) {
				$email = $contactEmails[0]['info'];
			}
		}

		return $email;
	}

}

?>