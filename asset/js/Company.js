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

Todoyu.Ext.contact.Company =  {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.contact,



	/**
	 * Open new company record for editing
	 *
	 * @method	add
	 */
	add: function() {
		this.edit(0);
	},



	/**
	 * Edit given company
	 *
	 * @method	edit
	 * @param	{Number}	idCompany
	 */
	edit: function(idCompany) {
		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			parameters: {
				'company':	idCompany,
				action:	'edit'
			},
			onComplete: this.onEdit.bind(this, idCompany)
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * On edit company handler
	 *
	 * @method	onEdit
	 * @param	{Number}			idCompany
	 * @param	{Ajax.Response}		response
	 */
	onEdit: function(idCompany, response) {

	},



	/**
	 * Confirm if really wanted and remove (delete) given company if
	 *
	 * @method	remove
	 * @param	{Number}	idCompany
	 */
	remove: function(idCompany) {
		if( confirm('[LLL:contact.ext.company.delete.confirm]') ) {
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
				parameters: {
					action:	'remove',
					'company':	idCompany
				},
				onComplete: this.onRemoved.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handle event after company deletion being performed
	 *
	 * @method	onRemoved
	 * @param	{Ajax.Response}	response
	 */
	onRemoved: function(response) {
		if( ! response.hasTodoyuError() ) {
			this.showList(this.ext.PanelWidget.ContactSearch.getValue());
		}
	},



	/**
	 * Save company record
	 *
	 * @method	save
	 * @param	{String}		form
	 */
	save: function(form) {
		$(form).request ({
				parameters: {
					action:	'save'
				},
				onComplete: this.onSaved.bind(this)
			});

		return false;
	},



	/**
	 * Handler being evoked OnComplete of save company request: check for and notify success / error, update display
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('[LLL:contact.ext.company.saved.error]');
			$('contact-form-content').update(response.responseText);
		} else {
				// Notify (implement)
			Todoyu.notifySuccess('[LLL:contact.ext.company.saved.ok]');

			this.showList(this.ext.PanelWidget.ContactSearch.getValue());
		}
	},



	/**
	 * Close company form, update list view
	 *
	 * @method	closeForm
	 * @param	{Element}	form
	 */
	closeForm: function(form) {
		this.removeUnusedImages(form);
		this.showList(this.ext.PanelWidget.ContactSearch.getValue());
	},



	/**
	 * Update company list
	 *
	 * @method	showList
	 * @param	{String}		sword		(search word)
	 */
	showList: function(sword) {
		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			parameters: {
				action:	'list',
				'sword':	sword
			}
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * Show detail view (popup) of given company
	 *
	 * @method	show
	 * @param	{Number}		idCompany
	 */
	show: function(idCompany) {
		var url		= Todoyu.getUrl('contact', 'company');
		var options	= {
			parameters: {
				action:	'detail',
				'company':	idCompany
			}
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * Save person record from wizard
	 *
	 * @method	saveWizard
	 * @param	{Object}		form
	 * @param	{String}		target
	 * @return	{Boolean}
	 */
	saveWizard: function(form, target) {
		$(form).request ({
			parameters: {
				action:	'saveWizard',
				'idTarget': target
			},
			onComplete: this.onSavedWizard.bind( this, target)
		});

		return false;
	},



	/**
	 * Handler evoked upon onComplete of saving from wizard. Check and notify success / error, update display
	 *
	 * @method	onSavedWizard
	 * @param	{String}			target
	 * @param	{Ajax.Response}		response
	 */
	onSavedWizard: function(target, response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('[LLL:contact.ext.company.saved.error]');

			Todoyu.Popups.setContent('popup-' + target, response.responseText);
		} else {
			Todoyu.notifySuccess('[LLL:contact.ext.company.saved.ok]');

			var label		= response.getTodoyuHeader('recordLabel');

			$(target).value = response.getTodoyuHeader('idRecord');
			$(target + '-fulltext').value = label;

			Todoyu.Popups.close('popup-' + target);
		}
	},



	/**
	 * Cancel handling for wizard: close popup
	 *
	 * @method	cancelWizard
	 */
	cancelWizard: function(form) {
		this.removeUnusedImages(form);
		Todoyu.Popups.closeLast();
	},



	/**
	 * Remove unused temporary image files
	 *
	 * @method	removeUnusedImages
	 * @param	{Element}	form
	 */
	removeUnusedImages: function(form) {
		if( form.down('[name = company[id]]').getValue() == 0 ) {
			if( form.down('[name = company[image_id]]').getValue() != 0 ) {
				var idImage	= form.down('[name=company[image_id]]').getValue();
				var url		= Todoyu.getUrl('contact', 'company');

				var options = {
					parameters: {
						action:	'removeimage',
						'idImage':	idImage
					}
				};

				Todoyu.send(url, options);
			}
		}
	}

};