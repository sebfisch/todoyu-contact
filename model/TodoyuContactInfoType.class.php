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
 * Object class Todoyufor contactinfo types
 *
 * @package Todoyu
 * @subpackage user
 */
class TodoyuContactInfoType extends TodoyuBaseObject {

	/**
	 * constructor of the class
	 *
	 * @param	Integer	$contactInfoTypeID
	 */
	public function __construct($idContactInfoType) {
		parent::__construct($idContactInfoType, 'ext_contact_contactinfotype');
	}



	/**
	 * Gets the header title of the contact info element (the header shown also when the element is collapsed)
	 *
	 * @return	String
	 */
	public function getTitle() {
//		return TodoyuDiv::getLabel($this->get('title'));
		return Label($this->get('title'));
	}



	/**
	 * Gets the label for the selector (of all available contact types) option
	 *
	 * @param	TodoyuForm	$form
	 * @param	Array	$option
	 * @return	String
	 */
	public function getLabelForFormElementSelect($form, $option)	{
		return TodoyuLanguage::getLabel($option['title']);
	}



	/**
	 * Gets the lable for the formElement databaserelation
	 *
	 * @param	TodoyuFormElement_DatabaseRelation	$formElement
	 * @param	BaseObj							$record
	 * @return	String
	 */
	public static function getLabelForDatabaseRelation($formElement, $data)	{
		$ContactInfoType = new TodoyuContactInfoType($data['id_contactinfotype']);

		return (strlen(trim($ContactInfoType['title'])) > 0) ? TodoyuLanguage::getLabel($ContactInfoType['title']) : '';
	}

}
?>