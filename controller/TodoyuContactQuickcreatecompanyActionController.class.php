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
 *  Company records quick creation action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactQuickCreateCompanyActionController extends TodoyuActionController {

	/**
	 * Get quick company form rendered
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		return TodoyuContactRenderer::renderCompanyQuickCreateForm();
	}



	/**
	 * Save company record
	 *
	 * @param	Array		$params
	 * @return	String		Form html or company ID
	 */
	public function saveAction(array $params) {
		restrict('contact', 'person:editAndDelete');

			// Get form object, call save hooks, set data
		$form	= TodoyuCompanyManager::getQuickCreateForm();

		$data	= $params['company'];
			// Call hooked save data functions
		$data	= TodoyuFormHook::callSaveData('ext/contact/config/form/company.xml', $data, 0);
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() )	{
			$storageData= $form->getStorageData();
			$idCompany	= TodoyuCompanyManager::saveCompany($storageData);

			TodoyuHeader::sendTodoyuHeader('idCompany', $idCompany);
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>