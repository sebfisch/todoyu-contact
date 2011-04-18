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
 * @param	Dwoo_Compiler 	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */

function Dwoo_Plugin_name_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactPersonManager::getPerson(' . $idPerson . ')->getFullName(true)';
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
function Dwoo_Plugin_personLabel(Dwoo $dwoo, $idPerson = 0, $idPrefix = '', $idRecord = 0, $tag = 'span', $class = '') {
	$htmlID		= $idPrefix . '-' . $idRecord . '-' . $idPerson;

	$openingTag	= '<' . $tag . ' id="' . $htmlID . '" class="quickInfoPerson ' . $class . '">';
	$closingTag	= '</' . $tag . '>';

	$label	= TodoyuContactPersonManager::getLabel($idPerson);
	$script	= TodoyuString::wrapScript('Todoyu.Ext.contact.QuickInfoPerson.add(\'' . $htmlID . '\');');

	return $openingTag . $label . $closingTag . $script;
}



/**
 * Get name of contact info type
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idContactinfotype
 * @return	String
 */
function Dwoo_Plugin_labelContactinfotype(Dwoo $dwoo, $idContactinfotype) {
	$idContactinfotype = intval($idContactinfotype);

	return TodoyuContactContactInfoManager::getContactInfoTypeName($idContactinfotype);
}



/**
 * Get name of country
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @todo	check	- relocate into core?
 * @param	Dwoo		$dwoo
 * @param	Integer		$idCountry
 * @return	String
 */
function Dwoo_Plugin_countryName(Dwoo $dwoo, $idCountry) {
	$idCountry = intval($idCountry);

	if( $idCountry > 0 ) {
		$country	= TodoyuStaticRecords::getCountry($idCountry);

		return TodoyuStaticRecords::getLabel('country', $country['iso_alpha3']);
	} else {
		return '';
	}
}



/**
 * Returns the label of the address type with given id
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idAddressType
 * @return	String
 */
function Dwoo_Plugin_addressType_compile(Dwoo_Compiler $compiler, $idAddressType) {
	return 'TodoyuContactAddressManager::getAddresstypeLabel(' . $idAddressType . ')';
}



/**
 * Returns the salutation Label of a person
 *
 * @param	Dwoo $dwoo
 * @param	Integer	$idPerson
 * @return	String
 */
function Dwoo_Plugin_salutationLabel(Dwoo $dwoo, $idPerson) {
	$idPerson	= intval($idPerson);

	if( $idPerson > 0 ) {
		return TodoyuContactPersonManager::getPerson($idPerson)->getSalutationLabel();
	}

	return '';
}



/**
 * Renders the image of given person
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_personImage_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactImageManager::getImage(' . $idPerson . ', \'person\')';
}



/**
 * Renders image of given company
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_companyImage_compile(Dwoo_Compiler $compiler, $idCompany) {
	return 'TodoyuContactImageManager::getImage(' . $idCompany . ', \'company\')';
}



/**
 * Checks if current person is allowed to edit given company
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idCompany
 * @return	Boolean
 */
function Dwoo_Plugin_isCompanyEditAllowed(Dwoo $dwoo, $idCompany) {
	$idCompany	= intval($idCompany);
	return TodoyuContactCompanyRights::isEditAllowed($idCompany);
}



/**
 * Checks if current person is allowed to see given company
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idCompany
 * @return	Boolean
 */
function Dwoo_Plugin_isCompanySeeAllowed(Dwoo $dwoo, $idCompany) {
	$idCompany	= intval($idCompany);
	return TodoyuContactCompanyRights::isSeeAllowed($idCompany );
}



/**
 * Checks if current person is allowed to delete given company
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idCompany
 * @return	Boolean
 */
function Dwoo_Plugin_isCompanyDeleteAllowed(Dwoo $dwoo, $idCompany) {
	$idCompany	= intval($idCompany);
	return TodoyuContactCompanyRights::isDeleteAllowed($idCompany);
}



/**
 * Checks if current person is allowed to edit given persons
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idCompany
 * @return	Boolean
 */
function Dwoo_Plugin_isPersonEditAllowed(Dwoo $dwoo, $idPerson) {
	$idPerson	= intval($idPerson);
	return TodoyuContactPersonRights::isEditAllowed($idPerson);
}



/**
 * Checks if current person is allowed to see given person
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idCompany
 * @return	Boolean
 */
function Dwoo_Plugin_isPersonSeeAllowed(Dwoo $dwoo, $idPerson) {
	$idPerson	= intval($idPerson);
	return TodoyuContactPersonRights::isSeeAllowed($idPerson);
}



/**
 * Checks if current person is allowed to delete given person
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idCompany
 * @return	Boolean
 */
function Dwoo_Plugin_isPersonDeleteAllowed(Dwoo $dwoo, $idPerson) {
	$idPerson	= intval($idPerson);
	return TodoyuContactPersonRights::isDeleteAllowed($idPerson);
}



/**
 * Checks if current Person is internal
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idPerson
 * @return	Boolean
 */
function Dwoo_Plugin_isInternal(Dwoo $dwoo) {
	return TodoyuContactPersonManager::getPerson(personid())->isInternal();
}



/**
 * Checks if current person has access to the addresstype of current record (company / person)
 *
 * @param	Dwoo		$dwoo
 * @param	String		$type
 * @param	Integer		$idRecord
 * @param	Integer		$idAddressType
 * @return	Boolean
 */
function Dwoo_Plugin_isAddressTypeSeeAllowed(Dwoo $dwoo, $type, $idRecord, $idAddressType) {
	$idRecord		= intval($idRecord);
	$idAddressType	= intval($idAddressType);

	if( $type === 'person' ) {
		return TodoyuContactRights::isAddresstypeOfPersonSeeAllowed($idRecord, $idAddressType);
	} else if( $type === 'company' ) {
		return TodoyuContactRights::isAddresstypeOfCompanySeeAllowed($idRecord, $idAddressType);
	}

	return false;
}



/**
 * Checks if current person has access to the contactinfotype of current record (company / person)
 *
 * @param	Dwoo		$dwoo
 * @param	String		$type
 * @param	Integer		$idRecord
 * @param	Integer		$idAddressType
 * @return	Boolean
 */
function Dwoo_Plugin_isContactinfotypeSeeAllowed(Dwoo $dwoo, $type, $idRecord, $idAddressType) {
	$idRecord		= intval($idRecord);
	$idAddressType	= intval($idAddressType);

	if( $type === 'person' ) {
		return TodoyuContactRights::isContactinfotypeOfPersonSeeAllowed($idRecord, $idAddressType);
	} else if( $type === 'company' ) {
		return TodoyuContactRights::isContactinfotypeOfCompanySeeAllowed($idRecord, $idAddressType);
	}

	return false;
}

?>