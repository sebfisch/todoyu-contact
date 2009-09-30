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
	 * Get list of records
	 *
	 * @return	Array
	 */
	public static function getAllRecordList()	{
		return self::callContactTypeListFunction('*');
	}



	/**
	 * Get record list suiting to given search word
	 *
	 * @return	Array
	 */
	public static function getRecordList()	{
		$searchWord = TodoyuRequest::getParam('sword');

		if(! $searchWord)	{
			return false;
		} else {
			return self::callContactTypeListFunction($searchWord);
		}
	}



	/**
	 * Evoke registered user_func to render list of records of current edited contact type
	 *
	 * @param	String	$searchWord
	 * @return	Mixed	Array (/ false if no records found)
	 */
	protected static function callContactTypeListFunction($searchWord)	{
		$contactType	= TodoyuContactPreferences::getActiveTab();
		$funcRef		= self::getListFuncRef($contactType);

		if( TodoyuDiv::isFunctionReference($funcRef) ) {
			return TodoyuDiv::callUserFunction($funcRef, $searchWord);
		} else {
			return false;
		}
	}



	/**
	 * List persons
	 *
	 * @param	String $sword
	 * @return	Array
	 */
	public function ListPersons($sword)	{
		$userResult = TodoyuUserManager::searchUsers( array('firstname', 'lastname'), $sword );

		return self::getAssociatedContacttypeRecordsList($userResult, 'TodoyuUser');
	}



	/**
	 * List companies
	 *
	 * @param	String	$sword
	 * @return	Array
	 */
	public function ListCompanies($sword)	{
		$companyResult = TodoyuCustomerManager::searchCustomers(array('title', 'shortname'), $sword);

		return self::getAssociatedContacttypeRecordsList($companyResult, 'TodoyuCustomer');
	}



	/**
	 * Get array of associated contact type records (e.g customers or persons)
	 *
	 * @param	DB-Reference	$searchResult
	 * @param	String			$contacttypeClass
	 * @return	Mixed			Array / false if no records found
	 */
	private static function getAssociatedContacttypeRecordsList($searchResult, $contacttypeClass = 'TodoyuCustomer') {
		$resArray	= array();
		$index		= 0;

		if( $searchResult === false )	{
				// No entry found
			$resArray	= false;
		} else {
				// Iterate associated records (persons/ companies..)
			while($assRecord = Todoyu::db()->fetchAssoc($searchResult))	{
				$typeObj = new $contacttypeClass($assRecord['id']);

				$resArray[$index]					= $typeObj->getTemplateData();
				$resArray[$index]['renderClass']	= ($index % 2) ? 'odd' : 'even';

				$index++;
			}
		}

		return $resArray;
	}



	/**
	 * List job types
	 *
	 * @param 	String $sword
	 * @return	Array
	 */
	public function ListJobtypes()	{

		return self::getContactTypeEntriesArray('ext_user_jobtype');
	}




	/**
	 * List customer roles
	 *
	 * @param	String $sword
	 * @return	Array
	 */
	public function ListCustomerroles()	{

		return self::getContactTypeEntriesArray('ext_user_customerrole');
	}



	/**
	 * Get array ('id' and 'label') of records to one of the various contact type records
	 *
	 * @param	String	$table
	 * @return	Array
	 */
	private static function getContactTypeEntriesArray($table = 'ext_user_customerrole') {
		$result	= Todoyu::db()->doSelect('id, title', $table, 'deleted = 0', '', 'title');

		$array	= array();
		while($row = Todoyu::db()->fetchAssoc($result))	{
			$array[] = array(
				'id'	=> $row['id'],
				'label'	=> $row['title'],
			);
		}

		return $array;
	}



	/**
	 * Prepare base obj for list data
	 *
	 * @param	Resource		$result
	 * @param	String			$table
	 * @return	Array
	 */
	protected function prepareBaseObjListData($result, $table)	{
		$baseObjArray	= array();
		$index			= 0;

		while($baseObjRecord = Todoyu::db()->fetchAssoc($result))	{
			$baseObj = new TodoyuBaseObject($baseObjRecord['id'], $table);

			$baseObjArray[$index] = $baseObj->getTemplateData();
			$baseObjArray[$index]['renderClass'] = ($index%2) ? 'odd':'even';

			$index++;
		}

		return $baseObjArray;
	}



	/**
	 * Get func_ref (listing renderer) of current contact type
	 *
	 * @return unknown
	 */
	protected static function getListFuncRef()	{
		$contactType = TodoyuContactPreferences::getActiveTab();

		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$contactType]['listFunc'];
	}



	/**
	 * Get list template to current contact type
	 *
	 * @return unknown
	 */
	public static function getListTemplate()	{
		$contactType	= TodoyuContactPreferences::getActiveTab();
		$tmpl			=  $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$contactType]['tmpl'];

		return $tmpl;
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
	 * @return unknown
	 */
	public static function getContactTypeObjClass($type) {
		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$type]['objClass'];
	}



	/**
	 * Get contact type (person / firm / firmrole) specific save funtion
	 *
	 * @return	String		function reference (class::method)
	 *
	 */
	public static function getContactTypeSaveFunc($contactType) {
//		$contactType = TodoyuContactPreferences::getActiveTab();

		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$contactType]['saveFunc'];
	}



	/**
	 * Get function reference of method to delete records of current contact type
	 *
	 * @return	String	function reference
	 */
	public static function getContactTypeDeleteFunc() {
		$contactType = TodoyuContactPreferences::getActiveTab();

		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$contactType]['deleteFunc'];
	}



	/**
	 * Get label of contact type
	 *
	 * @return	String
	 */
	public static function getContactTypeLabel($type)	{
		return $GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$type]['label'];
	}



	/**
	 * Get contact type object
	 *
	 * @param	Intger	$objID
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
	 * @param	TodoyuForm	$form
	 * @param	Array	$option
	 * @return	String
	 */
	public static function getCountryLabel($form, $option)	{

		return $option[ 'name_' . TodoyuLocale::getLocale() ];
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
	 * Get employee's working location options (address(es) of employer firm)
	 *
	 * @param	TodoyuForm	$formObject
	 * @return	Array
	 */
	public static function getEmployeeWorkaddressOptions(TodoyuFormElement $field) {
		$companyID	= TodoyuRequest::getParam('editID', true);
		$formData	= $field->getForm()->getFormData();
		$workCities	= TodoyuAddressManager::getCompanyAddresses( $companyID, 'ext_user_address.city' );

		return self::buildOptionsArray($workCities, 'id_address', 'city', $formData['id_workaddress'], null);
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
	 * Save (jobtype-selection of foreign records (employees) of) company form
	 *
	 * @param	Array	$formData
	 * @param	Integer	$companyID
	 * @return	Array	form data
	 */
//	public static function saveCompanyFormData($formData, $idCompany) {
//		$idCompany	= intval($idCompany);
//
//		$resourceFields = array('id_workaddress', 'id_jobtype');
//
//		if( is_array($formData['user']) ) {
//
//			foreach($formData['user'] as $index => $userData) {
//				if ( array_key_exists('ext_resources_efficiency', $formData['user'][$index]) ) {
//					$idUser	= $userData['id'];
//
//					TodoyuUserManager::saveUser($idUser, $userData);
//
//						// Unset resources fields of company's form object
//					foreach( $resourceFields as $resourceFieldName )	{
//						unset($formData['user'][$index][$resourceFieldName]);
//					}
//				}
//			}
//		}
//
//
//		return $formData;
//	}



	/**
	 * Save contact (person or company)
	 *
	 * @param	String		$type
	 * @param	Array		$data
	 * @return	Integer		idContact
	 */
	public static function saveContact($type, array $data) {
		$saveFunc 	= self::getContactTypeSaveFunc($type);

		return TodoyuDiv::callUserFunction($saveFunc, $data);
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
			$idUser = TodoyuUserManager::addUser(array());
		}

		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idUser);

		TodoyuUserManager::updateUser($idUser, $data);
		TodoyuUserManager::removeFromCache($idUser);

		return $idUser;
	}



	/**
	 * Saves customer
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public function saveCustomer(array $data)	{
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$idCustomer	= intval($data['id']);

		if( $idCustomer === 0 ) {
			$idCustomer = TodoyuCustomerManager::addCustomer();
		}

		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idCustomer);

		TodoyuCustomerManager::updateCustomer($idCustomer, $data);
		TodoyuCustomerManager::removeFromCache($idCustomer);

		return $idCustomer;
	}



	/**
	 * Hanldes the foreign records of a user
	 *
	 * Extract:
	 * 	- customer
	 *  - contactinfos
	 *  - address
	 *
	 * @param	Array	$userData
	 * @param	Integer	$userID
	 * @return	Array
	 */
	public static function saveUserForeignRecords(array $data, $idUser)	{
		$idUser		= intval($idUser);

			// Save customer records
		if( ! empty($data['customer']) ) {
			$customerIDs	= TodoyuDiv::getColumnFromArray($data['customer'], 'id');

			TodoyuUserManager::saveMMRelations($idUser, $customerIDs, 'ext_user_mm_customer_user', 'id_customer');
		}
		unset($data['customer']);


			// Save contactinfo records
		if( ! empty($data['contactinfo']) ) {
			$infoIDs	= array();
			foreach($data['contactinfo'] as $contactInfo) {
				$infoIDs[] = TodoyuContactInfoManager::saveContactInfos($contactInfo);
			}

			TodoyuUserManager::saveMMRelations($idUser, $infoIDs, 'ext_user_mm_user_contactinfo', 'id_contactinfo');
		}
		unset($data['contactinfo']);


			// Save address records
		if( ! empty($data['address']) ) {
			$addressIDs	= array();
			foreach($data['address'] as $address) {
				$addressIDs[] =  TodoyuAddressManager::saveAddress($address);
			}

			TodoyuUserManager::saveMMRelations($idUser, $addressIDs, 'ext_user_mm_user_address', 'id_address');
		}
		unset($data['address']);


		return $data;
	}



	/**
	 * Save customer foreign records form hook
	 * Extract:
	 * - contactinfo
	 * - address
	 * - user
	 *
	 * @param	Array		$data			Customer form data
	 * @param	Integer		$idCustomer		Customer ID
	 * @return	Array
	 */
	public static function saveCustomerForeignRecords(array $data, $idCustomer) {
		$idCustomer = intval($idCustomer);

			// Remove existing records
		TodoyuCustomerManager::removeAllContactinfo($idCustomer);
		TodoyuCustomerManager::removeAllAddresses($idCustomer);
		TodoyuCustomerManager::removeAllUsers($idCustomer);

			// Save contactinfo records
		if( ! empty($data['contactinfo']) ) {
			$infoIDs	= array();
			foreach($data['contactinfo'] as $contactInfo) {
				$infoIDs[] = TodoyuContactInfoManager::saveContactInfos($contactInfo);
			}

			TodoyuDbHelper::saveMMrelations('ext_user_mm_customer_contactinfo', 'id_customer', 'id_contactinfo', $idCustomer, $infoIDs);
		}
		unset($data['contactinfo']);


			// Save address records
		if( ! empty($data['address']) ) {
			$addressIDs	= array();
			foreach($data['address'] as $address) {
				$addressIDs[] =  TodoyuAddressManager::saveAddress($address);
			}

			TodoyuDbHelper::saveMMrelations('ext_user_mm_customer_address', 'id_customer', 'id_address', $idCustomer, $addressIDs);
		}
		unset($data['address']);


			// Save users
		if( ! empty($data['user']) ) {
			foreach($data['user'] as $user) {
				$idLink = TodoyuCustomerManager::addUserToCustomer($idCustomer, $user['id'], $user['id_workadress'], $user['id_jobtype']);
			}
		}
		unset($data['user']);


		return $data;
	}


	public static function getUserForeignRecordData(array $data, $idUser) {
		$idUser	= intval($idUser);

		$data['customer']	= TodoyuUserManager::getUserCompanyRecords($idUser);
		$data['contactinfo']= TodoyuUserManager::getUserContactinfoRecords($idUser);
		$data['address']	= TodoyuUserManager::getUserAddressRecords($idUser);

		return $data;
	}

}

?>