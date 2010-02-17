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

Todoyu.Headlet.QuickCreate.Company = {

	/**
	 * Evoked upon opening of company quick create wizard popup
	 */
	onPopupOpened: function() {

	},



	/**
	 * Save company
	 *
	 * @param	form
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
	 *	onSaved company custom event handler
	 *
	 *	@param	Ajax.Response		response
	 */
	onSaved: function(response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.Headlet.QuickCreate.updateFormDiv(response.responseText);
			Todoyu.notifyError('[LLL:contact.company.save.error]');
		} else {
			var idCompany	= response.getTodoyuHeader('idCompany');
			Todoyu.Hook.exec('onCompanySaved', idCompany);

			Todoyu.Headlet.QuickCreate.closePopup();
			Todoyu.notifySuccess('[LLL:contact.company.save.success]');
//			Todoyu.Ext.contact.Company.showList();
		}
	},

};