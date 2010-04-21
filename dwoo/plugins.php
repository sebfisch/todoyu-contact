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
 * Contact specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 */





/**
 * Check whether given ID belongs to the current person
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$dwoo
 * @param	Integer			$idPerson
 * @return	Boolean
 */
function Dwoo_Plugin_isPersonID_compile(Dwoo_Compiler $dwoo, $idPerson) {
	return 'personid() === intval(' . $idPerson . ')';
}



/**
 * Get the name to given person ID
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param 	Dwoo_Compiler 	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */

function Dwoo_Plugin_name_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuPersonManager::getPerson(' . $idPerson . ')->getFullName(true)';
}



/**
 * Returns a wrapped label tag of a person, evoking person-info tooltip on rollOver
 *
 * @param	Dwoo 			$dwoo
 * @param	Integer			$idPrefx	descriptive string: 'ext'_'recordtype'
 * @param	Integer			$idRecord	record containing the person ID, e.g. task, comment, etc.
 * @param	Integer			$idPerson
 * @param	String			$tag
 * @param	String			$class
 * @return	String
 */
function Dwoo_Plugin_personLabel(Dwoo $dwoo, $idPerson = 0, $idPrefix = '', $idRecord = 0, $tag = 'span', $class = '')	{
	$htmlID		= $idPrefix . '-' . $idRecord . '-' . $idPerson;

	$openingTag	= '<' . $tag . ' id="' . $htmlID . '" class="quickInfoPerson ' . $class . '">';
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