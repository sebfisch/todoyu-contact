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
 * Action controller for persons
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
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
	 * Show filtered persons list
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		restrict('contact', 'general:area');

		TodoyuContactPreferences::saveActiveTab('person');

		$sword	= trim($params['sword']);

			// Save search word
		TodoyuContactPreferences::saveSearchWord($sword);

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
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

			$idPerson	= TodoyuContactPersonManager::savePerson($storageData);

			TodoyuHeader::sendTodoyuHeader('idRecord', $idPerson);
			TodoyuHeader::sendTodoyuHeader('recordLabel', $storageData['lastname'].' '.$storageData['firstname']);

			return $idPerson;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	/**
	 * Save person from Wizard
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function saveWizardAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$xmlPath	= 'ext/contact/config/form/person.xml';
		$data		= $params['person'];
		$idPerson	= intval($data['id']);

		$form 		= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$idTarget = $params['idTarget'];

		// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

			$idPerson	= TodoyuContactPersonManager::savePerson($storageData);

			TodoyuHeader::sendTodoyuHeader('idRecord', $idPerson);
			TodoyuHeader::sendTodoyuHeader('recordLabel', $storageData['lastname'].' '.$storageData['firstname']);

			return $idPerson;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.cancelWizard(this.form);');
			$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.saveWizard(this.form, \''.$idTarget.'\');');

			return $form->render();
		}
	}



	/**
	 * Add a sub form to the person form
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

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idRecord);
	}



	/**
	 * Remove person
	 *
	 * @param	Array		$params
	 */
	public function removeAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$idPerson	= intval($params['person']);

		TodoyuContactPersonManager::deletePerson($idPerson);
	}



	/**
	 * Show person info. If its an ajax call, just returns the main-content. Else it returns the whole rendered page.
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		restrict('contact', 'general:area');

		$idPerson	= intval($params['person']);


		$content	= TodoyuContactRenderer::renderPersonInfo($idPerson);

		if( TodoyuRequest::isAjaxRequest() ) {
			$tabs		= TodoyuContactRenderer::renderTabs('person');
			return TodoyuRenderer::renderContent($content, $tabs);
		} else {
			return TodoyuContactRenderer::renderContactPage('person', $idPerson, '', $content);
		}
	}



	/**
	 * Content for the person-wizard popUp
	 *
	 * @todo Move restriction to displayCondition in form
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function addNewContactWizardAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$content= TodoyuString::wrapScript('Todoyu.Ext.contact.Person.onEdit(0);');
		$content.= TodoyuContactRenderer::renderPersonEditFormWizard(0, $params['idField']);

		return $content;
	}



	/**
	 * Renders the image - tag
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function loadimageAction(array $params) {
		$idImage	= $params['idImage'];

		return TodoyuContactImageManager::getImage($idImage, 'person');
	}



	/**
	 * Output of an image
	 *
	 * @param	Array	$params
	 */
	public function renderimageAction(array $params) {
		$idPerson	= $params['idImage'];

		TodoyuContactImageManager::renderImage($idPerson, 'person');
	}



	/**
	 * Remove the given Image
	 *
	 * @param	$params
	 */
	public function removeimageAction(array $params) {
		$idImage	= $params['idImage'];

		TodoyuContactImageManager::removeImage($idImage, 'person');
	}

}

?>