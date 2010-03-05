<?php

	// Add menu entries
if( TodoyuAuth::isLoggedIn() && allowed('contact', 'general:use') ) {
	TodoyuFrontend::addMenuEntry('contact', 'LLL:contact.page.title', '?ext=contact', 40);
	if(allowed('contact', 'contact:see')) TodoyuFrontend::addSubmenuEntry('contact', 'contactPerson', 'LLL:contact.subMenuEntry.person', '?ext=contact&type=person', 105);
	if(allowed('contact', 'contact:see'))TodoyuFrontend::addSubmenuEntry('contact', 'contactCompany', 'LLL:contact.subMenuEntry.company', '?ext=contact&type=company', 110);
}

?>