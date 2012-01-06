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
 * Manage persons
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonManager {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_person';



	/**
	 * Get form object for person quick creation
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuForm
	 */
	public static function getQuickCreateForm($idPerson = 0) {
		$idPerson	= intval($idPerson);

			// Construct form object
		$xmlPath	= 'ext/contact/config/form/person.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idPerson);

			// Adjust form to needs of quick creation wizard
		$form->setAttribute('action', '?ext=contact&amp;controller=quickcreateperson');
		$form->setAttribute('onsubmit', 'return false');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.QuickCreatePerson.save(this.form)');
		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.removeUnusedImages(this.form);Todoyu.Popups.close(\'quickcreate\')');

			// Make sure that birthday field isn't set to default
		$form->setFieldFormData('birthday', false);

		return $form;
	}



	/**
	 * Get a person object. This functions uses the cache to
	 * prevent double object initialisation
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuContactPerson
	 */
	public static function getPerson($idPerson) {
		return TodoyuRecordManager::getRecord('TodoyuContactPerson', $idPerson);
	}



	/**
	 * Get person data array
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 * @deprecated
	 */
	public static function getPersonArray($idPerson) {
		return TodoyuRecordManager::getRecordData(self::TABLE, $idPerson);
	}



	/**
	 * Form hook to load persons foreign record data
	 * Load: company, contactinfo, address
	 *
	 * @param	Array		$data
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function hookPersonLoadFormData(array $data, $idPerson) {
		$idPerson	= intval($idPerson);

			// Set salutation for new persons
		if( ! isset($data['salutation']) ) {
			$data['salutation'] = 'm';
		}

		$data['company']	= TodoyuContactPersonManager::getPersonCompanyRecords($idPerson);
		$data['contactinfo']= TodoyuContactPersonManager::getContactinfoRecords($idPerson);
		$data['address']	= TodoyuContactPersonManager::getAddressRecords($idPerson);

		return $data;
	}



	/**
	 * Get all active persons
	 *
	 * @param	Array		$fields			By default, all fields are selected. You can provide a field list instead
	 * @param	Boolean		$showInactive	Also show inactive persons
	 * @return	Array
	 */
	public static function getAllActivePersons(array $fields = array(), $showInactive = false) {
		$fields	= sizeof($fields) === 0 ? '*' : implode(',', Todoyu::db()->escapeArray($fields));
		$table	= self::TABLE;
		$where	= 'deleted = 0';
		$order	= 'lastname, firstname';

		if( $showInactive !== true ) {
			$where .= ' AND is_active	= 1';
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get all persons with an active login
	 *
	 * @return	Array
	 */
	public static function getAllLoginPersons() {
		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		deleted		= 0'
				. ' AND is_active	= 1'
				. ' AND username	!= \'\''
				. ' AND password	!= \'\'';
		$order	= 'lastname, firstname';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Check whether $username and $password are a valid login
	 *
	 * @param	String		$username		Username
	 * @param	String		$password		Password as sha1
	 * @return	Boolean
	 */
	public static function isValidLogin($username, $password) {
		$username	= trim($username);
		$password	= trim($password);

			// Prevent empty login data
		if( $username === '' || $password === '' ) {
			return false;
		}

		$field	= 'id';
		$table	= self::TABLE;
		$where	= '		`username`	= ' . Todoyu::db()->quote($username, true) .
				  ' AND	`password`	= ' . Todoyu::db()->quote($password, true) .
				  ' AND	`is_active`	= 1
					AND	`deleted`	= 0';

		return Todoyu::db()->hasResult($field, $table, $where);
	}



	/**
	 * Check whether $idPerson is a valid person ID
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPerson($idPerson) {
		return TodoyuRecordManager::isRecord(self::TABLE, $idPerson);
	}



	/**
	 * Check whether a person with the username $username exists
	 *
	 * @param	String		$username
	 * @return	Boolean
	 */
	public static function personExists($username) {
		return self::getPersonIDByUsername($username) !== 0;
	}



	/**
	 * Get person ID by username
	 *
	 * @param	String		$username
	 * @return	Integer
	 */
	public static function getPersonIDByUsername($username) {
		$fields	= 'id';
		$table	= self::TABLE;
		$where	= '		username	= ' . Todoyu::db()->quote($username, true)
				. ' AND is_active	= 1'
				. ' AND deleted		= 0';
		$limit	= '1';

		$row	= Todoyu::db()->doSelectRow($fields, $table, $where, '', '', $limit);

		return 	intval($row['id']);
	}



	/**
	 * Get person by username
	 *
	 * @param	String		$username
	 * @return	TodoyuContactPerson
	 */
	public static function getPersonByUsername($username) {
		$idPerson	= self::getPersonIDByUsername($username);

		return self::getPerson($idPerson);
	}



	/**
	 * Add a new person
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addPerson(array $data = array()) {
		$idPerson = TodoyuRecordManager::addRecord(self::TABLE, $data);

		TodoyuHookManager::callHook('contact', 'person.add', array($idPerson));

		return $idPerson;
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

		TodoyuHookManager::callHook('contact', 'person.delete', array($idPerson));
	}



	/**
	 * Update a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$data
	 */
	public static function updatePerson($idPerson, array $data) {
		TodoyuRecordManager::removeRecordCache('TodoyuContactPerson', $idPerson);

		TodoyuRecordManager::updateRecord(self::TABLE, $idPerson, $data);

		TodoyuHookManager::callHook('contact', 'person.update', array($idPerson, $data));
	}



	/**
	 * Save person
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function savePerson(array $data) {
		$idPerson	= intval($data['id']);
		$xmlPath	= 'ext/contact/config/form/person.xml';

			// Create person in database if not existing
		if( $idPerson === 0 ) {
			$idPerson = self::addPerson();
		}

		if( $data['image_id'] != 0 ) {
			TodoyuContactImageManager::renameStorageFolder('person', $data['image_id'], $idPerson);
		}

		unset($data['image_id']);

			// Update/set password?
		if( strlen($data['password']) > 0 ) {
			$data['password'] = md5($data['password']);
		} else {
			unset($data['password']);
		}

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
	 * @param	Integer		$idPerson
	 * @param	String		$password
	 * @param	Boolean		$alreadyHashed		Is password already a md5 hash?
	 * @return	Boolean		Updated
	 */
	public static function updatePassword($idPerson, $password, $alreadyHashed = true) {
		$idPerson	=	intval($idPerson);

		if( ! $alreadyHashed ) {
			$password = md5($password);
		}

		$data		= array(
			'password'	=> $password
		);

		return self::updatePerson($idPerson, $data);
	}



	/**
	 * Get role IDs of a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getRoleIDs($idPerson) {
		$idPerson	= Todoyu::personid($idPerson);

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
		$where	= '		id_person	= ' . $idPerson .
				  ' AND	mm.id_role	= r.id';

		return Todoyu::db()->getArray($fields, $table, $where);
	}



	/**
	 * Get labels of person's roles
	 *
	 * @param	$idPerson
	 * @return	Array
	 */
	public static function getPersonRoleLabels($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);
		$roleIDs	= self::getPerson($idPerson)->getRoleIDs();

		$roles	= array();
		foreach($roleIDs as $idRole) {
			$roles[]= TodoyuRoleManager::getRole($idRole)->getTitle();
		}

		return $roles;
	}



	/**
	 * Check whether the given person belongs to any of the given roles
	 *
	 * @param	Array		$roles
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function hasAnyRole(array $roles, $idPerson = 0) {
		$personRoles	= TodoyuContactPersonManager::getRoleIDs($idPerson);

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
		$fields	=	'p.*';

		if( $getJobType ) {
			$fields	.= ', mm.id_jobtype';
		}
		if( $getWorkAddress ) {
			$fields .= ', mm.id_workaddress';
		}

		$table	= 	self::TABLE . ' p,
					ext_contact_company c,
					ext_contact_mm_company_person mm';
		$where	= '		p.id			= mm.id_person
					AND	mm.id_company	= c.id
					AND	c.is_internal	= 1
					AND	p.deleted		= 0	';
		$order	= '	p.lastname,
					p.firstname';

		return Todoyu::db()->getIndexedArray('id', $fields, $table, $where, '', $order);
	}



	/**
	 * Search for person
	 *
	 * @param	String		$sword
	 * @param	Array		$searchFields
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @return	Array
	 */
	public static function searchPersons($sword = '', array $searchFields = null, $size = 100, $offset = 0) {
		$fields	= 'SQL_CALC_FOUND_ROWS *';
		$table	= self::TABLE;
		$where	= ' deleted = 0';
		$order	= 'lastname';
		$limit	= ($size != '') ? intval($offset) . ',' . intval($size) : '';

		$swords	= TodoyuArray::trimExplode(' ', $sword);
		if( sizeof($swords) > 0 ) {
			$searchFields	= is_null($searchFields) ? array('username', 'email', 'firstname', 'lastname', 'shortname') : $searchFields;

			$where	.= ' AND ' . Todoyu::db()->buildLikeQuery($swords, $searchFields);
		}

			// Limit results to allowed person records
		if( ! Todoyu::allowed('contact', 'person:seeAllPersons') ) {
			$where .= ' AND ' . TodoyuContactPersonRights::getAllowedToBeSeenPersonsWhereClause();
		}

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);
	}



	/**
	 * Get list of all persons within given amount limit (unconditional search)
	 *
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getList($limit) {
		return self::searchPersons('', null, $limit);
	}



	/**
	 * Get label of database relation
	 *
	 * @param	TodoyuFormElement		$field
	 * @param	Array					$record
	 * @return	String
	 */
	public static function getDatabaseRelationLabel(TodoyuFormElement $field, array $record) {
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
	public static function getLabel($idPerson, $showEmail = false, $lastnameFirst = true) {
		$idPerson	= intval($idPerson);
		$label		= '';

		if( $idPerson !== 0 ) {
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

			// Save contact info
		if( isset($data['contactinfo']) ) {
			$contactInfoIDs	= TodoyuArray::getColumn($data['contactinfo'], 'id');

				// Delete all contact infos which are no longer linked
			self::deleteRemovedContactInfos($idPerson, $contactInfoIDs);

				// If contact infos submitted
			if( sizeof($data['contactinfo']) > 0 ) {
				$infoIDs	= array();
				foreach($data['contactinfo'] as $contactInfo) {
					$infoIDs[] = TodoyuContactContactInfoManager::saveContactInfos($contactInfo);
				}

				self::linkContactInfos($idPerson, $infoIDs);
			}

			unset($data['contactinfo']);
		}



			// Save address
		if( isset($data['address']) ) {
			$addressIDs	= TodoyuArray::getColumn($data['address'], 'id');

				// Delete all addresses which are no longer linked
			self::deleteRemovedAddresses($idPerson, $addressIDs);

				// If addresses submitted
			if( is_array($data['address']) ) {
				$addressIDs	= array();
				foreach($data['address'] as $address) {
					$addressIDs[] =  TodoyuContactAddressManager::saveAddress($address);
				}

				self::linkAddresses($idPerson, $addressIDs);
			}

			unset($data['address']);
		}



			// Save company
		if( isset($data['company']) ) {
			$companyIDs	= TodoyuArray::getColumn($data['company'], 'id');

				// Remove all person links which are no longer active
			self::removeRemovedCompanies($idPerson, $companyIDs);

			if( sizeof($data['company']) > 0 ) {
				foreach($data['company'] as $index => $company) {
						// Prepare data form mm-table
					$data['company'][$index]['id_company']	= $company['id'];
					$data['company'][$index]['id_person']	= $idPerson;
					unset($data['company'][$index]['id']);
				}

				self::saveCompanyLinks($idPerson, $data['company']);
			}

			unset($data['company']);
		}



			// Save roles
		if( isset($data['role']) ) {
			$roleIDs	= TodoyuArray::getColumn($data['role'], 'id');

				// Remove all role links which are no longer active
			self::removeRemovedRoles($idPerson, $roleIDs);

				// Save roles
			if( sizeof($roleIDs) > 0 ) {
				TodoyuRoleManager::addPersonToRoles($idPerson, $roleIDs);
			}

			unset($data['role']);
		}

		return $data;
	}



	/**
	 * Remove person object from cache
	 *
	 * @param	Integer		$idPerson
	 */
	public static function removeFromCache($idPerson) {
		$idPerson		= intval($idPerson);

		TodoyuRecordManager::removeRecordCache('TodoyuContactPerson', $idPerson);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idPerson);
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
			$where	= '		id_person IN (' . implode(',', $personIDs) . ') '
					. ' AND id_workaddress != 0';

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
	 * @param	TodoyuDayRange	$range
	 * @return	Array
	 */
	public static function getBirthdayPersons(TodoyuDayRange $range) {
		$dateStart	= $range->getStart();
		$dateEnd	= $range->getEnd();

		$monthStart	= date('n', $dateStart);
		$monthEnd	= date('n', $dateEnd);
		$dayStart	= date('j', $dateStart);
		$dayEnd		= date('j', $dateEnd);


		if( $range->isInOneDay() ) { // One day range
			$rangeWhere	= 'MONTH(birthday) = ' . $monthStart . ' AND DAY(birthday) = ' . $dayStart;
		} elseif( $range->isInOneMonth() ) { // One month range
			$rangeWhere = 'MONTH(birthday) = ' . $monthStart . ' AND DAY(birthday) BETWEEN ' . $dayStart . ' AND ' . $dayEnd;
		} else { // all the rest
			if( $monthEnd < $monthStart ) { // Range overlaps two years
				$months			= array();
				$shiftedMonthEnd= $monthEnd + 12;

				for($monthCounter = $monthStart; $monthCounter <= $shiftedMonthEnd; $monthCounter++) {
					$month		= $monthCounter % 12;
					$months[]	= $month === 0 ? 12 : $month;
				}
			} else {
				$months = range($monthStart, $monthEnd);
			}
				// Fetch first and last month for day checks
			$firstMonth	= array_shift($months);
			$lastMonth	= array_pop($months);

			$rangeWhere	= '		MONTH(birthday) = ' . $firstMonth . ' AND DAY(birthday)	>= ' . $dayStart
						. ' OR	MONTH(birthday)	= ' . $lastMonth .  ' AND DAY(birthday)	<= ' . $dayEnd;

				// All months between
			if( sizeof($months) > 0 ) {
				$rangeWhere .= ' OR MONTH(birthday) IN(' . implode(',', $months) . ')';
			}
		}


		$fields	= '	id,
					email,
					firstname,
					lastname,
					shortname,
					salutation,
					title,
					birthday';
		$where	= '		deleted	= 0'
				. '	AND	(' . $rangeWhere . ')';
		$order	= '	IF(MONTH(birthday) >= ' . $monthStart . ',1,0) DESC,
					IF(MONTH(birthday) > ' . $monthStart . ',1,0) ASC,
					MONTH(birthday) ASC,
					DAY(birthday) ASC';

		$birthdayPersons	= Todoyu::db()->getArray($fields, self::TABLE, $where, '', $order);

			// Enrich data with date and age of persons
		$birthdayPersons	= self::addBirthdayPersonsDateAndAge($birthdayPersons, $dateStart, $dateEnd);

		return $birthdayPersons;
	}



	/**
	 * Enrich data array of persons and birthdays with resp. age and date
	 *
	 * @param	Array		$birthdayPersons
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	private static function addBirthdayPersonsDateAndAge(array $birthdayPersons, $dateStart, $dateEnd) {
		foreach($birthdayPersons as $index => $birthdayPerson) {
			$dateParts	= explode('-', $birthdayPerson['birthday']);
			$birthday	= mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateStart));

				// If a persons birthday is in the next year, use $dateEnd for year information
			if( $birthday < $dateStart ) {
				$birthday = mktime(0, 0, 0, $dateParts[1], $dateParts[2], date('Y', $dateEnd));

				$birthdayPersons[$index]['age']	= floor(date('Y', $dateStart) - date('Y', strtotime($birthdayPerson['birthday'])) + 1);
			} else {
				$birthdayPersons[$index]['age']	= floor(date('Y', $dateStart) - date('Y', strtotime($birthdayPerson['birthday'])));
			}

				// Set date of the upcoming birthday
			$birthdayPersons[$index]['date'] 	= $birthday;
		}

		return $birthdayPersons;
	}



	/**
	 * Get color IDs to given person id's (persons are given enumerated colors by their position in the list)
	 *
	 * @param	Array	$personIDs
	 * @return	Array
	 */
	public static function getSelectedPersonColor(array $personIDs) {
		$personIDs	= TodoyuArray::intval($personIDs, true, true);
		$cacheKey	= 'personcolors:' . md5(serialize($personIDs));

		if( ! TodoyuCache::isIn($cacheKey) ) {
			$colors 			= array();

				// Enumerate persons by system specific color to resp. list position
			foreach($personIDs as $idPerson) {
				$colors[$idPerson]	= TodoyuColors::getColorArray($idPerson);
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
		$where	= '		mm.id_company	= c.id
					AND	mm.id_person	= ' . $idPerson .
				  ' AND c.deleted 		= 0 ';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Remove company links which are no longer active
	 * Companies stays untouched, only the link with the extra data will be removed
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$companyIDs
	 */
	public static function removeRemovedCompanies($idPerson, array $companyIDs) {
		TodoyuDbHelper::deleteOtherMmLinks('ext_contact_mm_company_person', 'id_person', 'id_company', $idPerson, $companyIDs);
	}



	/**
	 * Save linked person and linking data
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$linkData
	 */
	public static function saveCompanyLinks($idPerson, array $linkData) {
		TodoyuDbHelper::saveExtendedMMLinks('ext_contact_mm_company_person', 'id_person', 'id_company', $idPerson, $linkData);
	}



	/**
	 * Remove role links which are no longer active
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$roleIDs
	 */
	public static function removeRemovedRoles($idPerson, array $roleIDs) {
		TodoyuDbHelper::deleteOtherMmLinks('ext_contact_mm_person_role', 'id_person', 'id_role', $idPerson, $roleIDs);
	}



	/**
	 * Delete all contactinfos except the given ones
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$currentContactInfoIDs
	 * @return	Integer		Deleted records
	 */
	public static function deleteRemovedContactInfos($idPerson, array $currentContactInfoIDs) {
		return TodoyuContactContactInfoManager::deleteLinkedContactInfos('person', $idPerson, $currentContactInfoIDs, 'id_person');
	}



	/**
	 * Link a person with contactinfos
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$contactinfoIDs
	 */
	public static function linkContactinfos($idPerson, array $contactinfoIDs) {
		TodoyuDbHelper::addMMLinks('ext_contact_mm_person_contactinfo', 'id_person', 'id_contactinfo', $idPerson, $contactinfoIDs);
	}



	/**
	 * Delete all company addresses which are no longer active
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$currentAddressIDs	Active addresses which will not be deleted
	 * @return	Integer
	 */
	public static function deleteRemovedAddresses($idPerson, array $currentAddressIDs) {
		return TodoyuContactAddressManager::deleteLinkedAddresses('ext_contact_mm_person_address', $idPerson, $currentAddressIDs, 'id_person');
	}



	/**
	 * Link a person with addresses
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$addressIDs
	 */
	public static function linkAddresses($idPerson, array $addressIDs) {
		TodoyuDbHelper::addMMLinks('ext_contact_mm_person_address', 'id_person', 'id_address', $idPerson, $addressIDs);
	}



	/**
	 * Get address records for a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getAddressRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	a.*';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_person_address mm';
		$where	= ' 	a.deleted		= 0'
				. ' AND mm.id_address	= a.id'
				. ' AND	mm.id_person	= ' . $idPerson;

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get contact records for a person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getContactinfoRecords($idPerson) {
		$idPerson	= intval($idPerson);

		$fields	= '	c.*, t.category infotype_category';
		$tables	= '	ext_contact_contactinfotype t,
					ext_contact_contactinfo c,
					ext_contact_mm_person_contactinfo mm';
		$where	= ' 	mm.id_contactinfo	= c.id
					AND	mm.id_person		= ' . $idPerson .
				  ' AND c.deleted			= 0' .
				  ' AND t.id				= c.id_contactinfotype';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Gets the preview image for the form
	 *
	 * @param	TodoyuFormElement_Comment	$formElement
	 * @return	String
	 */
	public static function getPreviewImageForm(TodoyuFormElement_Comment $formElement) {
		return TodoyuContactImageManager::renderImageForm($formElement, 'person');
	}




	/**
	 * Get link to detail view of a person
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getDetailLink($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= self::getPerson($idPerson);

		$linkParams	= array(
			'ext'		=> 'contact',
			'controller'=> 'person',
			'action'	=> 'detail',
			'person'	=> $idPerson,
		);

		return TodoyuString::wrapTodoyuLink($person->getLabel(), 'contact', $linkParams);
	}

}
?>