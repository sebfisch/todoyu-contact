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
 * Event search
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuPersonSearch implements TodoyuSearchEngineIf {

	/**
	 * Default table for database requests
	 */
	const TABLE = 'ext_contact_person';



	/**
	 * Search for persons in fulltext mode. Return the ID of the matching persons
	 *
	 * @param	Array		$find		Keywords which have to be in the person
	 * @param	Array		$ignore		Keywords which must not be in the person
	 * @param	Integer		$limit
	 * @return	Array		Project IDs
	 */
	public static function searchPersons(array $find, array $ignore = array(), $limit = 100) {
		$table	= self::TABLE;
		$fields	= array('email', 'firstname', 'lastname');

		return TodoyuSearch::searchTable($table, $fields, $find, $ignore, $limit);
	}



	/**
	 * Get search results for persons
	 *
	 * @param	Array		$find
	 * @param	Array		$ignore
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getResults(array $find, array $ignore = array(), $limit = 100) {
		return array();
	}



	/**
	 * Get suggestions data array for person search
	 *
	 * @param	Array		$find
	 * @param	Array		$ignore
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getSuggestions(array $find, array $ignore = array(), $limit = 5) {
		$limit			= intval($limit);
		$suggestions	= array();

		$personIDs		= self::searchPersons($find, $ignore, $limit);

			// Get comment details
		foreach($personIDs as $idPerson) {
			$label	= TodoyuPersonManager::getLabel($idPerson);

			$suggestions[] = array(
				'labelTitle'=> $label,
				'labelInfo'	=> $label,
				'title'		=> '',
				'onclick'	=> 'location.href=\'?ext=contact&amp;type=person&amp;id=' . $idPerson . '\''
			);
		}

		return $suggestions;
	}
}

?>