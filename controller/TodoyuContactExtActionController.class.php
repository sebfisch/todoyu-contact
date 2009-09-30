<?php

class TodoyuContactExtActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
		TodoyuContactRightsManager::checkOnRecordsViewAccess();

		TodoyuFrontend::setActiveTab('contact');

		TodoyuPage::init('ext/contact/view/view.tmpl');
		TodoyuPage::setTitle('LLL:contact.page.title');

		$type	= TodoyuContactPreferences::getActiveTab();

		$panelWidgets 	= TodoyuContactRenderer::renderPanelWidgets();
		$tabs 			= TodoyuContactRenderer::renderTabs();
		$contactList	= TodoyuContactRenderer::renderContactList($type);


		TodoyuPage::set('panelWidgets', $panelWidgets);
		TodoyuPage::set('tabs', $tabs);
		TodoyuPage::set('mainContent', $contactList);

		TodoyuPage::addJsOnloadedFunction('Todoyu.Ext.contact.init.bind(Todoyu.Ext.contact)');

			// Display output
		return TodoyuPage::render();
	}


	public function switchTypeAction(array $params) {
		$type	= $params['type'];

		TodoyuContactPreferences::saveActiveTab($type);
	}

}

?>