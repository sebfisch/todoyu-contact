<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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

	// Add main menu area entry for contact
if( Todoyu::person()->isInternal() && allowed('contact', 'general:area') ) {
	TodoyuFrontend::addMenuEntry('contact', 'LLL:contact.page.title', '?ext=contact', 40);

		// Add sub entries: person, company
	if( allowed('contact', 'general:area') ) {
		TodoyuFrontend::addSubmenuEntry('contact', 'contactPerson',		'LLL:contact.persons',	'?ext=contact&type=person', 105);
		TodoyuFrontend::addSubmenuEntry('contact', 'contactCompany',	'LLL:contact.companys',	'?ext=contact&type=company', 110);
	}
}

?>