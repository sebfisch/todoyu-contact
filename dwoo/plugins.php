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
 * Check if given ID belongs to the current user
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$dwoo
 * @param	Integer			$idUser
 * @return	Boolean
 */
function Dwoo_Plugin_isPersonID_compile(Dwoo_Compiler $dwoo, $idPerson) {
	return 'personid() === intval(' . $idUser . ')';
}



/**
 * Get the username to given user ID
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param 	Dwoo_Compiler 		$compiler
 * @param	Integer			$idPerson
 * @return	String
 */

function Dwoo_Plugin_name_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuPersonManager::getPerson(' . $idPerson . ')->getFullName(true)';
}







/**
 * Returns a wrapped label tag of a user, evoking user-info tooltip on rollOver
 *
 * @param	Dwoo 			$dwoo
 * @param	Integer			$idPrefx	descriptive string: 'ext'_'recordtype'
 * @param	Integer			$idRecord	record containing the user ID, e.g. task, comment, etc.
 * @param	Integer			$idPerson
 * @param	String			$tag
 * @param	String			$class
 * @return	String
 */
function Dwoo_Plugin_personLabel(Dwoo $dwoo, $idPrefix = '', $idRecord = 0, $idPerson = 0, $tag = 'span', $class = '')	{
	$htmlID		= $idPrefix . '-' . $idRecord . '-' . $idPerson;

	$openingTag	= '<' . $tag . ' id="' . $htmlID . '" class="quickInfoUser ' . $class . '">';
	$closingTag	= '</' . $tag . '>';

	$label	= TodoyuPersonManager::getLabel($idPerson);

	$script	= '<script type="text/javascript">Todoyu.Ext.contact.QuickInfoPerson.install(\'' . $htmlID . '\');</script>';

	return $openingTag . $label . $closingTag . $script;
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




/**
 * Returns the label of the addresstype with given id
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idAddressType
 * @return	String
 */
function Dwoo_Plugin_addressType_compile(Dwoo_Compiler $compiler, $idAddressType)	{
	return 'TodoyuAddressManager::getAddresstypeLabel(' . $idAddressType . ')';
}

?>