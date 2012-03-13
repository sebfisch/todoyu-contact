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

/**
 * Filter configurations for the contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */

	// Company filters
Todoyu::$CONFIG['FILTERS']['COMPANY'] = array(
	'key'		=> 'company',
	'config'	=> array(
		'label'				=> 'contact.ext.company',
		'position'			=> 30,
		'resultsRenderer'	=> 'TodoyuContactCompanyRenderer::renderCompanyListingSearch',
		'class'				=> 'TodoyuContactCompanyFilter',
		'defaultSorting'	=> 'ext_contact_company.title',
		'require'			=> 'contact.general:use'
	),
	'widgets' => array(
		'fulltext' => array(
			'label'		=> 'contact.filter.fulltext',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> 'default'
			)
		)
	)
);



	// Person filters
Todoyu::$CONFIG['FILTERS']['PERSON'] = array(
	'key'	=> 'person',
	'config'	=> array(
		'label'				=> 'contact.ext.person',
		'position'			=> 35,
		'resultsRenderer'	=> 'TodoyuContactPersonRenderer::renderPersonListingSearch',
		'class'				=> 'TodoyuContactPersonFilter',
		'defaultSorting'	=> 'ext_contact_person.lastname',
		'require'			=> 'contact.general:use'
	),
	'widgets' => array(
			// Optgroup persons
		'fulltext' => array(
			'label'		=> 'contact.filter.fulltext',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> false
			)
		),
		'name' => array(
			'label'		=> 'contact.filter.person.name',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> false
			)
		),
		'salutation' => array(
			'label'		=> 'contact.filter.person.salutation',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> false,
				'size'		=> 2,
				'FuncRef'	=> 'TodoyuContactPersonFilterDataSource::getSalutationOptions',
				'negation'	=> false
			)
		),
		'contactinformation' => array(
			'label'		=> 'contact.filter.contactinformation',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> false
			)
		),
		'systemrole' => array(
			'label'		=> 'contact.filter.person.system_role',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 8,
				'FuncRef'	=> 'TodoyuContactPersonFilterDataSource::getSystemRoleOptions',
				'negation'	=> 'default'
			)
		),
//		'isActive'	=> array(
//			'label'		=> 'contact.filter.person.isActive',
//			'optgroup'	=> 'contact.filter.person.label',
//			'widget'	=> 'checkbox',
//			'internal'	=> true,
//			'wConf'		=> array(
//				'checked'	=> true,
//				'negation'	=> false
//			)
//		),



			// Optgroup companies
		'company' => array(
			'label'		=> 'contact.filter.person.company',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactCompanyFilterDataSource::autocompleteCompanies',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactCompanyFilterDataSource::getCompanyLabel',
				'negation'		=> 'default'
			)
		),
		'isInternal'	=> array(
			'label'		=> 'contact.filter.person.company.isInternal',
			'optgroup'	=> 'contact.filter.company.label',
			'widget'	=> 'checkbox',
			'internal'	=> true,
			'wConf'		=> array(
				'checked'	=> true
			)
		),
	)
);

?>