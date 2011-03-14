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
 * Export manager for companies
 */
class TodoyuContactCompanyExportManager {

	/**
	 * Exports companies as CSV file
	 *
	 * @static
	 * @param	$searchWord
	 */
	public static function exportCSV($searchWord) {
		$persons	= TodoyuContactCompanyManager::searchCompany($searchWord, null, '', '');

		$exportData	= self::prepareDataForExport($persons);

		$export = new TodoyuExportCSV($exportData);

		$export->setFilename('todoyu_company_export_' . date('YmdHis') . '.csv');

		$export->download();
	}



	/**
	 * Prepares the given companies to be exported
	 *
	 * @static
	 * @param	Array	$companies
	 * @return	Array
	 */
	protected static function prepareDataForExport(array $companies) {
		$exportData = array();

		foreach($companies as $company) {
			$companyObj	= TodoyuContactCompanyManager::getCompany($company['id']);

			$companyObj->loadForeignData();
			$exportData[]	= self::parseDataForExport($companyObj);
		}

		return $exportData;
	}



	/**
	 * Parses company data for export
	 *
	 * @static
	 * @param	TodoyuContactCompany	$company
	 * @return	Array
	 */
	protected static function parseDataForExport(TodoyuContactCompany $company) {

		$exportData = array(
			TodoyuLabelManager::getLabel('LLL:contact.ext.company.attr.id')				=> $company->id,
			TodoyuLabelManager::getLabel('LLL:core.global.date_create')					=> TodoyuTime::format($company->date_create),
			TodoyuLabelManager::getLabel('LLL:core.global.date_update')					=> TodoyuTime::format($company->date_update),
			TodoyuLabelManager::getLabel('LLL:core.global.id_person_create')				=> TodoyuContactPersonManager::getPerson($company->id_person_create)->getFullName(),

			TodoyuLabelManager::getLabel('LLL:contact.ext.company.attr.title')			=> $company->title,
			TodoyuLabelManager::getLabel('LLL:contact.ext.company.attr.shortname')		=> $company->shortname,

			TodoyuLabelManager::getLabel('LLL:contact.ext.company.attr.is_internal')	=> $company->is_internal ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no'),
		);

			// Map & prepare contactinfo records of company
		foreach( $company->contactinfo as $index => $contactinfo ) {
			$prefix			= TodoyuLabelManager::getLabel('LLL:contact.ext.contactinfo') . '_' . ($index + 1) . '_';
			$contactinfoObj	= TodoyuContactContactInfoManager::getContactinfo($contactinfo['id']);

			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.contactinfo.attr.type')]	= $contactinfoObj->getTypeLabel();
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:contact.ext.contactinfo.attr.info')]	= $contactinfo['info'];
			$exportData[$prefix . TodoyuLabelManager::getLabel('LLL:core.form.is_preferred')]				= $contactinfo['preferred'] ? TodoyuLabelManager::getLabel('LLL:core.yes') : TodoyuLabelManager::getLabel('LLL:core.no');
		}

			// Map & prepare address records of company
		foreach( $company->address as $index => $address) {
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

			// Map & prepare employee records of company
		foreach( $company->getEmployeesRecords() as $index => $person ) {
			$exportData[TodoyuLabelManager::getLabel('LLL:contact.ext.company.attr.person') . '_' . ($index + 1)]	= $person['firstname'] . ' ' . $person['lastname'];
		}

		$exportData = TodoyuHookManager::callHookDataModifier('contact', 'companyCSVExportParseData', $exportData, array('company'	=> $company));

		return $exportData;
	}
}

?>