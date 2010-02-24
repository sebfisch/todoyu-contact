<?php



class TodoyuAddressTypeManager {

	public static function getAddressTypes() {
		return $GLOBALS['CONFIG']['EXT']['contact']['addressTypes'];
	}

	public static function getAddressType($idAddressType) {
		$idAddressType	= intval($idAddressType);

		return $GLOBALS['CONFIG']['EXT']['contact']['addressTypes'][$idAddressType];
	}


}