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
 * Person rights functions
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonRights {

	/**
	 * Deny access
	 * Shortcut for contact
	 *
	 * @param	String		$right		Denied right
	 */
	private static function deny($right) {
		TodoyuRightsManager::deny('contact', $right);
	}



	/**
	 * Check whether a person can see the given person
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isSeeAllowed($idPerson) {
		$idPerson	= intval($idPerson);

		if( TodoyuAuth::isAdmin() || allowed('contact', 'person:seeAllPersons') ) {
			return true;
		}

		if( allowed('contact', 'person::seeAllInternalPersons') ) {
			if( TodoyuContactPersonManager::getPerson($idPerson)->isInternal() ) {
				return true;
			}
		}

		return in_array($idPerson, self::getPersonIDsAllowedToBeSeen());
	}



	/**
	 * Checks if edit of given person is allowed to current user
	 *
	 * @param	Integer	$idPerson
	 * @return	Boolean
	 */
	public static function isEditAllowed($idPerson) {
		$idPerson	= intval($idPerson);

			// Cannot edit person if see of the person is not allowed
		if( ! self::isSeeAllowed($idPerson) ) {
			return false;
		}

			// Can edit if is admin or is allowed to edit all person or if its the person itself
		if( TodoyuAuth::isAdmin() || allowed('contact', 'person:editAndDeleteAll') || personid() == $idPerson ) {
			return true;
		}

		return false;
	}



	/**
	 * Checks if deleting given person is allowed to user
	 *
	 * @param	Integer	$idPerson
	 * @return	Boolean
	 */
	public static function isDeleteAllowed($idPerson) {
		$idPerson	= intval($idPerson);

		if( ! self::isSeeAllowed($idPerson) ) {
			return false;
		}

		return TodoyuAuth::isAdmin() || allowed('contact', 'person:editAndDeleteAll');
	}



	/**
	 * Get IDs of all persons the current (non-admin) user is allowed to see
	 *
	 * @return Array
	 */
	public static function getPersonIDsAllowedToBeSeen($withAccount = false) {
		$fields	= 'id';
		$table	= TodoyuContactPersonManager::TABLE;
		$where	= self::getAllowedToBeSeenPersonsWhereClause($withAccount);

		return Todoyu::db()->getColumn($fields, $table, $where, '', '', '', 'id');
	}



	/**
	 * Get WHERE clause for all persons the current user is allowed to see
	 *
	 * @param	Boolean		$withAccount	only persons with a todoyu account
	 * @return	String
	 */
	public static function getAllowedToBeSeenPersonsWhereClause($withAccount = false) {
		$personIDs	= array();

		if( TodoyuAuth::isAdmin() || allowed('contact', 'person:seeAllPersons')) {
			return ' 1';
		}

		if( allowed('contact', 'person:seeAllInternalPersons') ) {
			$personIDs	= TodoyuContactPersonManager::getInternalPersonIDs();
		}

			// Get all projects the current user is allowed to see
		$projectIDs	= TodoyuProjectProjectManager::getAvailableProjectsForPerson();
			// Get all persons marked "visible for externals" in any of their projects
		$projectsPersonsIDs	= TodoyuProjectProjectManager::getProjectsPersonsIDs($projectIDs, $withAccount);
			// Get all persons which are employees of current persons employer
		$companies	= TodoyuContactPersonManager::getPersonCompanyRecords(personid());

		foreach($companies as $company) {
			$companyPersonIDs	= TodoyuContactCompanyManager::getCompany($company['id'])->getEmployeeIds();
		}

		$personIDs	[]= personid();
		$allowedPersonsIDs	= array_unique(array_merge(array_merge($personIDs, $projectsPersonsIDs), $companyPersonIDs));

		return ' id IN ( ' . TodoyuArray::intImplode($allowedPersonsIDs, ',') . ')';
	}



	/**
	 * Restrict access to persons who are allowed to see the given person
	 *
	 * @param	Integer		$idPerson
	 */
	public static function restrictSee($idPerson) {
		if( ! self::isSeeAllowed($idPerson) ) {
			self::deny('person:see');
		}
	}


	
	/**
	 * Restrict access to persons who are allowed to add a new person
	 */
	public static function restrictAdd() {
		if( ! allowed('contact', 'person:add') ) {
			self::deny('person:add');
		}
	}



	/**
	 * Restrict access to persons who are allowed to edit given person
	 *
	 * @param	Integer	$idPerson
	 */
	public static function restrictEdit($idPerson) {
		if( ! self::isEditAllowed($idPerson) ) {
			self::deny('person:edit');
		}
	}



	/**
	 * Restrict access to persons who are allowed to delete given person
	 *
	 * @param	Integer	$idPerson
	 */
	public static function restrictDelete($idPerson) {
		if( ! self::isDeleteAllowed($idPerson) ) {
			self::deny('person:delete');
		}
	}
}
?>