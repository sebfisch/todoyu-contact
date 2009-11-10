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
 * Action controller for company
 * 
 */
class TodoyuContactCompanyActionController extends TodoyuActionController {

	/**
	 * Edit company
	 * 
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idCompany	= intval($params['company']);

		return TodoyuContactRenderer::renderCompanyEditForm($idCompany);
	}


	
	/**
	 * List companies
	 * 
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		TodoyuContactPreferences::saveActiveTab('company');

		$sword	= trim($params['sword']);

		return TodoyuContactRenderer::renderCompanyList($sword);
	}


	
	/**
	 * Save company record
	 * 
	 * @param	Array		$params
	 * @return	String		Form html or company ID
	 */
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



	/**
	 * Add a subform record to company form
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
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
		$idCompany	= intval($params['company']);
		
		TodoyuCustomerManager::deleteCustomer($idCompany);
	}


	
	/**
	 * Show company details
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		$idCompany	= intval($params['company']);
		$type		= 'company';

		return TodoyuContactRenderer::renderInfoPopupContent($type, $idCompany);
	}

}

?>