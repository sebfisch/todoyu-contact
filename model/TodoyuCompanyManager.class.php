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
 * Company manager
 *
 * @package		Todoyu
 * @subpackage	User
 */
class TodoyuCompanyManager {

	const TABLE = 'ext_contact_company';

	/**
	 * Get a company object
	 *
	 * @param	Integer		$idCompany
	 * @return	TodoyuCompany
	 */
	public static function getCompany($idCompany) {
		$idCompany	= intval($idCompany);

		return TodoyuRecordManager::getRecord('TodoyuCompany', $idCompany);
	}



	/**
	 * Get a company array
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyData($idCompany) {
		return TodoyuRecordManager::getRecordData(self::TABLE, $idCompany);
	}



	/**
	 * Get all company records
	 *
	 * @param	Array		$fields			Custom field list (instead of *)
	 * @param	String		$where			Extra where clause
	 * @return	Array
	 */
	public static function getAllCompanies(array $fields = array(), $where = '') {
		$fields	= sizeof($fields) === 0 ? '*' : implode(',', Todoyu::db()->escapeArray($fields));
		$table	= self::TABLE;
		$where	= ($where === '' ? '' : $where . ' AND ') . 'deleted = 0';
		$order	= 'title';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Save company data as record
	 *
	 * @param	Array		$data
	 * @return	Integer		Company ID
	 */
	public static function saveCompany(array $data) {
		$idCompany	= intval($data['id']);

		if( $idCompany === 0 ) {
			$idCompany	= self::addCompany();
		}

		self::updateCompany($idCompany, $data);

		return $idCompany;
	}



	/**
	 * Add a company record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addCompany(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a company record
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function updateCompany($idCompany, array $data) {
		$idCompany			= intval($idCompany);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idCompany, $data);
	}



	/**
	 * Delete a company in the database (set deleted flag to 1)
	 *
	 * @param	Integer		$idUser
	 */
	public static function deleteCompany($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuRecordManager::deleteRecord(self::TABLE, $idCompany);
	}



	/**
	 * Add a user to a company (linked)
	 * Additional data are the workaddress and the jobtype
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idUser
	 * @param	Integer		$idWorkadress
	 * @param	Integer		$idJobtype
	 */
	public static function addUserToCompany($idCompany, $idUser, $idWorkadress = 0, $idJobtype = 0) {
		$data	= array(
			'id_company'	=> intval($idCompany) ,
			'id_user'		=> intval($idUser),
			'id_workaddress'=> intval($idWorkadress),
			'id_jobtype'	=> intval($idJobtype)
		);

		Todoyu::db()->addRecord('ext_contact_mm_company_person', $data);
	}



	/**
	 * Remove user from company
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idUser
	 */
	public static function removeUserFromCompany($idCompany, $idUser) {
		$idCompany	= intval($idCompany);
		$idUser		= intval($idUser);

		$where		= '	id_company	= ' . $idCompany . ' AND
						id_user		= ' . $idUser;

		Todoyu::db()->deleteRecord('ext_contact_mm_company_person', $where);
	}



	/**
	 * Delete all users of a company (only the link will be removed, not the user)
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeAllUsers($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_person', 'id_company', $idCompany);
	}



	/**
	 * Remove all contact information of a company
	 *
	 * @todo	Keep the contact information, or delete? Dead records at the moment. See also next functions for same problem
	 * @param	Integer		$idCompany
	 */
	public static function removeAllContactinfo($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_contactinfo', 'id_company', $idCompany);
	}



	/**
	 * Remove all address information of a company
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeAllAddresses($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_address', 'id_company', $idCompany);
	}



	/**
	 * Get company label
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function getLabel($idCompany) {
		return self::getCompany($idCompany)->getLabel();
	}



	/**
	 * Search companies
	 *
	 * @param	String		$sword
	 * @param	Array		$searchFields
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @return	Array
	 */
	public static function searchCompany($sword, array $searchFields = null, $size = 100, $offset = 0)	{
		$fields	= 'SQL_CALC_FOUND_ROWS *';
		$table 	= self::TABLE;
		$order	= 'title';
		$swords	= TodoyuArray::trimExplode(' ', $sword);
		$limit	= intval($offset) . ',' . intval($size);

		$searchFields	= is_null($searchFields) ? array('title', 'shortname') : $searchFields;

		if( sizeof($swords) ) {
			$where = Todoyu::db()->buildLikeQuery($swords, $searchFields);
		} else {
			$where = '1';
		}

		$where .= ' AND deleted = 0';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);
	}



	/**
	 * Remove company object from cache
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeFromCache($idCompany)	{
		$idCompany	= intval($idCompany);

		TodoyuCache::removeRecord('TodoyuCompany', $idCompany);
		TodoyuCache::removeRecordQuery(self::TABLE, $idCompany);
	}



	/**
	 * Get user records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyUserRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	mm.*,
					u.*';
		$tables	= '	ext_contact_person u,
					ext_contact_mm_company_person mm';
		$where	= '	mm.id_user		= u.id AND
					mm.id_company	= ' . $idCompany;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get contactinfo records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyContactinfoRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	c.*';
		$tables	= '	ext_contact_contactinfo c,
					ext_contact_mm_company_contactinfo mm';
		$where	= ' mm.id_contactinfo	= c.id AND
					mm.id_company		= ' . $idCompany;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get address records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyAddressRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	a.*';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_company_address mm';
		$where	= ' mm.id_address	= a.id AND
					mm.id_company	= ' . $idCompany;
		$order	= ' a.is_preferred DESC';

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get the number of users on a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Integer
	 */
	public static function getNumUsers($idCompany) {
		$idCompany	= intval($idCompany);

		$field	= 'id_user';
		$table	= 'ext_contact_mm_company_person';
		$where	= 'id_company = ' . $idCompany;

		$users	= Todoyu::db()->getArray($field, $table, $where);

		return sizeof($users);
	}



	/**
	 * Get address label of the company
	 * Compiled from the first address record. Using, street, zip and city
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function getCompanyAddress($idCompany) {
		$addresses	= self::getCompanyAddressRecords($idCompany);
		$address= null;
		$label		= '';

		if( sizeof($addresses) > 0 ) {
			$address = $addresses[0];

			$label	= $address['street'] . ', ' . $address['zip'] . ' ' . $address['city'];
		}

		return $label;
	}

}

?>