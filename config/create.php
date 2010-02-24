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

if ( allowed('contact', 'general:use') ) {
		// Add quick create types
	TodoyuQuickCreateManager::addEngine('contact', 'person', 'contact.create.person.label', 50);
	TodoyuQuickCreateManager::addEngine('contact', 'company', 'contact.create.company.label', 60);

		// Add area related primary engines
	TodoyuQuickCreateManager::addAreaEngine(EXTID_CONTACT, 'contact', 'person', 'contact.create.person.label', 10);
	TodoyuQuickCreateManager::addAreaEngine(EXTID_CONTACT, 'contact', 'company', 'contact.create.company.label', 20);
}

?>