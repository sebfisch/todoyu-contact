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

Todoyu.Ext.contact.Person =  {

	/**
	 * Extension backlink
	 *
	 * @var	{Object}	ext
	 */
	ext:	Todoyu.Ext.contact,



	/**
	 * Add person (create and edit new person record)
	 */
	add: function() {
		this.edit(0);
	},



	/**
	 * Edit (person)
	 *
	 * @method	edit
	 * @param	{Number}		idPerson
	 */
	edit: function(idPerson) {
		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			'parameters': {
				'action':	'edit',
				'person':	idPerson
			},
			'onComplete': this.onEdit.bind(this, idPerson)
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * On edit (person) handler
	 *
	 * @method	onEdit
	 * @param	{Number}			idPerson
	 * @param	{Ajax.Response}		response
	 */
	onEdit: function(idPerson, response) {
		this.initEditForm(idPerson);
	},



	/**
	 * Initialize edit form
	 *
	 * @method	initEditForm
	 * @param	{Number}		idPerson
	 */
	initEditForm: function(idPerson) {
		this.observeFieldsForShortname(idPerson);

		this.showLoginFields(idPerson);

		$('person-' + idPerson + '-field-active').observe('change', this.showLoginFields.bind(this, idPerson));
	},



	/**
	 * Toggle display of login related fields of given person
	 *
	 * @method	showLoginFields
	 * @param	{Number}		idPerson
	 * @param	{Event}		event
	 */
	showLoginFields: function(idPerson, event) {
		var field	= $('person-' + idPerson + '-field-active');

		$('person-' + idPerson + '-fieldset-loginfields')[field.checked ? 'show' : 'hide']();
	},



	/**
	 * Delete given person record
	 *
	 * @method	remove
	 * @param	{Number}		idPerson
	 */
	remove: function(idPerson) {
		if( confirm('[LLL:contact.ext.person.delete.confirm]') ) {
			var url = Todoyu.getUrl('contact', 'person');
			var options = {
				'parameters': {
					'action':	'remove',
					'person':	idPerson
				},
				'onComplete': this.onRemoved.bind(this, idPerson)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler being evoked after onComplete of person deletion: update listing display
	 *
	 * @method	onRemoved
	 * @param	{Number}			idPerson
	 * @param	{Ajax.Response}		response
	 */
	onRemoved: function(idPerson, response) {
		this.showList(this.ext.PanelWidget.ContactSearch.getValue());
	},



	/**
	 * Start observation of modification of first- / lastname input fields (evoke auto-generation of shortname than)
	 *
	 * @method	observeFieldForShortname
	 * @param	{Number}		idPerson
	 */
	observeFieldsForShortname: function(idPerson) {
		$('person-' + idPerson + '-field-lastname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
		$('person-' + idPerson + '-field-firstname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
	},



	/**
	 * Generate person short name from it's first- + lastname
	 *
	 * @method	generateShortName
	 * @param	{Event}		event
	 * @param	{Number}	idPerson
	 */
	generateShortName: function(event, idPerson) {
		var lastname	= $F('person-' + idPerson + '-field-lastname');
		var firstname	= $F('person-' + idPerson + '-field-firstname');

		if( lastname.length >= 2 && firstname.length >= 2 ) {
			$('person-' + idPerson + '-field-shortname').value = firstname.substr(0,2).toUpperCase() + lastname.substr(0,2).toUpperCase();
		}
	},



	/**
	 * Updates working location selector with options of chosen company
	 *
	 * @method	updateCompanyAdressRecords
	 * @param	{Object}	inputField
	 * @param	{Object}	selectedListElement
	 * @param	{String}	baseID
	 * @param	{Mixed}	selectedValue
	 * @param	{Object}	list
	 * @param	{Object}	parent
	 */
	updateCompanyAddressRecords: function(inputField, idField, selectedValue, selectedText, autocompleter) {
		var refFieldName	= autocompleter.options['referencedFieldName'].replace('_', '-');
		var baseID			= idField.id.substr(0, idField.id.indexOf('-field-') + 6);
		var idAddressList	= baseID + '-' + refFieldName;

		if( Todoyu.exists(idAddressList) ) {
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
				'parameters': {
					'action':		'getCompanyAddressOptions',
					'idCompany':	selectedValue
				},
				'onComplete': this.onUpdateCompanyAddressRecords.bind(this, $(idAddressList))
			};

			Todoyu.Ui.update(idAddressList, url, options);
		}
	},



	/**
	 * Highlights the referenced selector of company address after updating the company-autocompleter
	 *
	 * @method	onUpdateCompanyAddressRecords
	 * @param	{String}	idTarget
	 */
	onUpdateCompanyAddressRecords: function(addressList) {
		new Effect.Highlight($(addressList), {
			'startcolor':	'#fffe98',
			'endcolor':		'#ffffff',
			'duration':		2.0
		});
	},



	/**
	 * Save person form
	 *
	 * @method	save
	 * @param	{String}		form
	 * @return	{Boolean}
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

			this.showList(this.ext.PanelWidget.ContactSearch.getValue());
		}
	},


	/**
	 * Close form by reloading the persons list
	 *
	 * @method	closeForm
	 */
	closeForm: function(form) {
		this.removeUnusedImages(form);
		this.showList(this.ext.PanelWidget.ContactSearch.getValue());
	},



	/**
	 * Show (filtered) persons list
	 *
	 * @method	showList
	 * @param	{String}		sword		(search word)
	 */
	showList: function(sword) {
		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			'parameters': {
				'action':	'list',
				'sword':	sword
			}
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * Show info popup to given person data
	 *
	 * @method	show
	 * @param	{Number}		idPerson
	 */
	show: function(idPerson) {
		var url		= Todoyu.getUrl('contact', 'person');
		var options	= {
			'parameters': {
				'action':	'detail',
				'person':	idPerson
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
			'parameters': {
				'action':	'saveWizard',
				'idTarget': target
			},
			'onComplete': this.onSavedWizard.bind( this, target)
		});

		return false;
	},



	/**
	 * Handler evoked upon onComplete of saving from wizard. Check and notify success / error, update display
	 *
	 * @method	onSaveWizard
	 * @param	{String}			target
	 * @param	{Ajax.Response}		response
	 */
	onSavedWizard: function(target, response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('[LLL:contact.ext.person.saved.error]');

			Todoyu.Popup.getContentElement('popup-' + target).update(response.responseText);
		} else {
			Todoyu.notifySuccess('[LLL:contact.ext.person.saved]');

			var label		= response.getTodoyuHeader('recordLabel');

			$(target).value = response.getTodoyuHeader('idRecord');
			$(target + '-fulltext').value = label;

			Todoyu.Popup.close('popup-' + target);
		}
	},



	/**
	 * Cancel handling for wizard: close popup
	 *
	 * @method	cancelWizard
	 */
	cancelWizard: function(form) {
		this.removeUnusedImages(form);
		Todoyu.Popup.getLastPopup().close();
	},



	/**
	 *
	 * @param form
	 */
	removeUnusedImages: function(form) {
		if( form.down('[name = person[id]]').getValue() == 0 ) {
			if( form.down('[name = person[image_id]]').getValue() != 0 ) {
				var idImage	= form.down('[name=person[image_id]]').getValue();
				var url		= Todoyu.getUrl('contact', 'person');

				var options = {
					'parameters': {
						'action':	'removeimage',
						'idImage':	idImage
					}
				}

				Todoyu.send(url, options);
			}
		}
	}


};