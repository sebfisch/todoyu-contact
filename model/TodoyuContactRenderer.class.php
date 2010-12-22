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
 * Render class for the contact module
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactRenderer {

	/**
	 * @var	String		Extension key
	 */
	const EXTKEY = 'contact';



	/**
	 * Render the tab menu
	 *
	 * @param	String		$activeTab			e.g 'person' / 'company'
	 * @param	Boolean		$onlyActive
	 * @return	String
	 */
	public static function renderTabs($activeTab, $onlyActive = false)	{
		$tabs	= TodoyuArray::assure(TodoyuTabManager::getTabs(Todoyu::$CONFIG['EXT']['contact']['tabs']));

			// Render only the currenty active tab?
		if( $onlyActive ) {
			foreach($tabs as $tab) {
				if( $tab['id'] == $activeTab ) {
					$tabs = array($tab);
					break;
				}
			}
		}

		$name		= 'contact';
		$jsHandler	= 'Todoyu.Ext.contact.onTabSelect.bind(Todoyu.Ext.contact)';

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render company quick creation form
	 *
	 * @return	String
	 */
	public static function renderCompanyQuickCreateForm() {
		$form	= TodoyuCompanyManager::getQuickCreateForm();

			// Preset (empty) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData('ext/contact/config/form/company.xml', $formData, 0);
		$form->setFormData($formData);

		return $form->render();
	}



	/**
	 * Render person quick creation form
	 *
	 * @return	String
	 */
	public static function renderPersonQuickCreateForm() {
		$form	= TodoyuPersonManager::getQuickCreateForm();

			// Preset (empty) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData('ext/contact/config/form/person.xml', $formData, 0);
		$form->setFormData($formData);

		return $form->render();
	}



	/**
	 * Render contacts list
	 *
	 * @param	String		$type
	 * @param	String		$searchWord
	 * @return	String
	 */
	public static function renderContactList($type, $searchWord = '')	{
		$content	= '';

		switch($type) {
			case 'person':
				$content = self::renderPersonList($searchWord);
				break;

			case 'company':
				$content = self::renderCompanyList($searchWord);
				break;
		}

		return $content;
	}



	/**
	 * Render edit form for given contact record of given type
	 *
	 * @param	String	$type
	 * @param	Integer	$idRecord
	 * @return	String
	 */
	public static function renderContactEdit($type, $idRecord = 0) {
		$content	= '';
		$idRecord	= intval($idRecord);

		switch($type) {
			case 'person':
				$content = self::renderPersonEditForm($idRecord);
				break;

			case 'company':
				$content = self::renderCompanyEditForm($idRecord);
				break;
		}

		return $content;
	}



	/**
	 * Render person list
	 *
	 * @param	String	$sword
	 * @param	Integer	$size
	 * @param	Integer	$offset
	 * @return	String
	 */
	public static function renderPersonList($sword = '', $offset = 0) {
		restrict('contact', 'general:area');

		return TodoyuListingRenderer::render('contact', 'person', $offset, $sword);
	}



	/**
	 * Render company list
	 *
	 * @param	String	$sword
	 * @return	String
	 */
	public static function renderCompanyList($sword = '') {
		restrict('contact', 'general:area');

		return TodoyuListingRenderer::render('contact', 'company');
	}



	/**
	 * Render person edit form
	 *
	 * @param	Integer	$idPerson
	 * @return	String
	 */
	public static function renderPersonEditForm($idPerson) {
		restrict('contact', 'person:editAndDelete');
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuPersonManager::getPerson($idPerson);
		$data	= $person->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idPerson);

		$form->setFormData($data);
		$form->setRecordID($idPerson);

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'header'	=> $person->getLabel(),
			'formhtml'	=> $form->render()
		);

		return render($tmpl, $data);
	}



	/**
	 * Render person edit form for popup (different save and cancel handling than conventional)
	 *
	 * @param	Integer	$idPerson
	 * @param	String	$idTarget		HTML Id of the input field
	 * @return	String
	 */
	public static function renderPersonEditFormWizard($idPerson, $idTarget)	{
		restrict('contact', 'person:editAndDelete');

		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuPersonManager::getPerson($idPerson);
		$data	= $person->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idPerson);

		$form->setFormData($data);
		$form->setRecordID($idPerson);

		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.cancelWizard();');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.saveWizard(this.form, \''.$idTarget.'\');');

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $person->getLabel(),
			'formhtml'		=> $form->render()
		);

		return render($tmpl, $data);
	}



	/**
	 * Render company edit form for popup (different save and cancel handling than conventional)
	 *
	 * @param	Integer	$idCompany
	 * @param	String	$idTarget		HTML Id of the input field
	 * @return	String
	 */
	public static function renderCompanyEditFormWizard($idCompany, $idTarget)	{
		restrict('contact', 'company:editAndDelete');

		$idCompany	= intval($idCompany);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$company	= TodoyuCompanyManager::getCompany($idCompany);
		$data	= $company->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idCompany);

		$form->setFormData($data);
		$form->setRecordID($idCompany);

		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.cancelWizard();');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.saveWizard(this.form, \''.$idTarget.'\');');

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $company->getLabel(),
			'formhtml'		=> $form->render()
		);

		return render($tmpl, $data);
	}



	/**
	 * Render company edit form
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function renderCompanyEditForm($idCompany) {
		restrict('contact', 'company:editAndDelete');
		$idCompany	= intval($idCompany);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$company= TodoyuCompanyManager::getCompany($idCompany);
		$data	= $company->getTemplateData(true);
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idCompany);

		$form->setFormData($data);
		$form->setRecordID($idCompany);

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $company->getLabel(),
			'formhtml'		=> $form->render()
		);

		return render($tmpl, $data);
	}



	/**
	 * Render panel widgets
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}



	/**
	 * Render content of record info popup (e.g. person visiting card or company summary)
	 *
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 * @return	String
	 */
	public static function renderInfoPopupContent($type, $idRecord) {
		switch($type) {
				// Render person visiting card
			case 'person':
				$content	= self::renderPersonInfo($idRecord);
				break;

				// Render company summary
			case 'company':
				$content	= self::renderCompanyInfo($idRecord);
				break;
		}

		return $content;
	}



	/**
	 * Render general person header
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function renderPersonHeader($idPerson, $withDetails = null) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuPersonManager::getPerson($idPerson);

		$tmpl		= 'ext/contact/view/person-header.tmpl';

		$data	= $person->getTemplateData();
		$data	= TodoyuHookManager::callHookDataModifier('person', 'renderPersonHeader', $data);

			// If not forced, check preference
		if( is_null($withDetails) ) {
			$withDetails = TodoyuContactPreferences::isPersonDetailsExpanded($idPerson);
		}

		if( $withDetails === true ) {
//			$data['details'] = self::renderPersonDetails($idPerson);
		}

		return render($tmpl, $data);
	}



	/**
	 * Render person info (shown in info popup)
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function renderPersonInfo($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuPersonManager::getPerson($idPerson);

		$tmpl	= 'ext/contact/view/info-person.tmpl';
		$data	= $person->getTemplateData(true);

		$companyIDs = $person->getCompanyIDs();
		foreach($companyIDs as $idCompany) {
			$company		= TodoyuCompanyManager::getCompany($idCompany);
			$companyData	= $company->getTemplateData(true);

			$data['companyData'][$idCompany] = $companyData['address'];
		}

		$data['email']	= $person->getEmail();

		return render($tmpl, $data);
	}



	/**
	 * Render company summary
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function renderCompanyInfo($idCompany)	{
		$idCompany = intval($idCompany);

		$tmpl		= 'ext/contact/view/info-company.tmpl';
		$company	= TodoyuCompanyManager::getCompany($idCompany);
		$data		= $company->getTemplateData(true);

		return render($tmpl, $data);
	}



	/**
	 * Render action buttons for person records
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function renderPersonActions($idPerson) {
		$tmpl	= 'ext/contact/view/person-actions.tmpl';
		$data	= array(
			'id'	=> intval($idPerson)
		);

		return render($tmpl, $data);
	}



	/**
	 * Render action buttons for company record
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function renderCompanyActions($idCompany) {
		$tmpl	= 'ext/contact/view/company-actions.tmpl';
		$data	= array(
			'id'	=> intval($idCompany)
		);

		return render($tmpl, $data);
	}

}

?>