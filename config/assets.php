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
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Assets registration of contact extension
 */

Todoyu::$CONFIG['EXT']['contact']['assets'] = array(
	'js'	=> array(
		array(
			'file'		=> 'ext/contact/assets/js/Ext.js',
			'position'	=> 100
		),
			// Add creation engines to quick create headlet
		array(
			'file'		=> 'ext/contact/assets/js/QuickCreateCompany.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/contact/assets/js/Autocomplete.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/contact/assets/js/Person.js',
			'position'	=> 105
		),
		array(
			'file'		=> 'ext/contact/assets/js/Company.js',
			'position'	=> 106
		),
		array(
			'file'		=> 'ext/contact/assets/js/QuickCreatePerson.js',
			'position'	=> 110
		),
		array(
			'file' 		=> 'ext/contact/assets/js/QuickInfoPerson.js',
			'position' 	=> 200
		),
		array(
			'file'		=> 'ext/contact/assets/js/PanelWidgetContactSearch.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/contact/assets/js/PanelWidgetStaffSelector.js',
			'position'	=> 110
		)
	),
	'css'	=> array(
		array(
			'file'		=> 'ext/contact/assets/css/global.css',
			'position'	=> 100
		),
		array(
			'file' 		=> 'ext/contact/assets/css/quickinfo.css',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/contact/assets/css/ext.css',
			'position'	=> 100
		),
		array(
			'file'	=> 'ext/contact/assets/css/panelwidget-contactsearch.css'
		),
		array(
			'file'		=> 'ext/contact/assets/css/panelwidget-staffselector.css',
			'position'	=> 110
		)
	)
);

?>