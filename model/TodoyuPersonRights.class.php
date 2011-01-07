<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
class TodoyuPersonRights {

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
			if ( TodoyuPersonManager::getPerson($idPerson)->isInternal() ) {
				return true;
			}
		}

		return in_array($idPerson, self::getPersonIDsAllowedToBeSeen());
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
			$personIDs	= TodoyuPersonManager::getInternalPersonIDs();
		}

			// Get all projects the current user is allowed to see
		$projectIDs	= TodoyuProjectManager::getAvailableProjectsForPerson();
			// Get all persons marked "visible for externals" in any of their projects
		$projectsPersonsIDs	= TodoyuProjectManager::getProjectsPersonsIDs($projectIDs, $withAccount);

		$allowedPersonsIDs	= array_unique(array_merge($personIDs, $projectsPersonsIDs));

		if( count($allowedPersonsIDs) > 0 ) {
			return ' id IN ( ' . TodoyuArray::intImplode($allowedPersonsIDs, ',') . ')';
		}

		return false;
	}



	/**
	 * Get IDs of all persons the current (non-admin) user is allowed to see
	 *
	 * @return Array
	 */
	public static function getPersonIDsAllowedToBeSeen($withAccount = false) {
		$fields	= 'id';
		$table	= TodoyuPersonManager::TABLE;
		$where	= self::getAllowedToBeSeenPersonsWhereClause($withAccount);

		return $where ? Todoyu::db()->getColumn($fields, $table, $where, '', '', '', 'id') : array();
	}



//	/**
//	 * Restrict access to persons who are allowed to see the given person
//	 *
//	 * @param	Integer		$idPerson
//	 */
//	public static function restrictSee($idPerson) {
//		if( ! self::isSeeAllowed($idPerson) ) {
//			self::deny('person:see');
//		}
//	}

}
?>