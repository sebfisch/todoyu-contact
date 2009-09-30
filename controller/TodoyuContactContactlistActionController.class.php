<?php

class TodoyuContactContactlistActionController extends TodoyuActionController {

	public function searchcontactsAction(array $params) {
		TodoyuContactPreferences::removeShowAll();

		return $this->refreshAction($params);
	}


	public function refreshAction(array $params) {
//		$contacts	= TodoyuContactManager::getRecordList();
		$type		= TodoyuContactPreferences::getActiveTab();

		return TodoyuContactRenderer::renderContactList($type);
	}

	public function showAllAction(array $params) {
		TodoyuContactPreferences::saveShowAll();
		$type	= TodoyuContactPreferences::getActiveTab();


		return TodoyuContactRenderer::renderContactList($type);
	}

	public function infoPopupContentAction(array $params) {
		$type		= $params['type'];
		$idRecord	= intval($params['idRecord']);

		return TodoyuContactRenderer::renderInfoPopupContent($type, $idRecord);
	}

}

?>