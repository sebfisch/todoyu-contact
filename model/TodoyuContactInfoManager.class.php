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
 * Manager class Todoyu for contact infos
 *
 * @package Todoyu
 * @subpackage user
 */
class TodoyuContactInfoManager {

	const TABLE = 'ext_contact_contactinfo';



	/**
	 * Returns the ContactInfo Object
	 *
	 * @param	Integer	$contactInfoID
	 * @return	TodoyuContactInfo
	 */
	public static function getContactInfo($contactInfoID)	{
		$contactInfoID	= intval($contactInfoID);

		return TodoyuCache::getRecord('TodoyuContactInfo', $contactInfoID);
	}



	/**
	 * Get name of given contact info type
	 *
	 * @param	Integer	$idContactinfotype
	 * @return	String
	 */
	public function getContactInfoTypeName($idContactinfotype) {
		$idContactinfotype = intval($idContactinfotype);

		$title = Todoyu::db()->getFieldValue('title', 'ext_contact_contactinfotype', 'id = ' . $idContactinfotype);

		return Label($title);
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
		unset($data['id']);

		$data['date_create']	= NOW;
		$data['id_user_create']	= personid();

		return Todoyu::db()->addRecord(self::TABLE, $data);
	}



	public static function updateContactinfo($idContactinfo, array $data) {
		$idContactinfo	= intval($idContactinfo);
		unset($data['id']);

		$data['date_update']	= NOW;

		return Todoyu::db()->updateRecord(self::TABLE, $idContactinfo, $data);
	}



	/**
	 * Creates a new contact info record in table ext_contact_contactinfo
	 *
	 * @return	Integer
	 */
	protected static function createNewContactInfoRecord()	{
		$insertArray = array(
			'date_create'		=> NOW,
			'id_user_create'	=> personid(),
			'deleted'			=> 0
		);

		return Todoyu::db()->doInsert(self::TABLE, $insertArray);
	}



	/**
	 * Removes record from cache
	 *
	 * @param	Integer		$idContactInfo
	 */
	protected static function removeFromCache($idContactInfo)	{
		$idContactInfo	= intval($idContactInfo);

		TodoyuCache::removeRecord('ContactInfo', $idContactInfo);
		TodoyuCache::removeRecordQuery(self::TABLE, $idContactInfo);
	}
}

?>