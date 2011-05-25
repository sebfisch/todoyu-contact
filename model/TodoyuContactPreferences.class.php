<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
 * This class handles the preferences of the contact module
 *
 * @package		Todoyu
 * @subpackage	contact
 */
class TodoyuContactPreferences {

	/**
	 * Save a preference for contact
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Boolean		$unique
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function savePref($preference, $value, $idItem = 0, $unique = false, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::savePreference(EXTID_CONTACT, $preference, $value, $idItem, $unique, $idArea, $idPerson);
	}



	/**
	 * Get a contact preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Boolean		$unserialize
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPref($preference, $idItem = 0, $idArea = 0, $unserialize = false, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreference(EXTID_CONTACT, $preference, $idItem, $idArea, $unserialize, $idPerson);
	}



	/**
	 * Get contact preferences
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPrefs($preference, $idItem = 0, $idArea = 0, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreferences(EXTID_CONTACT, $preference, $idItem, $idArea, $idPerson);
	}



	/**
	 * Delete contact preference
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function deletePref($preference, $value = null, $idItem = 0, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::deletePreference(EXTID_CONTACT, $preference, $value, $idItem, $idArea, $idPerson);
	}



	/**
	 * Get active tab in contact area
	 *
	 * @return	String
	 */
	public static function getActiveTab() {
		$tab	= self::getPref('tab');

		if( $tab === false ) {
			$tabs	= TodoyuTabManager::getAllowedTabs(Todoyu::$CONFIG['EXT']['contact']['tabs']);
			$tab	= $tabs[0]['id'];
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
	 * Save last used search word
	 *
	 * @param	String		$searchWord
	 */
	public static function saveSearchWord($searchWord) {
		self::savePref('searchword', trim($searchWord), 0, true);
	}



	/**
	 * Get last used searchword
	 *
	 * @return	String
	 */
	public static function getSearchWord() {
		return self::getPref('searchword');
	}



	/**
	 * Get person language
	 *
	 * @return	String
	 */
	public static function getLocale() {
		return TodoyuPreferenceManager::getPreference(0, 'locale');
	}



	/**
	 * Save persons language
	 *
	 * @param	String		$locale
	 */
	public static function saveLocale($locale) {
		$locale	= trim($locale);

		TodoyuPreferenceManager::savePreference(0, 'locale', $locale, 0, true);
	}



	/**
	 * Check whether given person header details are expanded
	 *
	 * @param	Integer		$idPerson
	 * @return	Boolean
	 */
	public static function isPersonDetailsExpanded($idPerson) {
		$idPerson	= intval($idPerson);

		return self::getPref('detailsexpanded', $idPerson) == 1;
	}



	/**
	 * Save staff selector preferences
	 *
	 * @param	Array	$prefs
	 */
	public static function saveStaffSelectorPrefs(array $prefs) {
		$prefs	= json_encode($prefs);

		self::savePref('panelwidget-staffselector', $prefs, 0, true, AREA);
	}

}
?>