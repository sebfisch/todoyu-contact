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
	 * Returns the full address as linefeed-separated string
	 * @todo	Is this function still in use? why linebreaks?
	 * @return	String
	 */
	public function getFullAddress() {
		$address = '';

		$address .= $this->get('street') . chr(10);
		if( $this->postbox )	{
			$address .= $this->postbox . chr(10);
		}

		$country	= TodoyuStaticRecords::getCountry($this->get('id_country'));

		$address .= $country['iso_alpha2'] . ' - ' . $this->get('zip') . ' ' . $this->get('city');

		return $address;
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
	 * @return	Array
	 */
	public function getCountry() {
		return TodoyuStaticRecords::getCountry($this->get('id_country'));
	}
}
?>