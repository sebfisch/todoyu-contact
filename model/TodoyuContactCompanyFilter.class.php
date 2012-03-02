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
 * Company filter
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyFilter extends TodoyuSearchFilterBase implements TodoyuFilterInterface {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_company';

	/**
	 * Init filter object
	 *
	 * @param	Array		$activeFilters		Active filters
	 * @param	String		$conjunction
	 * @param	Array		$sorting
	 */
	public function __construct(array $activeFilters = array(), $conjunction = 'AND', array $sorting = array()) {
		parent::__construct('COMPANY', self::TABLE, $activeFilters, $conjunction, $sorting);
	}



	/**
	 * Get company IDs which match to all filters
	 *
	 * @param	String		$sortingFallback		Force sorting column
	 * @param	String		$limit			Limit result items
	 * @return	Array
	 */
	public function getCompanyIDs($sortingFallback = 'sorting', $limit = '') {
		return parent::getItemIDs($sortingFallback, $limit, false);
	}



	/**
	 * General access to the result items
	 *
	 * @param	String		$sortingFallback
	 * @param	String		$limit
	 * @return	Array
	 */
	public function getItemIDs($sortingFallback = 'sorting', $limit = '') {
		return $this->getCompanyIDs($sortingFallback, $limit);
	}



	/**
	 * Fulltext search over company attributes
	 *
	 * @param   Mixed       $value
	 * @param   Boolean     $negate
	 * @return  Array
	 */
	public function Filter_fulltext($value, $negate = false) {
		$value		= trim($value);
		$queryParts	= false;

		if( $value !== '' ) {
			$tables	= array(self::TABLE);

			$logic		= $negate ? ' NOT LIKE ':' LIKE ';
			$conjunction= $negate ? ' AND ':' OR ';

			$keyword= Todoyu::db()->escape($value);
			$where	= ' ((	'
					.		                        self::TABLE . '.title	    ' . $logic . ' \'%' . $keyword . '%\''
					.       $conjunction . '	' . self::TABLE . '.shortname	' . $logic . ' \'%' . $keyword . '%\''
					. ' ))';

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * @param   Mixed   $value
	 * @param   Boolean $negate
	 */
	public function Filter_projectfilter($value, $negate = false) {

	}
}

?>