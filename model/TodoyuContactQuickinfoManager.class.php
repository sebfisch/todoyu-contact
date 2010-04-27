<?php

class TodoyuContactQuickinfoManager {

	public static function getQuickinfoPerson(TodoyuQuickinfo $quickinfo, $element) {
		$idPerson	= intval($element);

		$data	= TodoyuPersonManager::getPersonArray($idPerson);

		$phone		= TodoyuPersonManager::getPreferredPhone($idPerson);
		$email		= TodoyuPersonManager::getPreferredEmail($idPerson);
		$birthday	= $data['birthday'] === '0000-00-00' ? Label('core.unknown') : $data['birthday'];

		$quickinfo->addInfo('name', TodoyuPersonManager::getLabel($idPerson) );
		$quickinfo->addInfo('email', $email);

		if( $phone !== false ) {
			$quickinfo->addInfo('phone', $phone['info']);
		}

		$quickinfo->addInfo('birthday', $birthday);
	}

}

?>