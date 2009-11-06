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
	 *	Save a preference for contact
	 *
	 *	@param	String		$preference
	 *	@param	String		$value
	 *	@param	Integer		$idItem
	 *	@param	Boolean		$unique
	 *	@param	Integer		$idUser
	 */
	public static function savePref($preference, $value, $idItem = 0, $unique = false, $idArea = 0, $idUser = 0) {
		TodoyuPreferenceManager::savePreference(EXTID_CONTACT, $preference, $value, $idItem, $unique, $idArea, $idUser);
	}



	/**
	 *	Get a contact preference
	 *
	 *	@param	String		$preference
	 *	@param	Integer		$idItem
	 *	@param	Integer		$idUser
	 *	@return	String
	 */
	public static function getPref($preference, $idItem = 0, $idArea = 0, $unserialize = false, $idUser = 0) {
		$idItem	= intval($idItem);
		$idUser	= intval($idUser);

		return TodoyuPreferenceManager::getPreference(EXTID_CONTACT, $preference, $idItem, $idArea, $unserialize, $idUser);
	}



	/**
	 *	Get contact preferences
	 *
	 *	@param	String		$preference
	 *	@param	Integer		$idItem
	 *	@param	Integer		$idArea
	 *	@param	Integer		$idUser
	 *	@return	Array
	 */
	public static function getPrefs($preference, $idItem = 0, $idArea = 0, $idUser = 0) {
		return TodoyuPreferenceManager::getPreferences(EXTID_CONTACT, $preference, $idItem, $idArea, $idUser);
	}



	/**
	 *	Delete contact preference
	 *
	 *	@param	String		$preference
	 *	@param	String		$value
	 *	@param	Integer		$idItem
	 *	@param	Integer		$idArea
	 *	@param	Integer		$idUser
	 */
	public static function deletePref($preference, $value = null, $idItem = 0, $idArea = 0, $idUser = 0) {
		TodoyuPreferenceManager::deletePreference(EXTID_CONTACT, $preference, $value, $idItem, $idArea, $idUser);
	}



	/**
	 *	Get active tab in contact area
	 *
	 *	@return	String
	 */
	public static function getActiveTab() {
		$tab	= self::getPref('tab');

		if( $tab === false ) {
			$tab = $GLOBALS['CONFIG']['EXT']['contact']['defaultTypeTab'];
			self::saveActiveTab($tab);
		}

		return $tab;
	}



	/**
	 *	Save active tab in contact area
	 *
	 *	@param	String	$tab
	 */
	public static function saveActiveTab($tab) {
		self::savePref('tab', $tab, 0, true);
	}





	/**
	 *	Saves ID of currently edited record
	 *
	 *	@param	Integer		$idRecord		record id
	 */
	public static function saveEditId($idRecord)	{
		$idRecord	= intval($idRecord);

		self::savePref('last_edit_id', $idRecord, 0, true);
	}





	/**
	 *	Saves the showAll flag for the current contact type.
	 *	Removes it from the array if it already exists.
	 *
	 */
	public static function saveShowAll()	{
		if(self::getShowAll())	{
			self::removeShowAll();
		} else {
			$showAllArray = self::getShowAllArray();
			array_push($showAllArray, self::getActiveTab());
		}

		$value	= serialize($showAllArray);

		self::savePref('showAll', $value, 0, true);
	}



	/**
	 *	Checks if the current Contact type is in the preference array.
	 *
	 *	@return	Boolean
	 */
	public static function getShowAll()	{
		$showAllArray = self::getShowAllArray();
		return  $showAllArray ? in_array(self::getActiveTab(), $showAllArray) : false;
	}



	/**
	 *	Removes current contact type from the presets
	 */
	public static function removeShowAll()	{
		$contactType = self::getActiveTab();
		$showAllArray = self::getShowAllArray();

		$showAllArray = TodoyuArray::unsetEntryByValue($contactType, $showAllArray);

		$value	= serialize($showAllArray);

		self::savePref('showAll', $value, 0, true);
	}



	/**
	 *	Returns an array of contact types which have the show all flag set.
	 *	If there is no array in the database it returns an empty array
	 *
	 *	@return	Array
	 */
	protected static function getShowAllArray()	{
		$showAllArray	= self::getPref('showAll', 0, 0, true);

		return $showAllArray ? $showAllArray : array();
	}

}
?>