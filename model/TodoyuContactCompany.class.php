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
 * Company object
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompany extends TodoyuBaseObject {

	/**
	 * Constructor
	 *
	 * @param	Integer		$idCompany
	 */
	public function __construct($idCompany) {
		parent::__construct($idCompany, 'ext_contact_company');
	}



	/**
	 * Get company name (field name: title)
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->get('title');
	}



	/**
	 * Get company shortname
	 *
	 * @return	String
	 */
	public function getShortname() {
		return $this->get('shortname');
	}



	/**
	 * Get company label (field name: title)
	 *
	 * @return	String
	 */
	public function getLabel() {
		return $this->get('title');
	}



	/**
	 * Get short label
	 * Shortname or cropped title
	 *
	 * @return	String
	 */
	public function getShortLabel() {
		$label	= $this->getShortname();

		if( empty($label) ) {
			$label	= TodoyuString::crop($this->getTitle(), 8, '..', false);
		}

		return $label;
	}



	/**
	 * Get records of all persons which are employees in this company
	 *
	 * @return	Array
	 */
	public function getEmployeesRecords() {
		$employees	= TodoyuContactCompanyManager::getCompanyPersonRecords($this->getID());

		foreach( $employees as $index => $employee ) {
			$employees[$index]['workaddress']	= TodoyuContactAddressManager::getAddress($employee['id_workaddress']);
			$employees[$index]['jobtype']		= TodoyuContactJobTypeManager::getJobType($employee['id_jobtype']);
		}

		return $employees;
	}



	/**
	 * Get the ids of all persons which are employees of this company
	 *
	 * @return array
	 */
	public function getEmployeeIds() {
		$idCompany	= intval($this->getID());

		$fields	=	'	p.id';
		$tables	=	'	ext_contact_person p,
						ext_contact_mm_company_person mm';
		$where	=	'		mm.id_person	= p.id
						AND	mm.id_company	= ' . $idCompany .
					' AND	p.deleted		= 0';
		$order		= 'mm.id';
		$indexField	= 'id';

		return array_keys(Todoyu::db()->getArray($fields, $tables, $where, '', $order, '', $indexField));
	}



	/**
	 * Get all company address records
	 *
	 * @return	TodoyuContactAddress[]
	 */
	public function getAddresses() {
		$fields	= '	a.id';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_company_address mm';
		$where	= '		mm.id_address	= a.id'
				. ' AND	mm.id_company	= ' . $this->getID()
				. ' AND	a.deleted		= 0';
		$order	= ' a.is_preferred DESC';

		$addressIDs	= Todoyu::db()->getColumn($fields, $tables, $where, '', $order, '', 'id');

		return TodoyuRecordManager::getRecordList('TodoyuContactAddress', $addressIDs);
	}



	/**
	 * Check whether company is internal
	 *
	 * @return	Boolean
	 */
	public function isInternal() {
		return $this->getInt('is_internal') === 1;
	}



	/**
	 * Get contact info records data
	 *
	 * @return	Array
	 */
	public function getContactInfoRecords() {
		return TodoyuContactCompanyManager::getCompanyContactinfoRecords($this->getID());
	}



	/**
	 * Get address records data
	 *
	 * @return	Array
	 */
	public function getAddressRecords() {
		return TodoyuContactCompanyManager::getCompanyAddressRecords($this->getID());
	}



	/**
	 * Loads the related foreign record data to the company
	 */
	public function loadForeignData() {
		$this->data['person']		= $this->getEmployeesRecords();
		$this->data['contactinfo']	= $this->getContactInfoRecords();
		$this->data['address']		= $this->getAddressRecords();
	}



	/**
	 * Get template data for company
	 *
	 * @param	Boolean		$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData && $this->getID() !== 0 ) {
			$this->loadForeignData();
		}

		return parent::getTemplateData();
	}

}

?>