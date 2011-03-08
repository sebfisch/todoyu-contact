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
 * Person
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonFilter extends TodoyuSearchFilterBase {

	/**
	 * @var	String		Default table for database requests
	 */
	const TABLE = 'ext_contact_person';



	/**
	 * Init filter object
	 *
	 * @param	Array	$activeFilters		Active filters for request
	 */
	public function __construct(array $activeFilters = array()) {
		parent::__construct('PERSON', self::TABLE, $activeFilters);
	}



	/**
	 * Get person IDs which match to the given filters
	 *
	 * @param	Integer		$limit		Limit of results
	 * @return	Array
	 */
	public function getPersonIDs($limit = 100) {
		$order	= self::TABLE . '.lastname, ' . self::TABLE . '.firstname';
		$limit	= intval($limit);

		return parent::getItemIDs($order, $limit);
	}



	/**
	 * Full-text filter for all textual person data
	 *
	 * @param	String	 	$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_Fulltext($value, $negate = false) {
		$value		= trim($value);
		$valueParts	= TodoyuArray::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( sizeof($valueParts) > 0 ) {
			$fields		= array('username', 'lastname', 'firstname', 'shortname', 'email');
			$queryParts	= array(
				'where'		=> Todoyu::db()->buildLikeQuery($valueParts, $fields),
			);
		}

		return $queryParts;
	}



	/**
	 * Get filter for name (firstname, lastname, shortname)
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_Name($value, $negate = false) {
		$valueParts	= TodoyuArray::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( sizeof($valueParts) > 0 ) {
			$fields	= array(
				self::TABLE . '.lastname',
				self::TABLE . '.firstname',
				self::TABLE . '.shortname'
			);

			$queryParts	= array(
				'where'		=> Todoyu::db()->buildLikeQuery($valueParts, $fields),
			);
		}

		return $queryParts;
	}



	/**
	 * Get filter for linked companies
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_Company($value, $negate = false) {
		$valueParts	= TodoyuArray::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( sizeof($valueParts) > 0 ) {
			$fields	= array(
				'ext_contact_company.title'
			);

			$likeWhere	= Todoyu::db()->buildLikeQuery($valueParts, $fields);

			$where		= 'ext_contact_person.id IN
						(
							SELECT
								ext_contact_person.id
							FROM
								ext_contact_person,
								ext_contact_company,
								ext_contact_mm_company_person
							WHERE
									ext_contact_person.id					= ext_contact_mm_company_person.id_person
								AND	ext_contact_mm_company_person.id_company= ext_contact_company.id
								AND ' . $likeWhere .
						')';

			$queryParts	= array(
				'where'		=> $where,
			);
		}

		return $queryParts;
	}



	/**
	 * Get filter config to search for persons name attributes and its company name
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_NameAndCompany($value, $negate = false) {
		$filterName		= $this->Filter_Name($value, $negate);
		$filterCompany	= $this->Filter_Company($value, $negate);

		if( $filterName === false ) {
			$filterName = array('tables'=>array());
		}
		if( $filterCompany === false ) {
			$filterCompany = array('tables'=>array());
		}

		$tables	= TodoyuArray::mergeUnique($filterName['tables'], $filterCompany['tables']);
		$join	= array_unique((array)$filterName['join'], (array)$filterCompany['join']);

		if( array_key_exists('where', $filterName) && array_key_exists('where', $filterCompany) ) {
			$where = '((' . $filterName['where'] . ') OR (' . $filterCompany['where'] . '))';
		} else {
			$where = $filterName['where'] . $filterCompany['where'];
		}

		return array(
			'tables'=> $tables,
			'where'	=> $where,
			'join'	=> $join
		);
	}

}

?>