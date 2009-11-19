<?php

class TodoyuContactPreferenceActionController extends TodoyuActionController {

	/**
	 *	General panelWidget action, saves collapse status
	 *
	 *	@param	Array	$params
	 */
	public function pwidgetAction(array $params) {
		$idWidget	= $params['item'];
		$value		= $params['value'];

		TodoyuPanelWidgetManager::saveCollapsedStatus(EXTID_CONTACT, $idWidget, $value);
	}
}