<?php

class TodoyuContactRefreshActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
		if( ! TodoyuContactRightsManager::checkModuleAccess() ) {
			die('no Access');
		}


		$type	= TodoyuContactPreferences::getActiveTab();

		$content	=  TodoyuContactRenderer::renderTabs();
		$content	.= TodoyuContactRenderer::renderContactList($type);

		echo $content;
	}

}

?>