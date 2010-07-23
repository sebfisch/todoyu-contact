<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
 * class for the contact search input panelWidget
 *
 * @package		Todoyu
 * @subpackage	contact
 */
class TodoyuPanelWidgetContactSearch extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {

	/**
	 * Constructor of the class
	 */
	function __construct(array $config, array $params = array(), $idArea = 0, $expanded = true)	{
		parent::__construct(
				'contact',										// ext key
				'contactSearch',								// panel widget ID
				'LLL:panelwidget-contactsearchinput.title',		// widget title text
				$config,										// widget config array
				$params,										// widget parameters
				$idArea
		);

		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.PanelWidget.ContactSearch.init.bind(Todoyu.Ext.contact.PanelWidget.ContactSearch)', 100);

		$this->addHasIconClass();
	}



	/**
	 * Render content of contact search panel widget
	 * 
	 * @return String
	 */
	public function renderContent() {
		$contactType = TodoyuContactPreferences::getActiveTab();

		$tmpl	= 'ext/contact/view/panelwidget-contactsearch.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'sword'			=> $this->getSearchWord(),
			'contactType'	=> $contactType
		);

		$content = render($tmpl, $data);

		$this->setContent($content);

		return $content;
	}


	private function getSearchWord() {
		return TodoyuContactPreferences::getSearchWord();
	}



	/**
	 * Renders the panel widget
	 *
	 * @return	String
	 */
	public function render()	{
		$this->renderContent();

		return parent::render();
	}



	/**
	 * Check whether using the contact search widget is allowed to current logged in person
	 * -Currently this is allowed to any person having the right to use the contacts area and see contacts
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return true;
//		return allowed('contact', 'panelwidgets:contactSearch');
	}

}
?>