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
 * User object
 *
 * @package		Todoyu
 * @subpackage	Contact
 */

class TodoyuPerson extends TodoyuBaseObject {

	/**
	 * Initialize user.
	 *
	 * @param	Integer		$idUser
	 */
	public function __construct($idPerson) {
		parent::__construct($idUser, 'ext_contact_person');
	}



	/**
	 * Get the name label of the user
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
			$role	= TodoyuUserroleManager::getUserrole($idRole);

			$label	.= ', ' . $role->getTitle();
		}

		 if ( $showEmail === true ) {
		 	$label	.= ' (' . $this->getEmail() . ')';
		 }

		 return $label;
	}



	/**
	 * Check if user is an admin
	 *
	 * @return	Boolean
	 */
	public function isAdmin() {
		return intval($this->data['is_admin']) === 1;
	}



	/**
	 * Get IDs of the groups the user is a member of
	 *
	 * @return	Array
	 */
	public function getGroupIDs() {
		if( ! array_key_exists('groups', $this->cache) ) {
			$this->cache['groups'] = TodoyuPersonManager::getGroups();
		}

		return TodoyuArray::getColumn($this->cache['groups'], 'id');
	}



	/**
	 * Get fullname of the user
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
	 * Get current user's shortname
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
	 * Get user language
	 *
	 * @return $lang
	 */
	public function getLanguage() {
		$lang	= TodoyuUserPreferences::getLanguage();

			// If no preference found, try to detect the browser language
		if( $lang === false ) {
			$browserLang = TodoyuBrowserInfo::getBrowserLanguage();

			if( $browserLang !== false ) {
				$lang = $browserLang;
			}
		}

			// Last fallback is system language
		if( $lang === false ) {
			$lang = $GLOBALS['CONFIG']['SYSTEM']['language'];
		}

		return $lang;
	}



	/**
	 * Get all company IDs of a user (a user can work for multiple companies)
	 *
	 * @return	Array
	 */
	public function getCompanyIDs() {
		$field	= 'id_company';
		$table	= 'ext_contact_mm_company_person';
		$where	= 'id_user = ' . $this->getID();

		return Todoyu::db()->getColumn($field, $table, $where);
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
	 * Get usergroup IDs
	 *
	 * @return	Array
	 */
	public function getUsergroupIDs() {
		return TodoyuPersonManager::getUsergroupIDs($this->getID());
	}



	/**
	 * Load all foreing record of a user
	 *
	 */
	public function loadForeignData()	{
		$this->data['company']		= TodoyuPersonManager::getUserCompanyRecords($this->id);
		$this->data['contactinfo']	= TodoyuPersonManager::getUserContactinfoRecords($this->id);
		$this->data['address']		= TodoyuPersonManager::getUserAddressRecords($this->id);
		$this->data['usergroup']	= TodoyuPersonManager::getUsergroups($this->id);
	}



	/**
	 * Get user template data
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