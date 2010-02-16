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
		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'];
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
		$typeConfig	= $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$contactType];

		if( is_array($typeConfig) ) {
			return $typeConfig['formXml'];
		} else {
			TodoyuDebug::printInFirebug($contactType, 'Invalid contact tpye');
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
		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$type]['objClass'];
	}



	/**
	 * Get label of contact type
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getContactTypeLabel($type)	{
		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$type]['label'];
	}



	/**
	 * Get contact type object
	 *
	 * @param	String				$type
	 * @param	Intger				$idRecord
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

		return $option[ 'name_' . TodoyuLanguage::getLanguage() ];
	}



	/**
	 * Modify company persons' form to contain jobtype selector field (hooked)
	 *
	 * @param	TodoyuForm	$form
	 * @param	Integer	$userIndex
	 * @return	TodoyuForm
	 */
//	public static function modifyUserFormfields(TodoyuForm $form, $userIndex) {
//		$userIndex		= intval( $userIndex );
//		$contactType	= TodoyuContactPreferences::getActiveTab();
//
//		if ($contactType == 'company') {
//			$form->addElementsFromXML( 'ext/contact/config/form/user-jobtypeaddress.xml' );
//		}
//
//		return $form;
//	}
//



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
	 * Save Person (User from contact)
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function savePerson(array $data)	{
		$xmlPath= self::getContactTypeFromXml('person');
		$idUser	= intval($data['id']);

		if( $idUser === 0 )	{
			$idUser = TodoyuPersonManager::addUser();
		}

		$data	= self::savePersonForeignRecords($data, $idUser);
		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idUser);

		TodoyuPersonManager::updatePerson($idUser, $data);
		TodoyuPersonManager::removeFromCache($idUser);

		return $idUser;
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
	 * Hanldes the foreign records of a user
	 *
	 * Extract:
	 * 	- company
	 *  - contactinfos
	 *  - address
	 *
	 * @param	Array	$userData
	 * @param	Integer	$userID
	 * @return	Array
	 */
	public static function savePersonForeignRecords(array $data, $idUser)	{
		$idUser		= intval($idUser);







		return $data;
	}














	/**
	 * Save company foreign records form hook
	 * Extract:
	 * - contactinfo
	 * - address
	 * - user
	 *
	 * @param	Array		$data			Company form data
	 * @param	Integer		$idCompany		Company ID
	 * @return	Array
	 */
	public static function saveCompanyForeignRecords(array $data, $idCompany) {
		$idCompany = intval($idCompany);

			// Remove existing records
		TodoyuCompanyManager::removeAllContactinfo($idCompany);
		TodoyuCompanyManager::removeAllAddresses($idCompany);
		TodoyuCompanyManager::removeAllUsers($idCompany);

			// Save contactinfo records
		if( ! empty($data['contactinfo']) ) {
			$infoIDs	= array();
			foreach($data['contactinfo'] as $contactInfo) {
				$infoIDs[] = TodoyuContactInfoManager::saveContactInfos($contactInfo);
			}

			TodoyuDbHelper::saveMMrelations('ext_contact_mm_company_contactinfo', 'id_company', 'id_contactinfo', $idCompany, $infoIDs);
		}
		unset($data['contactinfo']);


			// Save address records
		if( ! empty($data['address']) ) {
			$addressIDs	= array();
			foreach($data['address'] as $address) {
				$addressIDs[] =  TodoyuAddressManager::saveAddress($address);
			}

			TodoyuDbHelper::saveMMrelations('ext_contact_mm_company_address', 'id_company', 'id_address', $idCompany, $addressIDs);
		}
		unset($data['address']);


			// Save users
		if( ! empty($data['user']) ) {
			foreach($data['user'] as $user) {
					// Prepare data form mm-table
				$user['id_person']	= $user['id'];
				$user['id_company']= $idCompany;
				unset($user['id']);

					// Remove existing link and save new data
				$idLink = TodoyuDbHelper::saveExtendedMMrelation('ext_contact_mm_company_person', 'id_company', 'id_person', $idCompany, $user['id_person'], $user);
			}
		}
		unset($data['user']);


		return $data;
	}



	/**
	 * Form hook to load users foreign record data
	 * Load: company, contactinfo, address
	 *
	 * @param	Array		$data
	 * @param	Integer		$idUser
	 * @return	Array
	 */
	public static function getUserForeignRecordData(array $data, $idUser) {
		$idUser	= intval($idUser);

		$data['company']	= TodoyuPersonManager::getPersonCompanyRecords($idUser);
		$data['contactinfo']= TodoyuPersonManager::getContactinfoRecords($idUser);
		$data['address']	= TodoyuPersonManager::getAddressRecords($idUser);

		return $data;
	}



	/**
	 * Get address types
	 *
	 * @return	Array
	 */
	public static function getAddressTypes() {
		return TodoyuArray::assure($GLOBALS['CONFIG']['EXT']['contact']['addresstypes']);
	}



	/**
	 * Get categories of contactinfo types
	 *
	 * @return	Array
	 */
	public static function getContactinfotypeCategories() {
		return TodoyuArray::assure($GLOBALS['CONFIG']['EXT']['contact']['contactinfotypecategories']);
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
				'iconClass'	=> $person['username'] !== '' ? 'user' : '',
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
				'users'		=> TodoyuCompanyManager::getNumUsers($company['id']),
				'address'	=> TodoyuCompanyManager::getCompanyAddress($company['id']),
				'actions'	=> TodoyuContactRenderer::renderCompanyActions($company['id'])
			);
		}

		return $data;
	}

}

?>