<?php

class TodoyuContactExtActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
//		TodoyuContactRightsManager::checkOnRecordsViewAccess();

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

		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.init.bind(Todoyu.Ext.contact)');

			// Display output
		return TodoyuPage::render();
	}

}

?>