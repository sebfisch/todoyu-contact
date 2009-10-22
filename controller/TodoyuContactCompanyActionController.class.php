<?php


class TodoyuContactCompanyActionController extends TodoyuActionController {

	public function editAction(array $params) {
		$idCompany	= intval($params['company']);

		return TodoyuContactRenderer::renderCompanyEditForm($idCompany);
	}


	public function listAction(array $params) {
		TodoyuContactPreferences::saveActiveTab('company');

		$sword	= trim($params['sword']);

		return TodoyuContactRenderer::renderCompanyList($sword);
	}


	public function saveAction(array $params) {
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$data		= $params['company'];
		$idCompany	= intval($data['id']);

		$form 		= new TodoyuForm($xmlPath);
		$form		= TodoyuFormHook::callBuildForm($xmlPath, $form, $idCompany);

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() )	{
			$storageData= $form->getStorageData();

			$idCompany	= TodoyuContactManager::saveCompany($storageData);

			return $idCompany;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	public function addSubformAction(array $params) {
		$fieldName	= $params['field'];
		$index		= intval($params['indexOfForeignRecord']);
		$xmlPath	= 'ext/contact/config/form/company.xml';

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


	public function removeAction(array $params) {
		$idCompany	= intval($params['company']);

		TodoyuUserManager::deleteUser($idCompany);
	}


	public function detailAction(array $params) {
		$idCompany	= intval($params['company']);
		$type		= 'company';

		return TodoyuContactRenderer::renderInfoPopupContent($type, $idCompany);
	}

}

?>