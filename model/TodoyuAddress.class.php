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
 * Address Record
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuAddress extends TodoyuBaseObject {

	/**
	 * constructor of the class
	 *
	 * @param	Integer		$idAddress
	 */
	function __construct($idAddress) {
		parent::__construct($idAddress, 'ext_contact_address');
	}



	/**
	 * Get timezone of address
	 *
	 * @return	String
	 */
	public function getTimezone() {
		$timezone	= TodoyuStaticRecords::getTimezone($this->get('id_timezone'));

		return is_array($timezone) ? $timezone['timezone'] : false;
	}



	/**
	 * Get country
	 * 
	 * @return	TodoyuCountry
	 */
	public function getCountry() {
		return TodoyuCountryManager::getCountry($this->get('id_country'));
	}
	


	/**
	 * Get address holidayset
	 *
	 * @return	TodoyuHolidaySet
	 */
	public function getHolidaySet() {
		return TodoyuHolidaySetManager::getHolidaySet($this->get('id_holidayset'));
	}



	/**
	 * Get address label with all informations
	 *
	 * @return	String
	 */
	public function getLabel() {
		 return $this->getStreet() . ', ' . $this->getZip() . ', ' . $this->getCity() . ', ' . $this->getCountry()->getCode2();
	}
	
}
?>