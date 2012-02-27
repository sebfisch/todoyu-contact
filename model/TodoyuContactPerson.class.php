<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Person object
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPerson extends TodoyuBaseObject {

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
	public function getLabel($showEmail = false, $lastnameFirst = true, $showTitle = false, $idRole = 0) {
		 $label	= $this->getFullName($lastnameFirst);

		if( $showTitle ) {
			/** @var	TodoyuRole		$role  */
			$role	= TodoyuRoleManager::getRole($idRole);

			$label	.= ', ' . $role->getTitle();
		}

		 if( $showEmail ) {
			$email	= $this->getEmail(true);

			if( $email ) {
				$label	.= ' (' . $this->getEmail() . ')';
			}
		}

		return $label;
	}



	/**
	 * Get last name
	 *
	 * @return	String
	 */
	public function getLastName() {
		return $this->get('lastname');
	}



	/**
	 * Get first name
	 *
	 * @return	String
	 */
	public function getFirstName() {
		return $this->get('firstname');
	}



	/**
	 * Check whether person is an admin
	 *
	 * @return	Boolean
	 */
	public function isAdmin() {
		return intval($this->data['is_admin']) === 1;
	}



	/**
	 * Get username
	 *
	 * @return	String
	 */
	public function getUsername() {
		return $this->get('username');
	}



	/**
	 * Get password
	 *
	 * @return	String
	 */
	public function getPassword() {
		return $this->get('password');
	}



	/**
	 * Check whether a person works in a internal company
	 *
	 * @return	Boolean
	 */
	public function isInternal() {
		if( !isset($this->cache['isInternal']) ) {
			$companies	= $this->getCompanies();
			$isInternal	= false;

			foreach($companies as $company) {
				if( $company->isInternal() ) {
					$isInternal = true;
					break;
				}
			}

			$this->cache['isInternal'] = $isInternal;
		}

		return $this->cache['isInternal'];
	}



	/**
	 * Check whether person is external (not in an internal company)
	 *
	 * @return	Boolean
	 */
	public function isExternal() {
		return !$this->isInternal();
	}



	/**
	 * Get IDs of the roles the person is a member of
	 *
	 * @return	Array
	 */
	public function getRoleIDs() {
		if( !$this->isInCache('roleids') ) {
			$roleIDs	= TodoyuContactPersonManager::getRoleIDs($this->getID());

			$this->addToCache('roleids', $roleIDs);
		}

		return $this->getCacheItem('roleids');
	}



	/**
	 * Check whether user is assigned to role
	 *
	 * @param	Integer		$idRole
	 * @return	Boolean
	 */
	public function hasRole($idRole) {
		$idRole	= intval($idRole);

		return in_array($idRole, $this->getRoleIDs());
	}


	/**
	 * Check whether the user is assigned to any of the roles
	 *
	 * @param	Array	$roleIDs
	 * @return	Boolean
	 */
	public function hasAnyRole(array $roleIDs) {
		return sizeof(array_intersect($roleIDs, $this->getRoleIDs())) > 0;
	}



	/**
	 * Get fullname of the person
	 *
	 * @param	Boolean		$lastnameFirst
	 * @return	String
	 */
	public function getFullName($lastnameFirst = false) {
		if( $this->getID() > 0 ) {
			if( $lastnameFirst ) {
				return $this->getLastname() . ' ' . $this->getFirstname();
			} else {
				return $this->getFirstname() . ' ' . $this->getLastname();
			}
		} else {
			return '';
		}
	}



	/**
	 * Get contact email address
	 *
	 * @param	Boolean		$checkContactInfo
	 * @return	String|Boolean
	 */
	public function getEmail($checkContactInfo = true) {
		$email	= $this->getAccountEmail();

		if( $email === '' && $checkContactInfo ) {
			$preferredEmails	= TodoyuContactContactInfoManagerPerson::getEmails($this->getID());

			if( sizeof($preferredEmails) > 0 ) {
				$email	= trim($preferredEmails[0]['info']);
			}
		}

		return $email === '' ? false : $email;
	}



	/**
	 * Get email of user account
	 *
	 * @return	String
	 */
	public function getAccountEmail() {
		return trim($this->get('email'));
	}



	/**
	 * Check whether the user has an email address for his account
	 *
	 * @param	Boolean		$checkContactInfo
	 * @return	Boolean
	 */
	public function hasEmail($checkContactInfo = false) {
		return trim($this->getEmail($checkContactInfo)) !== '';
	}



	/**
	 * Check whether person has an account email address
	 *
	 * @return	Boolean
	 */
	public function hasAccountEmail() {
		return trim($this->getAccountEmail()) !== '';
	}



	/**
	 * Check whether persons account is active
	 *
	 * @return	Boolean
	 */
	public function isActive() {
		return intval($this->get('is_active')) === 1;
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
	 * Get person comment
	 *
	 * @return	String
	 */
	public function getComment() {
		return $this->get('comment');
	}



	/**
	 * Get person locale preference
	 * Use system as fallback if not disabled by parameter
	 *
	 * @param	Boolean		$system			Use system locale when user locale not set yet
	 * @return	String
	 */
	public function getLocale($system = true) {
		$locale	= TodoyuContactPreferences::getLocale();

		if( !$locale && $system ) {
			$locale	= Todoyu::getLocale();
		}

		return $locale;
	}



	/**
	 * Get all company IDs of a person (a person can work for multiple companies)
	 *
	 * @return	Array
	 */
	public function getCompanyIDs() {
		$field	= '	mm.id_company';
		$table	= '	ext_contact_mm_company_person mm,
					ext_contact_company c';
		$where	= '		mm.id_person	= ' . $this->getID() . '
					AND	mm.id_company	= c.id
					AND	c.deleted		= 0';

		return Todoyu::db()->getColumn($field, $table, $where, '', '', '', 'id_company');
	}



	/**
	 * Get all companies of the person
	 *
	 * @return	TodoyuContactCompany[]
	 */
	public function getCompanies() {
		$fields	= '	c.id';
		$table	= '	ext_contact_mm_company_person mm,
					ext_contact_company c';
		$where	= '	mm.id_person	= ' . $this->getID() . ' AND
					mm.id_company	= c.id AND
					c.deleted		= 0';

		$companyIDs	= Todoyu::db()->getColumn($fields, $table, $where, '', '', '', 'id');

		return TodoyuRecordManager::getRecordList('TodoyuContactCompany', $companyIDs);
	}



	/**
	 * Get main company
	 *
	 * @todo	There is not really a main company, we just take the first one
	 * @return	TodoyuContactCompany
	 */
	public function getCompany() {
		$companyIDs		= $this->getCompanyIDs();
		$firstCompanyID	= intval($companyIDs[0]);

		return TodoyuContactCompanyManager::getCompany($firstCompanyID);
	}



	/**
	 * Get timezone of the person
	 * The timezone is defined in the assigned working address of the person's company
	 *
	 * @return	String		Or FALSE if non defined
	 */
	public function getTimezone() {
		if( $this->getID() !== 0 ) {
			$field	= '	tz.timezone';
			$tables	= '	ext_contact_mm_company_person mmcp,
						ext_contact_address a,
						static_timezone tz';
			$where	= '		mmcp.id_person		= ' . $this->getID() .
					  ' AND	mmcp.id_workaddress	= a.id
						AND	a.id_timezone		= tz.id';

			$timezones	= Todoyu::db()->getArray($field, $tables, $where);

			if( sizeof($timezones) > 0 ) {
				return $timezones[0]['timezone'];
			}
		}

		return false;
	}



	/**
	 * loads all employers of a person
	 *
	 * @return	Array
	 */
	public function getEmployers() {
		$employers	= TodoyuContactPersonManager::getPersonCompanyRecords($this->getID());

		foreach($employers as $index => $employer) {
			$employers[$index]['workaddress'] = TodoyuContactAddressManager::getAddress($employer['id_workaddress']);
			$employers[$index]['jobtype'] = TodoyuContactJobTypeManager::getJobType($employer['id_jobtype']);
		}

		return $employers;
	}


	/**
	 * Load all foreign records of a person
	 */
	public function loadForeignData() {
		$this->data['company']		= $this->getEmployers();
		$this->data['contactinfo']	= TodoyuContactPersonManager::getContactinfoRecords($this->getID());
		$this->data['address']		= TodoyuContactPersonManager::getAddressRecords($this->getID());
		$this->data['role']			= TodoyuContactPersonManager::getRoles($this->getID());
	}



	/**
	 * Get person template data
	 *
	 * @param	Boolean		$loadForeignRecords
	 * @return	Array
	 */
	public function getTemplateData($loadForeignRecords = false) {
		if( $loadForeignRecords ) {
			$this->loadForeignData();
		}

		$data = parent::getTemplateData();

		$data['fullname'] = $this->getFullName();

		return $data;
	}



	/**
	 * Parses the salutation to label
	 *
	 * @return	String
	 */
	public function getSalutationLabel() {
		if( $this->salutation == 'w' || $this->salutation == 'f' ) {
			$salutation	= 'f';
		} else {
				// Default
			$salutation	= 'm';
		}

		return Todoyu::Label('contact.ext.person.attr.salutation.' . $salutation);
	}



	/**
	 * Get phone contact infos
	 *
	 * @return	Array
	 */
	public function getPhones() {
		return TodoyuContactContactInfoManagerPerson::getPhones($this->getID());
	}


	public function getPhone() {
		$phones	= $this->getPhones();

		if( sizeof($phones) > 0 ) {
			return $phones[0]['info'];
		} else {
			return false;
		}
	}


	public function getContactEmails() {

	}



	/**
	 * Get main company (first linked) of the person
	 *
	 * @return	TodoyuContactCompany
	 */
	public function getMainCompany() {

		$field	= 'id_company';
		$table	= ' ext_contact_mm_company_person mm,
					ext_contact_company c';
		$where	= '		mm.id_company	= c.id
					AND c.deleted		= 0
					AND mm.id_person	= ' . $this->getID();
		$limit	= 1;

		$idCompany	= Todoyu::db()->getFieldValue($field, $table, $where, null, null, $limit);

		return TodoyuContactCompanyManager::getCompany($idCompany);
	}

}
?>