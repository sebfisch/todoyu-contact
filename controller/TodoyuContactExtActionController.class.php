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
 *  Default action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactExtActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 */
	public function init() {
		restrict('contact', 'general:area');
	}



	/**
	 * Default action: setup and render contact page view 
	 * 
	 * @param	Array	$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
			// Get record id from param
		$idRecord		= intval($params['id']);
		$searchWord		= trim($params['sword']);

		$type	= isset($params['tab']) ? $params['tab'] : $params['type'];

			// Get type from parameter or preferences
		if( empty($type) ) {
			$type	= TodoyuContactPreferences::getActiveTab();
		} else {
			TodoyuContactPreferences::saveActiveTab($type);
		}

		if( $idRecord !== 0 ) {
			TodoyuContactRights::restrictRecordEdit($type, $idRecord);
			$content	= TodoyuContactRenderer::renderContactEdit($type, $idRecord);
		} else {
			$content	= TodoyuContactRenderer::renderContactList($type, $searchWord);
		}

		return TodoyuContactRenderer::renderContactPage($type, $idRecord, $searchWord, $content);
	}

}

?>