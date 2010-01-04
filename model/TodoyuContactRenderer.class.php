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
	 * @return	String
	 */
	public static function renderTabs($activeTab, $onlyActive = false)	{
		$typesConfig= TodoyuContactManager::getTypesConfig();
		$tabs 		= array();

		if( $onlyActive ) {
			$typesConfig = array(
				$activeTab => $typesConfig[$activeTab]
			);
		}

		foreach($typesConfig as $type => $typeConfig)	{
				// Check if type is allowed
			if( allowed('contact', $type . ':use') ) {
				$tabs[$type] = array(
					'id'		=> $type,
					'htmlId'	=> 'contact-tabhead-' . $type,
					'label'		=> Label($typeConfig['label']),
					'hasIcon'	=> 1,
					'class'		=> $type,
					'classKey'	=> $type
				);
			}
		}

		$htmlID		= 'contact-tabs';
		$class		= 'tabs';
		$jsHandler	= 'Todoyu.Ext.contact.onTabSelect.bind(Todoyu.Ext.contact)';

		return TodoyuTabheadRenderer::renderTabs($htmlID, $class, $jsHandler, $tabs, $activeTab);
	}



	/**
	 * Render contacts list
	 *
	 *	@param	String	$type
	 *	@return	String
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
	 *	Render person list
	 *
	 *	@param	String	$sword
	 *	@return	String
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
	 *	Render person list
	 *
	 *	@param	String	$sword
	 *	@return	String
	 */
	public static function renderPersonList($sword = '', $size = 5, $offset = 0) {
		restrict('contact', 'person:use');

		$size	= intval($size);
		$offset	= intval($offset);

		$tmpl		= 'ext/contact/view/personlist.tmpl';
		$data		= array(
			'persons'	=> TodoyuUserManager::searchUsers($sword, null, $size, $offset),
			'offset'	=> $offset,
			'total'		=> Todoyu::db()->getTotalFoundRows()
		);

		return render($tmpl, $data);
	}



	/**
	 *	Render company list
	 *
	 *	@param	String	$sword
	 *	@return	String
	 */
	public static function renderCompanyList($sword = '') {
		restrict('contact', 'company:use');
		$tmpl		= 'ext/contact/view/companylist.tmpl';
		$data		= array();

		$companies	= TodoyuCompanyManager::searchCompany($sword);

		foreach($companies as $index => $company) {
			$companies[$index]['users']		= TodoyuCompanyManager::getNumUsers($company['id']);
			$companies[$index]['address']	= TodoyuCompanyManager::getCompanyAddress($company['id']);
		}

		$data['companys'] = $companies;

		return render($tmpl, $data);
	}



	/**
	 *	Render person edit form
	 *
	 *	@param	Integer	$idPerson
	 *	@return	String
	 */
	public static function renderPersonEditForm($idPerson) {
		restrict('contact', 'person:edit');
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuUserManager::getUser($idPerson);
		$data	= $person->getTemplateData(true);
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idPerson);

		$form->setFormData($data);
		$form->setRecordID($idPerson);

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $person->getLabel(),
			'formhtml'		=> $form->render()
		);

		return render($tmpl, $data);
	}



	/**
	 *	Render company edit form
	 *
	 *	@param	Integer	$idCompany
	 *	@return	String
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
	 * @param	Object	$editRecord
	 * @param	Integer	$editID
	 * @return	String
	 */
	public static function getContactFormHeader($type, TodoyuBaseObject $record,  $idRecord = 0) {
		$idRecord	= intval($idRecord);


		if( $idRecord === 0 )	{
				// Creating new contact record
			$header	= TodoyuLocale::getLabel('contact.contact.createnew') . ' ' . TodoyuLocale::getLabel(TodoyuContactManager::getContactTypeLabel($type));
		} else {
				// Editing existing contact record
			if( method_exists($record, 'getLabel') )	{
				$header = TodoyuLocale::getLabel('contact.contact.edit') . ' ' . TodoyuContactManager::getContactTypeObj($type, $idRecord)->getLabel();
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
	 * @param	String	$type
	 * @param	Integer	$idRecord
	 * @return	String
	 */
	public function renderInfoPopupContent($type, $idRecord) {
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
	 * @param	Integer	$idRecord
	 */
	public function renderUserInfo($idUser) {
		$idUser	= intval($idUser);

		$user	= TodoyuUserManager::getUser($idUser);

		$tmpl	= 'ext/contact/view/info-user.tmpl';
		$data	= $user->getTemplateData(true);

		$companyIDs = $user->getCompanyIDs();
		foreach($companyIDs as $idCompany) {
			$company		= TodoyuCompanyManager::getCompany($idCompany);
			$companyData	= $company->getTemplateData(true);

			$data['companyData'][$idCompany] = $companyData['address'];
		}

		$data['email']	= $user->getEmail();

		return render($tmpl, $data);
	}


	public function renderCompanyInfo($idCompany)	{
		$idCompany = intval($idCompany);

		$company	= TodoyuCompanyManager::getCompany($idCompany);

		$tmpl		= 'ext/contact/view/info-company.tmpl';
		$data		= $company->getTemplateData(true);

		return render($tmpl, $data);
	}


}

?>