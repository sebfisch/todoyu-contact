/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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

Todoyu.Ext.contact.Company =  {

	/**
	 * Extension backlink
	 *
	 * @var	{Object}	ext
	 */
	ext:	Todoyu.Ext.contact,



	add: function() {
		this.edit(0);
	},



	/**
	 * Edit given company
	 *
	 * @param	{Number}	idCompany
	 */
	edit: function(idCompany) {
		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			'parameters': {
				'company':	idCompany,
				'action':	'edit'
			},
			'onComplete': this.onEdit.bind(this, idCompany)
		};

		this.ext.updateContent(url, options);
	},



	onEdit: function(idCompany, response) {

	},



	/**
	 * Confirm if really wanted and remove (delete) given company if
	 *
	 * @param	{Number}	idCompany
	 */
	remove: function(idCompany) {
		if( confirm('[LLL:contact.company.delete.confirm]') )	{
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
				'parameters': {
					'action':	'remove',
					'company':	idCompany
				},
				'onComplete': this.onRemoved.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handle event after company deletion being performed
	 *
	 * @param	{Response}	response
	 */
	onRemoved: function(response) {
		if( ! response.hasTodoyuError() ) {
			this.showList(this.ext.PanelWidget.ContactSearch.getValue());
		}
	},



	/**
	 * Save company record
	 *
	 * @param	{String}		form
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
	 * Handler being evoked OnComplete of save company request: check for and notify success / error, update display
	 *
	 * @param	{Object}	response
	 */
	onSaved: function(response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('[LLL:contact.company.saved.error]');
			$('contact-form-content').update(response.responseText);
		} else {
				// Notify (implement)
			Todoyu.notifySuccess('[LLL:contact.company.saved.ok]');
			
			this.showList(this.ext.PanelWidget.ContactSearch.getValue());
		}
	},



	/**
	 * Close company form, update list view
	 */
	closeForm: function() {
		this.showList(this.ext.PanelWidget.ContactSearch.getValue());
	},



	/**
	 * Update company list
	 *
	 * @param	{String}		sword		(search word)
	 */
	showList: function(sword) {
		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			'parameters': {
				'action':	'list',
				'sword':	sword
			}
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * Show detail view (popup) of given company
	 *
	 * @param	{Number}		idCompany
	 */
	show: function(idCompany) {
		var url		= Todoyu.getUrl('contact', 'company');
		var options	= {
			'parameters': {
				'action':	'detail',
				'company':	idCompany
			}
		};

		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, url, options);
	}

};