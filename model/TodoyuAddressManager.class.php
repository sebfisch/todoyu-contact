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
 * Address Manager
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuAddressManager {

	/**
	 * @var	String		Ext DB table
	 */
	const TABLE = 'ext_contact_address';



	/**
	 * Return the requested Address object
	 *
	 * @param	Integer			$idAddress
	 * @return	TodoyuAddress
	 */
	public static function getAddress($idAddress) {
		$idAddress	= intval($idAddress);

		return TodoyuRecordManager::getRecord('TodoyuAddress', $idAddress);
	}



	/**
	 * Save the address record to table ext_contact_address
	 * returns the id of the saved record
	 *
	 * @param	Array	$addressData
	 * @return	Integer
	 */
	public static function saveAddress(array $data) {
		$idAddress	= intval($data['id']);

		if( $idAddress === 0 ) {
			$idAddress = self::addAddress();
		}

		self::updateAddress($idAddress, $data);

		self::removeFromCache($idAddress);

		return $idAddress;
	}



	/**
	 * Add address record to DB
	 *
	 * @param	Array	$data
	 * @return	Integer
	 */
	public static function addAddress(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update address record in DB
	 *
	 * @param	Integer		$idAddress
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updateAddress($idAddress, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idAddress, $data);
	}



	/**
	 * Set given address record in DB deleted
	 *
	 * @param	Integer		$idAddress
	 */
	public static function deleteAddress($idAddress) {
		$update	= array(
			'deleted'	=> 1
		);

		self::updateAddress($idAddress, $update);
	}



	/**
	 * Remove record from cache
	 *
	 * @param	Integer	$idAddress
	 */
	protected static function removeFromCache($idAddress) {
		$idAddress	= intval($idAddress);

		TodoyuRecordManager::removeRecordCache('TodoyuAddress', $idAddress);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idAddress);
	}



	/**
	 * Get all addresses of given company
	 *
	 * @param	Integer	$idCompany
	 * @param	String	$addressFields		e.g 'ext_contact_address.*' to get all fields, otherwise comma separated field names
	 * @param	String	$orderBy
	 * @return	Array
	 */
	public static function getCompanyAddresses($idCompany, $addressFields = '', $orderBy = 'city') {
		$idCompany	= intval($idCompany);

		$fields		= '	ext_contact_mm_company_address.*,
						' . ( $addressFields != '' ? $addressFields : 'ext_contact_address.*' ) . ',
						ext_contact_address.id addrId';
		$tables		= '	ext_contact_mm_company_address,
						ext_contact_address ';
		$where		= '		ext_contact_mm_company_address.id_company	= ' . $idCompany .
					  ' AND	ext_contact_address.id 						= ext_contact_mm_company_address.id_address';
		$orderBy	= 'city';
		$groupBy	= $limit	= '';
		$indexField	= 'addrId';

		return Todoyu::db()->getArray($fields, $tables, $where,  $groupBy, $orderBy, $limit, $indexField);
	}



	/**
	 * Get label for address type
	 *
	 * @param	Integer		$idAddressType
	 * @return	String
	 */
	public static function getAddresstypeLabel($idAddressType) {
		$idAddressType	= intval($idAddressType);

		return TodoyuLabelManager::getLabel('LLL:contact.address.attr.addresstype.' . $idAddressType);
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
		$limit	= intval(Todoyu::$CONFIG['EXT']['contact']['numFavoriteCountries']);

		return Todoyu::db()->getColumn($field, $table, $where, $group, $order, $limit);
	}



	/**
	 * Get label of given address
	 *
	 * @return String
	 */
	public static function getLabel($idAddress) {
		$idAddress	= intval($idAddress);

		if( $idAddress > 0 ) {
			$address= self::getAddress($idAddress);
			$countryLabel	= TodoyuStaticRecords::getCountryLabel($address['id_country']);
			$label			= $address['street'] . ', ' . $address['zip'] . ', ' . $address['city'] . ', ' . $countryLabel;
		} else {
			$label	= '';
		}

		return $label;
	}



	/**
	 * Delete linked addresses
	 *
	 * @param	String		$mmTable
	 * @param	Integer		$idRecord
	 * @param	Array		$currentAddressIDs
	 * @param	String		$fieldRecord
	 * @param	String 		$fieldInfo
	 * @return	Integer							Amount of deleted records
	 */
	public static function deleteLinkedAddresses($mmTable, $idRecord, array $currentAddressIDs,  $fieldRecord, $fieldInfo = 'id_address') {
		return TodoyuDbHelper::deleteOtherMmRecords($mmTable, 'ext_contact_address', $idRecord, $currentAddressIDs, $fieldRecord, $fieldInfo);
	}

}

?>