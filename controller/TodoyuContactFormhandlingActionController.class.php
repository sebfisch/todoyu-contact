<?php

class TodoyuContactFormhandlingActionController extends TodoyuActionController {

	public function addSubformAction(array $params) {

		$formName	= $params['form'];
		$fieldName	= $params['field'];
		$xmlPath	= TodoyuContactManager::getContactTypeFromXml($formName);
		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);

			// Construct form object
		$form 	= TodoyuFormManager::getForm($xmlPath, $index);

			// Load (/preset) form data
		$formData	= array();
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