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
 *  Action controller for company
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 */
	public function init() {
		Todoyu::restrict('contact', 'general:use');
	}



	/**
	 * Edit company
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		$idCompany	= intval($params['company']);

		TodoyuContactCompanyRights::restrictEdit($idCompany);

		$tabs	= TodoyuContactRenderer::renderTabs('company', true);
		$content= TodoyuContactRenderer::renderCompanyEditForm($idCompany);

		return TodoyuRenderer::renderContent($content, $tabs);
	}



	/**
	 * Show company list view with tabs
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listAction(array $params) {
		Todoyu::restrict('contact', 'general:area');

		TodoyuContactPreferences::saveActiveTab('company');

		$sword	= trim($params['sword']);

			// Save search-word
		TodoyuContactPreferences::saveSearchWord($sword);

		$tabs	= TodoyuContactRenderer::renderTabs('company');
		$content= TodoyuListingRenderer::render('contact', 'company', 0, $sword);

		return TodoyuRenderer::renderContent($content, $tabs);
	}



	/**
	 * Get company paged listing
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function listingAction(array $params) {
		Todoyu::restrict('contact', 'general:area');

		$offset	= intval($params['offset']);

		return TodoyuListingRenderer::render('contact', 'company', $offset);
	}



	/**
	 * Save company record
	 *
	 * @param	Array		$params
	 * @return	String		Form HTML or company ID
	 */
	public function saveAction(array $params) {
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$data		= $params['company'];
		$idCompany	= intval($data['id']);

		if( $idCompany === 0 ) {
			TodoyuContactCompanyRights::restrictAdd();
		} else {
			TodoyuContactCompanyRights::restrictEdit($idCompany);
		}

		$form 		= TodoyuFormManager::getForm($xmlPath, $idCompany);

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

			$idCompany	= TodoyuContactCompanyManager::saveCompany($storageData);

			return $idCompany;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	/**
	 * Add a sub form record to company form
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idCompany	= intval($params['record']);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		TodoyuContactCompanyRights::restrictEdit($idCompany);

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idCompany);
	}



	/**
	 * Remove company record
	 *
	 * @param	Array		$params
	 */
	public function removeAction(array $params) {
		$idCompany	= intval($params['company']);

		TodoyuContactCompanyRights::restrictDelete($idCompany);

		if( TodoyuContactCompanyManager::hasProjects($idCompany) ) {
			TodoyuHeader::sendTodoyuErrorHeader();
			TodoyuHeader::sendTodoyuHeader('errormessage', Todoyu::Label('contact.ext.company.delete.hasProjects'));
		} else {
			TodoyuContactCompanyManager::deleteCompany($idCompany);
		}
	}



	/**
	 * Show company details (popUp)
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		Todoyu::restrict('contact', 'general:area');

		$idCompany	= intval($params['company']);

		TodoyuContactCompanyRights::restrictSee($idCompany);

		$type		= 'company';

		$tabs		= TodoyuContactRenderer::renderTabs('company');
		$content	= TodoyuContactRenderer::renderDetailsContent($type, $idCompany);

		$content	= TodoyuRenderer::renderContent($content, $tabs);

		return TodoyuRequest::isAjaxRequest() ? $content : TodoyuContactRenderer::renderContactPage('company', $idCompany, '', $content);
	}



	/**
	 * Returns the Options for the Working Location selector
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function getCompanyAddressOptionsAction(array $params) {
		$tmpl		= 'core/view/form/FormElement_Select_Options.tmpl';

		$idCompany	= intval($params['idCompany']);

		TodoyuContactCompanyRights::restrictSee($idCompany);

		$data		= array(
				'options'	=> TodoyuContactViewHelper::getWorkaddressOptions($idCompany),
				'value'		=> array()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render select options of regions of given country
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function getRegionOptionsAction(array $params) {
		$tmpl	= 'core/view/form/FormElement_Select_Options.tmpl';

		$idCountry	= intval($params['idCountry']);

		$data	= array(
			'options'	=> TodoyuStaticRecords::getCountryZoneOptions($idCountry),
			'value'		=> array()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Content for the company-wizard popUp
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function createWizardAction(array $params) {
		TodoyuContactCompanyRights::restrictAdd();

		$idCompany	= intval($params['record']);
		$fieldName	= trim($params['field']);

		return TodoyuContactRenderer::renderCompanyCreateWizard(0, $fieldName);
	}



	/**
	 * Save company from Wizard
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public static function saveCreateWizardAction(array $params) {
		$xmlPath	= 'ext/contact/config/form/company.xml';
		$data		= $params['company'];
		$idCompany	= intval($data['id']);

		if( $idCompany === 0 ) {
			TodoyuContactCompanyRights::restrictAdd();
		} else {
			TodoyuContactCompanyRights::restrictEdit($idCompany);
		}

		$form 		= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$fieldName = $params['field'];

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() ) {
			$storageData= $form->getStorageData();

			$idCompany	= TodoyuContactCompanyManager::saveCompany($storageData);

			TodoyuHeader::sendTodoyuHeader('record', $idCompany);
			TodoyuHeader::sendTodoyuHeader('label', $storageData['title']);

			return $idCompany;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.cancelWizard(this.form)');
			$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.saveWizard(this.form, \'' . $fieldName . '\')');

			return $form->render();
		}
	}



	/**
	 * Renders the image - tag
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function loadimageAction(array $params) {
		$idCompany	= $params['idImage'];

		TodoyuContactCompanyRights::restrictSee($idCompany);

		return TodoyuContactImageManager::getImage($idCompany, 'company');
	}



	/**
	 * Output of an image
	 *
	 * @param	Array	$params
	 */
	public function renderimageAction(array $params) {
		$idCompany	= $params['idImage'];

		TodoyuContactCompanyRights::restrictSee($idCompany);

		TodoyuContactImageManager::renderImage($idCompany, 'company');
	}



	/**
	 * Remove the given Image
	 *
	 * @param	$params
	 */
	public function removeimageAction(array $params) {
		$idCompany	= $params['idImage'];

		TodoyuContactCompanyRights::restrictSee($idCompany);

		TodoyuContactImageManager::removeImage($idCompany, 'company');
	}

}
?>