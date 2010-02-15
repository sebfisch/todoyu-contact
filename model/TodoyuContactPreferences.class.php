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

/**
 * This class Todoyuhandles the preferences of the contact module
 *
 * @package Todoyu
 * @subpackage contact
 */
class TodoyuContactPreferences {

	static $contactType = '';



	/**
	 * Save a preference for contact
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Boolean		$unique
	 * @param	Integer		$idUser
	 */
	public static function savePref($preference, $value, $idItem = 0, $unique = false, $idArea = 0, $idUser = 0) {
		TodoyuPreferenceManager::savePreference(EXTID_CONTACT, $preference, $value, $idItem, $unique, $idArea, $idUser);
	}



	/**
	 * Get a contact preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idUser
	 * @return	String
	 */
	public static function getPref($preference, $idItem = 0, $idArea = 0, $unserialize = false, $idUser = 0) {
		$idItem	= intval($idItem);
		$idUser	= intval($idUser);

		return TodoyuPreferenceManager::getPreference(EXTID_CONTACT, $preference, $idItem, $idArea, $unserialize, $idUser);
	}



	/**
	 * Get contact preferences
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idUser
	 * @return	Array
	 */
	public static function getPrefs($preference, $idItem = 0, $idArea = 0, $idUser = 0) {
		return TodoyuPreferenceManager::getPreferences(EXTID_CONTACT, $preference, $idItem, $idArea, $idUser);
	}



	/**
	 * Delete contact preference
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idUser
	 */
	public static function deletePref($preference, $value = null, $idItem = 0, $idArea = 0, $idUser = 0) {
		TodoyuPreferenceManager::deletePreference(EXTID_CONTACT, $preference, $value, $idItem, $idArea, $idUser);
	}



	/**
	 * Get active tab in contact area
	 *
	 * @return	String
	 */
	public static function getActiveTab() {
		$tab	= self::getPref('tab');

		if( $tab === false ) {
			$tab = trim($GLOBALS['CONFIG']['EXT']['contact']['defaultTypeTab']);
		}

		return $tab;
	}



	/**
	 * Save active tab in contact area
	 *
	 * @param	String	$tab
	 */
	public static function saveActiveTab($tab) {
		self::savePref('tab', $tab, 0, true);
	}



	/**
	 * Get person language
	 *
	 * @return	String
	 */
	public static function getLanguage() {
		return self::getPref('language');
	}



	/**
	 * Save user language
	 *
	 * @param	String		$language
	 */
	public static function saveLanguage($language) {
		$language	= trim(strtolower($language));

		self::savePref('language', $language, 0, true);
	}

}
?>