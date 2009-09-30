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
 * Contact specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 */



/**
 * Checks whether current user has access to delete records of current contacttype
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @return	Boolean
 */
function Dwoo_Plugin_checkDeleteAccess(Dwoo $dwoo)	{
	return TodoyuContactRightsManager::checkOnRecordDeleteAccess();
}



/**
 * Checks whether current user has access to create records of current contacttype
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @return	Boolean
 */
function Dwoo_Plugin_checkCreateAccess(Dwoo $dwoo)	{
	return TodoyuContactRightsManager::checkOnRecordCreateAccess();
}



/**
 * Checks whether current user has access to edit records of current contacttype
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @return	Boolean
 */
function Dwoo_Plugin_checkEditAccess(Dwoo $dwoo)	{
	return TodoyuContactRightsManager::checkOnRecordEditAccess();
}



/**
 * Checks whether login user can be created
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @return	Boolean
 */
function Dwoo_Plugin_checkCreateLoginUserAccess(Dwoo $dwoo)	{
	return TodoyuContactRightsManager::checkOnCreateLoginUserAccess();
}



/**
 * Checks whether login user can be created
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @return	Boolean
 */
function Dwoo_Plugin_checkEditLoginUserAccess(Dwoo $dwoo)	{
	return TodoyuContactRightsManager::checkOnCreateLoginUserAccess();
}



/**
 * Check whether given value seems to be a phone number
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @param	String	$value
 */
function Dwoo_Plugin_isPhoneNumber(Dwoo $dwoo, $value) {
	$value = trim( str_replace(' ', '', $value) );

	return is_numeric($value) && strlen($value) > 5;
}



/**
 * Get name of contact info type
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @param	Integer	$idContactinfotype
 * @return	String
 */
function Dwoo_Plugin_labelContactinfotype(Dwoo $dwoo, $idContactinfotype) {
	$idContactinfotype = intval($idContactinfotype);

	return TodoyuContactInfoManager::getContactInfoTypeName($idContactinfotype);
}



/**
 * Get name of country
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo	$dwoo
 * @param	Integer	$idCountry
 * @return	String
 */
function Dwoo_Plugin_countryName(Dwoo $dwoo, $idCountry) {

	return TodoyuDatasource::getCountryLabel($idCountry);
}

?>