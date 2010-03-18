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

class TodoyuContactAutocompletionActionController extends TodoyuActionController {

	/**
	 * Get person autocompleter suggestions
	 *
	 * @param	Array	$params
	 */
	public function personAction(array $params) {
		$sword	= trim($params['sword']);
		$config	= array();

		$results= TodoyuPersonFilterDataSource::autocompletePersons($sword, $config);

		return TodoyuRenderer::renderAutocompleteList($results);
	}



	/**
	 * Get company autocompleter suggestions
	 *
	 * @param	Array	$params
	 */
	public function companyAction(array $params) {
		$sword	= trim($params['sword']);
		$config	= array();

		$results= TodoyuPersonFilterDataSource::autocompleteCompanies($sword, $config);

		return TodoyuRenderer::renderAutocompleteList($results);
	}



	/**
	 * Get region autocompleter suggestions
	 *
	 * @param	Array	$params
	 */
	public function regionAction(array $params) {
		$sword		= trim($params['sword']);
		$elemParts	= explode('-', $params['acelementid']);
		$index		= intval($elemParts[2]);
		$fieldName	= $elemParts[1];
		$formName	= $params['formName'];
		$idCountry	= intval($params[$formName][$fieldName][$index]['id_country']);

		if( $idCountry === 0 ) {
			$results	= array();
		} else {
			$results	= TodoyuDatasource::autocompleteRegions($sword, $idCountry);
		}

		return TodoyuRenderer::renderAutocompleteList($results);
	}

}

?>