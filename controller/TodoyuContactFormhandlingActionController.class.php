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

class TodoyuContactFormhandlingActionController extends TodoyuActionController {

	public function addSubformAction(array $params) {
		restrict('contact', 'general:use');

		$formName	= $params['form'];
		$fieldName	= $params['field'];
		$xmlPath	= TodoyuContactManager::getContactTypeFromXml($formName);
		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);

			// Construct form object
		$form 	= TodoyuFormManager::getForm($xmlPath, $index);

			// Load (/preset) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $index);

			// Set form data
		$form->setFormData($formData);
		$form->setRecordID($idRecord);

		TodoyuDebug::printInFirebug($idRecord);


		$field			= $form->getField($fieldName);
		$form['name']	= $formName;

		return $field->renderNewRecord($index);
	}

}

?>