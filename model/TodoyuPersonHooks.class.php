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
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Hooked methods for Person
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuPersonHooks {

	/**
	 * Extend task data attributes to implement person quickInfos
	 *
	 * @param	Array	$data
	 * @param	Integer	$idTask
	 * @return	Array
	 */
	public static function extendTaskDataAttributes(array $data, $idTask) {
		$idTask		= intval($idTask);
		$taskData	= TodoyuTaskManager::getTaskData($idTask);

			// person_assigned
		$htmlID	= 'task_personassigned-' . $idTask . '-' . $taskData['id_person_assigned'];

		$data['person_assigned']['id'] 		 = $htmlID;
		$data['person_assigned']['wrap'][1]	.= '<script type="text/javascript">Todoyu.Ext.contact.QuickInfoPerson.install(\'' .  $htmlID . '\');</script>';
		$data['person_assigned']['className'] .= ' quickInfoPerson';

			// person owner
		$htmlID	= 'task_personowner-' . $idTask . '-' . $taskData['id_person_owner'];

		$data['person_owner']['id']			= $htmlID;
		$data['person_owner']['wrap'][1]	.= '<script type="text/javascript">Todoyu.Ext.contact.QuickInfoPerson.install(\'' .  $htmlID . '\');</script>';
		$data['person_owner']['className'] 	.= ' quickInfoPerson';

		return $data;
	}

}

?>