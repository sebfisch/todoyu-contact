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

	// Add main menu area entry for contact
if( allowed('contact', 'general:area') ) {
	TodoyuFrontend::addMenuEntry('contact', 'LLL:contact.page.title', '?ext=contact', 40);

		// Add sub entries: person, company
	if( allowed('contact', 'contact:see') ) {
		TodoyuFrontend::addSubmenuEntry('contact', 'contactPerson',		'LLL:contact.subMenuEntry.person',	'?ext=contact&type=person', 105);
		TodoyuFrontend::addSubmenuEntry('contact', 'contactCompany',	'LLL:contact.subMenuEntry.company',	'?ext=contact&type=company', 110);
	}
}

?>