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


if( ! defined('TODOYU') ) die('NO ACCESS');


$CONFIG['EXT']['contact']['assets'] = array(
	'default' => array(
		'css'	=> array(
			array(
				'file'		=> 'ext/contact/assets/css/metamenu.css',
				'position'	=> 100
			)
		)
	),

	'public' => array(
		'js' => array(
			array(
				'file'		=> 'ext/contact/assets/js/Ext.js',
				'position'	=> 100
			),
			array(
				'file'		=> 'ext/contact/assets/js/Person.js',
				'position'	=> 101
			),
			array(
				'file'		=> 'ext/contact/assets/js/Company.js',
				'position'	=> 101
			)
		),
		'css' => array(
			array(
				'file'		=> 'ext/contact/assets/css/ext.css',
				'position'	=> 100
			)
		)
	),

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

	'panelwidget-quickcontact' => array(
		'js' => array(
			array(
				'file'		=> 'ext/contact/assets/js/PanelWidgetQuickContact.js',
				'position'	=> 102
			)
		),
		'css' => array(
			array(
				'file'	=> 'ext/contact/assets/css/panelwidget-quickcontact.css'
			)
		)
	),
);

?>