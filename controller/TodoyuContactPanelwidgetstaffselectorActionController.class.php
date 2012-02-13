<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
 * [Enter Class Description]
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPanelwidgetstaffselectorActionController extends TodoyuActionController {

	/**
	 * @param	Array	$params
	 */
	public function init(array $params) {
		Todoyu::restrict('contact', 'panelwidgets:staffSelector');
	}



	/**
	 * Get list of matching persons and groups
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function listAction(array $params) {
		$search	= trim($params['search']);

		$selectorWidget	= TodoyuPanelWidgetManager::getPanelWidget('contact', 'StaffSelector');
		/**
		 * @var	TodoyuContactPanelWidgetStaffSelector	$selectorWidget
		 */
		$selectorWidget->saveSearchText($search);

		return $selectorWidget->renderList();
	}



	/**
	 * Save selected persons
	 *
	 * @param	Array	$params
	 */
	public function saveAction(array $params) {
		$items	= TodoyuString::trimExplode(',', $params['selection'], true);

		$selectorWidget	= TodoyuPanelWidgetManager::getPanelWidget('contact', 'StaffSelector');
		/**
		 * @var	TodoyuContactPanelWidgetStaffSelector	$selectorWidget
		 */
		$selectorWidget->saveSelection($items);

			// Send back current selection
		TodoyuHeader::sendTypeJSON();

		echo json_encode(array(
			'items'		=> $items,
			'persons'	=> $selectorWidget->getSelectedPersons()
		));
	}

}

?>