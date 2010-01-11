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
 * class for the contact search input panelwidget
 *
 * @package Todoyu
 * @subpackage contact
 */

class TodoyuPanelWidgetContactSearch extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {

	/**
	 * Constructor of the class
	 *
	 */
	function __construct(array $config, array $params = array(), $idArea = 0, $expanded = true)	{
		parent::__construct(
				'contact',										// ext key
				'contactSearch',								// panel widget ID
				'LLL:panelwidget-contactsearchinput.title',		// widget title text
				$config,										// widget config array
				$params,										// widget params
				$idArea
		);

		TodoyuPage::addExtAssets('contact', 'panelwidget-contactsearch');
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.PanelWidget.ContactSearch.init.bind(Todoyu.Ext.contact.PanelWidget.ContactSearch)');

		$this->addHasIconClass();
	}


	public function renderContent() {
		$contactType = TodoyuContactPreferences::getActiveTab();

		$tmpl	= 'ext/contact/view/panelwidget-contactsearch.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'showAll'		=> TodoyuContactPreferences::getShowAll(),
			'sword'			=> TodoyuRequest::getParam('sword'),
			'contactType'	=> $contactType
		);

		$content = render($tmpl, $data);

		$this->setContent($content);

		return $content;
	}


	/**
	 * Renders the panelwidget
	 *
	 * @return	String
	 */
	public function render()	{
		$this->renderContent();

		return parent::render();
	}


	public static function isAllowed() {
		return allowed('contact', 'panelwidgets:contactSearch');
	}

}
?>