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
 * Class for the contact search input panelWidget
 *
 * @package		Todoyu
 * @subpackage	contact
 */
class TodoyuContactPanelWidgetSearch extends TodoyuPanelWidget {

	/**
	 * Constructor of the class
	 *
	 * @param	Array	$config
	 * @param	Array	$params
	 */
	function __construct(array $config, array $params = array()) {
		parent::__construct(
			'contact',											// ext key
			'contactsearch',									// panel widget ID
			'contact.panelwidget-contactsearchinput.title',	// widget title text
			$config,											// widget config array
			$params												// widget parameters
		);

		TodoyuPage::addJsInit('Todoyu.Ext.contact.PanelWidget.ContactSearch.init()', 100);

		$this->addHasIconClass();
	}



	/**
	 * Render content of contact search panel widget
	 *
	 * @return String
	 */
	public function renderContent() {
		$contactType = TodoyuContactPreferences::getActiveTab();

		$tmpl	= 'ext/contact/view/panelwidget/contactsearch.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'sword'			=> $this->getSearchWord(),
			'contactType'	=> $contactType
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get stored search word from contact preferences
	 *
	 * @return	String
	 */
	private function getSearchWord() {
		return TodoyuContactPreferences::getSearchWord();
	}



	/**
	 * Check whether using the contact search widget is allowed to current logged in person
	 * -Currently this is allowed to any person having the right to use the contacts area and see contacts
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return true;
//		return Todoyu::allowed('contact', 'panelwidgets:contactsearch');
	}

}
?>