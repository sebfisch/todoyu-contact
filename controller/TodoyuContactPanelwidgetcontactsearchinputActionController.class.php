<?php

class TodoyuContactPanelwidgetContactsearchinputActionController extends TodoyuActionController {

	public function refreshAction(array $params) {
		$config		= array();
		$panelWidget= TodoyuPanelWidgetManager::getPanelWidget('ContactSearchInput', AREA, $config);

		return $panelWidget->render();
	}
		
}

?>