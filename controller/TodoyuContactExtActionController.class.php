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
 *  Default action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactExtActionController extends TodoyuActionController {

	/**
	 * Default action: setup and render contact page view 
	 * 
	 * @param	Array	$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		restrict('contact', 'general:use');

			// Set active tab
		TodoyuFrontend::setActiveTab('contact');

		TodoyuPage::init('ext/contact/view/ext.tmpl');
		TodoyuPage::setTitle('LLL:contact.page.title');

			// Get type from parameter or preferences
		$type	= $params['type'];
		if( empty($type) ) {
			$type	= TodoyuContactPreferences::getActiveTab();
		} else {
			TodoyuContactPreferences::saveActiveTab($type);
		}

			// Get record id from param
		$idRecord		= intval($params['id']);

		$panelWidgets 	= TodoyuContactRenderer::renderPanelWidgets();
		$tabs 			= TodoyuContactRenderer::renderTabs($type);

		if( $idRecord !== 0 ) {
			$content	= TodoyuContactRenderer::renderContactEdit($type, $idRecord);
		} else {
			$content	= TodoyuContactRenderer::renderContactList($type);
		}


		TodoyuPage::set('panelWidgets', $panelWidgets);
		TodoyuPage::set('tabs', $tabs);
		TodoyuPage::set('content', $content);

			// Display output
		return TodoyuPage::render();
	}

}

?>