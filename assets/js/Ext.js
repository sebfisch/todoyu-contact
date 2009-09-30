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

Todoyu.Ext.contact = {

	PanelWidget: {},

	Headlet: {},

	curContactInfoType: 'person',


	/**
	 *	Initialize
	 */
	init: function()	{
		
	},
	
	onTabSelect: function(event, tab) {
		//this.saveType(tab, this.)
		
		console.log(tab);
	},
	
	saveType: function(type, onComplete) {
		var url		= Todoyu.getUrl('contact', 'ext');
		var options	= {
			'parameters': {
				'cmd': 'switchType',
				'type': type
			}			
		}
		
		if( typeof onComplete === 'function' ) {
			options.onComplete = onComplete;
		}
		
		Todoyu.send(url, options);		
	},
	
	
	update: function(url, options) {
		Todoyu.Ui.update('contact-main', url, options);
	},



	/**
	 *	Load contact info type
	 *
	 *	@param	String	contactInfoType
	 */
	loadContactInfoType: function(contactInfoType)	{
		var url = Todoyu.getUrl('contact', 'setcontactinfotype');
		var options = {
			'parameters': {
				'contactInfoType': contactInfoType
			},
			'onComplete': function(response)	{
				this.changeContactInfoType('', contactInfoType);
				this.refreshList();
				this.PanelWidget.ContactSearchInput.refresh();
				this.PanelWidget.QuickContact.refresh();
			}.bind(this)
		}



		Todoyu.send(url, options);
	},



	/**
	 *	Change contact info type
	 *
	 *	@param	unknown	event
	 *	@param	unknown	newContactInfoType
	 */
	changeContactInfoType: function(event, newContactInfoType)	{
		oldTabID = 'contact-tabhead-'+this.curContactInfoType;
		newTabID = 'contact-tabhead-'+newContactInfoType;

		if($(oldTabID))	{
			if($(oldTabID).hasClassName('active'))	{
				$(oldTabID).removeClassName('active');
			}
		}

		$(newTabID).addClassName('active');

		this.curContactInfoType = newContactInfoType;
	},



	/**
	 *	Load form
	 *
	 *	@param	Integer	idRecord
	 */
	loadForm: function(idRecord)	{
		var url = Todoyu.getUrl('contact', 'formhandling');
		var options = {
			'parameters': {
				'editID': idRecord,
				'cmd': 'showForm'
			}
		};

		Todoyu.Ui.update('contact-main', url, options);
	},



	/**
	 *	Save contact
	 *
	 *	@param	Object	form
	 *	@return	Boolean
	 */
	save: function(form)	{
		$(form).request ({
				'parameters': {
					'cmd': 'save',
					'form': form.name
				},
				'onComplete': this.onSaved.bind(this)
			});

		return false;
	},



	/**
	 *	'onSaved' custom event handler
	 *
	 *	@param	JSON	response
	 */
	onSaved: function(response) {
		var error	= response.hasTodoyuError();
		
		if( error ) {
			$('contact-form-content').update(response.responseText);
		} else {
			alert('Saved: ' + response.responseText);
		}
		
		/*
		var JSON = response.responseJSON;

		if(JSON.saved == true)	{
			this.closeForm();
		} else {
			$('contact-form-content').update(JSON.formHTML);
		}
		*/
	},



	/**
	 *	Remove entry
	 *
	 *	@param	Integer	idEntry
	 */
	removeEntry: function(idEntry)	       {
		if(confirm('[LLL:contact.confirmRemoving]'))	{
			var url = Todoyu.getUrl('contact', 'formhandling');
			var options = {
				'parameters': {
					'cmd':		'removeEntry',
					'removeID':	idEntry
				},
				'onComplete': this.onEntryRemoved.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 *	'onEntryRemoved' event handler
	 *
	 *	@param	unknown	response
	 */
	onEntryRemoved: function(response) {
		this.refreshList();
	},



	/**
	 * Close form
	 */
	closeForm: function()	{
		$($('contact-main').firstChild).remove();
		this.refreshList();
	},



	/**
	 *	Refresh list
	 */
	refreshList: function()	{
		var url = Todoyu.getUrl('contact', 'contactlist');
		var options = {
			'parameters': {
				'cmd': 'refresh'
			}
		};

		Todoyu.Ui.update('contact-main', url, options);
	},



	/**
	 *	Generate (and set) person shortname from person's first and lastname
	 *	@todo	add AJAX request to avoid multiple identic shortnames
	 *
	 *	@param	String	idButton
	 */
	generatePersonShortname: function(idButton) {
		var idPerson	= idButton.split('-')[1];
		var lastname	= $F('person-' + idPerson + '-field-lastname');
		var firstname	= $F('person-' + idPerson + '-field-firstname');
		var shortname	= (firstname.substring(0, 2) + lastname.substring(0, 2)).toUpperCase();

		$('person-' + idPerson + '-field-shortname').value = shortname;
	},



	/**
	 *	Tab JS handler
	 */
	tabJsHandler: function()	{
		//do nothing;
	},



	/**
	 *	Edit login user
	 *
	 *	@param	Integer	idUser
	 */
	editLoginUser: function(idUser)	{
		var url = {ext: 'admin'};

		url.mod = 'user';
		url.cmd = 'edit';
		url.user = idUser;

		location.href = '?' + Object.toQueryString(url);
	},


	/**
	 *	Open popup with record info
	 *
	 *	@param	String	type		'user' / 'company'
	 *	@param	Integer	idRecord
	 */
	openInfoPopup: function(type, idRecord) {

		var contentUrl = Todoyu.getUrl('contact', 'contactlist');
		contentUrl = contentUrl + '&cmd=infoPopupContent';

		var requestOptions	= {
			'parameters': {
				'type':		type,
				'idRecord':	idRecord
			}
		};

		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, 340, 810, 200, contentUrl, requestOptions);
	}

};