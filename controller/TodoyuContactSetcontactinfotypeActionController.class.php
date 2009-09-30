<?php

class TodoyuContactSetcontactinfotypeActionController extends TodoyuActionController {

	public function defaultAction(array $params) {
		if( ! TodoyuContactRightsManager::checkModuleAccess() ) {
			die('no Access');
		}

		TodoyuContactPreferences::saveActiveTab($params['contactInfoType']);
	}
		
}

?>