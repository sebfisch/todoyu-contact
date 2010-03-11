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
 * Assets registration of contact extension
 *
 */

$CONFIG['EXT']['contact']['assets'] = array(
		// Default assets: loaded all over the installation always
	'default' => array(
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
				'file'		=> 'ext/contact/assets/js/QuickCreatePerson.js',
				'position'	=> 110
			),
			array(
				'file' 		=> 'ext/contact/assets/js/QuickInfoPerson.js',
				'position' 	=> 200
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
			)
		)
	),

		// Public assets: basic assets for this extension
	'public' => array(
		'js' => array(
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
			)
		),
		'css' => array(
			array(
				'file'		=> 'ext/contact/assets/css/ext.css',
				'position'	=> 100
			)
		)
	),

		// Assets of panel widgets
	'panelwidget-contactsearch' => array(
		'js' => array(
			array(
				'file'		=> 'ext/contact/assets/js/PanelWidgetContactSearch.js',
				'position'	=> 101
			)
		),
		'css' => array(
			array(
				'file'	=> 'ext/contact/assets/css/panelwidget-contactsearch.css'
			)
		)
	),

			// Staff selector
	'panelwidget-staffselector' => array(
		'js' => array(
			array(
				'file'		=> 'ext/contact/assets/js/PanelWidgetStaffSelector.js',
				'position'	=> 110
			)
		),
		'css' => array(
			array(
				'file'		=> 'ext/contact/assets/css/panelwidget-staffselector.css',
				'position'	=> 110
			)
		)
	),

);

?>