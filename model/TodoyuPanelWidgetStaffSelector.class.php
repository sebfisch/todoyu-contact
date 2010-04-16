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
 * Panel widget: Staff Selector
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuPanelWidgetStaffSelector extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {

	/**
	 * Constructor (init widget)
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 * @param	Integer		$idArea
	 * @param	Boolean		$expanded
	 */
	public function __construct(array $config, array $params = array(), $idArea = 0) {
			// construct PanelWidget (init basic configuration)
		parent::__construct(
			'contact',								// ext key
			'staffselector',						// panel widget ID
			'LLL:panelwidget-staffselector.title',	// widget title text
			$config,								// widget config array
			$params,								// widget parameters
			$idArea									// area ID
		);

			// Add classes
		$this->addHasIconClass();

			// Get job type <> person mapping
		$jobTypes2PersonsJSON	= $this->getJobtypes2PersonsJSON();

			// init widget JS (observers)
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.PanelWidget.StaffSelector.init.bind(Todoyu.Ext.contact.PanelWidget.StaffSelector, ' . $jobTypes2PersonsJSON . ')', 100);

			// Generate person color css and graphic
		TodoyuColors::generate();
	}



	/**
	 * Render panel content (staff selector)
	 *
	 * @return	String
	 */
	public function renderContent() {
		$prefs		= self::getPrefs();
		$tmpl	= 'ext/contact/view/panelwidget-staffselector.tmpl';

		$personOptions	= $this->getStaffPersonOptions();
		$data	= array(
				// Configs
			'id'				=> $this->getID(),
			'jobTypeMultiple'	=> intval($prefs['multiple'])===1,
			'listSize'			=> $this->getListSize(sizeof($personOptions)),
			'numColors'			=> sizeof(Todoyu::$CONFIG['COLORS']),
			'colorizeOptions'	=> $this->config['colorizePersonOptions'],

				// Selector options
			'jobTypeOptions'	=> $this->getJobtypeOptions(),
			'personOptions'		=> $personOptions,

				// Prefs
			'selectedJobtypes'	=> TodoyuArray::intval($prefs['jobtypes']),
			'selectedPersons'	=> TodoyuArray::intval($prefs['persons']),
		);

		$content	= render($tmpl, $data);
		$this->setContent($content);

		return $content;
	}



	/**
	 * Get options config array of persons assigned to internal company (staff members)
	 *
	 * @return	Array
	 */
	public function getStaffPersonOptions() {
		$persons	= TodoyuJobtypeManager::getInternalPersonsWithJobtype();
		$options	= array();

		foreach($persons as $person) {
			$options[] = array(
				'value'	=> $person['id'],
				'label'	=> $person['lastname'] . ' ' . $person['firstname'] . ' (' . ( ! empty($person['jobtype']) ? $person['jobtype'] : Label('panelwidget-staffselector.noFunction') ) . ')',
				'class'	=> 'enumColOptionLeftIcon' . TodoyuColors::getColorIndex($person['id'])
			);
		}

		return $options;
	}



	/**
	 * Render widget (get evoked)
	 *
	 * @return	String
	 */
	public function render() {
		$this->renderContent();

		return parent::render();
	}



	/**
	 * Build the json code for mapping jobtypes and persons
	 *
	 * @return	String
	 */
	private function getJobtypes2PersonsJSON() {
		$persons	= TodoyuPersonManager::getInternalPersons(true, true);
		$mapping	= array();

		foreach($persons as $person) {
			$mapping[intval($person['id_jobtype'])][] = intval($person['id']);
		}

		return json_encode($mapping);
	}



	/**
	 * Get job type options config array, listing only jobtypes being assigned labels including the amount of assigned persons
	 *
	 * @return	Array
	 */
	private function getJobtypeOptions() {
		$persons	= TodoyuJobtypeManager::getInternalPersonsWithJobtype();
		$options	= array();

		$jobtypes	= array(
				// Select all staff option
			array(
				'id'	=> 0,
				'label'	=> Label('panelwidget-staffselector.selectAllStaff'),
				'count'	=> 0
			)
		);

			// Find all jobtypes and the person count
		foreach($persons as $person) {
			if( array_key_exists($person['id_jobtype'], $jobtypes) ) {
				$jobtypes[$person['id_jobtype']]['count']++;
			} else {
				$jobtypes[$person['id_jobtype']] = array(
					'id'	=> $person['id_jobtype'],
					'label'	=> $person['jobtype'],
					'count'	=> 1
				);
			}
		}

			// Create options: label is Person name + jobtype
		foreach($jobtypes as $jobtype) {
			if ( $jobtype['id'] == 0 ) {
				$amount	= ' (' . sizeof($persons) . ')';
			} else {
				$amount	= $jobtype['count'] > 0 ? ' (' . $jobtype['count'] . ')' : '';
			}

			$options[] = array(
				'value'	=> $jobtype['id'],
				'label'	=> $jobtype['label'] . $amount
			);
		}

		return $options;
	}



	/**
	 * Get list size
	 *
	 * @param	Integer		$numDisplayedPersons
	 * @return	Integer
	 */
	private function getListSize($numDisplayedPersons) {
		$numDisplayedPersons= intval($numDisplayedPersons);
		$maxListSize		= intval(Todoyu::$CONFIG['contact']['panelWidgetStaffSelector']['maxListSize']);
		$size				= $maxListSize;

		if( $numDisplayedPersons < $maxListSize ) {
			$size = $numDisplayedPersons;
		}

		return $size;
	}


	/**
	 * Get preferences of staff selector
	 *
	 * @return	Array
	 */
	public static function getPrefs() {
		$prefs	= TodoyuContactPreferences::getPref('panelwidget-staffselector', 0, AREA, false);

		return $prefs !== false ? json_decode($prefs, true) : array();
	}



	/**
	 * Get selected staff members person's IDs
	 *
	 * @return	Array
	 */
	public static function getSelectedPersons() {
		$prefs = self::getPrefs();

		return TodoyuArray::assure($prefs['persons']);
	}



	/**
	 * Check access rights to staff selector widget
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return allowed('contact', 'panelwidgets:staffSelector');
	}

}
?>