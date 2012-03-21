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
 * Export manager for companies
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyExportManager {

	/**
	 * Exports companies as CSV file
	 *
	 * @param	String	$searchWord
	 */
	public static function exportCSV($searchWord) {
		$companies	= TodoyuContactCompanyManager::searchCompany($searchWord, null, '', '');

		self::sendCSVfromData(self::getExportDataByCompaniesData($companies));
	}



	/**
	 * Exports data of companies of given IDs as CSV file
	 *
	 * @param	Integer[]	$companyIDs
	 */
	public static function exportCSVfromIDs(array $companyIDs) {
		$companyIDs = TodoyuArray::intval($companyIDs);

		self::sendCSVfromData(self::getExportDataByCompanyIDs($companyIDs));
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
	 * Prepares data of given companies for export
	 *
	 * @param	Array	$companiesData
	 * @return	Array
	 */
	public static function getExportDataByCompaniesData(array $companiesData) {
		$exportData = array();
		foreach($companiesData as $companyData) {
			if( intval($companyData['id']) !== 0 ) {
				$company	= TodoyuContactCompanyManager::getCompany($companyData['id']);
				$company->loadForeignData();

				$exportData[]	= self::getCompanyExportData($company);
			}
		}

		return $exportData;
	}



	/**
	 * Prepares data of given companies for export
	 *
	 * @param	Array	$companyIDs
	 * @return	Array
	 */
	public static function getExportDataByCompanyIDs(array $companyIDs) {
		$companyIDs = TodoyuArray::intval($companyIDs);

		$exportData = array();
		foreach($companyIDs as $idCompany) {
			if( $idCompany !== 0 ) {
				$company	= TodoyuContactCompanyManager::getCompany($idCompany);
				$company->loadForeignData();

				$exportData[]	= self::getCompanyExportData($company);
			}
		}

		return $exportData;
	}



	/**
	 * Parses company data for export
	 *
	 * @param	TodoyuContactCompany	$company
	 * @return	Array
	 */
	public static function getCompanyExportData(TodoyuContactCompany $company) {
		$creator	= $company->getPerson('create');

		$exportData = array(
			Todoyu::Label('contact.ext.company.attr.id')			=> $company->getID(),
			Todoyu::Label('core.global.date_create')				=> TodoyuTime::format($company->getDateCreate()),
			Todoyu::Label('core.global.date_update')				=> TodoyuTime::format($company->getDateUpdate()),
			Todoyu::Label('core.global.id_person_create')			=> $creator ? $creator->getFullName() : '',
			Todoyu::Label('contact.ext.company.attr.title')			=> $company->getTitle(),
			Todoyu::Label('contact.ext.company.attr.shortname')		=> $company->getShortname(),
			Todoyu::Label('contact.ext.company.attr.is_internal')	=> Todoyu::Label('core.global.' . $company->isInternal() ? 'yes' : 'no'),
		);

			// Map & prepare contactinfo records of company
		foreach( $company->contactinfo as $index => $contactinfo ) {
			$prefix			= Todoyu::Label('contact.ext.contactinfo') . '_' . ($index + 1) . '_';
			$contactinfoObj	= TodoyuContactContactInfoManager::getContactinfo($contactinfo['id']);

			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.type')]	= $contactinfoObj->getTypeLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.contactinfo.attr.info')]	= $contactinfo['info'];
			$exportData[$prefix . Todoyu::Label('core.form.is_preferred')]				= Todoyu::Label('core.global.' . $contactinfo['is_preferred'] ? 'yes' : 'no');
		}

			// Map & prepare address records of company
		foreach( $company->address as $index => $address) {
			$prefix			= Todoyu::Label('contact.ext.address') . '_' . ($index + 1) . '_';
			$addressObj		= TodoyuContactAddressManager::getAddress($address['id']);

			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.addresstype')]= TodoyuContactAddressTypeManager::getAddressTypeLabel($address['id_addresstype']);
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.street')]		= $address['street'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.postbox')]	= $address['postbox'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.zip')]		= $address['zip'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.city')]		= $address['city'];
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.region')]		= $addressObj->getRegionLabel();
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.country')]	= $addressObj->getCountry()->getLabel();
			$exportData[$prefix . Todoyu::Label('core.form.is_preferred')]				= Todoyu::Label('core.global.' . $address['is_preferred'] ? 'yes' : 'no');
			$exportData[$prefix . Todoyu::Label('contact.ext.address.attr.comment')]	= $address['comment'];
		}

			// Map & prepare employee records of company
		foreach( $company->getEmployeesRecords() as $index => $person ) {
			$exportData[Todoyu::Label('contact.ext.company.attr.person') . '_' . ($index + 1)]	= $person['firstname'] . ' ' . $person['lastname'];
		}

		$exportData = TodoyuHookManager::callHookDataModifier('contact', 'companyCSVExportParseData', $exportData, array('company'	=> $company));

		return $exportData;
	}

}

?>