/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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

/**
 * Main contact object
 *
 * @class		Contact
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.contact = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},



	/**
	 * Initialize
	 *
	 * @method	init
	 */
	init: function() {
		this.initObservers();

	},



	/**
	 * @method	initObservers
	 */
	initObservers: function() {
		if( Todoyu.getArea() === 'contact' ) {
			this.initPersonQuickInfos();
			this.initListingObserver();
		}
	},



	/**
	 * @method	initPersonQuickInfos
	 */
	initPersonQuickInfos: function() {
		$$('.quickInfoPerson').each(function(element) {
			Todoyu.Ext.contact.QuickInfoPerson.add(element.id);
		});
	},



	/**
	 * Install observer on contact listing
	 *
	 * @method	initObservers
	 */
	initListingObserver: function() {
		var typeKey	= this.getActiveTypeKey();

		if( Todoyu.exists('paging-' + typeKey) ) {
			$('paging-' + typeKey).on('click', 'td', this.onClickListTD.bind(this, typeKey));
		}
	},



	/**
	 * @method	onClickListTD
	 * @param	{String}		typeKey		'person'/'company'
	 * @param	{Event}			event
	 */
	onClickListTD: function(typeKey, event) {
		var parentSpan	= event.target.up('span');
		if( event.target.hasClassName('actions') || parentSpan && parentSpan.hasClassName('actions') ) {
			return ;
		}

		var itemID	= event.target.up('tr').id.split('-').last();
		switch( typeKey ) {
			case 'person':
				this.Person.show(itemID);
				break;
			case 'company':
				this.Company.show(itemID);
				break;
		}
	},



	/**
	 * Handler to be called on selecting tabs of contact
	 *
	 * @method	onTabSelect
	 * @param	{Event}		event
	 * @param	{String}	tab
	 */
	onTabSelect: function(event, tab) {
		this[tab.capitalize()].showList();
	},



	/**
	 * Update contact page content with response of AJAX request with given URL + options
	 *
	 * @method	updateContent
	 * @param	{String}		url
	 * @param	{Array}			options
	 * @param	{String}		type
	 */
	updateContent: function(url, options, type) {
		var typeKey	= type || url.split('controller=')[1];

		options.onComplete	= this.onContentUpdated.bind(this, typeKey, options.onComplete);

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Handler when content body has been updated - reinit listing observer
	 *
	 * @method	onContentUpdated
	 * @param	{String}		type		Type: person or company
	 * @param	{Function}		onComplete
	 * @param	{Ajax.Response}	response
	 */
	onContentUpdated: function(type, onComplete, response) {
		this.initObservers();

		this.setTabActive(type);

		if( onComplete ) {
			onComplete(response);
		}
	},



	/**
	 * Set tab active
	 *
	 * @method	setTabActive
	 * @param	{String}	type
	 */
	setTabActive: function(type) {
		Todoyu.Tabs.setActive('contact', type);
	},



	/**
	 * Switch display of contacts type to given type
	 *
	 * @method	changeType
	 * @param	{String}		type
	 */
	changeType: function(type) {
		this.setTabActive(type);

		var typeKey = type.capitalize();
		this[typeKey].showList();
	},



	/**
	 * Get key of currently active contact type
	 *
	 * @method	getActiveType
	 * @param	{String}		listName
	 * @return	{String}		'company' / 'person'
	 */
	getActiveTypeKey: function(listName) {
		listName	= listName || 'contact';

		return	Todoyu.Tabs.getActiveKey(listName);
	},



	/**
	 * Save contact pref
	 *
	 * @method	savePref
	 * @param	{String}	preference
	 * @param	{String}	value
	 * @param	{Number}	idItem
	 * @param	{String}	onComplete
	 */
	savePref: function(preference, value, idItem, onComplete) {
		Todoyu.Pref.save('contact', preference, value, idItem, onComplete);
	},



	/**
	 * Remove unused temporary contact (person / company) image files
	 *
	 * @method	removeUnusedImages
	 * @param	{Element}	form
	 * @param	{String}	typeKey		'person' / 'image'
	 */
	removeUnusedImages: function(form, typeKey) {
		if( form.down('[name = ' + typeKey + '[id]]').getValue() == 0 ) {
			if( form.down('[name = ' + typeKey + '[image_id]]').getValue() != 0 ) {
				var idImage	= form.down('[name=' + typeKey + '[image_id]]').getValue();
				var url		= Todoyu.getUrl('contact', typeKey);

				var options = {
					parameters: {
						action:		'removeimage',
						idImage:	idImage
					}
				};

				Todoyu.send(url, options);
			}
		}
	}

};