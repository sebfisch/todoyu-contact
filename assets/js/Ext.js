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


	/**
	 *	Initialize
	 */
	init: function() {

	},

	onTabSelect: function(event, tab) {
		this[tab.capitalize()].showList();
	},

	updateContent: function(url, options) {
		Todoyu.Ui.updateContent(url, options);
	},

	changeType: function(type) {
		Todoyu.Tabs.setActive('contact-tabhead-' + type);

		objName	= type.capitalize();

		this[objName].showList();
	},

	getType: function() {

	},

	saveType: function(type, onComplete) {
		var url		= Todoyu.getUrl('contact', 'ext');
		var options	= {
			'parameters': {
				'action':	'switchType',
				'type':		type
			}
		}

		if( typeof onComplete === 'function' ) {
			options.onComplete = onComplete;
		}

		Todoyu.send(url, options);
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
					'action': 'save',
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
			$('contact-form-content').updateContent(response.responseText);
		} else {
			alert('Saved: ' + response.responseText);
		}
	},



	/**
	 * Close form
	 */
	closeForm: function() {
		if( $('contact-main') ) {
			$($('contact-main').firstChild).remove();
		}

		this.refreshList();
	},



	/**
	 *	Refresh list
	 */
	refreshList: function()	{
		var url = Todoyu.getUrl('contact', 'contactlist');
		var options = {
			'parameters': {
				'action': 'refresh'
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
		url.action = 'edit';
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
		contentUrl = contentUrl + '&action=infoPopupContent';

		var requestOptions	= {
			'parameters': {
				'type':		type,
				'idRecord':	idRecord
			}
		};

		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, 340, contentUrl, requestOptions);
	}

};