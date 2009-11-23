<?php

	// Add menu entries
if( TodoyuAuth::isLoggedIn() && allowed('contact', 'use') ) {
	TodoyuFrontend::addMenuEntry('contact', 'LLL:contact.page.title', '?ext=contact', 100);
	TodoyuFrontend::addSubmenuEntry('contact', 'contact', 'LLL:contact.subMenuEntry.person', '?ext=contact&type=person', 105, 'person');
	TodoyuFrontend::addSubmenuEntry('contact', 'contact', 'LLL:contact.subMenuEntry.company', '?ext=contact&type=company', 110, 'company');
}

?>