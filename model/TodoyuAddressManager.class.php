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
 * Address Manager
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuAddressManager {

	/**
	 * Ext DB table
	 *
	 */
	const TABLE = 'ext_contact_address';



	/**
	 * Return the requested Address object
	 *
	 * @param	Integer		$idAddress
	 * @return	TodoyuAddress
	 */
	public static function getAddress($idAddress)	{
		$idAddress	= intval($idAddress);

		return TodoyuCache::getRecord('TodoyuAddress', $idAddress);
	}



	/**
	 * Save the address record to table ext_contact_address
	 * returns the id of the saved record
	 *
	 * @param	Array	$addressData
	 * @return	Integer
	 */
	public static function saveAddress(array $data)	{
		$idAddress	= intval($data['id']);

		if( $idAddress === 0 ) {
			$idAddress = self::addAddress();
		}

		self::updateAddress($idAddress, $data);

		self::removeFromCache($idAddress);

		return $idAddress;
	}

	public static function addAddress(array $data = array()) {
		unset($data['id']);

		$data['id_person_create']	= personid();
		$data['date_create']	= NOW;

		return Todoyu::db()->addRecord(self::TABLE, $data);
	}

	public static function updateAddress($idAddress, array $data) {
		$idAddress	= intval($idAddress);
		unset($data['id']);

		$data['date_update']	= NOW;

		return Todoyu::db()->updateRecord(self::TABLE, $idAddress, $data);
	}


	public static function deleteAddress($idAddress) {
		$update	= array(
			'deleted'	=> 1
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idAddress, $update);
	}


	/**
	 * Remove record from cache
	 *
	 * @param	Integer	$addressID
	 */
	protected static function removeFromCache($idAddress)	{
		$idAddress	= intval($idAddress);

		TodoyuCache::removeRecord('TodoyuAddress', $idAddress);
		TodoyuCache::removeRecordQuery(self::TABLE, $idAddress);
	}



	/**
	 * Get all addresses of given company
	 *
	 * @param	Integer	$companyID
	 * @param	String	$addressFields		e.g 'ext_contact_address.*' to get all fields, otherwise comma separated field names
	 * @return	Array
	 */
	public static function getCompanyAddresses($idCompany, $addressFields = '', $orderBy = 'city') {
		$idCompany	= intval($idCompany);

		$fields		= '	ext_contact_mm_company_address.*,
						' .($addressFields != '' ? $addressFields : 'ext_contact_address.*') .',
						ext_contact_address.id addrId';
		$tables		= '	ext_contact_mm_company_address,
						ext_contact_address ';
		$where		= ' ext_contact_mm_company_address.id_company = ' . $idCompany . ' AND
						ext_contact_address.id = ext_contact_mm_company_address.id_address';
		$orderBy	= 'city';
		$groupBy	= $limit	= '';
		$indexField	= 'addrId';

		return Todoyu::db()->getArray($fields, $tables, $where,  $groupBy, $orderBy, $limit, $indexField);
	}



	/**
	 * Get label for addresstype
	 *
	 * @param	Integer		$idAddresstype
	 * @return	String
	 */
	public static function getAddresstypeLabel($idAddresstype) {
		$idAddresstype	= intval($idAddresstype);

		return TodoyuLanguage::getLabel('LLL:contact.address.attr.addresstype.' . $idAddresstype);
	}



	/**
	 * Get the IDs of the most used countries in the address records
	 *
	 * @return	Array
	 */
	public static function getMostUsedCountryIDs() {
		$field	= 'id_country';
		$table	= self::TABLE;
		$where	= '';
		$group	= 'id_country';
		$order	= 'COUNT(*) DESC';
		$limit	= intval($GLOBALS['CONFIG']['EXT']['contact']['numFavoriteCountries']);

		return Todoyu::db()->getColumn($field, $table, $where, $group, $order, $limit);
	}


	public static function deleteLinkedAddresses($mmTable, $idRecord, array $currentAddressIDs,  $fieldRecord, $fieldInfo = 'id_address') {
		return TodoyuDbHelper::deleteOtherMmRecords($mmTable, 'ext_contact_address', $idRecord, $currentAddressIDs, $fieldRecord, $fieldInfo);
	}

}

?>