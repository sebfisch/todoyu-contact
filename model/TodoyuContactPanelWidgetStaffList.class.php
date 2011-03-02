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
 * Panel widget for project tree
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPanelWidgetStaffList extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {

	/**
	 * Initialize staff list PanelWidget
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 * @param	Integer		$idArea
	 * @param	Boolean		$expanded
	 */
	public function __construct(array $config, array $params = array(), $idArea = 0) {

			// Construct PanelWidget (init basic configuration)
		parent::__construct(
			'contact',								// ext key
			'stafflist',							// panel widget ID
			'LLL:contact.panelwidget-stafflist.title',		// widget title text
			$config,								// widget config array
			$params,								// widget parameters
			$idArea									// area ID
		);

		$this->addHasIconClass();

		$filterJSON	= json_encode(self::getFilters());

			// Init widget JS (observers)
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.PanelWidget.StaffList.init.bind(Todoyu.Ext.contact.PanelWidget.StaffList, ' . $filterJSON . ')', 100);
	}



	/**
	 * Get person IDs which match to current filters
	 *
	 * @return	Array
	 */
	private function getPersonIDs() {
		$filters	= self::getFilters();
		$filter		= new TodoyuContactPersonFilter($filters);
		$limit		= intval(Todoyu::$CONFIG['EXT']['contact']['panelWidgetStaffList']['maxPersons']);

			// Get matching project IDs
		$personIDs	= $filter->getPersonIDs('', $limit);

		return $personIDs;
	}



	/**
	 * Get persons which match the filters
	 *
	 * @return	Array
	 */
	private function getListedPersons() {
		$personIDs	= $this->getPersonIDs();

		if( sizeof($personIDs) > 0 ) {
			$fields	=	'p.id,
						 p.firstname,
						 p.lastname,
						 p.shortname,
						 p.salutation';
			$tables	= 	TodoyuContactPersonManager::TABLE . ' p,
						ext_contact_company c,
						ext_contact_mm_company_person mm';
			$where	= '		p.id			= mm.id_person
						AND	mm.id_company	= c.id
						AND	c.is_internal	= 1
						AND	p.deleted		= 0
						AND p.id			IN (' . implode(',', $personIDs) . ')';
			$order	= '	CHAR_LENGTH(p.salutation) DESC,
						p.lastname,
						p.firstname';

			$persons= Todoyu::db()->getArray($fields, $tables, $where, '', $order);
		} else {
			$persons	= array();
		}

		return $persons;
	}



	/**
	 * Get value of the full-text filter
	 *
	 * @return	String
	 */
	public static function getSearchText() {
		$filters	= self::getFilters();
		$fulltext	= '';

		foreach($filters as $filter) {
			if( $filter['filter'] === 'fulltext' ) {
				$fulltext = $filter['value'];
			}
		}

		return $fulltext;
	}



	/**
	 * Render filter form
	 *
	 * @return	String
	 */
	public static function renderFilter() {
		$xmlPath= 'ext/contact/config/form/panelwidget-stafflist.xml';
		$form	= TodoyuFormManager::getForm($xmlPath);
		$data	= array(
			'fulltext'	=> self::getSearchText()
		);

		$form->setFormData($data);
		$form->setUseRecordID(false);

		return $form->render();
	}



	/**
	 * Render staff list
	 *
	 * @return	String
	 */
	public function renderList() {
		$tmpl	= 'ext/contact/view/panelwidgets/panelwidget-stafflist-list.tmpl';
		$data	= array(
			'id'		=> $this->getID(),
			'persons'	=> $this->getListedPersons()
		);

		return render($tmpl, $data);
	}



	/**
	 * Render the panel widget content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$filter	= self::renderFilter();
		$list	= $this->renderList();

		$tmpl	= 'ext/contact/view/panelwidgets/panelwidget-stafflist.tmpl';
		$data	= array(
			'id'		=> $this->getID(),
			'filter'	=> $filter,
			'list'		=> $list
		);

		$content = render($tmpl, $data);

		$this->setContent($content);

		return $content;
	}



	/**
	 * Render the whole panel widget
	 *
	 * @return	String
	 */
	public function render() {
		$this->renderContent();

		return parent::render();
	}



	/**
	 * Get active filters
	 *
	 * @param 	Integer	$idArea
	 * @return	Array
	 */
	public static function getFilters() {
		$filters = TodoyuContactPreferences::getPref('panelwidget-stafflist-filter', 0, AREA);

		if( $filters === false || $filters === '' ) {
			return array();
		} else {
			return json_decode($filters, true);
		}
	}



	/**

	 *
	 * @param	Array		$activeFilters
	 * @param	Integer		$idArea
	 */
	public function saveFilters(array $filters) {
		$filterConfig = array();

		foreach($filters as $name => $value) {
			$filterConfig[] = array(
				'filter'=> $name,
				'value'	=> $value
			);
		}

		$filterPref	= json_encode($filterConfig);

		TodoyuContactPreferences::savePref('panelwidget-stafflist-filter', $filterPref, 0, true, AREA);
	}



	/**
	 * Check panelWidget access permission
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return allowed('contact', 'general:use');
	}

}

?>