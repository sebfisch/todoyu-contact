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

class TodoyuContactExtActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
		restrict('contact', 'general:use');

			// set active tab
		TodoyuFrontend::setActiveTab('contact');
			// Add page assets
		TodoyuPage::addExtAssets('contact', 'public');

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

		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.init.bind(Todoyu.Ext.contact)', 100);

			// Display output
		return TodoyuPage::render();
	}

}

?>