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
 * 
 */
class TodoyuPanelWidgetContactExport extends TodoyuPanelWidget implements TodoyuPanelWidgetIf {



	/**
	 * Constructor of the class
	 */
	function __construct(array $config, array $params = array(), $idArea = 0, $expanded = true)	{
		parent::__construct(
				'contact',										// ext key
				'contactExport',								// panel widget ID
				'LLL:panelwidget-contactexport.title',			// widget title text
				$config,										// widget config array
				$params,										// widget parameters
				$idArea
		);

		$this->addHasIconClass();
	}



	/**
	 * Render content of contact export panel widget
	 *
	 * @return String
	 */
	public function renderContent() {
		$contactType = TodoyuContactPreferences::getActiveTab();

		$tmpl	= 'ext/contact/view/panelwidgets/panelwidget-contactexport.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'contactType'	=> $contactType,
			'instructionText'	=> TodoyuLabelManager::getLabel('LLL:panelwidget-contactexport.export.instruction'),
			'buttonText'		=> TodoyuLabelManager::getLabel('LLL:panelwidget-contactexport.export.button')
		);

		$content = render($tmpl, $data);

		$this->setContent($content);

		return $content;
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
	 * Check whether using the contact export widget is allowed to current logged in person
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return allowed('contact', 'panelwidgets:contactexport');
	}
	
}

?>