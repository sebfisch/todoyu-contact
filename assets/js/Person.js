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

Todoyu.Ext.contact.Person =  {

	ext: Todoyu.Ext.contact,

	
	
	/**
	 *	Add person (create and edit new person record) 
	 */
	add: function() {
		this.edit(0);
	},

	
	
	/**
	 *	Edit (person)
	 *
	 *	@paran	Integer		idPerson
	 */
	edit: function(idPerson) {
		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			'parameters': {
				'person': idPerson,
				'cmd': 'edit'
			},
			'onComplete': this.onEdit.bind(this, idPerson)
		};

		this.ext.updateContent(url, options);
	},

	
	
	/**
	 *	On edit (person) handler
	 *
	 *	@paran	Integer		idPerson
	 *	@paran	Unknown		response
	 */
	onEdit: function(idPerson, response) {
		this.observeFieldsForShortname(idPerson);
	},

	
	
	/**
	 *	Remove person
	 *
	 *	@paran	Integer		idPerson
	 */
	remove: function(idPerson) {
		if( confirm('[LLL:contact.person.remove.confirm]') )	{
			var url = Todoyu.getUrl('contact', 'person');
			var options = {
				'parameters': {
					'cmd':		'remove',
					'person':	idPerson
				},
				'onComplete': this.onRemoved.bind(this, idPerson)
			};

			Todoyu.send(url, options);
		}
	},

	
	
	/**
	 *	On removed handler
	 *
	 *	@paran	Integer		idPerson
	 *	@paran	Unknown		response
	 */
	onRemoved: function(idPerson, response) {
		this.showList();
	},


	
	/**
	 *	Observe fields for short name
	 *
	 *	@paran	Integer		idPerson
	 */
	observeFieldsForShortname: function(idPerson) {
		$('person-' + idPerson + '-field-lastname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
		$('person-' + idPerson + '-field-firstname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
	},

	
	
	/**
	 *	Generate short name
	 *
	 *	@paran	unknown	event
	 *	@paran	Integer	idPerson
	 */
	generateShortName: function(event, idPerson) {
		var lastname	= $F('person-' + idPerson + '-field-lastname');
		var firstname	= $F('person-' + idPerson + '-field-firstname');

		if( lastname.length >= 2 && firstname.length >= 2 ) {
			$('person-' + idPerson + '-field-shortname').value = firstname.substr(0,2).toUpperCase() + lastname.substr(0,2).toUpperCase();
		}
	},



	/**
	 *	Save form
	 *
	 *	@paran	String	form
	 */
	save: function(form) {
		$(form).request ({
				'parameters': {
					'cmd': 'save'
				},
				'onComplete': this.onSaved.bind(this)
			});

		return false;
	},

	
	
	/**
	 *	On saved handle
	 *
	 *	@paran	Array	response
	 */
	onSaved: function(response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('Saving person failed');
			$('contact-form-content').update(response.responseText);
		} else {
			Todoyu.notifySuccess('Person saved');
			this.showList();
		}
	},

	
	
	/**
	 *	Show list
	 *
	 *	@paran	String	sword
	 */
	showList: function(sword) {
		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			'parameters': {
				'cmd': 'list',
				'sword': sword
			}
		};

		this.ext.updateContent(url, options);
	},

	
	
	/**
	 *	Edit user record
	 *
	 *	@paran	Integer	idUser
	 */
	editUserRecord: function(idUser) {
		var params	= {
			'ext': 'admin',
			'mod': 'user',
			'cmd': 'edit',
			'user': idUser
		}

		location.href = '?' + Object.toQueryString(params);
	},


	
	/**
	 *	Show person
	 *
	 *	@paran	Integer	idPerson
	 */
	show: function(idPerson) {
		var url		= Todoyu.getUrl('contact', 'person')
		var options	= {
			'parameters': {
				'cmd': 'detail',
				'person': idPerson
			}
		};

		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, 340, url, options);
	}
};