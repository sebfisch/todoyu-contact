<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
 * Quick info action controller
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactQuickinfoActionController extends TodoyuActionController {

	/**
	 * Get a person quickinfo
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function personAction(array $params) {
		$idPerson	= intval($params['key']);

		$quickInfo	= new TodoyuQuickinfo();

		$data	= TodoyuPersonManager::getPersonArray($idPerson);
		$phone	= TodoyuPersonManager::getPreferredPhone($idPerson);

		$quickInfo->addInfo('name', TodoyuPersonManager::getLabel($idPerson) );
		$quickInfo->addInfo('email', 	$data['email'] );

		if( $phone !== false ) {
			$quickInfo->addInfo('phone', $phone['info']);
		}

		$quickInfo->addInfo('birthday', $data['birthday'] );

		$quickInfo->printInfoJSON();
	}

}

?>
