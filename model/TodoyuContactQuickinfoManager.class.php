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
 * Add information to person quickinfo
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactQuickinfoManager {

	/**
	 * Add items to person quickinfo
	 *
	 * @param	TodoyuQuickinfo		$quickinfo
	 * @param	Integer				$idPerson
	 */
	public static function getQuickinfoPerson(TodoyuQuickinfo $quickinfo, $idPerson) {
		$idPerson	= intval($idPerson);

		$data	= TodoyuPersonManager::getPersonArray($idPerson);

		$phone		= TodoyuPersonManager::getPreferredPhone($idPerson);
		$email		= TodoyuPersonManager::getPreferredEmail($idPerson);
		$fullName	= TodoyuPersonManager::getPerson($idPerson)->getFullName();

		$linkedName	= '<a href="?ext=contact&type=person&id=' . $idPerson . '">' . TodoyuPersonManager::getLabel($idPerson) . '</a>';
		$quickinfo->addInfo('name', $linkedName, 0, false);

		$quickinfo->addEmail('email', $email, $fullName);

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

}

?>