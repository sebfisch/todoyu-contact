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
 * Person filter data source
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuPersonFilterDataSource {

	/**
	 * Get autocomplete list for person
	 *
	 * @param 	String		$input
	 * @param	Array		$formData
	 * @param	String		$name
	 * @return	Array
	 */
	public static function autocompletePersons($input, array $formData = array(), $name = '')	{
		$data = array();

		$fieldsToSearchIn = array(
			'firstname',
			'lastname',
			'shortname'
		);

		$persons = TodoyuPersonManager::searchPersons($input, $fieldsToSearchIn);

		foreach($persons as $person) {
			$data[$person['id']] = TodoyuPersonManager::getLabel($person['id']);
		}

		return $data;
	}



	/**
	 * Get person filter definition label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getLabel($definitions)	{
		$definitions['value_label'] = TodoyuPersonManager::getLabel($definitions['value']);

		return $definitions;
	}



	/**
	 * Get company autocompletion data
	 *
	 * @param	String	$search
	 * @param	Array	$conf
	 * @return	Array
	 */
	public static function autocompleteCompanies($input, array $formData = array(), $name = '')	{
		$result		= array();
		$companies	= TodoyuCompanyManager::searchCompany($input);

		foreach($companies as $companyData)	{
			$company	= TodoyuCompanyManager::getCompany($companyData['id']);
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
		$company	= TodoyuCompanyManager::getCompany($idCompany);

		$definitions['value_label'] = $company->getTitle();

		return $definitions;
	}

}
?>