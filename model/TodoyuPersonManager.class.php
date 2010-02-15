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
 * Manage users
 *
 * @package		Todoyu
 * @subpackage	Contact
 */

class TodoyuPersonManager {

	/**
	 * Default working table
	 *
	 */
	const TABLE = 'ext_contact_person';


	/**
	 * Get a user object. This functions uses the cache to
	 * prevent double object initialisation
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuPerson
	 */
	public static function getPerson($idPerson) {
		return TodoyuCache::getRecord('TodoyuPerson', $idPerson);
	}



	/**
	 * Get person data array
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPersonArray($idPerson) {
		return TodoyuRecordManager::getRecordData(self::TABLE, $idPerson);
	}



	/**
	 * Get all active users
	 *
	 * @param	Array		$fields			By default, all fields are selected. You can provide a field list instead
	 * @param	Bool		$showInactive	Also show inactive persons
	 * @return	Array
	 */
	public static function getAllActivePersons(array $fields = array(), $showInactive = false) {
		$fields	= sizeof($fields) === 0 ? '*' : implode(',', Todoyu::db()->escapeArray($fields));
		$table	= self::TABLE;
		$where	= 'deleted = 0';
		$order	= 'lastname, firstname';

		if( $showInactive !== true ) {
			$where .= ' AND active	= 1';
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Check if $username and $password are a valid login
	 *
	 * @param	String		$username		Username
	 * @param	String		$password		Password as sha1
	 * @return	Boolean
	 */
	public static function isValidLogin($username, $password) {
		$field	= 'id';
		$table	= self::TABLE;
		$where	= '	username = ' . Todoyu::db()->quote($username, true) . ' AND
					password = ' . Todoyu::db()->quote($password, true) . ' AND
					active	 = 1 AND
					deleted  = 0';

		return Todoyu::db()->hasResult($field, $table, $where);
	}



	/**
	 * Check if $idPerson is a valid person ID
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPerson($idPerson) {
		return TodoyuRecordManager::isRecord(self::TABLE, $idPerson);
	}



	/**
	 * Check if a person with the username $username exists
	 *
	 * @param	String		$username
	 * @return	Boolean
	 */
	public static function personExists($username) {
		return self::getPersonIDByUsername($username) !== 0;
	}



	/**
	 * Get user data by username
	 *
	 * @param	String		$username
	 * @return	Array
	 */
	public static function getPersonIDByUsername($username) {
		$fields	= 'id';
		$table	= self::TABLE;
		$where	= 'username = ' . Todoyu::db()->quote($username, true);
		$limit	= '1';

		$row	= Todoyu::db()->doSelectRow($fields, $table, $where, '', '', $limit);

		return 	intval($row['id']);
	}



	/**
	 * Add a new user to database
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addPerson(array $data = array()) {
		unset($data['id']);

		$data['date_create']	= NOW;
		$data['id_user_create']	= TodoyuAuth::getPersonID();

		return Todoyu::db()->addRecord(self::TABLE, $data);
	}



	/**
	 * Delete a user in the database (set deleted flag to 1)
	 *
	 * @param	Integer		$idUser
	 */
	public static function deletePerson($idPerson) {
		$idPerson	= intval($idPerson);

		$data	= array(
			'deleted'	=> 1
		);

		self::updatePerson($idPerson, $data);
	}



	/**
	 * Update a user in the database
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public static function updatePerson($idPerson, array $data) {
		return TodoyuRecordManager::updateRecord(self::TABLE, $idPerson, $data);
	}



	/**
	 * Save user
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function savePerson($idPerson, array $data) {
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/user/config/form/user.xml';

			// Create user in database if not existing
		if( $idPerson === 0 ) {
			$idPerson = self::addPerson();
		}

			// Check password update
		unset($data['password']);
		if( strlen($data['password_first']) > 0 ) {
			$data['password'] = md5($data['password_first']);
		}
		unset($data['password_first']);
		unset($data['password_second']);

			// Call internal save function
		$data	= self::savePersonForeignRecords($data, $idPerson);
			// Call hooked save functions
		$data 	= TodoyuFormHook::callSaveData($xmlPath, $data, $idUser);

		self::updatePerson($idPerson, $data);

		return $idPerson;
	}



	/**
	 * Update current users password
	 *
	 * @param	String		$password
	 * @param	Bool		$alreadyHashed		Is password already a md5 hash?
	 * @return	Bool		Updated
	 */
	public static function updatePassword($password, $alreadyHashed = true) {
		if( ! $alreadyHashed ) {
			$password = md5($password);
		}

		$idUser	= personid();
		$data	= array(
			'password'	=> $password
		);

		return self::updatePerson($idUser, $data);
	}



	/**
	 * Get usergroups of an user
	 *
	 * @param 	Integer		$idUser
	 * @return	Array
	 */
	public static function getRoleIDs($idPerson) {
		$idPerson	= personid($idPerson);

		$field	= 'id_group';
		$table	= 'ext_contact_mm_person_role';
		$where	= 'id_user = ' . $idPerson;

		return Todoyu::db()->getColumn($field, $table, $where);
	}



	/**
	 * Get usergroups of given user
	 *
	 * @todo	check if needed (getUsergroups vs. getGroups)
	 * @param	Integer	$idUser
	 * @return	Array
	 */
	public static function getRoles($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	r.*';
		$table	= '	ext_contact_mm_person_role mm,
					system_role r';
		$where	= '	id_user		= ' . $idPerson . ' AND
					mm.id_group	= r.id';

		return Todoyu::db()->getArray($fields, $table, $where);
	}



	/**
	 * Check whether the given user belongs to any of the given usergroups
	 *
	 * @param	Integer	$idUser
	 * @param	Array	$groupIDs
	 * @return	Boolean
	 */
	public static function hasAnyRole(array $roles, $idUser = 0) {
		$personRoles	= TodoyuPersonManager::getRoleIDs($idUser);

		return sizeof(array_intersect($roles, $personRoles)) > 0;
	}



	/**
	 * Get IDs of internal users (staff)
	 *
	 * @return	Array
	 */
	public static function getInternalPersonIDs() {
		$persons	= self::getInternalPersons();

		return TodoyuArray::getColumn($persons, 'id');
	}



	/**
	 * Get internal users (staff)
	 *
	 * @param	Boolean	$getJobType
	 * @param	Boolean	$getWorkAddress
	 * @return	Array
	 */
	public static function getInternalPersons($getJobType = false, $getWorkAddress = false) {
		$fields	=	'	u.*'
					. ($getJobType		=== true ? ', mm.id_jobtype' : '')
					. ($getWorkAddress	=== true ? ', mm.id_workaddress' : '');

		$table	= 	self::TABLE . ' u,
					ext_contact_company c,
					ext_contact_mm_company_person mm';
		$where	= '	u.id			= mm.id_user AND
					mm.id_company	= c.id AND
					c.is_internal	= 1 AND
					u.deleted		= 0	AND
					u.active		= 1';
		$order	= '	u.lastname,
					u.firstname';

		$persons= Todoyu::db()->getIndexedArray('id', $fields, $table, $where, '', $order);

		return $persons;
	}



	/**
	 * Get contact infos of given user
	 *
	 * @param	Integer		$idUser
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getContactInfos($idPerson, $type = null, $onlyPreferred = false) {
		$idPerson	= intval($idPerson);

		$fields	= '	ci.*,
					cit.key,
					cit.title';
		$tables	= '	ext_contact_contactinfo ci,
					ext_contact_contactinfotype cit,
					ext_contact_mm_person_contactinfo mm';
		$where	= '	mm.id_user			= ' . $idUser . ' AND
					mm.id_contactinfo	= ci.id AND
					ci.id_contactinfotype = cit.id';
		$order	= '	ci.id_contactinfotype ASC,
					ci.preferred DESC';

		if( $onlyPreferred ) {
			$where .= ' AND ci.preferred = 1';
		}

		if( ! is_null($type) ) {
			$where .= ' AND cit.key LIKE \'%' . Todoyu::db()->escape($type) . '%\'';
		}

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get preferred phone number of given user
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPreferredPhone($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	ci.info,
					cit.title';
		$tables	= '	ext_contact_contactinfo ci,
					ext_contact_contactinfotype cit,
					ext_contact_mm_person_contactinfo mm';
		$where	= '	mm.id_user			= ' . $idPerson . ' AND
					mm.id_contactinfo	= ci.id AND
					cit.category	= 2 AND
					ci.id_contactinfotype = cit.id AND
					ci.preferred = 1';

		return	Todoyu::db()->getRecordByQuery($fields, $tables, $where);
	}



	/**
	 * Get all contact infos marked as being preferred
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getPreferredContactInfos($idPerson, $type = null) {
		return self::getContactInfos($idPerson, $type, true);
	}



	/**
	 * Search for user
	 *
	 * @param	Array	$searchFieldsArray
	 * @param	String	$search
	 * @return	Array
	 */
	public static function searchPersons($sword = '', array $searchFields = null, $size = 100, $offset = 0) {
		$fields	= 'SQL_CALC_FOUND_ROWS *';
		$table	= self::TABLE;
		$order	= 'lastname';
		$swords	= TodoyuArray::trimExplode(' ', $sword);
		$limit	= intval($offset) . ',' . intval($size);

		$searchFields	= is_null($searchFields) ? array('username', 'email', 'firstname', 'lastname', 'shortname') : $searchFields;

		if( sizeof($swords) > 0 ) {
			$where = Todoyu::db()->buildLikeQuery($swords, $searchFields);
		} else {
			$where = '1';
		}

		$where .= ' AND deleted = 0';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);
	}



	/**
	 * Database relation function
	 *
	 * @param	String		$field
	 * @param	TodoyuBaseObject	$record
	 * @return	String
	 */
	public static function getDatabaseRelationLabel(TodoyuFormElement $field, array $record)	{
		$idPerson	= intval($record['id']);

		if( $idPerson === 0 ) {
			$label	= 'New person';
		} else {
			$label	= self::getLabel($idPerson);
		}

		return $label;
	}



	/**
	 * Get person label
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$showEmail
	 * @param	Boolean		$lastnameFirst
	 * @return	String
	 */
	public static function getLabel($idPerson, $showEmail = false, $lastnameFirst = true)	{
		$idPerson	= intval($idPerson);
		$label	= '';

		if ( $idPerson !== 0 ) {
			$label	= self::getUser($idPerson)->getLabel($showEmail, $lastnameFirst);
		}

		return  $label;
	}



	/**
	 * When user form is saved, extract usergroups from data and save them
	 *
	 * @param	Array		$data
	 * @param	Integer		$idUser
	 * @return	Array
	 */
	public static function savePersonForeignRecords(array $data, $idPerson) {
		$idPerson	= intval($idPerson);

			// Save usergroup records
		if( ! empty($data['usergroup']) ) {
			$usergroupIDs	= array_unique(TodoyuArray::getColumn($data['usergroup'], 'id'));

			self::addRoles($idPerson, $usergroupIDs);
		}
		unset($data['usergroup']);

		return $data;
	}



	/**
	 * Add a role for a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Integer		$idRole
	 */
	public static function addRole($idPerson, $idRole) {
		TodoyuRoleManager::addUserToGroup($idRole, $idPerson);
	}



	/**
	 * Add multiple roles for a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$roleIDs
	 */
	public static function addRoles($idPerson, array $roleIDs) {
		TodoyuRoleManager::addUserToGroups($idPerson, $roleIDs);
	}



	/**
	 * Remove user object from cache
	 *
	 * @param	Integer		$idPerson
	 */
	public static function removeFromCache($idPerson)	{
		$idUser		= intval($idUser);

		TodoyuCache::removeRecord('TodoyuPerson', $idPerson);
		TodoyuCache::removeRecordQuery(self::TABLE, $idPerson);
	}



	/**
	 * Get IDs of working addresses of given user(s)
	 *
	 * @param	Array	$userIDs
	 * @return	Array
	 */
	public static function getWorkaddressIDsOfUsers(array $userIDs) {
		$userIDs	= TodoyuArray::intval($userIDs, true, true);
		$addressIDs	= array();

		if (count($userIDs) > 0) {
			$fields	= 'id_user,id_workaddress';
			$table	= 'ext_contact_mm_company_person';
			$where	= 'id_user IN (' . implode(',', $userIDs) . ') ';
			$res	= Todoyu::db()->getArray($fields, $table, $where);

			foreach($res as $entry) {
				$addressIDs[]	= $entry['id_workaddress'];
			}

			$addressIDs	= array_unique($addressIDs);
		}

		return $addressIDs;
	}



	/**
	 * Get users which celebrate birthday in the given range
	 * Gets the user records with some extra keys:
	 * - date: date of the birthday in this view (this year)
	 * - age: new age on this birthday
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	public static function getBirthdayUsers($dateStart, $dateEnd) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);

		$mysqlDateStart	= date('Y-m-d', $dateStart);
		$mysqlDateEnd	= date('Y-m-d', $dateEnd);
		$monthStart		= date('n', $dateStart);
		$monthEnd		= date('n', $dateEnd);
		$monthDiff		= $monthEnd - $monthStart;
		$dayStart		= date('j', $dateStart);
		$dayEnd			= date('j', $dateEnd);

			// If range is only one day, only check this day
		if( $monthStart === $monthEnd && $dayStart === $dayEnd ) {
			$rangeWhere		= '	(MONTH(birthday) = ' . $monthStart . ' AND DAY(birthday) = ' . $dayStart . ') ';
		} else { // If end month is not equal start month, set start limit
			$rangeWhere		= '	(MONTH(birthday) = ' . $monthStart . ' AND DAY(birthday) >= ' . $dayStart . ') ';
		}

			// If end month is not equal start month, set end limit
		if( $monthStart !== $monthEnd ) {
			$rangeWhere .= ' OR	(MONTH(birthday) = ' . $monthEnd . ' AND DAY(birthday) <= ' . $dayEnd . ')';
		}

			// Month difference higher than one (ex: range over 3 months)
			// End month is lower than start month (two years are included in the range (dec-jan))
		if( $monthDiff > 1 || $monthEnd < $monthStart ) {
				// Multiple months in the same year
			if( $monthDiff > 1 ) {
				$monthsBetween = array_splice(range($monthStart, $monthEnd), 1, -1);
			}
				// Crossing the year border (ex: nov-feb)
			if( $monthEnd < $monthStart ) {
				$monthsBetween = array();
				$mEnd	= $monthEnd + 12;
				for($i=$monthStart+1; $i<$mEnd-1; $i++) {
					$monthsBetween[] = $i%12+1;
				}
			}

			if( sizeof($monthsBetween) > 0 ) {
					// Months in the middle of the range. Day doesn't matter
				$rangeWhere .= ' OR ( MONTH(birthday) IN(' . implode(',', $monthsBetween) . '))';
			}
		}

		$fields	= '	*,
					YEAR(birthday) as birthyear';
		$table	= self::TABLE;
		$where	= '	birthday < \'' . $mysqlDateStart . '\' AND
					(' . $rangeWhere . ')';
		$order	= 'birthday';

		$birthdayUsers	= Todoyu::db()->getArray($fields, $table, $where, '', $order);

			// Enrich data with date and age
		foreach($birthdayUsers as $index => $birthdayUser) {
			$dateParts	= explode('-', $birthdayUser['birthday']);
			$birthday	= mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateStart));

				// If a users birthday is in the new year, use $dateEnd for year information
			if( $birthday < $dateStart ) {
				$birthday = mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateEnd));
			}

				// Set date of the birthday this year
			$birthdayUsers[$index]['date'] 	= $birthday;
				// Set age on this birthday
			$birthdayUsers[$index]['age']	= floor(date('Y', $dateStart)-intval($birthdayUser['birthyear']));
		}

		return $birthdayUsers;
	}



	/**
	 * Have user IDs listed comma-separated
	 *
	 * @param	Array	$jobTypes
	 * @param	Array	$prefs
	 * @param	Mixed	$prefs		Boolean / Array
	 */
	public static function listUserIDsToJobtypes(array $jobTypes, array $prefs) {
		foreach($jobTypes as $idJobtype => $jobtypeData) {
			$jobTypes[$idJobtype]['user_ids'] = implode(',', $jobTypes[$idJobtype]['user_ids'] );

				// Set job types selected as stored in user prefs
			if (is_array($prefs['selectedJobtypeIDs']) && in_array($idJobtype, $prefs['selectedJobtypeIDs']) ) {
				$jobTypes[ $idJobtype ]['selected'] = 1;
			} else {
				$jobTypes[ $idJobtype ]['selected'] = 0;
			}
		}

		return $jobTypes;
	}



	/**
	 * Get color IDs to given user id's (users are enumeratedly given colors by their position in the list)
	 *
	 * @param	Array	$selectedUserIDs
	 * @return	Array
	 */
	public static function getSelectedUsersColor(array $userIDs) {
		$userIDs	= TodoyuArray::intval($userIDs, true, true);
		$cacheKey	= 'usercolors:' . md5(serialize($userIDs));

		if( ! TodoyuCache::isIn($cacheKey) ) {
			$colors 		= array();
			$internalUserIDs= TodoyuPersonManager::getInternalPersonIDs();
			$numColors		= sizeof(TodoyuArray::assure($GLOBALS['CONFIG']['COLORS']));

				// Enumerate users by system specific color to resp. list position
			foreach($userIDs as $idUser) {
				$colors[$idUser]	= TodoyuColors::getColorArray($idUser%$numColors);
			}

			TodoyuCache::set($cacheKey, $colors);
		}

		return TodoyuCache::get($cacheKey);
	}



	/**
	 * Get company records for an user
	 *
	 * @param	Integer		$idUser
	 * @return	Array
	 */
	public static function getUserCompanyRecords($idUser) {
		$idUser	= intval($idUser);

		$fields	= '	mm.*,
					c.*';
		$tables	= '	ext_contact_company c,
					ext_contact_mm_company_person mm';
		$where	= '	mm.id_company	= c.id AND
					mm.id_user		= ' . $idUser;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get main company (first linked) of the user
	 *
	 * @param	Integer		$idUser
	 * @return	TodoyuCompany
	 */
	public static function getUsersMainCompany($idUser) {
		$idUser	= intval($idUser);

		$field	= 'id_company';
		$table	= 'ext_contact_mm_company_person';
		$where	= 'id_user = ' . $idUser;

		$idCompany	= Todoyu::db()->getFieldValue($field, $table, $where);

		return TodoyuCompanyManager::getCompany($idCompany);
	}



	/**
	 * Get contact records for an user
	 *
	 * @param	Integer		$idUser
	 * @param	Boolean		$getOnlyPreferred
	 * @return	Array
	 */
	public static function getUserContactinfoRecords($idUser) {
		$idUser	= intval($idUser);

		$fields	= '	c.*';
		$tables	= '	ext_contact_contactinfo c,
					ext_contact_mm_person_contactinfo mm';
		$where	= ' mm.id_contactinfo	= c.id AND
					mm.id_user			= ' . $idUser;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get address records for an user
	 *
	 * @param	Integer		$idUser
	 * @return	Array
	 */
	public static function getUserAddressRecords($idUser) {
		$idUser	= intval($idUser);

		$fields	= '	a.*';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_person_address mm';
		$where	= ' mm.id_address	= a.id AND
					mm.id_user		= ' . $idUser;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get usertypes
	 *
	 * @return	Array
	 */
	public static function getUserTypes() {
		return $GLOBALS['CONFIG']['EXT']['user']['usertype'];
	}

}
?>