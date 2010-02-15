<?php



class TodoyuAddressTypeManager {
	
	public static function getAddressTypes() {
		return $GLOBALS['CONFIG']['EXT']['user']['addressTypes'];
	}
	
	public static function getAddressType($idAddressType) {
		$idAddressType	= intval($idAddressType);
		
		return $GLOBALS['CONFIG']['EXT']['user']['addressTypes'][$idAddressType];
	}
	
	
}