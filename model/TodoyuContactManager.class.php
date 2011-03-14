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
 * Manager class Todoyu for the contact module
 */
class TodoyuContactManager {

	/**
	 * Get configuration array for all contact type records
	 *
	 * @return	Array
	 */
	public static function getTypesConfig() {
		return Todoyu::$CONFIG['EXT']['contact']['contacttypes'];
	}



	/**
	 * Get contact type from XML
	 *
	 * @param	String	$contactType
	 * @return	Boolean|String
	 */
	public static function getContactTypeFromXml($contactType) {
		$typeConfig	= Todoyu::$CONFIG['EXT']['contact']['contacttypes'][$contactType];

		return is_array($typeConfig) ? $typeConfig['formXml'] : false;
	}



	/**
	 * Get contact type object class
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getContactTypeObjClass($type) {
		return Todoyu::$CONFIG['EXT']['contact']['contacttypes'][$type]['objClass'];
	}



	/**
	 * Get label of contact type
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getContactTypeLabel($type) {
		return Todoyu::$CONFIG['EXT']['contact']['contacttypes'][$type]['label'];
	}



	/**
	 * Get contact type object
	 *
	 * @param	String				$type
	 * @param	Integer				$idRecord
	 * @return	TodoyuBaseObject
	 */
	public static function getContactTypeObj($type, $idRecord) {
		$idRecord	= intval($idRecord);
		$className	= self::getContactTypeObjClass($type);

		return new $className($idRecord);
	}



	/**
	 * Render options array (value, label, selected-state of all options)
	 *
	 * @param	Array	$res
	 * @param	String	$valueField
	 * @param	String	$labelField
	 * @param	Mixed	$selectedIndex	Integer / null
	 * @param	Mixed	$selectedValue	Integer / null
	 * @return	Array	options array
	 */
	public static function buildOptionsArray($res, $valueField, $labelField, $selectedIndex = null, $selectedValue = null ) {
		$options	= array();
		foreach($res as $index => $data) {
			$options[]	= array(
				'value'		=> $data[ $valueField ],
				'label' 	=> $data[ $labelField ],
				'selected'	=> ( (! is_null($selectedIndex) && $index == $selectedIndex)  || (! is_null($selectedValue) && $data[ $valueField ] == $selectedValue) ) ? 'selected' : '',
				'disabled'	=> $data[ $valueField ] == -1 ? '1' : '',
			);
		}

		return $options;
	}



	/**
	 * Get address types
	 *
	 * @return	Array
	 */
	public static function getAddressTypes() {
		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['contact']['addresstypes']);
	}



	/**
	 * Get categories of contactinfo types
	 *
	 * @return	Array
	 */
	public static function getContactinfotypeCategories() {
		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['contact']['contactinfotypecategories']);
	}



	/**
	 * Get country label
	 *
	 * @param	TodoyuForm		$form
	 * @param	Array			$option
	 * @return	String
	 */
	public static function getCountryLabel($form, $option) {
		return $option[ 'name_' . TodoyuLabelManager::getLocale() ];
	}

}

?>