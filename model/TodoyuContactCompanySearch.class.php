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
 * Event search
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanySearch implements TodoyuSearchEngineIf {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_contact_company';



	/**
	 * Search company in full-text mode. Return the ID of the matching companies
	 *
	 * @param	Array		$find		Keywords which have to be in the company
	 * @param	Array		$ignore		Keywords which must not be in the company
	 * @param	Integer		$limit
	 * @return	Array		Project IDs
	 */
	public static function searchCompanies(array $find, array $ignore = array(), $limit = 100) {
		$table	= self::TABLE;
		$fields	= array('title', 'shortname');

		$addToWhere = ' AND deleted = 0';
		if( ! Todoyu::allowed('contact', 'company:seeAllCompanies') ) {
			$addToWhere	.= ' AND ' . TodoyuContactCompanyRights::getAllowedToBeSeenCompaniesWhereClause();
		}

		return TodoyuSearch::searchTable($table, $fields, $find, $ignore, $limit, $addToWhere);
	}



	/**
	 * Get suggestions data array for company search
	 *
	 * @param	Array		$find
	 * @param	Array		$ignore
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getSuggestions(array $find, array $ignore = array(), $limit = 5) {
		$limit			= intval($limit);
		$suggestions	= array();

		$companyIDs		= self::searchCompanies($find, $ignore, $limit);

			// Get comment details
		if( sizeof($companyIDs) > 0 ) {
			$fields	= '	c.id,
						c.title,
						c.shortname';
			$table	= self::TABLE . ' c';
			$where	= '	c.id IN(' . implode(',', $companyIDs) . ')';
			$order	= '	c.title ASC';

			$companies	= Todoyu::db()->getArray($fields, $table, $where, '', $order);

			foreach($companies as $company) {
				$label	= $company['title'] . ($company['shortname'] !== '' ? ' (' . $company['shortname'] . ')' : '');

				$suggestions[] = array(
					'labelTitle'=> $label,
					'labelInfo'	=> $label,
					'title'		=> '',
					'onclick'	=> 'location.href=\'?ext=contact&amp;controller=company&amp;action=detail&amp;company=' . $company['id'] . '\''
				);
			}
		}

		return $suggestions;
	}



	/**
	 * Get listing data for companies
	 *
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @param	String		$searchWord
	 * @return	Array
	 */
	public static function getCompanyListingData($size, $offset = 0, $searchWord = '') {
		$companies	= TodoyuContactCompanyManager::searchCompany($searchWord, null, $size, $offset);

		$data	= array(
			'rows'	=> array(),
			'total'	=> Todoyu::db()->getTotalFoundRows()
		);

		foreach($companies as $company) {
			$data['rows'][] = array(
				'icon'		=> '',
				'title'		=> $company['title'],
				'address'	=> TodoyuContactCompanyManager::getCompanyAddressLabel($company['id']),
				'actions'	=> TodoyuContactRenderer::renderCompanyActions($company['id'])
			);
		}

		return $data;
	}

}

?>