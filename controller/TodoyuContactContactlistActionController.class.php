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

class TodoyuContactContactlistActionController extends TodoyuActionController {

	public function searchcontactsAction(array $params) {
		TodoyuContactPreferences::removeShowAll();

		return $this->refreshAction($params);
	}


	public function refreshAction(array $params) {
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