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
 * Manager class Todoyufor the contact module
 *
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
	 * Get list of all persons within given amount limit (unconditional search)
	 *
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getListPersons($limit) {
		return TodoyuPersonManager::searchPersons('', null, $limit);
	}



	/**
	 * Get contact type from XML
	 *
	 * @param	String	$contactType
	 * @return	String
	 */
	public static function getContactTypeFromXml($contactType) {
		$typeConfig	= Todoyu::$CONFIG['EXT']['contact']['contacttypes'][$contactType];

		if( is_array($typeConfig) ) {
			return $typeConfig['formXml'];
		} else {
//			TodoyuDebug::printInFirebug($contactType, 'Invalid contact tpye');
			return false;
		}
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
	public static function getContactTypeLabel($type)	{
		return Todoyu::$CONFIG['EXT']['contact']['contacttypes'][$type]['label'];
	}



	/**
	 * Get contact type object
	 *
	 * @param	String				$type
	 * @param	Integer				$idRecord
	 * @return	TodoyuBaseObject
	 */
	public static function getContactTypeObj($type, $idRecord)	{
		$idRecord	= intval($idRecord);
		$className	= self::getContactTypeObjClass($type);

		return new $className($idRecord);
	}



	/**
	 * Get country label
	 *
	 * @param	TodoyuForm		$form
	 * @param	Array			$option
	 * @return	String
	 */
	public static function getCountryLabel($form, $option)	{

		return $option[ 'name_' . TodoyuLanguage::getLocale() ];
	}



	/**
	 * Render options array (value, label, selected-state of all options)
	 *
	 * @param	Array	$res
	 * @param 	String	$valueField
	 * @param 	String	$labelField
	 * @param 	Mixed	$selectedIndex	Integer / null
	 * @param 	Mixed	$selectedValue	Integer / null
	 * @return	Array	options array
	 */
	public static function buildOptionsArray( $res, $valueField, $labelField, $selectedIndex = null, $selectedValue = null ) {
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
	 * Saves company
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public function saveCompany(array $data)	{
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$idCompany	= intval($data['id']);

		if( $idCompany === 0 ) {
			$idCompany = TodoyuCompanyManager::addCompany();
		}

			// Save own external fields
		$data	= self::saveCompanyForeignRecords($data, $idCompany);

			// Call save data hook
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idCompany);

		TodoyuCompanyManager::updateCompany($idCompany, $data);
		TodoyuCompanyManager::removeFromCache($idCompany);

		return $idCompany;
	}









	/**
	 * Form hook to load persons foreign record data
	 * Load: company, contactinfo, address
	 *
	 * @param	Array		$data
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPersonForeignRecordData(array $data, $idPerson) {
		$idPerson	= intval($idPerson);

		$data['company']	= TodoyuPersonManager::getPersonCompanyRecords($idPerson);
		$data['contactinfo']= TodoyuPersonManager::getContactinfoRecords($idPerson);
		$data['address']	= TodoyuPersonManager::getAddressRecords($idPerson);

		return $data;
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
	 * Get listing data for persons
	 * Keys: [total,rows]
	 *
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @return	Array
	 */
	public static function getPersonListingData($size, $offset = 0, $searchWord = '') {
		$data	= array();
		$persons= TodoyuPersonManager::searchPersons($searchWord, null, $size, $offset);

		$data	= array(
			'rows'	=> array(),
			'total'	=> Todoyu::db()->getTotalFoundRows()
		);

		foreach($persons as $person) {
			$data['rows'][] = array(
				'icon'		=> '',
				'iconClass'	=> intval($person['active']) === 1 ? 'login' : '',
				'lastname'	=> $person['lastname'],
				'firstname'	=> $person['firstname'],
				'email'		=> $person['email'],
				'company'	=> TodoyuPersonManager::getPersonsMainCompany($person['id'])->getTitle(),
				'actions'	=> TodoyuContactRenderer::renderPersonActions($person['id'])
			);
		}

		return $data;
	}



	/**
	 * Get listing data for companies
	 *
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @param	Integer		$searchWord
	 * @return	Array
	 */
	public static function getCompanyListingData($size, $offset = 0, $searchWord = '') {
		$companies	= TodoyuCompanyManager::searchCompany($searchWord, null, $size, $offset);

		$data	= array(
			'rows'	=> array(),
			'total'	=> Todoyu::db()->getTotalFoundRows()
		);

		foreach($companies as $index => $company) {
			$data['rows'][] = array(
				'icon'		=> '',
				'title'		=> $company['title'],
				'persons'	=> TodoyuCompanyManager::getNumPersons($company['id']),
				'address'	=> TodoyuCompanyManager::getCompanyAddress($company['id']),
				'actions'	=> TodoyuContactRenderer::renderCompanyActions($company['id'])
			);
		}

		return $data;
	}

}

?>