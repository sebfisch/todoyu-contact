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

			// user_assigned
		$htmlID	= 'task_userassigned-' . $idTask . '-' . $taskData['id_person_assigned'];

		$data['user_assigned']['id'] 		 = $htmlID;
		$data['user_assigned']['wrap'][1]	.= '<script type="text/javascript">Todoyu.Ext.user.Quickinfo.User.installOnElement($(\'' .  $htmlID . '\'));</script>';
		$data['user_assigned']['className'] .= ' quickInfoUser';

			// user_owner
		$htmlID	= 'task_userowner-' . $idTask . '-' . $taskData['id_person_owner'];

		$data['user_owner']['id']			= $htmlID;
		$data['user_owner']['wrap'][1]		.= '<script type="text/javascript">Todoyu.Ext.user.Quickinfo.User.installOnElement($(\'' .  $htmlID . '\'));</script>';
		$data['user_owner']['className'] 	.= ' quickInfoUser';

		return $data;
	}

}

?>