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
 * Extension main file for contact extension
 *
 * @package		Todoyu
 * @subpackage	Admin
 */





	// Declare ext ID, path
define('EXTID_CONTACT', 106);
define('PATH_EXT_CONTACT', PATH_EXT . '/contact');

	// Register module locales
TodoyuLanguage::register('contact', PATH_EXT_CONTACT . '/locale/ext.xml');
TodoyuLanguage::register('panelwidget-contactsearchinput', PATH_EXT_CONTACT . '/locale/panelwidget-contactsearchinput.xml');
TodoyuLanguage::register('panelwidget-quickcontact', PATH_EXT_CONTACT . '/locale/panelwidget-quickcontact.xml');

	// Request configurations
require_once( PATH_EXT_CONTACT . '/config/extension.php' );
require_once( PATH_EXT_CONTACT . '/config/panelwidgets.php' );
require_once( PATH_EXT_CONTACT . '/config/admin.php');
require_once( PATH_EXT_CONTACT . '/dwoo/plugins.php');

?>