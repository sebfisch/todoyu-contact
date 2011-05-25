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
 * Person filter data source
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyFilterDataSource {

	/**
	 * Get person filter definition label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getLabel($definitions) {
		$definitions['value_label'] = TodoyuContactCompanyManager::getLabel($definitions['value']);

		return $definitions;
	}



	/**
	 * Get company autocompletion data
	 *
	 * @param	String	$input
	 * @param	Array	$formData
	 * @param	String	$name
	 * @return	Array
	 */
	public static function autocompleteCompanies($input, array $formData = array(), $name = '') {
		$result		= array();
		$companies	= TodoyuContactCompanyManager::searchCompany($input);

		foreach($companies as $companyData) {
			$company	= TodoyuContactCompanyManager::getCompany($companyData['id']);
			$result[$companyData['id']] = $company->getTitle();
		}

		return $result;
	}



	/**
	 * Get company label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getCompanyLabel($definitions) {
		$idCompany	= intval($definitions['value']);
		$company	= TodoyuContactCompanyManager::getCompany($idCompany);

		$definitions['value_label'] = $company->getTitle();

		return $definitions;
	}

}
?>