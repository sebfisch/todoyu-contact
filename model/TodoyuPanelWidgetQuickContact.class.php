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
 * @package todoyu
 * @subpackage contact
 */

class TodoyuPanelWidgetQuickContact extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {

	private $type;

	/**
	 * Constructor of the class
	 *
	 */
	function __construct(array $config, array $params = array(), $idArea = 0, $expanded = true)	{
		$this->type	= TodoyuContactPreferences::getActiveTab();

		parent::__construct(
				'contact',														// ext key
				'quickcontact',													// panel widget ID
				'LLL:panelwidget-quickcontact.' . $this->type . '.title',	// widget title text
				$config,														// widget config array
				$params,														// widget params
				$idArea
		);

		TodoyuPage::addExtAssets('contact', 'panelwidget-quickcontact');

//		self::$contactType = TodoyuContactPreferences::getActiveTab();


		$this->addHasIconClass();
		$this->addClass($this->type);
	}


	public function renderContent() {
		$tmpl	= 'ext/contact/view/panelwidget-quickcontact.tmpl';
		$data	= array();


//			'labels' => array(
//				'title' => 'panelwidget-quickcontact.'.self::$contactType.'.title'
//			),
//			'type'	=> self::$contactType
//		);
//
		switch($this->type) {
			case 'person':
				$data['title']			= 'panelwidget-quickcontact.person.title';
				$data['buttonLabel']	= 'panelwidget-quickcontact.person.title';
				$data['buttonAction']	= 'Todoyu.Ext.contact.Person.add()';
				break;

			case 'company':
				$data['title']			= 'panelwidget-quickcontact.company.title';
				$data['buttonLabel']	= 'panelwidget-quickcontact.company.title';
				$data['buttonAction']	= 'Todoyu.Ext.contact.Company.add()';
				break;
		}


		$content = render($tmpl, $data);

		$this->setContent($content);

		return $content;
	}


	/**
	 * Renders the panelwidget
	 *
	 * @return	String
	 */
	function render()	{
		$this->renderContent();

		return parent::render();
	}

}
?>