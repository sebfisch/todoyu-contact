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
 * General configuration of the contact extension
 */





$CONFIG['EXT']['contact']['addresstypes'] = array(
	array(
		'index'	=> 1,
		'label'	=> 'LLL:user.address.attr.addresstype.1'
	),
	array(
		'index'	=> 2,
		'label'	=> 'LLL:user.address.attr.addresstype.2'
	),
	array(
		'index'	=> 3,
		'label'	=> 'LLL:user.address.attr.addresstype.3'
	)
);

$CONFIG['EXT']['contact']['numFavoriteCountries']	= 5;


$CONFIG['EXT']['contact']['contacttypes']	= array(
	'person' => array(
		'label'		=> 'LLL:contact.contacttypes.person',
		'formXml'	=> 'ext/contact/config/form/person.xml',
		'objClass'	=> 'TodoyuUser',
		'listFunc'	=> 'TodoyuContactManager::ListPersons',
		'saveFunc'	=> 'TodoyuContactManager::savePerson',
		'deleteFunc'=> 'TodoyuUserManager::deleteUser'
	),
	'company' => array(
		'label'		=> 'LLL:contact.contacttypes.company',
		'formXml'	=> 'ext/contact/config/form/company.xml',
		'objClass'	=> 'TodoyuCompany',
		'listFunc'	=> 'TodoyuContactManager::ListCompanies',
		'saveFunc'	=> 'TodoyuContactManager::saveCompany',
		'deleteFunc'=> 'TodoyuCompanyManager::deleteCompany'
	)
);

$CONFIG['EXT']['contact']['defaultTypeTab'] = 'person';

	// Load person foreign records data
TodoyuFormHook::registerLoadData('ext/contact/config/form/person.xml', 'TodoyuContactManager::getUserForeignRecordData');

?>