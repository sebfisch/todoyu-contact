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
 * Address object
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactAddress extends TodoyuBaseObject {

	/**
	 * constructor of the class
	 *
	 * @param	Integer		$idAddress
	 */
	function __construct($idAddress) {
		parent::__construct($idAddress, 'ext_contact_address');
	}



	/**
	 * Get address type ID
	 *
	 * @return	Integer
	 */
	public function getAddressTypeID() {
		return $this->getInt('id_addresstype');
	}



	/**
	 * Get timezone ID
	 *
	 * @return	Integer
	 */
	public function getTimezoneID() {
		return $this->getInt('id_timezone');
	}



	/**
	 * Get timezone of address
	 *
	 * @return	String|Boolean
	 */
	public function getTimezone() {
		$timezone	= TodoyuStaticRecords::getTimezone($this->getTimezoneID());

		return is_array($timezone) ? $timezone['timezone'] : false;
	}



	/**
	 * Get country ID
	 *
	 * @return	Integer
	 */
	public function getCountryID() {
		return $this->getInt('id_country');
	}



	/**
	 * Get country
	 *
	 * @return	TodoyuCountry
	 */
	public function getCountry() {
		return TodoyuCountryManager::getCountry($this->getCountryID());
	}



	/**
	 * Get holiday set ID
	 *
	 * @return	Integer
	 */
	public function getHolidaySetID() {
		return $this->getInt('id_holidayset');
	}



	/**
	 * Get holidayset
	 *
	 * @return	TodoyuCalendarHolidaySet
	 */
	public function getHolidaySet() {
		return TodoyuCalendarHolidaySetManager::getHolidaySet($this->getHolidaySetID());
	}



	/**
	 * Get street
	 *
	 * @return	String
	 */
	public function getStreet() {
		return $this->get('street');
	}



	/**
	 * Get postbox
	 *
	 * @return	String
	 */
	public function getPostbox() {
		return $this->get('postbox');
	}



	/**
	 * Get city
	 *
	 * @return	String
	 */
	public function getCity() {
		return $this->get('city');
	}



	/**
	 * Get region ID
	 *
	 * @return	Integer
	 */
	public function getRegionID() {
		return $this->getInt('region');
	}



	/**
	 * Get region data
	 *
	 * @return	Array
	 */
	public function getRegion() {
		return TodoyuStaticRecords::getRecord('country_zone', $this->getRegionID());
	}



	/**
	 * Get label for region
	 *
	 * @return	String
	 */
	public function getRegionLabel() {
		$region	= $this->getRegion();

		return $region['id'] > 0 ? TodoyuStaticRecords::getLabel('core.static_country_zone', $region['iso_alpha3_country'] . '.' . $region['code']) : '';
	}



	/**
	 * Get zip
	 *
	 * @return	String
	 */
	public function getZip() {
		return $this->get('zip');
	}



	/**
	 * Get comment
	 *
	 * @return	String
	 */
	public function getComment() {
		return $this->get('comment');
	}



	/**
	 * Check whether address is marked as preferred
	 *
	 * @return	Boolean
	 */
	public function isPreferred() {
		return $this->getInt('is_preferred') === 1;
	}



	/**
	 * Get address label with all informations
	 *
	 * @return	String
	 */
	public function getLabel() {
		 return $this->getStreet() . ', ' . $this->getZip() . ', ' . $this->getCity() . ', ' . $this->getCountry()->getCode2();
	}



	/**
	 * Load foreign data
	 *
	 */
	protected function loadForeignData() {
		$this->data['country']	= $this->getCountry()->getTemplateData();
	}



	/**
	 * Get template data of address
	 * Foreign data: country
	 *
	 * @param	Boolean		$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData();
		}

		return parent::getTemplateData();
	}

}
?>