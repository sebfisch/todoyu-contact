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

Todoyu.Ext.contact.Profile = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.contact,



	/**
	 * Handles Tab events for the profile
	 *
	 * @param	{Event}		event
	 * @param	{String}	tabKey
	 */
	onTabClick: function(event, tabKey) {
		// do nothing
	},



	/**
	 * Sends the save Request for the profile form
	 *
	 * @param	{Element}	form
	 */
	save: function(form) {
		$(form).request({
			'parameters': {
				'action':	'save'
			},
			'onComplete': this.onSaved.bind(this)
		});

		return false;
	},



	/**
	 * Handler evoked upon onComplete of person saving: check for and notify success / error, update display
	 *
	 * @method	onSaved
	 * @param	{Array}		response
	 */
	onSaved: function(response) {
		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:contact.ext.person.saved.error]');
			$('contact-form-content').update(response.responseText);
			var idPerson	= parseInt(response.request.parameters['person[id]'], 10);
			this.initEditForm(idPerson);
		} else {
			Todoyu.notifySuccess('[LLL:contact.ext.person.saved]');
			$('contact-form-content').update(response.responseText);
		}
	},



	/**
	 * Initialize edit form
	 *
	 * @method	initEditForm
	 * @param	{Number}		idPerson
	 */
	initEditForm: function(idPerson) {
		this.Ext.contact.Person.observeFieldsForShortname(idPerson);
	}
};