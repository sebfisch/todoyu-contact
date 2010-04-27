<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Address type manager
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuAddressTypeManager {

	/**
	 * @todo	comment
	 * @return	Array
	 */
	public static function getAddressTypes() {
		return Todoyu::$CONFIG['EXT']['contact']['addressTypes'];
	}



	/**
	 * @todo	comment
	 * @param	Integer		$idAddressType
	 * @return	Array
	 */
	public static function getAddressType($idAddressType) {
		$idAddressType	= intval($idAddressType);

		return Todoyu::$CONFIG['EXT']['contact']['addressTypes'][$idAddressType];
	}

}