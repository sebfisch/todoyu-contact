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

		$type	= TodoyuContactPreferences::getActiveTab();

		$panelWidgets 	= TodoyuContactRenderer::renderPanelWidgets();
		$tabs 			= TodoyuContactRenderer::renderTabs();
		$list			= TodoyuContactRenderer::renderContactList($type);


		TodoyuPage::set('panelWidgets', $panelWidgets);
		TodoyuPage::set('tabs', $tabs);
		TodoyuPage::set('list', $list);

		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.init.bind(Todoyu.Ext.contact)');

			// Display output
		return TodoyuPage::render();
	}

}

?>