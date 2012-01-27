<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
TodoyuAutocompleter::addAutocompleter('company', 'TodoyuContactCompanyFilterDataSource::autocompleteCompanies', array('contact', 'general:use'));
	// Person
TodoyuAutocompleter::addAutocompleter('person', 'TodoyuContactPersonFilterDataSource::autocompletePersons', array('contact', 'general:use'));
	// Jobtype
TodoyuAutocompleter::addAutocompleter('jobtype', 'TodoyuContactJobTypeManager::autocompleteJobtypes', array('contact', 'general:use'));



/* ---------------------------------------------
	Add quickInfo callback for person labels
   --------------------------------------------- */
TodoyuQuickinfoManager::addFunction('person', 'TodoyuContactPersonQuickinfoManager::addPersonInfos');



/* ---------------------------------------------
	Categories of dynamic contact info types
   --------------------------------------------- */
Todoyu::$CONFIG['EXT']['contact']['contactinfotypecategories'] = array(
	array(	// Email
		'index'	=> CONTACT_INFOTYPE_CATEGORY_EMAIL,
		'label'	=> 'contact.ext.record.contactinfotype.email'
	),
	array(	// Phone
		'index'	=> CONTACT_INFOTYPE_CATEGORY_PHONE,
		'label'	=> 'contact.ext.record.contactinfotype.phone'
	),
	array(	// Other
		'index'	=> CONTACT_INFOTYPE_CATEGORY_OTHER,
		'label'	=> 'contact.ext.record.contactinfotype.other'
	),
);



/* -----------------------
	Types of addresses
   ----------------------- */
Todoyu::$CONFIG['EXT']['contact']['addresstypes'] = array(
	array(	// Home address
		'index'	=> 1,
		'label'	=> 'contact.ext.address.attr.addresstype.1'
	),

	array(	// Business address
		'index'	=> 2,
		'label'	=> 'contact.ext.address.attr.addresstype.2'
	),
	array(	// Billing address
		'index'	=> 3,
		'label'	=> 'contact.ext.address.attr.addresstype.3'
	)
);

Todoyu::$CONFIG['EXT']['contact']['numFavoriteCountries']	= 5;



/* -------------------------------
	Content Tabs Configuration
   ------------------------------- */
Todoyu::$CONFIG['EXT']['contact']['tabs'] = array(
	'person'	=> array(
		'key'		=> 'person',
		'id'		=> 'person',
		'label'		=> 'contact.ext.persons',
		'require'	=> 'contact.general:area',
		'position'	=> 105
	),
	'company'	=> array(
		'key'		=> 'company',
		'id'		=> 'company',
		'label'		=> 'contact.ext.companys',
		'require'	=> 'contact.general:area',
		'position'	=> 110
	)
);

Todoyu::$CONFIG['EXT']['contact']['defaultTypeTab'] = 'person';




	// Load person foreign records data
TodoyuFormHook::registerLoadData('ext/contact/config/form/person.xml', 'TodoyuContactPersonManager::hookPersonLoadFormData');
TodoyuFormHook::registerLoadData('ext/contact/config/form/address.xml', 'TodoyuContactAddressManager::hookAddressLoadFormData');

// Implement person quickInfo class to various person labels
TodoyuHookManager::registerHook('project', 'taskdataattributes', 'TodoyuContactPersonHooks::extendTaskDataAttributes', 10);

/* ----------------------------------------
	Configure contact data types listings
   --------------------------------------- */
	// Person
Todoyu::$CONFIG['EXT']['contact']['listing']['person'] = array(
	'name'		=> 'person',
	'update'	=> 'contact/person/listing',
	'dataFunc'	=> 'TodoyuContactPersonSearch::getPersonListingData',
	'size'		=> Todoyu::$CONFIG['LIST']['size'],
	'columns'	=> array(
		'icon'		=> '',
		'lastname'	=> 'contact.ext.person.attr.lastname',
		'firstname'	=> 'contact.ext.person.attr.firstname',
		'email'		=> 'contact.ext.person.attr.email',
		'company'	=> 'contact.ext.company',
		'actions'	=> '',
	),
	'truncate'	=> array(
		'lastname'	=> 20,
		'firstname'	=> 20,
		'email'		=> 23,
		'company'	=> 25
	)
);

	// Company
Todoyu::$CONFIG['EXT']['contact']['listing']['company'] = array(
	'name'		=> 'company',
	'update'	=> 'contact/company/listing',
	'dataFunc'	=> 'TodoyuContactCompanySearch::getCompanyListingData',
	'size'		=> Todoyu::$CONFIG['LIST']['size'],
	'columns'	=> array(
		'icon'		=> '',
		'title'		=> 'contact.ext.company.attr.title',
	//	'persons'	=> 'contact.ext.company.employees',
		'address'	=> 'contact.ext.address',
		'actions'	=> ''
	),
	'truncate'	=> array(
		'title'		=> 45,
		'address'	=> 50
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



/* ----------------------------
	Configure Contact Images
   ---------------------------- */
	//configuration of the contact-image
Todoyu::$CONFIG['EXT']['contact']['contactimage'] = array(
	'pathperson'	=> 'files/contact/person',
	'pathcompany'	=> 'files/contact/company',
	'max_file_size'	=> 512000,
	'dimension'		=> array(
		'x'	=> 100,
		'y'	=> 100
	),
	'allowedTypes'	=> array(
		'image/png',
		'image/jpeg',
		'image/gif'
	)
);


/* -------------------------------------
	Add contact module to profile
   ------------------------------------- */
if( TodoyuExtensions::isInstalled('profile') && Todoyu::allowed('contact', 'general:use') ) {
	TodoyuProfileManager::addModule('contact', array(
		'position'	=> 2,
		'tabs'		=> 'TodoyuContactProfileRenderer::renderTabs',
		'content'	=> 'TodoyuContactProfileRenderer::renderContent',
		'label'		=> 'contact.ext.profile.module',
		'class'		=> 'contact'
	));
}

	// Tabs for bookmark section in profile
Todoyu::$CONFIG['EXT']['profile']['contactTabs'] = array(
	array(
		'id'			=> 'contact',
		'label'			=> 'contact.ext.profile.module'
	)
);


TodoyuCreateWizardManager::addWizard('person', array(
	'ext'		=> 'contact',
	'controller'=> 'person',
	'action'	=> 'createWizard',
	'title'		=> 'contact.ext.person.create',
	'restrict'	=> array(
		array(
			'contact',
			'person:add'
		)
	)
));

TodoyuCreateWizardManager::addWizard('company', array(
	'ext'		=> 'contact',
	'controller'=> 'company',
	'action'	=> 'createWizard',
	'title'		=> 'contact.ext.create.company.label',
	'restrict'	=> array(
		array(
			'contact',
			'company:add'
		)
	)
));


Todoyu::$CONFIG['FORM']['TYPES']['recordsStaff'] = array(
	'class'		=> 'TodoyuContactFormElement_RecordsStaff',
	'template'	=> 'core/view/form/FormElement_Records.tmpl'
);

?>