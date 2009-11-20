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

/**
 * Action controller for persons
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonActionController extends TodoyuActionController {

	/**
	 * Edit person, show form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idPerson	= intval($params['person']);

		$content	= TodoyuContactRenderer::renderTabs('person', true);
		$content	.=TodoyuContactRenderer::renderPersonEditForm($idPerson);

		return $content;
	}


	/**
	 * Show person list
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		TodoyuContactPreferences::saveActiveTab('person');

		$sword	= trim($params['sword']);

		$content	= TodoyuContactRenderer::renderTabs('person');
		$content 	.=TodoyuContactRenderer::renderPersonList($sword);

		return $content;
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

		$form 		= TodoyuFormManager::getForm($xmlPath, $idPerson);

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



	/**
	 * Add a subform to the person form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		return TodoyuFormManager::renderSubformRecord($xmlPath, $fieldName, $formName, $index, $idRecord);
	}



	/**
	 * Remove person
	 *
	 * @param	Array		$params
	 */
	public function removeAction(array $params) {
		$idPerson	= intval($params['person']);

		TodoyuUserManager::deleteUser($idPerson);
	}



	/**
	 * Show person info
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		$idPerson	= intval($params['person']);
		$type		= 'user';

		return TodoyuContactRenderer::renderInfoPopupContent($type, $idPerson);
	}

}

?>