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

class TodoyuContactQuickCreatePersonActionController extends TodoyuActionController {

	/**
	 * Get quick person creation form rendered
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		return TodoyuContactRenderer::renderPersonQuickCreateForm();
	}



	/**
	 * Save person record
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function saveAction(array $params) {
		restrict('contact', 'person:edit');

		$data		= $params['person'];
		$idPerson	= intval($data['id']);

			// Get form, call save hooks, set data
		$form	=	TodoyuPersonManager::getQuickCreateForm($idPerson);
		$data	= TodoyuFormHook::callSaveData('ext/contact/config/form/person.xml', $data, 0);
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() )	{
			$storageData= $form->getStorageData();

			$idPerson	= TodoyuPersonManager::savePerson($storageData);

			TodoyuHeader::sendTodoyuHeader('idRecord', $idPerson);
			TodoyuHeader::sendTodoyuHeader('recordLabel', $storageData['lastname'] . ' ' . $storageData['firstname']);

			return $idPerson;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>