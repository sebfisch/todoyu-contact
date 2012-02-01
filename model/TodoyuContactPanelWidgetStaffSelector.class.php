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
 * Staff selector panel widget
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPanelWidgetStaffSelector extends TodoyuPanelWidgetSearchList {

	/**
	 * Preference name for selected items
	 *
	 * @var	String
	 */
	protected $selectionPref	= 'staffselector';

	/**
	 * Cached selection
	 *
	 * @var	Array
	 */
	protected $selection;



	/**
	 * Constructor (init widget)
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 * @param	Integer		$idArea
	 */
	public function __construct(array $config, array $params = array(), $idArea = 0) {
		parent::__construct(
			'contact',										// ext key
			'staffselector',								// panel widget ID
			'contact.panelwidget-staffselector.title',	// widget title text
			$config,										// widget config array
			$params,										// widget parameters
			$idArea											// area ID
		);

			// Add classes
		$this->addHasIconClass();

		$this->setJsObject('Todoyu.Ext.contact.PanelWidget.StaffSelector');

			// Generate person color CSS and graphic
		TodoyuColors::generate();
	}



	/**
	 * Render content
	 *
	 * @param	Boolean		$listOnly		Render list items only
	 * @return	String
	 */
	public function renderContent($listOnly = false) {
		$searchList	= parent::renderContent($listOnly);
		$selection	= '';

		if( !$listOnly ) {
			$selection	= $this->renderSelection();
		}

		return $searchList . $selection;
	}



	/**
	 * Render selection box
	 * Selected persons and groups
	 *
	 * @return	String
	 */
	protected function renderSelection() {
		$tmpl	= 'ext/contact/view/panelwidget/staffselector.tmpl';
		$data	= array(
			'items'	=> $this->getSelectedItems(),
			'id'	=> $this->getID()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get items for search result list
	 *
	 * @return	Array
	 */
	protected function getItems() {
		$items	= array();

		if( sizeof($this->getSearchWords()) === 0 ) {
			return $items;
		}

		if( $this->isGroupSearchActive() ) {
			$groups	= $this->searchGroups($this->getSearchWords());

			foreach($groups as $group) {
				$jobTypePersons	= $this->getJobtypePersons($group['id']);
				$items[] = array(
					'id'	=> 'g' . $group['id'],
					'label'	=> $group['label'] . ' (' . sizeof($jobTypePersons) . ')',
					'title'	=> $group['label'] . ' (' . sizeof($jobTypePersons) . ')',
					'class'	=> 'group'
				);
			}
		}


		$persons= $this->searchPersons($this->getSearchWords());

		foreach($persons as $person) {
			$colorIndex	= TodoyuColors::getColorIndex($person['id']);
			$items[] = array(
				'id'	=> 'p' . $person['id'],
				'label'	=> $person['label'],
				'title'	=> $person['label'],
				'class'	=> 'person enumColBorLef' . $colorIndex
			);
		}

		return $items;
	}



	/**
	 * Get search words
	 *
	 * @return	Array
	 */
	protected function getSearchWords() {
		return TodoyuString::trimExplode(' ', $this->getSearchText(), true);
	}



	/**
	 * Search groups which match to search words
	 *
	 * @param	Array		$searchWords
	 * @return	Array
	 */
	protected function searchGroups(array $searchWords) {
		$searchFields	= array(
			'jt.title'
		);
		$like	= Todoyu::db()->buildLikeQuery($searchWords, $searchFields);

		$fields	= '	jt.id,
					jt.title as label';
		$table	= '	ext_contact_jobtype jt,
					ext_contact_mm_company_person mmcp,
					ext_contact_person p,
					ext_contact_company c';
		$where	= '		mmcp.id_jobtype	= jt.id'
				. ' AND mmcp.id_person	= p.id'
				. ' AND mmcp.id_company	= c.id'
				. ' AND	jt.deleted		= 0'
				. ' AND p.deleted		= 0'
				. ' AND c.deleted		= 0'
				. ' AND c.is_internal	= 1'
				. '	AND	' . $like;
		$order	= 'jt.title';
		$group	= 'jt.id';
		$limit	= 10;

		$selectedJobtypeIDs	= $this->getSelectedGroupIDs();

		if( sizeof($selectedJobtypeIDs) > 0 ) {
			$where .= ' AND jt.id NOT IN(' . implode(',', $selectedJobtypeIDs) . ')';
		}

		return Todoyu::db()->getArray($fields, $table, $where, $group, $order, $limit);
	}



	/**
	 * Search persons which match the search words
	 *
	 * @param	Array	$searchWords
	 * @return	Array
	 */
	protected function searchPersons(array $searchWords) {
		$selectedPersons= $this->getSelectedPersonIDs();

		return TodoyuContactPersonManager::searchStaff($searchWords, $selectedPersons, 10);
	}



	/**
	 * Get jobtype person IDs
	 *
	 * @param	Integer		$idJobtype
	 * @return	Array
	 */
	protected function getJobtypePersons($idJobtype) {
		return TodoyuContactJobTypeManager::getPersonIDsWithJobtype($idJobtype);
	}




	/**
	 * Get selected persons. Based on selected persons and person with a selected jobtype
	 *
	 * @return	Array
	 */
	public function getSelectedPersons() {
		$selection	= $this->getSelection();
		$persons	= array();

		foreach($selection as $item) {
				// Ignore item with dash (they are disabled)
			if( substr($item, 0, 1) === '-' ) {
				continue;
			}

			switch(substr($item, 0, 1)) {
				case 'p':
					$persons[] = intval(substr($item, 1));
					break;

				case 'g':
					$persons = array_merge($persons, $this->getJobtypePersons(substr($item, 1)));
					break;
			}
		}

		return array_unique($persons);
	}



	/**
	 * Get items for selection list
	 *
	 * @return	Array
	 */
	public function getSelectedItems() {
		$selection	= $this->getSelection();
		$items		= array();

		foreach($selection as $item) {
				// Handle disabled items
			$disabled	= false;
			if( substr($item, 0, 1) === '-' ) {
				$disabled	= true;
				$item		= substr($item, 1);
			}
			$disabledClass	= $disabled ? ' disabled' : '';

				// Add item per type
			switch(substr($item, 0, 1)) {
				case 'p':
					$idPerson	= intval(substr($item, 1));
					$person		= TodoyuContactPersonManager::getPerson($idPerson);
					$colorIndex	= TodoyuColors::getColorIndex($person['id']);
					$items[]	= array(
						'id'	=> 'p' . $idPerson,
						'label'	=> $person->getFullName(true),
						'title'	=> $person->getFullName(true),
						'class'	=> 'person enumColBorLef' . $colorIndex . $disabledClass
					);
					break;

				case 'g':
					$idJobtype	= intval(substr($item, 1));
					$jobType	= TodoyuContactJobTypeManager::getJobType($idJobtype);
					$items[]	= array(
						'id'	=> 'g' . $idJobtype,
						'label'	=> $jobType->getTitle(),
						'title'	=> $jobType->getTitle(),
						'class'	=> 'group' . $disabledClass
					);
					break;
			}
		}

		return $items;
	}



	/**
	 * Get active selection from preference
	 *
	 * @return	Array
	 */
	public function getSelection() {
		if( is_null($this->selection) ) {
			$pref			= TodoyuContactPreferences::getPref($this->selectionPref, 0, AREA);
			$this->selection= TodoyuArray::trimExplode(',', $pref);
		}

		return $this->selection;
	}



	/**
	 * Get IDs of selected groups (jobtypes)
	 *
	 * @return	Array
	 */
	protected function getSelectedGroupIDs() {
		return $this->getSelectedTypeIDs('g');
	}



	/**
	 * Get IDs of selected persons
	 *
	 * @return	Array
	 */
	protected function getSelectedPersonIDs() {
		return $this->getSelectedTypeIDs('p');
	}



	/**
	 * Get IDs of selected items of a specific type
	 * Type is marked with the first letter in the key
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	protected function getSelectedTypeIDs($type) {
		$items		= $this->getSelection();
		$typeItems	= array();

		foreach($items as $item) {
			$item = ltrim($item, '-');
			if( substr($item, 0, 1) === $type ) {
				$typeItems[] = intval(substr($item, 1));
			}
		}

		return $typeItems;
	}



	/**
	 * Save selected items in preference
	 *
	 * @param	Array	$selection
	 */
	public function saveSelection(array $selection) {
		$selection	= TodoyuArray::trim($selection, true);
		$value		= implode(',', $selection);

		TodoyuContactPreferences::savePref($this->selectionPref, $value, 0, true, AREA);
	}



	/**
	 * Check whether group search is active in config
	 *
	 * @return	Boolean
	 */
	protected function isGroupSearchActive() {
		return $this->config['group'] === true;
	}

}

?>