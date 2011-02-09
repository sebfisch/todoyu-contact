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
 *  Form handling action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactFormhandlingActionController extends TodoyuActionController {

	/**
	 * Get additional sub form
	 * 
	 * @param	Array	$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		restrict('contact', 'general:use');

		$formName	= $params['form'];
		$fieldName	= $params['field'];
		$xmlPath	= TodoyuContactManager::getContactTypeFromXml($formName);
		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);

			// Construct form object
		$form 	= TodoyuFormManager::getForm($xmlPath, $index);

			// Load (/preset) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $index);

			// Set form data
		$form->setFormData($formData);
		$form->setRecordID($idRecord);


		$field			= $form->getField($fieldName);
		$form['name']	= $formName;

		return $field->renderNewRecord($index);
	}



	/**
	 * @param	Array	$params
	 * @return	void
	 */
	public function contactimageuploadformAction(array $params) {
		$idRecord	= intval($params['idRecord']);
		$recordType	= $params['recordType'];

		return TodoyuContactRenderer::renderContactImageUploadForm($idRecord, $recordType);
	}



	/**
	 * @param  $params
	 * @return void
	 */
	public function uploadcontactimageAction(array $params) {
		$file		= TodoyuRequest::getUploadFile('file', 'uploadcontactimage');
		$error		= intval($file['error']);
		$data		= $params['uploadcontactimage'];
		$idContact	= intval($data['idContact']);
		$recordType	= $data['recordType'];
		
			// Check again for file limit
		$maxFileSize	= intval(Todoyu::$CONFIG['EXT']['contact']['contactimage']['max_file_size']);

		if( $file['size'] > $maxFileSize ) {
			$error	= UPLOAD_ERR_FORM_SIZE;
		}

			// Render frame content. Success or error
		if( $error === UPLOAD_ERR_OK ) {
			$idReplace	= TodoyuContactImageManager::store($file['tmp_name'], $file['name'], $file['type'], $idContact, $recordType);

			return TodoyuContactRenderer::renderUploadFormFinished($recordType, $idContact, $idReplace);
		} else {
				// Notify upload failure
			Todoyu::log('File upload failed: ' . $file['name'] . ' (ERROR:' . $error . ')', TodoyuLogger::LEVEL_ERROR);
		}
	}

}

?>