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
 * Manage person quickinfo
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonQuickInfoManager {

	/**
	 * Add items to person quickinfo
	 *
	 * @param	TodoyuQuickinfo		$quickinfo
	 * @param	Integer				$idPerson
	 */
	public static function addPersonInfos(TodoyuQuickinfo $quickinfo, $idPerson) {
		$idPerson	= intval($idPerson);

		$data	= TodoyuContactPersonManager::getPersonArray($idPerson);

			// Get preferred or only phone
		$phone		= TodoyuContactPersonManager::getPreferredPhone($idPerson);
		if( $phone === false ) {
			$phones	= TodoyuContactPersonManager::getPhones($idPerson, false);
			if( count($phones) == 1) {
				$phone	= $phones[0];
			}
		}

		$email		= TodoyuContactPersonManager::getPreferredEmail($idPerson);
		$fullName	= TodoyuContactPersonManager::getPerson($idPerson)->getFullName();

		$linkedName	= '<a href="?ext=contact&controller=person&action=detail&person=' . $idPerson . '">' . TodoyuContactPersonManager::getLabel($idPerson) . '</a>';
		$quickinfo->addInfo('name', $linkedName, 0, false);

		if( ! empty($email) ) {
			$quickinfo->addEmail('email', $email, $fullName);
		}

		if( $phone !== false ) {
			$quickinfo->addInfo('phone', $phone['info']);
		}

			// Add birthday information for internal persons
		if( Todoyu::person()->isAdmin() || Todoyu::person()->isInternal() ) {
			if( $data['birthday'] !== '0000-00-00' ) {
				$quickinfo->addInfo('birthday', $data['birthday']);
			}
		}
	}



	/**
	 * Add JS onload function to page (hooked into TodoyuPage::render())
	 */
	public static function addJSonloadFunction() {
		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.QuickinfoPerson.init.bind(Todoyu.Ext.contact.QuickinfoPerson)');
	}

}

?>