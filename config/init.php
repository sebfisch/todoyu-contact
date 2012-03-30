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
		'label'		=> 'contact.ext.companies',
		'require'	=> 'contact.general:area',
		'position'	=> 110
	)
);

Todoyu::$CONFIG['EXT']['contact']['defaultTypeTab'] = 'person';



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



	// Load person foreign records data
TodoyuFormHook::registerLoadData('ext/contact/config/form/person.xml', 'TodoyuContactPersonManager::hookPersonLoadFormData');
TodoyuFormHook::registerLoadData('ext/contact/config/form/address.xml', 'TodoyuContactAddressManager::hookAddressLoadFormData');

	// Add person quickInfos to task persons
if( Todoyu::allowed('contact', 'general:use') ) {
	TodoyuHookManager::registerHook('project', 'taskdata', 'TodoyuContactTaskManager::hookModifyTaskPersonAttributes', 200);
}


/* ----------------------------------------
	Configure contact data types listings
   --------------------------------------- */
	// Person listing
Todoyu::$CONFIG['EXT']['contact']['listing']['person'] = array(
	'name'		=> 'person',
	'update'	=> 'contact/person/listing',
	'dataFunc'	=> 'TodoyuContactPersonSearch::getPersonListingData',
	'size'		=> Todoyu::$CONFIG['LIST']['size'],
	'columns'	=> array(
		'icon'		=> '',
		'lastname'	=> 'contact.ext.person.attr.lastname',
		'firstname'	=> 'contact.ext.person.attr.firstname',
		'company'	=> 'contact.ext.company',
		'actions'	=> '',
	),
	'truncate'	=> array(
		'lastname'	=> 20,
		'firstname'	=> 20,
		'company'	=> 45
	)
);

	// Company listing
Todoyu::$CONFIG['EXT']['contact']['listing']['company'] = array(
	'name'		=> 'company',
	'update'	=> 'contact/company/listing',
	'dataFunc'	=> 'TodoyuContactCompanySearch::getCompanyListingData',
	'size'		=> Todoyu::$CONFIG['LIST']['size'],
	'columns'	=> array(
		'icon'		=> '',
		'title'		=> 'contact.ext.company.attr.title',
		'street'	=> 'contact.ext.address.attr.street',
		'place'		=> 'contact.ext.place',
		'actions'	=> ''
	),
	'truncate'	=> array(
		'title'		=> 45,
		'street'	=> 40,
		'place'		=> 40
	)
);

Todoyu::$CONFIG['EXT']['contact']['listing']['employee'] = array(
	'name'		=> 'person',
	'update'	=> 'contact/empoyee/listing',
	'dataFunc'	=> 'TodoyuContactCompanySearch::getEmployeeListingData',
	'size'		=> 999,
	'columns'	=> array(
		'name'		=> 'contact.ext.person',
		'jobtype'	=> 'contact.ext.jobtype',
	),
	'truncate'	=> array(
		'name'		=> 45,
		'jobtype'	=> 50,
	)
);


/* ----------------------------------------
	Configure search + results listing
   --------------------------------------- */
			// Person search
Todoyu::$CONFIG['EXT']['contact']['listing']['personSearch'] = Todoyu::$CONFIG['EXT']['contact']['listing']['person'];
Todoyu::$CONFIG['EXT']['contact']['listing']['personSearch']['dataFunc']	= 'TodoyuContactPersonSearch::getPersonListingDataSearch';

	// Company search
Todoyu::$CONFIG['EXT']['contact']['listing']['companySearch'] = Todoyu::$CONFIG['EXT']['contact']['listing']['company'];
Todoyu::$CONFIG['EXT']['contact']['listing']['companySearch']['dataFunc']	= 'TodoyuContactCompanySearch::getCompanyListingDataSearch';



/* ----------------------------------------------
	Add exports to search area action panel
   ---------------------------------------------- */
TodoyuSearchActionPanelManager::addExport('company', 'csvexport', 'TodoyuContactCompanyExportManager::exportCSVfromIDs', 'contact.ext.export.companycsv', 'exportCsv', 'contactcompanysearch:export:companycsv');
TodoyuSearchActionPanelManager::addExport('person', 'csvexport', 'TodoyuContactPersonExportManager::exportCSVfromIDs', 'contact.ext.export.personcsv', 'exportCsv', 'contactpersonsearch:export:personcsv');



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

	// Tabs for contact section in profile: "personal data"
Todoyu::$CONFIG['EXT']['profile']['contactTabs'] = array(
	array(
		'id'	=> 'contact',
		'label'	=> 'contact.ext.profile.module'
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