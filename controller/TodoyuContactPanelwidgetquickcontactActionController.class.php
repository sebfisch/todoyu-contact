<?php

class TodoyuContactPanelwidgetQuickcontactActionController extends TodoyuActionController {

	public function refreshAction(array $params) {
		$config		= array();
		$panelWidget= TodoyuPanelWidgetManager::getPanelWidget('QuickContact', AREA, $config);

		return $panelWidget->render();
	}

}

?>