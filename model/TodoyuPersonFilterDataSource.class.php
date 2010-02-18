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
 * User filter data source
 *
 * @package		Todoyu
 * @subpackage	User
 */
class TodoyuPersonFilterDataSource {

	/**
	 * Get users autocompletion data
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

		$users = TodoyuPersonManager::searchPersons($search, $fieldsToSearchIn);

		foreach($users as $user) {
			$data[$user['id']] = TodoyuPersonManager::getLabel($user['id']);
		}

		return $data;
	}



	/**
	 * Get user filter definition label
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