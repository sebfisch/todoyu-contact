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
 * 
 */
class TodoyuContactProfileRenderer {

	/**
	 * @static
	 * @param  $params
	 * @return String
	 */
	public static function renderTabs(array $params) {
		$name		= 'contact';
		$class		= 'contact';

		$jsHandler	= 'Todoyu.Ext.contact.Profile.onTabClick.bind(Todoyu.Ext.contact.Profile)';

		$tabs		= TodoyuTabManager::getAllowedTabs(Todoyu::$CONFIG['EXT']['profile']['contactTabs']);
		$active		= $params['tab'];

		if( is_null($active) ) {
			$active = $tabs[0]['id'];
		}

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $active, $class);
	}



	/**
	 * @static
	 * @param  $params
	 * @return void
	 */
	public static function renderContent(array $params) {
		$tab	= $params['tab'];

		switch($tab) {
			case 'contact':
			default:
				return self::renderContentForm();
				break;
		}
	}



	/**
	 * @static
	 * @return void
	 */
	public static function renderContentForm() {
		$idPerson	= personid();
		$xmlPath	= 'ext/contact/config/form/profile-person.xml';

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
}

?>