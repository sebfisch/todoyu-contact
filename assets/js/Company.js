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

Todoyu.Ext.contact.Company =  {

	/**
	 * Ext shortcut
	 */
	ext:	Todoyu.Ext.contact,



	add: function() {
		this.edit(0);
	},



	/**
	 * Edit given company
	 *
	 * @param	Integer	idCompany
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
	 * @param	Integer	idCompany
	 */
	remove: function(idCompany) {
		if( confirm('[LLL:contact.confirmRemoving]') )	{
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
	 * @param	Response	response
	 */
	onRemoved: function(response) {
		this.showList();
	},



	/**
	 * Save company record
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
	 * Handler being evoked OnComplete of save company request: check for and notify success / error, update display
	 * 
	 * @param	Object	response
	 */
	onSaved: function(response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('[LLL:contact.company.save.error]');
			$('contact-form-content').update(response.responseText);
		} else {
				// Notify (implement)
			Todoyu.notifySuccess('[LLL:contact.company.save.ok]');
			this.showList();
		}
	},



	/**
	 * Close company form, update list view
	 */
	closeForm: function() {
		this.showList();
	},



	/**
	 * Update company list
	 * 
	 * @param	String		sword		(search word)
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
	 * @param	Integer		idCompany
	 */
	show: function(idCompany) {
		var url		= Todoyu.getUrl('contact', 'company')
		var options	= {
			'parameters': {
				'action':	'detail',
				'company':	idCompany
			}
		};

		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, url, options);
	}

};