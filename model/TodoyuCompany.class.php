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
 * Company
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuCompany extends TodoyuBaseObject {

	/**
	 * Constructor
	 *
	 * @param	Integer		$idCompany
	 */
	public function __construct($idCompany) {
		parent::__construct($idCompany, 'ext_contact_company');
	}



	/**
	 * Get company name (fieldname: title)
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
	 * Get company label (fieldname: title)
	 *
	 * @return	String
	 */
	public function getLabel()	{
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
	 * Get all company data as array
	 *
	 * @return	Array
	 */
	public function getData() {
		return $this->data;
	}



	/**
	 * Loads the related foreign record data to the company
	 *
	 */
	public function loadForeignData()	{
		$this->data['person']		= TodoyuCompanyManager::getCompanyPersonRecords($this->id);
		$this->data['contactinfo']	= TodoyuCompanyManager::getCompanyContactinfoRecords($this->id);
		$this->data['address']		= TodoyuCompanyManager::getCompanyAddressRecords($this->id);
	}


	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData();
		}

		return parent::getTemplateData();
	}

}

?>