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
 * Class to check the rights in contact module
 *
 * @package		todoyu
 * @subpackage	contact
 */
class TodoyuContactRightsManager {



	/**
	 * checks if user has access to the module
	 *
	 * @return	Boolean
	 */
	public static function checkModuleAccess()	{
		return allowed('contact', 'module');
	}



	/**
	 * Checks if current User has access to see records of contacttype
	 */
	public static function checkOnRecordsViewAccess()	{
		if(is_array($GLOBALS['CONFIG']['EXT']['contact']['contacttypes']))	{
			foreach($GLOBALS['CONFIG']['EXT']['contact']['contacttypes'] as $recordType => $recordConfig)	{
				if(!allowed('contact', 'see'.ucfirst($recordType)))	{
					unset($GLOBALS['CONFIG']['EXT']['contact']['contacttypes'][$recordType]);
				}
			}
		}
	}



	/**
	 * Checks if current User has access to see records of current contacttype
	 *
	 * @return	Boolean
	 */
	public static function checkOnRecordViewAccess()	{
		return allowed('contact', 'see'.ucfirst(TodoyuContactPreferences::getActiveTab()));
	}



	/**
	 * Checks if current User has access to create a record of current contactype
	 *
	 * @return	Boolean
	 */
	public static function checkOnRecordCreateAccess()	{
		return allowed('contact', 'create'.ucfirst(TodoyuContactPreferences::getActiveTab()));
	}



	/**
	 * Checks if current User has access to edit a record of current contacttype
	 *
	 * @return	Boolean
	 */
	public static function checkOnRecordEditAccess()	{
		return allowed('contact', 'edit'.ucfirst(TodoyuContactPreferences::getActiveTab()));
	}



	/**
	 * Checks if current User has access to delete a record of current contacttype
	 *
	 * @return	Boolean
	 */
	public static function checkOnRecordDeleteAccess()	{
		return allowed('contact', 'delete'.ucfirst(TodoyuContactPreferences::getActiveTab()));
	}



	/**
	 * Checks if current User has access to current command of formhandling
	 *
	 * @param	String	$command
	 * @return	Boolean
	 */
	public static function checkOnFormHandlingAccess($command)	{
		switch($command)	{
				// Show form
			case 'showForm':
				if(TodoyuRequest::getParam('editID'))	{
					if( ! self::checkOnRecordCreateAccess() ) return false;
				} else {
					if( ! self::checkOnRecordEditAccess() ) return false;
				}
				break;

				// Remove entry
			case 'removeEntry':
					if( ! self::checkOnRecordDeleteAccess() ) return false;
				break;
		}

		return true;
	}



	/**
	 * Checks if current user can access login users
	 *
	 * @return	Boolean
	 */
	public static function checkOnCreateLoginUserAccess()	{
		return allowed('contact', 'createLoginUser');
	}



	/**
	 * Checks if current user can access login users
	 *
	 * @return	Boolean
	 */
	public static function checkOnEditLoginUserAccess()	{
		return allowed('contact', 'editLoginUser');
	}
}
?>