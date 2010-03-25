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

/**
 * Action controller for persons
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 *
	 */
	public function init() {
		restrict('contact', 'general:area');
	}



	/**
	 * Edit person, show form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$idPerson	= intval($params['person']);

		$tabs	= TodoyuContactRenderer::renderTabs('person', true);
		$content= TodoyuContactRenderer::renderPersonEditForm($idPerson);

		return TodoyuRenderer::renderContent($content, $tabs);
	}



	/**
	 * Show person list
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		restrict('contact', 'general:area');

		TodoyuContactPreferences::saveActiveTab('person');

		$sword	= trim($params['sword']);

		$tabs	= TodoyuContactRenderer::renderTabs('person');
		$content= TodoyuListingRenderer::render('contact', 'person', 0, $sword);

		return TodoyuRenderer::renderContent($content, $tabs);
	}



	/**
	 * Get person paged listing
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listingAction(array $params) {
		restrict('contact', 'general:area');

		$offset	= intval($params['offset']);

		return TodoyuListingRenderer::render('contact', 'person', $offset);
	}



	/**
	 * Save person
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function saveAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$xmlPath	= 'ext/contact/config/form/person.xml';
		$data		= $params['person'];
		$idPerson	= intval($data['id']);

		$form 		= TodoyuFormManager::getForm($xmlPath, $idPerson);

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() )	{
			$storageData= $form->getStorageData();

			$idPerson	= TodoyuPersonManager::savePerson($storageData);

			TodoyuHeader::sendTodoyuHeader('idRecord', $idPerson);
			TodoyuHeader::sendTodoyuHeader('recordLabel', $storageData['lastname'].' '.$storageData['firstname']);

			return $idPerson;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	/**
	 * Add a subform to the person form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$xmlPath	= 'ext/contact/config/form/person.xml';

		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);

		return TodoyuFormManager::renderSubformRecord($xmlPath, $fieldName, $formName, $index, $idRecord);
	}



	/**
	 * Remove person
	 *
	 * @param	Array		$params
	 */
	public function removeAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$idPerson	= intval($params['person']);

		TodoyuPersonManager::deletePerson($idPerson);
	}



	/**
	 * Show person info
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		restrict('contact', 'general:area');

		$idPerson	= intval($params['person']);

		return TodoyuContactRenderer::renderPersonInfo($idPerson);;
	}



	/**
	 * Content for the person-wizard popup
	 *
	 * @todo Move restriction to displayCondition in form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function addNewContactWizardAction(array $params)	{
		restrict('contact', 'person:editAndDelete');

		$content = TodoyuPage::getExtJSinline('contact');
		$content.= TodoyuContactRenderer::renderPersonEditFormWizard(0, $params['idField']);

		return $content;
	}

}

?>