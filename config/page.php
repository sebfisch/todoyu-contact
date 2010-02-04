<?php

	// Add menu entries
if( TodoyuAuth::isLoggedIn() && allowed('contact', 'general:use') ) {
	TodoyuFrontend::addMenuEntry('contact', 'LLL:contact.page.title', '?ext=contact', 40);
	TodoyuFrontend::addSubmenuEntry('contact', 'contactPerson', 'LLL:contact.subMenuEntry.person', '?ext=contact&type=person', 105);
	TodoyuFrontend::addSubmenuEntry('contact', 'contactCompany', 'LLL:contact.subMenuEntry.company', '?ext=contact&type=company', 110);
}

?>