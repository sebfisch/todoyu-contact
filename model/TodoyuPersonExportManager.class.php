<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
 */
class TodoyuPersonExportManager {

	/**
	 * Exports persons as csv - file
	 *
	 * @static
	 * @param	$searchword
	 */
	public static function exportCSV($searchword) {
		$persons	= TodoyuPersonManager::searchPersons($searchword, null, '', '');

		$exportData	= self::prepareDataForExport($persons);

		$export = new TodoyuExportCSV($exportData);

		$export->setFilename('todoyu_person_export_' . date('YmdHis') . '.csv');

		$export->download();
	}



	/**
	 * Prepares the given persons to be exported
	 *
	 * @static
	 * @param	Array	$persons
	 * @return	Array
	 */
	protected static function prepareDataForExport(array $persons) {
		$exportData = array();

		foreach($persons as $person) {
			$personObj	= TodoyuPersonManager::getPerson($person['id']);
			
			$personObj->loadForeignData();
			$exportData[]	= self::parseDataForExport($personObj);
		}

		return $exportData;
	}



	/**
	 * Parses person-data for export
	 *
	 * @static
	 * @param	TodoyuPerson	$person
	 * @return	Array
	 */
	protected static function parseDataForExport(TodoyuPerson $person) {
		$exportData = array(
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.id')			=> $person->id,
			TodoyuLabelManager::getLabel('LLL:core.date_create')				=> TodoyuTime::format($person->date_create),
			TodoyuLabelManager::getLabel('LLL:core.date_update')				=> TodoyuTime::format($person->date_update),
			TodoyuLabelManager::getLabel('LLL:core.id_person_create')			=> TodoyuPersonManager::getPerson($person->id_person_create)->getFullName(),

			TodoyuLabelManager::getLabel('LLL:contact.person.attr.lastname')	=> $person->lastname,
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.firstname')	=> $person->firstname,
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.salutation')	=> $person->getSalutationLabel(),
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.shortname')	=> $person->shortname,

			TodoyuLabelManager::getLabel('LLL:contact.person.attr.username')	=> $person->username,
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.email')		=> $person->email,
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.is_admin')	=> $person->is_admin ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no'),
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.active')		=> $person->active ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no'),
			TodoyuLabelManager::getLabel('LLL:contact.person.attr.birthday')	=> TodoyuTime::format($person->getBirthday(), 'date'),

		);

		// map & prepare company records of person
		foreach( $person->company as $index => $company ) {
			$exportData[TodoyuLabelManager::getLabel('LLL:contact.person.attr.company') . '_' . ($index + 1)]	= $company['title']; 
		}

		// map & prepare contactinfo records of person
		foreach( $person->contactinfo as $index => $contactinfo ) {
			$prefix			= TodoyuLabelManager::getLabel('LLL:contact.contactinfo') . '_' . ($index + 1) . '_';
			$contactinfoObj	= TodoyuContactInfoManager::getContactinfo($contactinfo['id']);

			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.contactinfo.attr.type')]	= $contactinfoObj->getTypeLabel();
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.contactinfo.attr.info')]	= $contactinfo['info'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:form.is_preferred')]				= $contactinfo['preferred'] ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no');
		}

		// map & prepare address records of person
		foreach( $person->address as $index => $address) {
			$prefix			= TodoyuLabelManager::getLabel('LLL:contact.address') . '_' . ($index + 1) . '_';
			$addressObj		= TodoyuAddressManager::getAddress($address['id']);
			
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.addresstype')]	= TodoyuAddressTypeManager::getAddressTypeLabel($address['id_addresstype']);
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.street')]		= $address['street'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.postbox')]		= $address['postbox'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.zip')]			= $address['zip'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.city')]		= $address['city'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.region')]		= $addressObj->getRegionLabel();
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.country')]		= $addressObj->getCountry()->getLabel();
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:form.is_preferred')]				= $address['is_preferred'] ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no');
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.address.attr.comment')]		= $address['comment'];
		}

		// map & prepare role records of person
		foreach( $person->role as $index => $role) {
			$exportData[TodoyuLabelManager::getLabel('LLL:core.role') . '_' . ($index + 1)]	= $role['title'];
		}

		$exportData = TodoyuHookManager::callHookDataModifier('contact', 'personCSVExportParseData', $exportData, array('company'	=> $company));

		return $exportData;
	}
}

?>