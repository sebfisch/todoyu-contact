/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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

/**
 * @module	Contact
 */

Todoyu.Ext.contact.QuickCreateCompany = {

	/**
	 * Evoked upon opening of company quick create wizard popup
	 *
	 * @method	onPopupOpened
	 */
	onPopupOpened: function() {

	},



	/**
	 * Save company
	 *
	 * @method	save
	 * @param	{Element}		form
	 * @return	{Boolean}		false
	 */
	save: function(form) {
		$(form).request ({
				'parameters':	{
					'action':	'save'
				},
				'onComplete':	this.onSaved.bind(this)
			});

		return false;
	},



	/**
	 * Handler evoked upon onComplete of saving: check for and notify success / error, update display
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(response) {
		if( response.hasTodoyuError() ) {
			this.getQuickCreateHeadlet().updatePopupContent(response.responseText);
			Todoyu.notifyError('[LLL:contact.ext.company.saved.error]');
		} else {
			var idCompany	= response.getTodoyuHeader('idCompany');
			Todoyu.Hook.exec('contact.company.saved', idCompany);

			this.getQuickCreateHeadlet().closePopup();
			Todoyu.notifySuccess('[LLL:contact.ext.company.saved.ok]');

			if( Todoyu.getArea() == 'contact' ) {
				Todoyu.Ext.contact.Company.showList();
			}
		}
	},

	getQuickCreateHeadlet: function() {
		return Todoyu.Headlets.getHeadlet('todoyuheadletquickcreate');
	}




};