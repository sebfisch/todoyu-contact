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
 * class for address records
 *
 * @package Todoyu
 * @subpackage user
 */
class TodoyuAddress extends TodoyuBaseObject {

	/**
	 * constructor of the class
	 *
	 * @param	Integer	$addressID
	 */
	function __construct($idAddress) {
		parent::__construct($idAddress, 'ext_contact_address');
	}



	/**
	 * returns the full address as newline - splitted string
	 *
	 */
	public function getFullAddress() {
		$address = '';

		$address .= $this->get('street') . chr(10);
		if( $this->postbox )	{
			$address .= $this->postbox . chr(10);
		}

		$address .= TodoyuDatasource::getCountryShort($this->get('id_country')).' - '.$this->get('zip') . ' ' . $this->get('city');

		return $address;
	}
}
?>