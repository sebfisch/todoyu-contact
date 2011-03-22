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
 *
 */
class TodoyuContactPanelWidgetExport extends TodoyuPanelWidget {



	/**
	 * Constructor of the class
	 */
	function __construct(array $config, array $params = array()) {
		parent::__construct(
				'contact',										// ext key
				'contactexport',								// panel widget ID
				'LLL:contact.panelwidget-contactexport.title',			// widget title text
				$config,										// widget config array
				$params										// widget parameters
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
			'instructionText'	=> Label('contact.panelwidget-contactexport.export.instruction'),
			'buttonText'		=> Label('contact.panelwidget-contactexport.export.button')
		);

		return render($tmpl, $data);
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