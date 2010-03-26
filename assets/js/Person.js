/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
	 * Ext shortcut
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
	 * @paran	Integer		idPerson
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
	 * @paran	Integer		idPerson
	 * @paran	Unknown		response
	 */
	onEdit: function(idPerson, response) {
		this.observeFieldsForShortname(idPerson);

		this.showLoginFields(idPerson);

		$('person-' + idPerson + '-field-active').observe('change', this.showLoginFields.bind(this, idPerson));
	},



	/**
	 * Toggle display of login related fields of given person
	 *
	 * @param	Integer		idPerson
	 * @param	Event		event
	 */
	showLoginFields: function(idPerson, event) {
		var field	= $('person-' + idPerson + '-field-active');

		$('person-' + idPerson + '-fieldset-loginfields')[field.checked ? 'show' : 'hide']();
	},



	/**
	 * Delete given person record
	 *
	 * @paran	Integer		idPerson
	 */
	remove: function(idPerson) {
		if( confirm('[LLL:contact.person.delete.confirm]') )	{
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
	 * @paran	Integer		idPerson
	 * @paran	Unknown		response
	 */
	onRemoved: function(idPerson, response) {
		this.showList();
	},



	/**
	 * Start observation of modification of first- / lastname input fields (evoke auto-generation of shortname than)
	 *
	 * @paran	Integer		idPerson
	 */
	observeFieldsForShortname: function(idPerson) {
		$('person-' + idPerson + '-field-lastname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
		$('person-' + idPerson + '-field-firstname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
	},



	/**
	 * Generate person shortname from it's first- + lastname
	 *
	 * @paran	Event		event
	 * @paran	Integer		idPerson
	 */
	generateShortName: function(event, idPerson) {
		var lastname	= $F('person-' + idPerson + '-field-lastname');
		var firstname	= $F('person-' + idPerson + '-field-firstname');

		if( lastname.length >= 2 && firstname.length >= 2 ) {
			$('person-' + idPerson + '-field-shortname').value = firstname.substr(0,2).toUpperCase() + lastname.substr(0,2).toUpperCase();
		}
	},



	/**
	 * Updates working location selector with options of choosen company
	 *
	 * @param	Object	inputField
	 * @param	Object	selectedListElement
	 * @param	String	baseID
	 * @param	Mixed	selectedValue
	 * @param	Object	list
	 * @param	Object	parent
	 */
	updateCompanyAddressRecords: function(inputField, selectedListElement, baseID, selectedValue, list, parent)	{
		var idInputFieldArr		= inputField.id.split('-').without('fulltext')
		var referencedFieldName = parent.acRefs[baseID].options.referencedFieldName
		var idTarget = idInputFieldArr.join('-').replace(idInputFieldArr.last(), referencedFieldName);

		if($(idTarget))	{
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
					'parameters': {
						'action':		'getCompanyAddressOptions',
						'idCompany':	selectedValue
				}
			};

			Todoyu.Ui.update(idTarget, url, options);
		}
	},



	/**
	 * Save person form
	 *
	 * @paran	String		form
	 * @return	Boolean
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
	 * Handler evoked upon onComplete of person saving: check for and notify success / error, update display
	 *
	 * @paran	Array	response
	 */
	onSaved: function(response) {
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('[LLL:contact.person.saved.error]');
			$('contact-form-content').update(response.responseText);
		} else {
			Todoyu.notifySuccess('[LLL:contact.person.saved]');
			this.showList();
		}
	},


	/**
	 * Close form by reloading the persons list
	 */
	closeForm: function() {
		this.showList();
	},



	/**
	 * Show (filtered) persons list
	 *
	 * @paran	String		sword		(search word)
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
	 * @paran	Integer		idPerson
	 */
	show: function(idPerson) {
		var url		= Todoyu.getUrl('contact', 'person')
		var options	= {
			'parameters': {
				'action':	'detail',
				'person':	idPerson
			}
		};

		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, url, options);
	},



	/**
	 * Save person record from wizard
	 *
	 * @param	Object		form
	 * @param	String		target
	 * @return	Boolean
	 */
	saveWizard: function(form, target)	{
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
	 * @param	String	target
	 * @param	Object	response
	 */
	onSavedWizard: function(target, response)	{
		var error	= response.hasTodoyuError();

		if( error ) {
			Todoyu.notifyError('Saving person failed');

			Todoyu.Popup.getContentElement('popup-'+target).update(response.responseText);
		} else {
			Todoyu.notifySuccess('Person saved');

			var label		= response.getTodoyuHeader('recordLabel');
			var idRecord	= response.getTodoyuHeader('idRecord');

			$(target).value = idRecord;
			$(target + '-fulltext').value = label;

			Todoyu.Popup.close('popup-'+target);
		}
	},



	/**
	 * Cancel handling for wizard: close popup
	 */
	cancelWizard: function()	{
		Todoyu.Popup.getLastPopup().close();
	}

};