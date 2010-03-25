<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

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


		$field			= $form->getField($fieldName);
		$form['name']	= $formName;

		return $field->renderNewRecord($index);
	}

}

?>