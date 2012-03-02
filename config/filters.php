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
		'resultsRenderer'	=> 'TodoyuContactRenderer::renderCompanyListingSearch',
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
		'resultsRenderer'	=> 'TodoyuContactRenderer::renderPersonListingSearch',
		'class'				=> 'TodoyuContactPersonFilter',
		'defaultSorting'	=> 'ext_contact_person.lastname',
		'require'			=> 'contact.general:use'
	),
	'widgets' => array(
		'fulltext' => array(
			'label'		=> 'contact.filter.fulltext',
			'optgroup'	=> 'contact.filter.person.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> 'default'
			)
		)
	)
);

?>