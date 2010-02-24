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
			$params,								// widget params
			$idArea									// area ID
		);

				// have public ext. and widget specific assets added
		TodoyuPage::addExtAssets('contact', 'public');
		TodoyuPage::addExtAssets('contact', 'panelwidget-staffselector');

			// Add classes
		parent::addHasIconClass();

			// Get jobtype <> person mapping
		$jobTypes2PersonsJSON	= $this->getJobTypes2PersonsJSON();

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
		$jobTypes	=


		$tmpl	= 'ext/contact/view/panelwidget-staffselector.tmpl';
		$data	= array(
				// Configs
			'id'				=> $this->getID(),
			'jobTypeMultiple'	=> intval($prefs['multiple'])===1,
			'listSize'			=> $this->getListSize(sizeof($persons)),
			'numColors'			=> sizeof($GLOBALS['CONFIG']['COLORS']),
			'colorizeOptions'	=> $this->config['colorizePersonOptions'],
				// Selector options
			'jobTypeOptions'	=> $this->getJobtypeOptions(),
			'personOptions'		=> $this->getPersonOptions(),
				// Prefs
			'selectedJobTypes'	=> TodoyuArray::intExplode(',', $prefs['jobtypes']),
			'selectedPersons'	=> TodoyuArray::intExplode(',', $prefs['persons']),
		);

		$content	= render($tmpl, $data);

		$this->setContent($content);

		return $content;
	}


	public function getPersonOptions() {
		$persons	= TodoyuJobtypeManager::getInternalPersonsWithJobtype();
		$options	= array();

		foreach($persons as $person) {
			$options[] = array(
				'value'	=> $person['id'],
				'label'	=> $person['lastname'] . ' ' . $person['firstname'] . ' (' . $person['jobtype'] . ')',
				'class'	=> 'enumColOptionLeftIcon' . $person['id']
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
	private function getJobTypes2PersonsJSON() {
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

			// Select all staff option
		$jobTypes	= array(
			array(
				'id'	=> 0,
				'label'	=> Label('panelwidget-staffselector.selectAllStaff'),
				'count'	=> 0
			)
		);

			// Find all jobtypes and the person count
		foreach($persons as $person) {
			if( array_key_exists($person['id_jobtype'], $jobTypes) ) {
				$jobTypes[$person['id_jobtype']]['count']++;
			} else {
				$jobTypes[$person['id_jobtype']] = array(
					'id'	=> $person['id_jobtype'],
					'label'	=> $person['jobtype'],
					'count'	=> 1
				);
			}
		}

			// Create options
		foreach($jobTypes as $jobType) {
			$options[] = array(
				'value'	=> $jobType['id'],
				'label'	=> $jobType['label'] . ($jobType['count']>0?' (' . $jobType['count'] . ')':'')
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
		$maxListSize		= intval($GLOBALS['CONFIG']['contact']['panelWidgetStaffSelector']['maxListSize']);
		$size				= $maxListSize;

		if( $numDisplayedPersons < $maxListSize ) {
			$size = $numDisplayedPersons;
		}

		return $size;
	}





	public static function getPrefs() {
		return TodoyuContactPreferences::getStaffSelectorPrefs();
	}



	/**
	 * Get selected staff person's IDs
	 *
	 * @return	Array
	 */
	public static function getSelectedPersons() {
		$persons = TodoyuContactPreferences::getPref('staffSelector-persons');

		return TodoyuArray::intExplode(',', $persons);
	}



	
	public static function getSelectedJobTypes() {
		$jobtypes = TodoyuContactPreferences::getPref('staffSelector-jobtypes');

		return TodoyuArray::intExplode(',', $jobtypes);
	}


}

?>