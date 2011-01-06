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
 * Company rights functions
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuCompanyRights {

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
	 * Get WHERE clause for all companies the current user is allowed to see
	 *
	 * @return	String
	 */
	public static function getAllowedToBeSeenCompaniesWhereClause() {
		$allowedCompanyIDs	= TodoyuCompanyManager::getInternalCompanyIDs();

		if( TodoyuAuth::isAdmin() || allowed('contact', 'company:seeAllCompanies')) {
			return ' 1';
		}

			// Get all companies the user belongs to
		$person			= TodoyuPersonManager::getPerson(personid());
		$ownCompanyIDs	= $person->getCompanyIDs();

		$allowedCompanyIDs	= array_unique(array_merge($allowedCompanyIDs, $ownCompanyIDs));

		return ' id IN ( ' . TodoyuArray::intImplode($allowedCompanyIDs, ',') . ')';;
	}

}
?>