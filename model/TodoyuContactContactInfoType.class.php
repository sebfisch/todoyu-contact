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
 * Object class Todoyufor contactinfo types
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactContactInfoType extends TodoyuBaseObject {

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
		return Label($this->get('title'));
	}



	/**
	 * Gets the label for the selector (of all available contact types) option
	 *
	 * @param	TodoyuForm	$form
	 * @param	Array	$option
	 * @return	String
	 */
	public function getLabelForFormElementSelect($form, $option) {
		return TodoyuLabelManager::getLabel($option['title']);
	}



	/**
	 * Checks if the contactinfotype is public or not
	 *
	 * @return	Boolean
	 */
	public function isPublic() {
		TodoyuDebug::printInFirebug($this->data);
		return intval($this->data['is_public']) === 1;
	}



	/**
	 * Gets the lable for the formElement databaserelation
	 *
	 * @param	TodoyuFormElement_DatabaseRelation	$formElement
	 * @param	BaseObj							$record
	 * @return	String
	 */
	public static function getLabelForDatabaseRelation($formElement, $data) {
		$ContactInfoType = new TodoyuContactContactInfoType($data['id_contactinfotype']);

		return (strlen(trim($ContactInfoType['title'])) > 0) ? TodoyuLabelManager::getLabel($ContactInfoType['title']) : '';
	}

}
?>