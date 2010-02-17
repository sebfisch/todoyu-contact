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

Todoyu.Headlet.QuickCreate.Person = {

	/**
	 * Evoked upon opening of person quick create wizard popup
	 */
	onPopupOpened: function() {

	},



	/**
	 *	Save person
	 *
	 *	@param	unknown	form
	 */
	save: function(form){
		tinyMCE.triggerSave();

		$(form).request({
			'parameters': {
				'action':	'save'
			},
			onComplete: this.onSaved.bind(this)
		});

		return false;
	},



	/**
	 *	onSaved person custom event handler
	 *
	 *	@param	Ajax.Response		response
	 */
	onSaved: function(response){
		var idPerson	= response.getTodoyuHeader('idPerson');
		var error		= response.hasTodoyuError();

		if( error ) {
			Todoyu.Headlet.QuickCreate.updateFormDiv(response.responseText);
			Todoyu.notifyError('[LLL:contact.person.save.error]');
		} else {
//			Todoyu.Hook.exec('onPersonSaved', idPerson);
			
			Todoyu.Popup.close('quickcreate');		
			Todoyu.notifySuccess('[LLL:contact.person.save.success]');
		}
	}

};