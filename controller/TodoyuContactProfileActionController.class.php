<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * Handles the profile actions for the contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactProfileActionController  extends TodoyuActionController {

	/**
	 * @param	Array	$params
	 */
	public function init(array $params) {
		Todoyu::restrict('contact', 'general:profile');
	}



	/**
	 * Handler for the save profile action
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function saveAction($params) {
		$xmlPath	= 'ext/contact/config/form/profile-person.xml';
		$data		= $params['person'];
		$idPerson	= Todoyu::personid();

		TodoyuContactPersonRights::restrictEdit($idPerson);

		$form		= TodoyuFormManager::getForm($xmlPath, $idPerson);

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

			$idPerson	= TodoyuContactPersonManager::savePerson($storageData);

			TodoyuHeader::sendTodoyuHeader('idRecord', $idPerson);
			TodoyuHeader::sendTodoyuHeader('recordLabel', $storageData['lastname'].' '.$storageData['firstname']);

			return TodoyuContactProfileRenderer::renderPersonForm($idPerson);
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}
}

?>