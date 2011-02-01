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

/* ---------------------------------------------
	Add autocompleters for contact data types
   --------------------------------------------- */
	// Company
TodoyuAutocompleter::addAutocompleter('company', 'TodoyuCompanyFilterDataSource::autocompleteCompanies', array('contact', 'general:use'));
	// Person
TodoyuAutocompleter::addAutocompleter('person', 'TodoyuPersonFilterDataSource::autocompletePersons', array('contact', 'general:use'));
	// Jobtype
TodoyuAutocompleter::addAutocompleter('jobtype', 'TodoyuJobTypeManager::autocompleteJobtypes', array('contact', 'general:use'));

	// Add quickInfo callback for person labels
TodoyuQuickinfoManager::addFunction('person', 'TodoyuContactQuickinfoManager::getQuickinfoPerson');



/* ---------------------------------------------
	Categories of dynamic contact info types
   --------------------------------------------- */
Todoyu::$CONFIG['EXT']['contact']['contactinfotypecategories'] = array(
	array(	// Email
		'index'	=> CONTACT_INFOTYPE_CATEGORY_EMAIL,
		'label'	=> 'LLL:contact.record.contactinfotype.email'
	),	
	array(	// Phone
		'index'	=> CONTACT_INFOTYPE_CATEGORY_PHONE,
		'label'	=> 'LLL:contact.record.contactinfotype.phone'
	),
	array(	// Other
		'index'	=> CONTACT_INFOTYPE_CATEGORY_OTHER,
		'label'	=> 'LLL:contact.record.contactinfotype.other'
	),
);



/* -----------------------
	Types of addresses
   ----------------------- */
Todoyu::$CONFIG['EXT']['contact']['addresstypes'] = array(
	array(	// Home address
		'index'	=> 1,
		'label'	=> 'LLL:contact.address.attr.addresstype.1'
	),
		
	array(	// Business address
		'index'	=> 2,
		'label'	=> 'LLL:contact.address.attr.addresstype.2'
	),
	array(	// Billing address
		'index'	=> 3,
		'label'	=> 'LLL:contact.address.attr.addresstype.3'
	)
);

Todoyu::$CONFIG['EXT']['contact']['numFavoriteCountries']	= 5;



/* -------------------------------
	Content Tabs Configuration
   ------------------------------- */
Todoyu::$CONFIG['EXT']['contact']['tabs'] = array(
	array(
		'id'		=> 'person',
		'label'		=> 'LLL:contact.persons',
		'require'	=> 'contact.general:area'
	),
	array(
		'id'		=> 'company',
		'label'		=> 'LLL:contact.companys',
		'require'	=> 'contact.general:area'
	)
);

Todoyu::$CONFIG['EXT']['contact']['defaultTypeTab'] = 'person';




	// Load person foreign records data
TodoyuFormHook::registerLoadData('ext/contact/config/form/person.xml', 'TodoyuContactManager::getPersonForeignRecordData');
TodoyuFormHook::registerBuildForm('ext/contact/config/form/address.xml', 'TodoyuCompanyManager::hookAddTimezone');



/* ----------------------------------------
	Configure contact data types listings
   --------------------------------------- */
	// Person
Todoyu::$CONFIG['EXT']['contact']['listing']['person'] = array(
'name'		=> 'person',
'update'	=> 'contact/person/listing',
'dataFunc'	=> 'TodoyuContactManager::getPersonListingData',
'size'		=> Todoyu::$CONFIG['LIST']['size'],
'columns'	=> array(
	'icon'		=> '',
	'lastname'	=> 'LLL:contact.person.attr.lastname',
	'firstname'	=> 'LLL:contact.person.attr.firstname',
	'email'		=> 'LLL:contact.person.attr.email',
	'company'	=> 'LLL:contact.company',
	'actions'	=> '',
),
'truncate'	=> array(
	'lastname'	=> 17,
	'firstname'	=> 17,
	'email'		=> 16,
	'company'	=> 22
)
);

	// Company
Todoyu::$CONFIG['EXT']['contact']['listing']['company'] = array(
'name'		=> 'company',
'update'	=> 'contact/company/listing',
'dataFunc'	=> 'TodoyuContactManager::getCompanyListingData',
'size'		=> Todoyu::$CONFIG['LIST']['size'],
'columns'	=> array(
	'icon'		=> '',
	'title'		=> 'LLL:contact.company.attr.title',
//	'persons'	=> 'LLL:contact.company.employees',
	'address'	=> 'LLL:contact.address',
	'actions'	=> ''
),
'truncate'	=> array(
	'title'		=> 30,
	'address'	=> 45
)
);



/* ----------------------------
	Configure panel widgets
   ---------------------------- */
	// Maximum persons in staff listing widget
Todoyu::$CONFIG['EXT']['contact']['panelWidgetProjectList']['maxPersons']	= 30;
	// Max size of person selector widget
Todoyu::$CONFIG['EXT']['contact']['panelWidgetStaffSelector'] = array(
	'maxListSize'	=> 15
);

?>