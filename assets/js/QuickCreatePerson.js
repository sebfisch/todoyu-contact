/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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

Todoyu.Ext.contact.QuickCreatePerson = {
	
	ext: Todoyu.Ext.contact,
	
	person: Todoyu.Ext.contact.Person,

	/**
	 * Evoked upon opening of person quick create wizard popup
	 */
	onPopupOpened: function() {
		this.person.onEdit(0);
	},



	/**
	 * Save person
	 *
	 * @param	String		form
	 */
	save: function(form) {
		$(form).request ({
				'parameters': {
					'action':	'save'
				},
				'onComplete': this.onSaved.bind(this)
			});

		return false;
	},



	/**
	 * Handler being evoked upon onComplete of person saving. Checks for and notify error / success, updates display
	 *
	 * @paran	Object		response
	 */
	onSaved: function(response) {
		if( response.hasTodoyuError() ) {
				// Saving person failed
			Todoyu.notifyError('[LLL:contact.person.saved.error]');
			Todoyu.Headlet.QuickCreate.updatePopupContent(response.responseText);
		} else {
				// Saving succeeded
			var idPerson	= response.getTodoyuHeader('idPerson');
			Todoyu.Hook.exec('onPersonSaved', idPerson);

			Todoyu.Popup.close('quickcreate');
			Todoyu.notifySuccess('[LLL:contact.person.saved]');
			
			if ( Todoyu.getArea() == 'contact' ) {
				Todoyu.Ext.contact.Person.showList();
			}
		}
	}

};