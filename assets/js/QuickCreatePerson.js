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
	 * Handler being evoked upon onComplete of person saving. Check for and notify error / success, update display
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
//			this.showList();

			Todoyu.Popup.close('quickcreate');
			Todoyu.notifySuccess('[LLL:contact.person.saved]');

		}
	}

};