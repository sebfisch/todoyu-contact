<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Export manager for person - records
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonExportManager {

	/**
	 * Exports persons as CSV file
	 *
	 * @param	String	$searchWord
	 */
	public static function exportCSV($searchWord) {
		$persons	= TodoyuContactPersonManager::searchPersons($searchWord, null, '', '');
		self::sendCSVfromData(self::getExportDataByPersonsData($persons));
	}



	/**
	 * Exports data of companies of given IDs as CSV file
	 *
	 * @param	Integer[]	$personIDs
	 */
	public static function exportCSVfromIDs(array $personIDs) {
		$personIDs = TodoyuArray::intval($personIDs);

		self::sendCSVfromData(self::getExportDataByPersonIDs($personIDs));
	}



	/**
	 * Send CSV for download
	 *
	 * @param	Array	$exportData
	 */
	public static function sendCSVfromData(array $exportData) {
		$export = new TodoyuExportCSV($exportData);
		$export->download('todoyu_company_export_' . date('YmdHis') . '.csv');
	}



	/**
	 * Prepares the given persons to be exported
	 *
	 * @param	Array	$personsData
	 * @return	Array
	 */
	protected static function getExportDataByPersonsData(array $personsData) {
		$exportData = array();

		foreach($personsData as $personData) {
			$person	= TodoyuContactPersonManager::getPerson($personData['id']);
			$person->loadForeignData();

			$exportData[]	= self::getPersonExportData($person);
		}

		return $exportData;
	}



	/**
	 * Prepares data of given persons of given IDs for export
	 *
	 * @param	Array	$personIDs
	 * @return	Array
	 */
	public static function getExportDataByPersonIDs(array $personIDs) {
		$personIDs = TodoyuArray::intval($personIDs);

		$exportData = array();
		foreach($personIDs as $idPerson) {
			if( $idPerson !== 0 ) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);
				$person->loadForeignData();

				$exportData[]	= self::getPersonExportData($person);
			}
		}

		return $exportData;
	}



	/**
	 * Parses person-data for export
	 *
	 * @param	TodoyuContactPerson		$person
	 * @return	Array
	 */
	protected static function getPersonExportData(TodoyuContactPerson $person) {
		$exportData = array(
			Todoyu::Label('contact.ext.person.attr.id')			=> $person->getID(),
			Todoyu::Label('core.global.date_create')			=> TodoyuTime::format($person->getDateCreate()),
			Todoyu::Label('core.global.date_update')			=> TodoyuTime::format($person->getDateUpdate()),
			Todoyu::Label('core.global.id_person_create')		=> TodoyuContactPersonManager::getPerson($person->getPerson('create'))->getFullName(),

			Todoyu::Label('contact.ext.person.attr.lastname')	=> $person->getLastName(),
			Todoyu::Label('contact.ext.person.attr.firstname')	=> $person->getFirstName(),
			Todoyu::Label('contact.ext.person.attr.salutation')	=> $person->getSalutationLabel(),
			Todoyu::Label('contact.ext.person.attr.shortname')	=> $person->getShortname(),

			Todoyu::Label('contact.ext.person.attr.username')	=> $person->getUsername(),
			Todoyu::Label('contact.ext.person.attr.email')		=> $person->getEmail(),
			Todoyu::Label('contact.ext.person.attr.is_admin')	=> Todoyu::Label('core.global.' . ($person->isAdmin()	? 'yes' : 'no')),
			Todoyu::Label('contact.ext.person.attr.is_active')	=> Todoyu::Label('core.global.' . ($person->isActive()	? 'yes' : 'no')),
			Todoyu::Label('contact.ext.person.attr.birthday')	=> TodoyuTime::format($person->getBirthday(), 'date'),

		);

			// Map & prepare company records of person
		foreach( $person->getCompanies() as $index => $company ) {
			$exportData[Todoyu::Label('contact.ext.person.attr.company') . '_' . ($index + 1)]	= $company->getTitle();
		}

			// Map & prepare contactinfo records of person
		foreach( $person->contactinfo as $index => $contactinfo ) {
			$prefix			= Todoyu::Label('contact.ext.contactinfo') . '_' . ($index + 1) . '_';
			$contactinfoObj	= TodoyuContactContactInfoManager::getContactinfo($contactinfo['id']);

			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.type')]	= $contactinfoObj->getTypeLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.info')]	= $contactinfo['info'];
			$exportData[$prefix . Todoyu::Label('core.form.is_preferred')]				= $contactinfo['preferred'] ? Todoyu::Label('core.global.yes') : Todoyu::Label('core.global.no');
		}

			// Map & prepare address records of person
		foreach( $person->address as $index => $address) {
			$prefix			= Todoyu::Label('contact.ext.address') . '_' . ($index + 1) . '_';
			$addressObj		= TodoyuContactAddressManager::getAddress($address['id']);

			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.addresstype')]= TodoyuContactAddressTypeManager::getAddressTypeLabel($address['id_addresstype']);
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.street')]		= $address['street'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.postbox')]	= $address['postbox'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.zip')]		= $address['zip'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.city')]		= $address['city'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.region')]		= $addressObj->getRegionLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.country')]	= $addressObj->getCountry()->getLabel();
			$exportData[$prefix . Todoyu::Label('core.form.is_preferred')]				= $address['is_preferred'] ? Todoyu::Label('core.global.yes') : Todoyu::Label('core.global.no');
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.comment')]	= $address['comment'];
		}

			// Map & prepare role records of person
		foreach( $person->role as $index => $role) {
			$exportData[Todoyu::Label('core.global.role') . '_' . ($index + 1)]	= $role['title'];
		}

		$exportData = TodoyuHookManager::callHookDataModifier('contact', 'personCSVExportParseData', $exportData, array('company' => $company));

		return $exportData;
	}
}

?>