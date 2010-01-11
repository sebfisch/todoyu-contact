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
 * Panel widget to create a new contact
 *
 * @package Todoyu
 * @subpackage contact
 */

class TodoyuPanelWidgetQuickContact extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {

	/**
	 * Constructor of the class
	 *
	 */
	function __construct(array $config, array $params = array(), $idArea = 0, $expanded = true)	{
		parent::__construct(
				'contact',								// ext key
				'quickcontact',							// panel widget ID
				'LLL:panelwidget-quickcontact.title',	// widget title text
				$config,								// widget config array
				$params,								// widget params
				$idArea
		);

		TodoyuPage::addExtAssets('contact', 'panelwidget-quickcontact');

		$this->addHasIconClass();
		$this->addClass($this->type);
	}


	public function renderContent() {
		$tmpl	= 'ext/contact/view/panelwidget-quickcontact.tmpl';
		$data	= array();

		$data['person'] = array(
			'buttonLabel'	=> 'panelwidget-quickcontact.button.person',
			'buttonAction'	=> 'Todoyu.Ext.contact.PanelWidget.QuickContact.addPerson()'
		);

		$data['company'] = array(
			'buttonLabel'	=> 'panelwidget-quickcontact.button.company',
			'buttonAction'	=> 'Todoyu.Ext.contact.PanelWidget.QuickContact.addCompany()'
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
		return allowed('contact', 'panelwidgets:quickContact');
	}

}
?>