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
 * Render class for the contact module
 *
 * @package		Todoyu
 * @subpackage	contact
 */

class TodoyuContactRenderer {

	/**
	 * Render the tab menu
	 *
	 * @param	String		$activeTab			e.g 'person' / 'company'
	 * @param	Boolean		$onlyActive
	 * @return	String
	 */
	public static function renderTabs($activeTab, $onlyActive = false)	{
		$tabs		= TodoyuArray::assure($GLOBALS['CONFIG']['EXT']['contact']['tabs']);

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
	 * Render contacts list
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function renderContactList($type)	{
		$content	= '';

		switch($type) {
			case 'person':
				$content = self::renderPersonList();
				break;

			case 'company':
				$content = self::renderCompanyList();
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
	public static function renderPersonList($sword = '', $size = 5, $offset = 0) {
		restrict('contact', 'person:use');

		return TodoyuListingRenderer::render('contact', 'person');
	}



	/**
	 * Render company list
	 *
	 * @param	String	$sword
	 * @return	String
	 */
	public static function renderCompanyList($sword = '') {
		restrict('contact', 'company:use');

		return TodoyuListingRenderer::render('contact', 'company');

		$tmpl	= 'ext/contact/view/companylist.tmpl';
		$data	= array();

		$companies	= TodoyuCompanyManager::searchCompany($sword);

		foreach($companies as $index => $company) {
			$companies[$index]['users']		= TodoyuCompanyManager::getNumUsers($company['id']);
			$companies[$index]['address']	= TodoyuCompanyManager::getCompanyAddress($company['id']);
		}

		$data['companys'] = $companies;

		return render($tmpl, $data);
	}



	/**
	 * Render person edit form
	 *
	 * @param	Integer	$idPerson
	 * @return	String
	 */
	public static function renderPersonEditForm($idPerson) {
		restrict('contact', 'person:edit');
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuPersonManager::getPerson($idPerson);
		$data	= $person->getTemplateData(true);
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
		restrict('contact', 'person:edit');

		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuPersonManager::getPerson($idPerson);
		$data	= $person->getTemplateData(true);
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
	 * Render company edit form
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function renderCompanyEditForm($idCompany) {
		restrict('contact', 'company:edit');
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
	 * Render contact creation/ editing form header label
	 *
	 * @param	String				$type
	 * @param	TodoyuBaseObject	$record
	 * @param	Integer				$idRecord
	 * @return	String
	 */
	public static function getContactFormHeader($type, TodoyuBaseObject $record,  $idRecord = 0) {
		$idRecord	= intval($idRecord);

		if( $idRecord === 0 )	{
				// Creating new contact record
			$header	= TodoyuLanguage::getLabel('contact.contact.createnew') . ' ' . TodoyuLanguage::getLabel(TodoyuContactManager::getContactTypeLabel($type));
		} else {
				// Editing existing contact record
			if( method_exists($record, 'getLabel') )	{
				$header = TodoyuLanguage::getLabel('contact.contact.edit') . ' ' . TodoyuContactManager::getContactTypeObj($type, $idRecord)->getLabel();
			}
		}

		return $header;
	}



	/**
	 * Render panel widgets
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		$params	= array();

		return TodoyuPanelWidgetRenderer::renderPanelWidgets('contact', $params);
	}



	/**
	 * Render content of record info popup (e.g. user visiting card or company summary)
	 *
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 * @return	String
	 */
	public static function renderInfoPopupContent($type, $idRecord) {
		switch($type) {
				// Render user visiting card
			case 'user':
				$content	= self::renderUserInfo($idRecord);
				break;

				// Render company summary
			case 'company':
				$content	= self::renderCompanyInfo($idRecord);
				break;
		}

		return $content;
	}



	/**
	 * Render user info (shown in info popup)
	 *
	 * @param	Integer	$idUser
	 * @return	String
	 */
	public static function renderUserInfo($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuPersonManager::getPerson($idPerson);

		$tmpl	= 'ext/contact/view/info-user.tmpl';
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

		$company	= TodoyuCompanyManager::getCompany($idCompany);

		$tmpl		= 'ext/contact/view/info-company.tmpl';
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