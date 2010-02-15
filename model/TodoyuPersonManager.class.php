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
 * Manage persons
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
	 * Get a person object. This functions uses the cache to
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
	 * Get all active persons
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
	 * Get person data by username
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
	 * Add a new person
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addPerson(array $data = array()) {
		unset($data['id']);

		$data['date_create']	= NOW;
		$data['id_person_create']	= TodoyuAuth::getPersonID();

		return Todoyu::db()->addRecord(self::TABLE, $data);
	}



	/**
	 * Delete a person in the database (set deleted flag to 1)
	 *
	 * @param	Integer		$idPerson
	 */
	public static function deletePerson($idPerson) {
		$idPerson	= intval($idPerson);

		$data	= array(
			'deleted'	=> 1
		);

		self::updatePerson($idPerson, $data);
	}



	/**
	 * Update a person
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

			// Create person in database if not existing
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
		$data 	= TodoyuFormHook::callSaveData($xmlPath, $data, $idPerson);

		self::updatePerson($idPerson, $data);

		return $idPerson;
	}



	/**
	 * Update current persons password
	 *
	 * @param	String		$password
	 * @param	Bool		$alreadyHashed		Is password already a md5 hash?
	 * @return	Bool		Updated
	 */
	public static function updatePassword($password, $alreadyHashed = true) {
		if( ! $alreadyHashed ) {
			$password = md5($password);
		}

		$idPerson	= personid();
		$data		= array(
			'password'	=> $password
		);

		return self::updatePerson($idPerson, $data);
	}



	/**
	 * Get role IDs of a person
	 *
	 * @param 	Integer		$idPerson
	 * @return	Array
	 */
	public static function getRoleIDs($idPerson) {
		$idPerson	= personid($idPerson);

		$field	= 'id_role';
		$table	= 'ext_contact_mm_person_role';
		$where	= 'id_person = ' . $idPerson;

		return Todoyu::db()->getColumn($field, $table, $where);
	}



	/**
	 * Get roles of a person
	 *
	 * @param	Integer	$idPerson
	 * @return	Array
	 */
	public static function getRoles($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	r.*';
		$table	= '	ext_contact_mm_person_role mm,
					system_role r';
		$where	= '	id_person		= ' . $idPerson . ' AND
					mm.id_role	= r.id';

		return Todoyu::db()->getArray($fields, $table, $where);
	}



	/**
	 * Check whether the given person belongs to any of the given roles
	 *
	 * @param	Array		$roles
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function hasAnyRole(array $roles, $idPerson = 0) {
		$personRoles	= TodoyuPersonManager::getRoleIDs($idPerson);

		return sizeof(array_intersect($roles, $personRoles)) > 0;
	}



	/**
	 * Get IDs of internal persons (staff)
	 *
	 * @return	Array
	 */
	public static function getInternalPersonIDs() {
		$persons	= self::getInternalPersons();

		return TodoyuArray::getColumn($persons, 'id');
	}



	/**
	 * Get internal persons (staff)
	 *
	 * @param	Boolean		$getJobType
	 * @param	Boolean		$getWorkAddress
	 * @return	Array
	 */
	public static function getInternalPersons($getJobType = false, $getWorkAddress = false) {
		$fields	=	'	u.*'
					. ($getJobType		=== true ? ', mm.id_jobtype' : '')
					. ($getWorkAddress	=== true ? ', mm.id_workaddress' : '');

		$table	= 	self::TABLE . ' u,
					ext_contact_company c,
					ext_contact_mm_company_person mm';
		$where	= '	u.id			= mm.id_person AND
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
	 * Get contact infos of given person
	 *
	 * @param	Integer		$idPerson
	 * @param	String		$type
	 * @param	Bool		$onlyPreferred
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
		$where	= '	mm.id_person			= ' . $idPerson . ' AND
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
	 * Get preferred phone number of given person
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
		$where	= '	mm.id_person			= ' . $idPerson . ' AND
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
	 * Search for person
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
			$label	= self::getPerson($idPerson)->getLabel($showEmail, $lastnameFirst);
		}

		return  $label;
	}



	/**
	 * When person form is saved, extract roles from data and save them
	 *
	 * @param	Array		$data
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function savePersonForeignRecords(array $data, $idPerson) {
		$idPerson	= intval($idPerson);

			// Save role records
		if( ! empty($data['role']) ) {
			$roleIDs	= array_unique(TodoyuArray::getColumn($data['role'], 'id'));

			self::addRoles($idPerson, $roleIDs);
		}
		unset($data['role']);

		return $data;
	}



	/**
	 * Add a role for a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Integer		$idRole
	 */
	public static function addRole($idPerson, $idRole) {
		TodoyuRoleManager::addPerson($idRole, $idPerson);
	}



	/**
	 * Add multiple roles for a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$roleIDs
	 */
	public static function addRoles($idPerson, array $roleIDs) {
		TodoyuRoleManager::addPersonToRoles($idPerson, $roleIDs);
	}



	/**
	 * Remove person object from cache
	 *
	 * @param	Integer		$idPerson
	 */
	public static function removeFromCache($idPerson)	{
		$idPerson		= intval($idPerson);

		TodoyuCache::removeRecord('TodoyuPerson', $idPerson);
		TodoyuCache::removeRecordQuery(self::TABLE, $idPerson);
	}



	/**
	 * Get IDs of working addresses of given person(s)
	 *
	 * @param	Array	$personIDs
	 * @return	Array
	 */
	public static function getWorkaddressIDsOfPersons(array $personIDs) {
		$personIDs	= TodoyuArray::intval($personIDs, true, true);
		$addressIDs	= array();

		if( sizeof($personIDs) > 0) {
			$field	= 'id_workaddress';
			$table	= 'ext_contact_mm_company_person';
			$where	= 'id_person IN (' . implode(',', $personIDs) . ') ';

			$addressIDs	= Todoyu::db()->getColumn($field, $table, $where);
		}

		return $addressIDs;
	}



	/**
	 * Get persons which celebrate birthday in the given range
	 * Gets the person records with some extra keys:
	 * - date: date of the birthday in this view (this year)
	 * - age: new age on this birthday
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	public static function getBirthdayPersons($dateStart, $dateEnd) {
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

		$birthdayPersons = Todoyu::db()->getArray($fields, $table, $where, '', $order);

			// Enrich data with date and age
		foreach($birthdayPersons as $index => $birthdayPerson) {
			$dateParts	= explode('-', $birthdayPerson['birthday']);
			$birthday	= mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateStart));

				// If a persons birthday is in the new year, use $dateEnd for year information
			if( $birthday < $dateStart ) {
				$birthday = mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateEnd));
			}

				// Set date of the birthday this year
			$birthdayPersons[$index]['date'] 	= $birthday;
				// Set age on this birthday
			$birthdayPersons[$index]['age']	= floor(date('Y', $dateStart)-intval($birthdayPerson['birthyear']));
		}

		return $birthdayPersons;
	}



	/**
	 * Get color IDs to given person id's (persons are enumeratedly given colors by their position in the list)
	 *
	 * @param	Array	$personIDs
	 * @return	Array
	 */
	public static function getSelectedPersonColor(array $personIDs) {
		$personIDs	= TodoyuArray::intval($personIDs, true, true);
		$cacheKey	= 'personcolors:' . md5(serialize($personIDs));

		if( ! TodoyuCache::isIn($cacheKey) ) {
			$colors 			= array();
			$numColors			= sizeof(TodoyuArray::assure($GLOBALS['CONFIG']['COLORS']));

				// Enumerate persons by system specific color to resp. list position
			foreach($personIDs as $idPerson) {
				$colors[$idPerson]	= TodoyuColors::getColorArray($idPerson%$numColors);
			}

			TodoyuCache::set($cacheKey, $colors);
		}

		return TodoyuCache::get($cacheKey);
	}



	/**
	 * Get company records for a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPersonCompanyRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	mm.*,
					c.*';
		$tables	= '	ext_contact_company c,
					ext_contact_mm_company_person mm';
		$where	= '	mm.id_company	= c.id AND
					mm.id_person		= ' . $idPerson;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get main company (first linked) of the person
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuCompany
	 */
	public static function getPersonsMainCompany($idPerson) {
		$idPerson = intval($idPerson);

		$field	= 'id_company';
		$table	= 'ext_contact_mm_company_person';
		$where	= 'id_person = ' . $idPerson;

		$idCompany	= Todoyu::db()->getFieldValue($field, $table, $where);

		return TodoyuCompanyManager::getCompany($idCompany);
	}



	/**
	 * Get contact records for a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$getOnlyPreferred
	 * @return	Array
	 */
	public static function getPersonContactinfoRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	c.*';
		$tables	= '	ext_contact_contactinfo c,
					ext_contact_mm_person_contactinfo mm';
		$where	= ' mm.id_contactinfo	= c.id AND
					mm.id_person			= ' . $idPerson;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get address records for a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPersonAddressRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	a.*';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_person_address mm';
		$where	= ' mm.id_address	= a.id AND
					mm.id_person		= ' . $idPerson;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}

}
?>