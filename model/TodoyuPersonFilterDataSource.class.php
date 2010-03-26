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
	 * Get persons autocompletion data
	 *
	 * @param	String	$search
	 * @param	Array	$conf
	 * @return	Array
	 * @todo	Unused param $conf
	 */
	public static function autocompletePersons($search, array $conf = array())	{
		$data = array();

		$fieldsToSearchIn = array(
			'firstname',
			'lastname',
			'shortname'
		);

		$persons = TodoyuPersonManager::searchPersons($search, $fieldsToSearchIn);

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
	public static function autocompleteCompanies($search, array $conf = array())	{
		$data = array();

		$foundCompanies = TodoyuCompanyManager::searchCompany($search);

		foreach($foundCompanies as $companyData)	{
			$company	= TodoyuCompanyManager::getCompany($companyData['id']);
			$data[$companyData['id']] = $company->getTitle();
		}

		return $data;
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