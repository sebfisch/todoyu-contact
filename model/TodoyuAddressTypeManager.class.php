<?php



class TodoyuAddressTypeManager {

	public static function getAddressTypes() {
		return Todoyu::$CONFIG['EXT']['contact']['addressTypes'];
	}

	public static function getAddressType($idAddressType) {
		$idAddressType	= intval($idAddressType);

		return Todoyu::$CONFIG['EXT']['contact']['addressTypes'][$idAddressType];
	}


}