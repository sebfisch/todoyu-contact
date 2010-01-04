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
 *  Action controller for company
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 *
	 */
	public function init() {
		restrict('contact', 'company:use');
	}

	/**
	 * Edit company
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		restrict('contact', 'company:edit');

		$idCompany	= intval($params['company']);

		$content	= TodoyuContactRenderer::renderTabs('company', true);
		$content	.=TodoyuContactRenderer::renderCompanyEditForm($idCompany);

		return $content;
	}



	/**
	 * List companies
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		restrict('contact', 'company:use');

		TodoyuContactPreferences::saveActiveTab('company');

		$sword	= trim($params['sword']);

		$content	= TodoyuContactRenderer::renderTabs('company');
		$content	.=TodoyuContactRenderer::renderCompanyList($sword);

		return $content;
	}



	/**
	 * Save company record
	 *
	 * @param	Array		$params
	 * @return	String		Form html or company ID
	 */
	public function saveAction(array $params) {
		restrict('contact', 'company:edit');

		$xmlPath	= 'ext/contact/config/form/company.xml';
		$data		= $params['company'];
		$idCompany	= intval($data['id']);

		$form 		= TodoyuFormManager::getForm($xmlPath, $idCompany);

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



	/**
	 * Add a subform record to company form
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		restrict('contact', 'company:edit');

		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		return TodoyuFormManager::renderSubformRecord($xmlPath, $fieldName, $formName, $index, $idRecord);
	}



	/**
	 * Remove company record	 *
	 * @param	Array		$params
	 * @return	void
	 */
	public function removeAction(array $params) {
		restrict('contact', 'company:delete');

		$idCompany	= intval($params['company']);

		TodoyuCompanyManager::deleteCompany($idCompany);
	}



	/**
	 * Show company details
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		restrict('contact', 'company:use');

		$idCompany	= intval($params['company']);
		$type		= 'company';

		return TodoyuContactRenderer::renderInfoPopupContent($type, $idCompany);
	}

}

?>