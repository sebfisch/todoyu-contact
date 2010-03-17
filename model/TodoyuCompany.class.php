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
		return empty($this->data['shortname']) ? TodoyuString::crop($this->getTitle(), 6, '', false) : $this->getShortname();
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