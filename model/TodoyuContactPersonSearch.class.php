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
 * Event search
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonSearch implements TodoyuSearchEngineIf {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_person';



	/**
	 * Search for persons in full-text mode. Return the ID of the matching persons
	 *
	 * @param	Array		$find		Keywords which have to be in the person
	 * @param	Array		$ignore		Keywords which must not be in the person
	 * @param	Integer		$limit
	 * @return	Integer[]				Project IDs
	 */
	public static function searchPersons(array $find, array $ignore = array(), $limit = 100) {
		$table	= self::TABLE;
		$fields	= array('email', 'firstname', 'lastname');

		$addToWhere = ' AND deleted = 0';
		if( ! Todoyu::allowed('contact', 'person:seeAllPersons') ) {
			$addToWhere	.= ' AND ' . TodoyuContactPersonRights::getAllowedToBeSeenPersonsWhereClause();
		}

		return TodoyuSearch::searchTable($table, $fields, $find, $ignore, $limit, $addToWhere);
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
			$person	= TodoyuContactPersonManager::getPerson($idPerson);
			if ( Todoyu::allowed('contact', 'relation:seeAllContactinfotypes') ) {
				$phone	= $person->getPhone();
				$email	= $person->getEmail(true);
			}
			$labelTitle = TodoyuString::wrap($person->getFullName(), '<span class="keyword">|</span>') . ($email
					? ' | ' . $email : '') . ($phone ? ' | ' . $phone : '');
			$suggestions[] = array(
				'labelTitle'=> $labelTitle,
				'labelInfo'	=> $person->getCompany()->getLabel(),
				'title'		=> strip_tags($labelTitle),
				'onclick'	=> 'location.href=\'index.php?ext=contact&amp;controller=person&amp;action=detail&amp;person=' . $idPerson . '\''
			);
		}

		return $suggestions;
	}



	/**
	 * Get persons listing data. Keys: [total,rows]
	 *
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @param	String		$searchWord
	 * @return	Array
	 */
	public static function getPersonListingData($size, $offset = 0, array $params) {
		$persons= TodoyuContactPersonManager::searchPersons($params['sword'], null, $size, $offset);
		$data	= array(
			'rows'	=> array(),
			'total'	=> Todoyu::db()->getTotalFoundRows()
		);

		foreach($persons as $personData) {
			$idPerson	= $personData['id'];
			$data['rows'][] = array(
				'id'		=> $idPerson,
				'columns'	=> self::getPersonListingDataRow($idPerson)
			);
		}

		return $data;
	}



	/**
	 * Get persons search results listing data
	 *
	 * @param	Integer		$size
	 * @param	Integer		$offset
	 * @param	Array		$params
	 * @return	Array
	 */
	public static function getPersonListingDataSearch($size, $offset = 0, array $params) {
		$personIDs	= TodoyuArray::intval($params['personIDs']);
		$data	= array(
			'rows'	=> array(),
			'total'	=> Todoyu::db()->getTotalFoundRows()
		);

		foreach($personIDs as $idPerson) {
			$data['rows'][] = array(
				'id'		=> $idPerson,
				'columns'	=> self::getPersonListingDataRow($idPerson)
			);
		}

		return $data;
	}



	/**
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	private static function getPersonListingDataRow($idPerson) {
		$idPerson	= intval($idPerson);
		$person	= TodoyuContactPersonManager::getPerson($idPerson);

		if( Todoyu::allowed('contact', 'relation:seeAllContactinfotypes') ) {
			$email  = $person->getEmail(true);
			if( empty($email) ) {
				$email  = '-';
			}
		} else {
			$email  = false;
		}

		$company	= $person->getMainCompany()->getTitle();
		if( empty($company) ) {
			$company	= '-';
		}

		return array(
			'icon'		=> '',
			'iconClass'	=> ($person->isActive() ? 'login' : '') . ($person->isAdmin() ? ' admin' : ''),
			'lastname'	=> $person->getLastname(),
			'firstname'	=> $person->getFirstname(),
			'email'		=> $email,
			'company'	=> $company,
			'actions'	=> TodoyuContactPersonRenderer::renderPersonActions($person->getID())
		);
	}

}

?>