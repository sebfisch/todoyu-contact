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
	 * Renders the contact page. The content is given from controller
	 *
	 * @param	String	$type
	 * @param	Integer	$idRecord
	 * @param	String	$searchWord
	 * @param	String	$content
	 * @return	String
	 */
	public static function renderContactPage($type, $idRecord, $searchWord, $content = '') {
			// Set active tab
		TodoyuFrontend::setActiveTab('contact');

		TodoyuPage::init('ext/contact/view/ext.tmpl');
		TodoyuPage::setTitle('LLL:contact.ext.page.title');

		$panelWidgets 	= self::renderPanelWidgets();
		$tabs 			= self::renderTabs($type);

		TodoyuPage::set('panelWidgets', $panelWidgets);
		TodoyuPage::set('tabs', $tabs);
		TodoyuPage::set('content', $content);

			// Display output
		return TodoyuPage::render();
	}



	/**
	 * Render the tab menu
	 *
	 * @param	String		$activeTab			e.g 'person' / 'company'
	 * @param	Boolean		$onlyActive
	 * @return	String
	 */
	public static function renderTabs($activeTab, $onlyActive = false) {
		$tabs	= TodoyuTabManager::getAllowedTabs(Todoyu::$CONFIG['EXT']['contact']['tabs']);

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
		$form	= TodoyuContactCompanyManager::getQuickCreateForm();

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
		$form	= TodoyuContactPersonManager::getQuickCreateForm();

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
	public static function renderContactList($type, $searchWord = '') {
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
	 * @param	String		$sword
	 * @param	Integer		$offset
	 * @return	String
	 */
	public static function renderPersonList($sword = '', $offset = 0) {
		Todoyu::restrict('contact', 'general:area');

		return TodoyuListingRenderer::render('contact', 'person', $offset, $sword);
	}



	/**
	 * Render company list
	 *
	 * @param	String	$sword
	 * @return	String
	 */
	public static function renderCompanyList($sword = '') {
		Todoyu::restrict('contact', 'general:area');

		return TodoyuListingRenderer::render('contact', 'company');
	}



	/**
	 * Render person edit form
	 *
	 * @param	Integer	$idPerson
	 * @return	String
	 */
	public static function renderPersonEditForm($idPerson) {
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuContactPersonManager::getPerson($idPerson);
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

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render person edit form for popup (different save and cancel handling than conventional)
	 *
	 * @param	Integer	$idPerson
	 * @param	String	$fieldName		HTML Id of the input field
	 * @return	String
	 */
	public static function renderPersonCreateWizard($idPerson, $fieldName) {
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuContactPersonManager::getPerson($idPerson);
		$data	= $person->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idPerson);

		$form->setFormData($data);
		$form->setRecordID($idPerson);

		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.cancelWizard(this.form);');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.saveWizard(this.form, \''.$fieldName.'\');');

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $person->getLabel(),
			'formhtml'		=> $form->render()
		);

		$content	= Todoyu::render($tmpl, $data);
		$content	.= TodoyuString::wrapScript('Todoyu.Ext.contact.Person.onEdit(' . $idPerson. ')');

		return $content;
	}



	/**
	 * Render company edit form for popup (different save and cancel handling than conventional)
	 *
	 * @param	Integer	$idCompany
	 * @param	String	$idTarget		HTML Id of the input field
	 * @return	String
	 */
	public static function renderCompanyCreateWizard($idCompany, $idTarget) {
		$idCompany	= intval($idCompany);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$company	= TodoyuContactCompanyManager::getCompany($idCompany);
		$data	= $company->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idCompany);

		$form->setFormData($data);
		$form->setRecordID($idCompany);

		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.cancelWizard(this.form);');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.saveWizard(this.form, \''.$idTarget.'\');');

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $company->getLabel(),
			'formhtml'		=> $form->render()
		);

		$content	= Todoyu::render($tmpl, $data);
		$content	.= TodoyuString::wrapScript('Todoyu.Ext.contact.Company.onEdit(' . $idCompany. ')');

		return $content;
	}



	/**
	 * Render company edit form
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function renderCompanyEditForm($idCompany) {
		$idCompany	= intval($idCompany);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$company= TodoyuContactCompanyManager::getCompany($idCompany);
		$data	= $company->getTemplateData(true);
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idCompany);

		$form->setFormData($data);
		$form->setRecordID($idCompany);

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $company->getLabel(),
			'formhtml'		=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
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
	public static function renderDetailsContent($type, $idRecord) {
		switch($type) {
			case 'person':
				$content	= self::renderPersonDetails($idRecord);
				break;

			case 'company':
				$content	= self::renderCompanyDetails($idRecord);
				break;
		}

		return $content;
	}



	/**
	 * Render general person header
	 *
	 * @param	Integer		$idPerson
	 * @param	Boolean		$withDetails
	 * @return	String
	 */
	public static function renderPersonHeader($idPerson, $withDetails = null) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		$tmpl		= 'ext/contact/view/person-header.tmpl';

		$data	= $person->getTemplateData();
		$data	= TodoyuHookManager::callHookDataModifier('contact', 'person.renderHeader', $data);

			// If not forced, check preference
		if( is_null($withDetails) ) {
			$withDetails = TodoyuContactPreferences::isPersonDetailsExpanded($idPerson);
		}

		if( $withDetails === true ) {
//			$data['details'] = self::renderPersonDetails($idPerson);
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render person details
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function renderPersonDetails($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		$tmpl	= 'ext/contact/view/person-detail.tmpl';
		$data	= $person->getTemplateData(true);

		$companyIDs = $person->getCompanyIDs();
		foreach($companyIDs as $idCompany) {
			$company		= TodoyuContactCompanyManager::getCompany($idCompany);
			$companyData	= $company->getTemplateData(true);

			$data['companyData'][$idCompany] = $companyData['address'];
		}

		$data['email']			= $person->getEmail();
		$data['hookedContent']	= implode('', TodoyuHookManager::callHook('contact', 'person.renderDetail', array($idPerson)));

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render company summary
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function renderCompanyDetails($idCompany) {
		$idCompany = intval($idCompany);

		$tmpl		= 'ext/contact/view/company-detail.tmpl';
		$company	= TodoyuContactCompanyManager::getCompany($idCompany);
		$data		= $company->getTemplateData(true);

		$data['hookedContent']	= implode('', TodoyuHookManager::callHook('contact', 'company.renderDetail', array($idCompany)));

		return Todoyu::render($tmpl, $data);
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

		return Todoyu::render($tmpl, $data);
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

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * @param	Integer		$idRecord
	 * @param	String		$recordType
	 * @return	String
	 */
	public static function renderContactImageUploadForm($idRecord, $recordType) {
		$idRecord	= intval($idRecord);

				// Construct form object
		$xmlPath	= 'ext/contact/config/form/uploadcontactimage.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);

			// Set form data
		$formData	= array(
			'MAX_FILE_SIZE'	=> intval(Todoyu::$CONFIG['EXT']['contact']['contactimage']['max_file_size'])
		);

		$formData				= TodoyuFormHook::callLoadData($xmlPath, $formData);
		$formData['idContact']	= $idRecord;
		$formData['recordType']	= $recordType;

		$form->setFormData($formData);
		$form->setUseRecordID(false);


			// Render form
		$data	= array(
			'formhtml'	=> $form->render()
		);

			// Render form wrapped via dwoo template
		return Todoyu::render('ext/contact/view/contactimageuploadform.tmpl', $data);
	}



	/**
	 * Render upload iframe form after uploading finished
	 *
	 * @param	String		$recordType
	 * @param	Integer		$idContact
	 * @param	Integer		$idReplace
	 * @return	String
	 */
	public static function renderUploadFormFinished($recordType, $idContact, $idReplace) {
		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.contact.Upload.uploadFinished(\'' . $recordType . '\', ' . $idContact . ', \'' .$idReplace . '\');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render content of upload frame after failed upload
	 *
	 * @param	Integer		$error
	 * @param	String		$filename
	 * @return	String
	 */
	public static function renderUploadframeContentFailed($error, $filename) {
		$error		= intval($error);
		$maxFileSize= intval(Todoyu::$CONFIG['EXT']['contact']['contactimage']['max_file_size']);

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.contact.Upload.uploadFailed(' . $error . ', \'' . $filename . '\', \'' . $maxFileSize . '\');')
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>