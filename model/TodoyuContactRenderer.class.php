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

if( ! defined('TODOYU') ) die('NO ACCESS');

/**
 * Render class Todoyufor the contact module
 *
 * @package		todoyu
 * @subpackage	contact
 */

class TodoyuContactRenderer extends TodoyuRenderer {


	public static function renderContactView(array $params) {

	}


	/**
	 * Render the tab menu
	 *
	 */
	public static function renderTabs()	{
		$typesConfig= TodoyuContactManager::getTypesConfig();
		$tabs 		= array();

		foreach($typesConfig as $type => $typeConfig)	{
			$tabs[] = array(
				'id'		=> $type,
				'htmlId'	=> 'contact-tabhead-' . $type,
				'label'		=> Label($typeConfig['label']),
				'hasIcon'	=> 1,
				'class'		=> $type,
				'classKey'	=> $type
			);
		}

		$activeTab	= TodoyuContactPreferences::getActiveTab();
		$htmlID		= 'contact-tabs';
		$class		= 'tabs';
		$jsHandler	= 'Todoyu.Ext.contact.onTabSelect.bind(Todoyu.Ext.contact)';

		return TodoyuTabheadRenderer::renderTabs($htmlID, $class, $jsHandler, $tabs, $activeTab);
	}


	/**
	 * Render contacts list
	 *
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


	public static function renderPersonList($sword = '') {
		$tmpl		= 'ext/contact/view/personlist.tmpl';
		$data		= array(
			'persons'	=> TodoyuUserManager::searchUsers($sword)
		);

		return render($tmpl, $data);
	}


	public static function renderCompanyList($sword = '') {
		$tmpl		= 'ext/contact/view/companylist.tmpl';
		$data		= array();

		$customers	= TodoyuCustomerManager::searchCustomers($sword);

		foreach($customers as $index => $customer) {
			$customers[$index]['users']		= TodoyuCustomerManager::getNumUsers($customer['id']);
			$customers[$index]['address']	= TodoyuCustomerManager::getCustomerAddress($customer['id']);
		}

		$data['companys'] = $customers;

		return render($tmpl, $data);
	}


	public static function renderPersonEditForm($idPerson) {
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= new TodoyuForm($xmlPath);
		$form	= TodoyuFormHook::callBuildForm($xml, $form, $idPerson);

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
	 * Render contact creation / edit form
	 *
	 * @param	Integer	$editID
	 * @return	String
	 */
	public static function renderForm($type, $idRecord)	{
		$idRecord	= intval($idRecord);
		$xmlPath	= TodoyuContactManager::getContactTypeFromXml($type);

			// Construct form object
		$form	= new TodoyuForm($xmlPath);
		$form	= TodoyuFormHook::callBuildForm($xml, $form, $idRecord);

			// Get data
		$record	= TodoyuContactManager::getContactTypeObj($type, $idRecord);

		$formData	= $record->getTemplateData(true);

			// Call data loading hooks (modifying $formData array)
		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData, $idRecord);

//		TodoyuDebug::printInFirebug($formData['user'], 'formData[user]');

//		TodoyuDebug::printHtml($formData);

			// Render
//		TodoyuDebug::printInFirebug('setformData1');
		$form->setFormData($formData);
		$form->setRecordID($idRecord);

//		TodoyuDebug::printInFirebug('setformData2');

		$data = array(
			'formheader'	=> self::getContactFormHeader($type, $record, $idRecord),
			'formhtml'		=> $form->render()
		);

		return render('ext/contact/view/form.tmpl', $data);
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
//				$content	= self::renderCompanyInfo($idRecord);
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
		$data	= $user->getTemplateData(true);

		$customerIDs = $user->getCustomerIDs();
		foreach($customerIDs as $idCustomer) {
			$customer		= TodoyuCustomerManager::getCustomer($idCustomer);
			$customerData	= $customer->getTemplateData(true);

			$data['customerData'][$idCustomer] = $customerData['address'];
		}

		$data['email']	= $user->getEmail();

		return render('ext/contact/view/info-user.tmpl', $data);
	}


}

?>