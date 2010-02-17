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
 * Company manager
 *
 * @package		Todoyu
 * @subpackage	User
 */
class TodoyuCompanyManager {

	const TABLE = 'ext_contact_company';



	/**
	 * Get form object for company quick creation
	 *
	 * @param	Integer		$idCompany
	 * @return	TodoyuForm
	 */
	public static function getQuickCreateForm($idCompany = 0) {
		$idCompany	= intval($idCompany);

			// Construct form object
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$form		= TodoyuFormManager::getForm($xmlPath, $idCompany);

			// Adjust form to needs of quick creation wizard
		$form->setAttribute('action', '?ext=contact&amp;controller=quickcreatecompany');
		$form->setAttribute('onsubmit', 'return false');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Headlet.QuickCreate.Company.save(this.form)');
		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Popup.close(\'quickcreate\')');

		return $form;
	}



	/**
	 * Get a company object
	 *
	 * @param	Integer		$idCompany
	 * @return	TodoyuCompany
	 */
	public static function getCompany($idCompany) {
		$idCompany	= intval($idCompany);

		return TodoyuRecordManager::getRecord('TodoyuCompany', $idCompany);
	}



	/**
	 * Get a company array
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyData($idCompany) {
		return TodoyuRecordManager::getRecordData(self::TABLE, $idCompany);
	}



	/**
	 * Get all company records
	 *
	 * @param	Array		$fields			Custom field list (instead of *)
	 * @param	String		$where			Extra where clause
	 * @return	Array
	 */
	public static function getAllCompanies(array $fields = array(), $where = '') {
		$fields	= sizeof($fields) === 0 ? '*' : implode(',', Todoyu::db()->escapeArray($fields));
		$table	= self::TABLE;
		$where	= ($where === '' ? '' : $where . ' AND ') . 'deleted = 0';
		$order	= 'title';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Save company data as record
	 *
	 * @param	Array		$data
	 * @return	Integer		Company ID
	 */
	public static function saveCompany(array $data) {
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$idCompany	= intval($data['id']);

		if( $idCompany === 0 ) {
			$idCompany	= self::addCompany();
		}

			// Save own external fields
		$data	= self::saveCompanyForeignRecords($data, $idCompany);

			// Update company data with basic field data
		self::updateCompany($idCompany, $data);

			// Remove company record from cache
		self::removeFromCache($idCompany);

		return $idCompany;
	}



	/**
	 * Add a company record
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addCompany(array $data = array()) {
		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a company record
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function updateCompany($idCompany, array $data) {
		$idCompany			= intval($idCompany);

		return TodoyuRecordManager::updateRecord(self::TABLE, $idCompany, $data);
	}



	/**
	 * Delete a company in the database (set deleted flag to 1)
	 *
	 * @param	Integer		$idUser
	 */
	public static function deleteCompany($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuRecordManager::deleteRecord(self::TABLE, $idCompany);
	}



	/**
	 * Save company foreign records form hook
	 * Extract:
	 * - contactinfo
	 * - address
	 * - person
	 *
	 * @param	Array		$data			Company form data
	 * @param	Integer		$idCompany		Company ID
	 * @return	Array
	 */
	public static function saveCompanyForeignRecords(array $data, $idCompany) {
		$idCompany = intval($idCompany);

			// Contactinfo
		if( isset($data['contactinfo']) ) {
			$contactInfoIDs	= TodoyuArray::getColumn($data['contactinfo'], 'id');

				// Delete all contactinfos which are no longer linked
			self::deleteRemovedContactInfos($idCompany, $contactInfoIDs);

				// If contactinfos submitted
			if( sizeof($data['contactinfo']) > 0 ) {
				$infoIDs	= array();
				foreach($data['contactinfo'] as $contactInfo) {
					$infoIDs[] = TodoyuContactInfoManager::saveContactInfos($contactInfo);
				}

				self::linkContactInfos($idCompany, $infoIDs);
			}

			unset($data['contactinfo']);
		}


			// Address
		if( isset($data['address']) ) {
			$addressIDs	= TodoyuArray::getColumn($data['address'], 'id');

				// Delete all addresses which are no longer linked
			self::deleteRemovedAddresses($idCompany, $addressIDs);

				// If addresses submitted
			if( is_array($data['address']) ) {
				$addressIDs	= array();
				foreach($data['address'] as $address) {
					$addressIDs[] =  TodoyuAddressManager::saveAddress($address);
				}

				self::linkAddresses($idCompany, $addressIDs);
			}

			unset($data['address']);
		}


			// Person
		if( isset($data['person']) ) {
			$personIDs	= TodoyuArray::getColumn($data['person'], 'id');

				// Remove all person links which are no longer active
			self::removeRemovedPersons($idCompany, $personIDs);

			if( sizeof($data['person']) > 0 ) {
				foreach($data['person'] as $index => $person) {
						// Prepare data form mm-table
					$data['person'][$index]['id_person']	= $person['id'];
					$data['person'][$index]['id_company']	= $idCompany;
					unset($data['person'][$index]['id']);
				}

				self::savePersonLinks($idCompany, $data['person']);
			}

			unset($data['person']);
		}

		return $data;
	}



	/**
	 * Delete all contactinfos except the given ones
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$currentContactInfoIDs
	 * @return	Integer		Deleted records
	 */
	public static function deleteRemovedContactInfos($idCompany, array $currentContactInfoIDs) {
		return TodoyuContactInfoManager::deleteLinkedContactInfos('ext_contact_mm_company_contactinfo', $idCompany, $currentContactInfoIDs, 'id_company');
	}



	/**
	 * Link contactinfos to company
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$contactInfoIDs
	 * @return
	 */
	public static function linkContactInfos($idCompany, array $contactInfoIDs) {
		TodoyuDbHelper::addMMLinks('ext_contact_mm_company_contactinfo', 'id_company', 'id_contactinfo', $idCompany, $contactInfoIDs);
	}



	/**
	 * Delete all company addresses which are no longer active
	 *
	 * @param	String		$idCompany
	 * @param	Array		$currentAddressIDs	Active addresses which will not be deleted
	 * @return	Integer
	 */
	public static function deleteRemovedAddresses($idCompany, array $currentAddressIDs) {
		return TodoyuAddressManager::deleteLinkedAddresses('ext_contact_mm_company_address', $idCompany, $currentAddressIDs, 'id_company');
	}



	/**
	 * Link addresses to company
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$addressIDs
	 * @return	Integer
	 */
	public static function linkAddresses($idCompany, array $addressIDs) {
		return TodoyuDbHelper::addMMLinks('ext_contact_mm_company_address', 'id_company', 'id_address', $idCompany, $addressIDs);
	}



	/**
	 * Save linked person and linking data
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$linkData
	 */
	public static function savePersonLinks($idCompany, array $linkData) {
		TodoyuDbHelper::saveExtendedMMLinks('ext_contact_mm_company_person', 'id_company', 'id_person', $idCompany, $linkData);
	}



	/**
	 * Remove person links which are no longer active
	 * Person stays untouched, only the link with the extra data will be removed
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$personIDs
	 */
	public static function removeRemovedPersons($idCompany, array $personIDs) {
		TodoyuDbHelper::deleteOtherMmLinks('ext_contact_mm_company_person', 'id_company', 'id_person', $idCompany, $personIDs);
	}



	/**
	 * Add a user to a company (linked)
	 * Additional data are the workaddress and the jobtype
	 * @deprecated
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idUser
	 * @param	Integer		$idWorkadress
	 * @param	Integer		$idJobtype
	 */
	public static function addPerson($idCompany, $idPerson, $idWorkadress = 0, $idJobtype = 0) {
		$data	= array(
			'id_company'	=> intval($idCompany) ,
			'id_person'		=> intval($idUser),
			'id_workaddress'=> intval($idWorkadress),
			'id_jobtype'	=> intval($idJobtype)
		);

		Todoyu::db()->addRecord('ext_contact_mm_company_person', $data);
	}



	/**
	 * Remove user from company
	 *
	 * @param	Integer		$idCompany
	 * @param	Integer		$idUser
	 */
	public static function removePerson($idCompany, $idPerson) {
		$idCompany	= intval($idCompany);
		$idPerson	= intval($idPerson);

		TodoyuDbHelper::removeMMrelation('ext_contact_mm_company_person', 'id_company', 'id_person', $idCompany, $idPerson);
	}



	/**
	 * Delete all users of a company (only the link will be removed, not the user)
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeAllUsers($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_person', 'id_company', $idCompany);
	}



	/**
	 * Remove all contact information of a company
	 *
	 * @todo	Keep the contact information, or delete? Dead records at the moment. See also next functions for same problem
	 * @param	Integer		$idCompany
	 */
	public static function removeContactinfoLinks($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_contactinfo', 'id_company', $idCompany);
	}


	public static function deleteContactinfos($idCompany) {
		TodoyuContactInfoManager::deleteLinkedContactInfos('ext_contact_mm_company_contactinfo', $idCompany, 'id_company');
	}



	/**
	 * Remove all address information of a company
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeAddressesLinks($idCompany) {
		$idCompany	= intval($idCompany);

		TodoyuDbHelper::removeMMrelations('ext_contact_mm_company_address', 'id_company', $idCompany);
	}



	/**
	 * Get company label
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function getLabel($idCompany) {
		return self::getCompany($idCompany)->getLabel();
	}



	/**
	 * Search companies
	 *
	 * @param	String		$sword
	 * @param	Array		$searchFields
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @return	Array
	 */
	public static function searchCompany($sword, array $searchFields = null, $size = 100, $offset = 0)	{
		$fields	= 'SQL_CALC_FOUND_ROWS *';
		$table 	= self::TABLE;
		$order	= 'title';
		$swords	= TodoyuArray::trimExplode(' ', $sword);
		$limit	= intval($offset) . ',' . intval($size);

		$searchFields	= is_null($searchFields) ? array('title', 'shortname') : $searchFields;

		if( sizeof($swords) ) {
			$where = Todoyu::db()->buildLikeQuery($swords, $searchFields);
		} else {
			$where = '1';
		}

		$where .= ' AND deleted = 0';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);
	}



	/**
	 * Remove company object from cache
	 *
	 * @param	Integer		$idCompany
	 */
	public static function removeFromCache($idCompany)	{
		$idCompany	= intval($idCompany);

		TodoyuCache::removeRecord('TodoyuCompany', $idCompany);
		TodoyuCache::removeRecordQuery(self::TABLE, $idCompany);
	}



	/**
	 * Get user records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyPersonRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	mm.*,
					p.*';
		$tables	= '	ext_contact_person p,
					ext_contact_mm_company_person mm';
		$where	= '	mm.id_person	= p.id AND
					mm.id_company	= ' . $idCompany . ' AND
					p.deleted		= 0';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get contactinfo records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyContactinfoRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	c.*';
		$tables	= '	ext_contact_contactinfo c,
					ext_contact_mm_company_contactinfo mm';
		$where	= ' mm.id_contactinfo	= c.id AND
					mm.id_company		= ' . $idCompany . ' AND
					c.deleted			= 0';

		return Todoyu::db()->getArray($fields, $tables, $where);
	}



	/**
	 * Get address records of a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Array
	 */
	public static function getCompanyAddressRecords($idCompany) {
		$idCompany	= intval($idCompany);

		$fields	= '	a.*';
		$tables	= '	ext_contact_address a,
					ext_contact_mm_company_address mm';
		$where	= ' mm.id_address	= a.id AND
					mm.id_company	= ' . $idCompany . ' AND
					a.deleted		= 0';
		$order	= ' a.is_preferred DESC';

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}



	/**
	 * Get the number of users on a company
	 *
	 * @param	Integer		$idCompany
	 * @return	Integer
	 */
	public static function getNumUsers($idCompany) {
		$idCompany	= intval($idCompany);

		$field	= 'id_person';
		$table	= 'ext_contact_mm_company_person';
		$where	= 'id_company = ' . $idCompany;

		$users	= Todoyu::db()->getArray($field, $table, $where);

		return sizeof($users);
	}



	/**
	 * Get address label of the company
	 * Compiled from the first address record. Using, street, zip and city
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function getCompanyAddress($idCompany) {
		$addresses	= self::getCompanyAddressRecords($idCompany);
		$address= null;
		$label		= '';

		if( sizeof($addresses) > 0 ) {
			$address = $addresses[0];

			$label	= $address['street'] . ', ' . $address['zip'] . ' ' . $address['city'];
		}

		return $label;
	}

}

?>