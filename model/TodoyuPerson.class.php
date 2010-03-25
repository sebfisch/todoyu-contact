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
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * person object
 *
 * @package		Todoyu
 * @subpackage	Contact
 */

class TodoyuPerson extends TodoyuBaseObject {

	/**
	 * Initialize person
	 *
	 * @param	Integer		$idPerson
	 */
	public function __construct($idPerson) {
		parent::__construct($idPerson, 'ext_contact_person');
	}



	/**
	 * Get the name label of the person
	 *
	 * @param	Boolean		$showEmail
	 * @param	Boolean		$lastnameFirst
	 * @param	Boolean		$showTitle
	 * @param	Integer		$idRole
	 * @return	String
	 */
	public function getLabel($showEmail = false, $lastnameFirst = true, $showTitle = false, $idRole = 0)	{
		 $label	= $this->getFullName($lastnameFirst);

		if ( $showTitle === true) {
			$role	= TodoyuRoleManager::getRole($idRole);

			$label	.= ', ' . $role->getTitle();
		}

		 if ( $showEmail === true ) {
		 	$label	.= ' (' . $this->getEmail() . ')';
		 }

		 return $label;
	}



	/**
	 * Check if person is an admin
	 *
	 * @return	Boolean
	 */
	public function isAdmin() {
		return intval($this->data['is_admin']) === 1;
	}



	/**
	 * Check if a person works in a internal company
	 *
	 * @return	Bool
	 */
	public function isInternal() {
		if( ! isset($this->cache['isInternal']) ) {
			$companies	= $this->getCompanies();
			$internals	= TodoyuArray::getColumn($companies, 'is_internal');
			$this->cache['isInternal'] = array_sum($internals) > 0;
		}

		return $this->cache['isInternal'];
	}



	/**
	 * Check if person is external (not in an internal company)
	 *
	 * @return	Bool
	 */
	public function isExternal() {
		return $this->isInternal() === false;
	}



	/**
	 * Get IDs of the roles the person is a member of
	 *
	 * @return	Array
	 */
	public function getRoleIDs() {
		return TodoyuPersonManager::getRoleIDs($this->id);
	}



	/**
	 * Get fullname of the person
	 *
	 * @param	Boolean		$lastnameFirst
	 * @return	String
	 */
	public function getFullName($lastnameFirst = false) {
		if( $lastnameFirst ) {
			return $this->getLastname() . ', ' . $this->getFirstname();
		} else {
			return $this->getFirstname() . ', ' . $this->getLastname();
		}
	}



	/**
	 * Get contact email address
	 *
	 * @return	String
	 */
	public function getEmail() {
		return $this->get('email');
	}



	/**
	 * Get current person's shortname
	 *
	 * @return	String
	 */
	public function getShortname() {
		return $this->get('shortname');
	}



	/**
	 * Get birthday timestamp
	 *
	 * @return	Integer
	 */
	public function getBirthday() {
		return intval(strtotime($this->get('birthday')));
	}



	/**
	 * Get person language
	 *
	 * @return $lang
	 */
	public function getLanguage() {
		$lang	= TodoyuContactPreferences::getLanguage();

			// If no preference found, try to detect the browser language
		if( $lang === false ) {
			$browserLang = TodoyuBrowserInfo::getBrowserLanguage();

			if( $browserLang !== false ) {
				$lang = $browserLang;
			}
		}

			// Last fallback is system language
		if( $lang === false ) {
			$lang = Todoyu::$CONFIG['SYSTEM']['language'];
		}

		return $lang;
	}



	/**
	 * Get all company IDs of a person (a person can work for multiple companies)
	 *
	 * @return	Array
	 */
	public function getCompanyIDs() {
		$field	= 'id_company';
		$table	= 'ext_contact_mm_company_person';
		$where	= 'id_person = ' . $this->id;

		return Todoyu::db()->getColumn($field, $table, $where);
	}



	/**
	 * Get all companies of the person
	 *
	 * @return	Array
	 */
	public function getCompanies() {
		$fields	= '	*';
		$table	= '	ext_contact_mm_company_person mm,
					ext_contact_company c';
		$where	= '	mm.id_person	= ' . $this->id . ' AND
					mm.id_company	= c.id AND
					c.deleted		= 0';

		return Todoyu::db()->getArray($fields, $table, $where);
	}



	/**
	 * Get main company
	 *
	 * @todo	There is not really a main company, we just take the first one
	 * @return	TodoyuCompany
	 */
	public function getCompany() {
		$companyIDs 	= $this->getCompanyIDs();
		$firstCompanyID	= intval($companyIDs[0]);

		return TodoyuCompanyManager::getCompany($firstCompanyID);
	}



	/**
	 * Load all foreing record of a person
	 *
	 */
	public function loadForeignData()	{
		$this->data['company']		= TodoyuPersonManager::getPersonCompanyRecords($this->id);
		$this->data['contactinfo']	= TodoyuPersonManager::getContactinfoRecords($this->id);
		$this->data['address']		= TodoyuPersonManager::getAddressRecords($this->id);
		$this->data['role']			= TodoyuPersonManager::getRoles($this->id);
	}



	/**
	 * Get person template data
	 *
	 * @param	Bool		$loadForeignRecords
	 * @return	Array
	 */
	public function getTemplateData($loadForeignRecords = false) {
		if( $loadForeignRecords ) {
			$this->loadForeignData();
		}

		return parent::getTemplateData();
	}


}


?>