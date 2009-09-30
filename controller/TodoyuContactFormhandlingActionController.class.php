<?php

class TodoyuContactFormhandlingActionController extends TodoyuActionController {

	public function showFormAction(array $params) {
		$idRecord	= intval($params['editID']);
		$type		= TodoyuContactPreferences::getActiveTab();

		return TodoyuContactRenderer::renderForm($type, $idRecord);
	}


	public function saveAction(array $params) {
		$type		= $params['form'];
		$xmlPath	= TodoyuContactManager::getContactTypeFromXml($type);
		$data		= $params[$type];
		$idRecord	= intval($data['id']);



		$jsonResponse	= new stdClass();
		//$xmlPath		= TodoyuContactManager::getContactTypeFromXml();

			// Load form data
		$activeTab	= TodoyuContactPreferences::getActiveTab();
//		$data 	= $params[$activeTab];
//		$idRecord	= 0;
//		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $idRecord);

			// Construct form object
		$form 		= new TodoyuForm($xmlPath);
		$form		= TodoyuFormHook::callBuildForm($xmlPath, $form, $idRecord);

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() )	{
			$storageData= $form->getStorageData();

			$idContact = TodoyuContactManager::saveContact($type, $storageData);


//			$saveFunc 	= TodoyuContactManager::getContactTypeSaveFunc($type);
//			TodoyuDebug::printInFirebug($saveFunc);
//			TodoyuDiv::callUserFunction($saveFunc, $storageData, $xmlPath);
			$jsonResponse->saved = true;
		} else {
				// Not valid: re-render with errors marked
			$jsonResponse->saved	= false;
			$jsonResponse->formHTML = $form->render();
		}

		TodoyuHeader::sendHeaderJSON();

		return json_encode($jsonResponse);
	}

	public function removeEntryAction(array $params) {
		$removeFunc = TodoyuContactManager::getContactTypeDeleteFunc();
		$idRecord	= intval($params['removeID']);

		TodoyuDiv::callUserFunction($removeFunc, $idRecord);
	}


	public function addSubformAction(array $params) {

		$formName	= $params['formname'];
		$fieldName	= $params['field'];
		$xmlPath	= TodoyuContactManager::getContactTypeFromXml($formName);
		$index		= intval($params['indexOfForeignRecord']);
		$idSubform	= $index;

			// Construct form object
		$form 	= new TodoyuForm($xmlPath);
		$form	= TodoyuFormHook::callBuildForm($xmlPath, $form, $index);

			// Load (/preset) form data
		$formData	= array();
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $index);

			// Set form data
		$form->setFormData($formData);


		$field			= $form->getField($fieldName);
		$form['name']	= $formName;

		return $field->addNewRecord($index);
	}

}

?>