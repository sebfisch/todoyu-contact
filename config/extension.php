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


	// Categories of dynamic contactinfo types
$CONFIG['EXT']['contact']['contactinfotypecategories'] = array(
		// Email
	array(
		'index'	=> 1,
		'label'	=> 'LLL:contact.record.contactinfotype.email'
	),
		// Phone
	array(
		'index'	=> 2,
		'label'	=> 'LLL:contact.record.contactinfotype.phone'
	),
		// Other
	array(
		'index'	=> 3,
		'label'	=> 'LLL:contact.record.contactinfotype.other'
	),
);


	// Types of addresses
$CONFIG['EXT']['contact']['addresstypes'] = array(
		// Home address
	array(
		'index'	=> 1,
		'label'	=> 'LLL:contact.address.attr.addresstype.1'
	),
		// Business address
	array(
		'index'	=> 2,
		'label'	=> 'LLL:contact.address.attr.addresstype.2'
	),
		// Billing address
	array(
		'index'	=> 3,
		'label'	=> 'LLL:contact.address.attr.addresstype.3'
	)
);

$CONFIG['EXT']['contact']['numFavoriteCountries']	= 5;



/**
 * @todo	Which of this keys are still in use?
 */
/*
$CONFIG['EXT']['contact']['contacttypes']	= array(
	'person' => array(
		'label'		=> 'LLL:contact.contacttypes.person',
		'formXml'	=> 'ext/contact/config/form/person.xml',
		'objClass'	=> 'TodoyuPerson',
		'listFunc'	=> 'TodoyuContactManager::ListPersons',
		'saveFunc'	=> 'TodoyuContactManager::savePerson',
		'deleteFunc'=> 'TodoyuPersonManager::deletePerson'
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
*/

$CONFIG['EXT']['contact']['tabs'] = array(
	array(
		'id'	=> 'person',
		'label'	=> 'LLL:contact.person'
	),
	array(
		'id'	=> 'company',
		'label'	=> 'LLL:contact.company'
	)
);



$CONFIG['EXT']['contact']['defaultTypeTab'] = 'person';

	// Load person foreign records data
TodoyuFormHook::registerLoadData('ext/contact/config/form/person.xml', 'TodoyuContactManager::getPersonForeignRecordData');



/**
 * Configure listings for persons
 */
$CONFIG['EXT']['contact']['listing']['person'] = array(
	'name'		=> 'person',
	'update'	=> 'contact/person/listing',
	'dataFunc'	=> 'TodoyuContactManager::getPersonListingData',
	'size'		=> $GLOBALS['CONFIG']['LIST']['size'],
	'columns'	=> array(
		'icon'		=> '',
		'lastname'	=> 'LLL:contact.person.attr.lastname',
		'firstname'	=> 'LLL:contact.person.attr.firstname',
		'email'		=> 'LLL:contact.email',
		'company'	=> 'LLL:contact.company',
		'actions'	=> ''
	)
);



/**
 * Configure listing for companies
 */
$CONFIG['EXT']['contact']['listing']['company'] = array(
	'name'		=> 'company',
	'update'	=> 'contact/company/listing',
	'dataFunc'	=> 'TodoyuContactManager::getCompanyListingData',
	'size'		=> $GLOBALS['CONFIG']['LIST']['size'],
	'columns'	=> array(
		'icon'		=> '',
		'title'		=> 'LLL:contact.company.attr.title',
		'persons'	=> 'LLL:contact.comapny.employees',
		'address'	=> 'LLL:contact.company.attr.address',
		'actions'	=> ''
	)
);



$CONFIG['EXT']['contact']['panelWidgetStaffSelector'] = array(
	'maxListSize'	=> 15 // Max size of person selector
);


	// Implement person quickInfo class to various person labels
TodoyuHookManager::registerHook('project', 'taskdataattributes', 'TodoyuPersonHooks::extendTaskDataAttributes', 10);

?>