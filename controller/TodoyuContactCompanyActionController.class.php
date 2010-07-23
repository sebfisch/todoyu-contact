<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
		restrict('contact', 'general:use');
	}



	/**
	 * Edit company
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function editAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$idCompany	= intval($params['company']);

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
		restrict('contact', 'general:area');

		TodoyuContactPreferences::saveActiveTab('company');

		$sword	= trim($params['sword']);

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
		restrict('contact', 'general:area');

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
		restrict('contact', 'person:editAndDelete');

		$xmlPath	= 'ext/contact/config/form/company.xml';
		$data		= $params['company'];
		$idCompany	= intval($data['id']);

		$form 		= TodoyuFormManager::getForm($xmlPath, $idCompany);

			// Set form data
		$form->setFormData($data);

			// Validate, render
		if( $form->isValid() )	{
			$storageData= $form->getStorageData();

			$idCompany	= TodoyuCompanyManager::saveCompany($storageData);

			return $idCompany;
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}



	/**
	 * Add a sub form record to company form
	 * @param	Array		$params
	 * @return	String
	 */
	public function addSubformAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$formName	= $params['form'];
		$fieldName	= $params['field'];

		$index		= intval($params['index']);
		$idRecord	= intval($params['record']);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		return TodoyuFormManager::renderSubFormRecord($xmlPath, $fieldName, $formName, $index, $idRecord);
	}



	/**
	 * Remove company record
	 *
	 * @param	Array		$params
	 * @return	void
	 */
	public function removeAction(array $params) {
		restrict('contact', 'person:editAndDelete');

		$idCompany	= intval($params['company']);

		if( TodoyuCompanyManager::hasProjects($idCompany) ) {
			TodoyuHeader::sendTodoyuErrorHeader();
			TodoyuNotification::notifyError('LLL:contact.company.delete.hasProjects');
		} else {
			TodoyuCompanyManager::deleteCompany($idCompany);
		}
	}



	/**
	 * Show company details (popUp)
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function detailAction(array $params) {
		restrict('contact', 'general:area');

		$idCompany	= intval($params['company']);
		$type		= 'company';

		return TodoyuContactRenderer::renderInfoPopupContent($type, $idCompany);
	}



	/**
	 * Returns the Options for the Working Location selector
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function getCompanyAddressOptionsAction(array $params)	{
		$tmpl		= 'core/view/form/FormElement_Select_Options.tmpl';

		$idCompany	= intval($params['idCompany']);

		$data		= array(
				'options'	=> TodoyuContactViewHelper::getWorkaddressOptions($idCompany),
				'value'		=> array()
		);

		return render($tmpl, $data);
	}



	/**
	 * @todo	comment
	 * @param	Array		$params
	 */
	public function getRegionOptionsAction(array $params)	{
		$tmpl	= 'core/view/form/FormElement_Select_Options.tmpl';

		$idCountry	= intval($params['idCountry']);

		$data	= array(
			'options'	=> TodoyuStaticRecords::getCountryZoneOptions($idCountry),
			'value'		=> array()
		);

		return render($tmpl, $data);
	}

}

?>