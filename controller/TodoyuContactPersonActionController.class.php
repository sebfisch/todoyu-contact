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
		Todoyu::restrict('contact', 'general:use');
	}



	/**
	 * Edit person, show form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idPerson	= intval($params['person']);

		TodoyuContactPersonRights::restrictEdit($idPerson);

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
		Todoyu::restrict('contact', 'general:area');

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
		Todoyu::restrict('contact', 'general:area');

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
		$xmlPath	= 'ext/contact/config/form/person.xml';
		$data		= $params['person'];
		$idPerson	= intval($data['id']);

		if( $idPerson === 0 ) {
			TodoyuContactPersonRights::restrictAdd();
		} else {
			TodoyuContactPersonRights::restrictEdit($idPerson);
		}

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
		$xmlPath	= 'ext/contact/config/form/person.xml';
		$data		= $params['person'];
		$idPerson	= intval($data['id']);

		if( $idPerson === 0 ) {
			TodoyuContactPersonRights::restrictAdd();
		} else {
			TodoyuContactPersonRights::restrictEdit($idPerson);
		}

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
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idPerson	= intval($params['record']);

		TodoyuContactPersonRights::restrictEdit($idPerson);

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idPerson);
	}



	/**
	 * Remove person
	 *
	 * @param	Array		$params
	 */
	public function removeAction(array $params) {
		$idPerson	= intval($params['person']);

		TodoyuContactPersonRights::restrictDelete($idPerson);

		TodoyuContactPersonManager::deletePerson($idPerson);
	}



	/**
	 * Show person info. If its an AJAX call, just returns the main-content. Else it returns the whole rendered page.
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		$idPerson	= intval($params['person']);

		TodoyuContactPersonRights::restrictSee($idPerson);

		$content	= TodoyuContactRenderer::renderPersonDetails($idPerson);

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
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function createWizardAction(array $params) {
		TodoyuContactPersonRights::restrictAdd();

		$field		= trim($params['field']);
		$idRecord	= intval($params['record']);

		return TodoyuContactRenderer::renderPersonCreateWizard(0, $field);
	}



	/**
	 * Renders the image - tag
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function loadimageAction(array $params) {
		$idPerson	= $params['idImage'];

		TodoyuContactPersonRights::restrictSee($idPerson);

		return TodoyuContactImageManager::getImage($idPerson, 'person');
	}



	/**
	 * Output of an image
	 *
	 * @param	Array	$params
	 */
	public function renderimageAction(array $params) {
		$idPerson	= $params['idImage'];

		TodoyuContactPersonRights::restrictSee($idPerson);

		TodoyuContactImageManager::renderImage($idPerson, 'person');
	}



	/**
	 * Remove the given Image
	 *
	 * @param	$params
	 */
	public function removeimageAction(array $params) {
		$idPerson	= $params['idImage'];

		TodoyuContactPersonRights::restrictEdit($idPerson);

		TodoyuContactImageManager::removeImage($idPerson, 'person');
	}

}

?>