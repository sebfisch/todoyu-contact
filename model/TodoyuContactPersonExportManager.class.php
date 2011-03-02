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
 * Export manager for person - records
 */
class TodoyuContactPersonExportManager {

	/**
	 * Exports persons as csv - file
	 *
	 * @static
	 * @param	$searchWord
	 */
	public static function exportCSV($searchWord) {
		$persons	= TodoyuContactPersonManager::searchPersons($searchWord, null, '', '');

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
			$personObj	= TodoyuContactPersonManager::getPerson($person['id']);

			$personObj->loadForeignData();
			$exportData[]	= self::parseDataForExport($personObj);
		}

		return $exportData;
	}



	/**
	 * Parses person-data for export
	 *
	 * @static
	 * @param	TodoyuContactPerson	$person
	 * @return	Array
	 */
	protected static function parseDataForExport(TodoyuContactPerson $person) {
		$exportData = array(
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.id')			=> $person->id,
			TodoyuLabelManager::getLabel('LLL:core.global.date_create')				=> TodoyuTime::format($person->date_create),
			TodoyuLabelManager::getLabel('LLL:core.global.date_update')				=> TodoyuTime::format($person->date_update),
			TodoyuLabelManager::getLabel('LLL:core.global.id_person_create')			=> TodoyuContactPersonManager::getPerson($person->id_person_create)->getFullName(),

			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.lastname')	=> $person->lastname,
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.firstname')	=> $person->firstname,
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.salutation')	=> $person->getSalutationLabel(),
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.shortname')	=> $person->shortname,

			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.username')	=> $person->username,
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.email')		=> $person->email,
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.is_admin')	=> $person->is_admin ? TodoyuLabelManager::getLabel('LLL:core.global.yes') : TodoyuLabelManager::getLabel('LLL:core.no'),
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.active')		=> $person->active ? TodoyuLabelManager::getLabel('LLL:core.global.yes') : TodoyuLabelManager::getLabel('LLL:core.no'),
			TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.birthday')	=> TodoyuTime::format($person->getBirthday(), 'date'),

		);

			// Map & prepare company records of person
		foreach( $person->company as $index => $company ) {
			$exportData[TodoyuLabelManager::getLabel('LLL:contact.ext.person.attr.company') . '_' . ($index + 1)]	= $company['title'];
		}

			// Map & prepare contactinfo records of person
		foreach( $person->contactinfo as $index => $contactinfo ) {
			$prefix			= TodoyuLabelManager::getLabel('LLL:contact.ext.contactinfo') . '_' . ($index + 1) . '_';
			$contactinfoObj	= TodoyuContactContactInfoManager::getContactinfo($contactinfo['id']);

			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.contactinfo.attr.type')]	= $contactinfoObj->getTypeLabel();
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.contactinfo.attr.info')]	= $contactinfo['info'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:core.form.is_preferred')]				= $contactinfo['preferred'] ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no');
		}

			// Map & prepare address records of person
		foreach( $person->address as $index => $address) {
			$prefix			= TodoyuLabelManager::getLabel('LLL:contact.ext.address') . '_' . ($index + 1) . '_';
			$addressObj		= TodoyuContactAddressManager::getAddress($address['id']);

			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.addresstype')]	= TodoyuContactAddressTypeManager::getAddressTypeLabel($address['id_addresstype']);
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.street')]		= $address['street'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.postbox')]		= $address['postbox'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.zip')]			= $address['zip'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.city')]		= $address['city'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.region')]		= $addressObj->getRegionLabel();
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.country')]		= $addressObj->getCountry()->getLabel();
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:core.form.is_preferred')]				= $address['is_preferred'] ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no');
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.address.attr.comment')]		= $address['comment'];
		}

			// Map & prepare role records of person
		foreach( $person->role as $index => $role) {
			$exportData[TodoyuLabelManager::getLabel('LLL:core.global.role') . '_' . ($index + 1)]	= $role['title'];
		}

		$exportData = TodoyuHookManager::callHookDataModifier('contact', 'personCSVExportParseData', $exportData, array('company'	=> $company));

		return $exportData;
	}
}

?>