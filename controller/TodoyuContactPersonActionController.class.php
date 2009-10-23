<?php


class TodoyuContactPersonActionController extends TodoyuActionController {

	public function editAction(array $params) {
		$idPerson	= intval($params['person']);

		return TodoyuContactRenderer::renderPersonEditForm($idPerson);
	}


	public function listAction(array $params) {
		$sword	= trim($params['sword']);

		return TodoyuContactRenderer::renderPersonList($sword);
	}


	public function saveAction(array $params) {
		$xmlPath	= 'ext/contact/config/form/person.xml';
		$data		= $params['person'];
		$idPerson	= intval($data['id']);

		$form 		= new TodoyuForm($xmlPath);
		$form		= TodoyuFormHook::callBuildForm($xmlPath, $form, $idPerson);

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() )	{
			$storageData= $form->getStorageData();

			$idPerson	= TodoyuContactManager::savePerson($storageData);

			return $idPerson;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	public function addSubformActionOLD(array $params) {
		$fieldName	= $params['field'];
		$index		= intval($params['indexOfForeignRecord']);
		$xmlPath	= 'ext/contact/config/form/person.xml';

			// Construct form object
		$form 	= new TodoyuForm($xmlPath);
		$form	= TodoyuFormHook::callBuildForm($xmlPath, $form, $index);

			// Load (/preset) form data
		$formData	= array();
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $index);

			// Set form data
		$form->setFormData($formData);

		return $form->getField($fieldName)->renderNewRecord($index);
	}



	public function addSubformAction(array $params) {
		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);
		$xmlPath	= 'ext/contact/config/form/person.xml'; // TodoyuContactManager::getContactTypeFromXml($formName);

		return TodoyuFormManager::renderSubformRecord($xmlPath, $fieldName, $formName, $index, $idRecord);


//
//
//			// Construct form object
//		$form 	= new TodoyuForm($xmlPath);
//		$form	= TodoyuFormHook::callBuildForm($xmlPath, $form, $index);
//
//			// Load (/preset) form data
//		$formData	= array();
//		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $index);
//
//			// Set form data
//		$form->setFormData($formData);
//		$form->setRecordID($idRecord);
//
//		TodoyuDebug::printInFirebug($idRecord);
//
//
//		$field			= $form->getField($fieldName);
//		$form['name']	= $formName;
//
//		return $field->renderNewRecord($index);
	}





	public function removeAction(array $params) {
		$idPerson	= intval($params['person']);

		TodoyuUserManager::deleteUser($idPerson);
	}


	public function detailAction(array $params) {
		$idPerson	= intval($params['person']);
		$type		= 'user';

		return TodoyuContactRenderer::renderInfoPopupContent($type, $idPerson);
	}


}

?>