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
 * Contact Information
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactContactInfo extends TodoyuBaseObject {

	/**
	 * table of the record
	 *
	 * @var string
	 */
	private $table = 'ext_contact_contactinfo';



	/**
	 * constructor of the class
	 *
	 * @param	Integer		$idContactInfo
	 */
	function __construct($idContactInfo) {
		parent::__construct($idContactInfo, $this->table);
	}



	/**
	 * Get contact info type ID
	 *
	 * @return	Integer
	 */
	public function getContactInfoTypeID() {
		return intval($this->get('id_contactinfotype'));
	}



	/**
	 * Get contact info type
	 *
	 * @return	TodoyuContactContactInfoType
	 */
	public function getContactInfoType() {
		return 	TodoyuContactContactInfoTypeManager::getContactInfoType($this->getContactInfoTypeID());
	}



	/**
	 * Get type title
	 *
	 * @return	String
	 */
	function getTypeLabel() {
		return $this->getContactInfoType()->getTitle();
	}
}

?>