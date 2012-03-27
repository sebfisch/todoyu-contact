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
		$('content-body').select('span.quickInfoPerson').each(function(element) {
			Todoyu.Ext.contact.QuickInfoPerson.add(element.id);
		})
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
	 * @param	{Array}		options
	 */
	updateContent: function(url, options) {
		options.onComplete	= this.onContentUpdated.bind(this);

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Handler when content body has been updated - reinit listing observer
	 *
	 * @method	onContentUpdated
	 */
	onContentUpdated: function() {
		this.initObservers();
	},



	/**
	 * Switch display of contacts type to given type
	 *
	 * @method	changeType
	 * @param	{String}		type
	 */
	changeType: function(type) {
		Todoyu.Tabs.setActive('contact', type);

		var objName = type.capitalize();

		this[objName].showList();
	},



	/**
	 * Get key of currently active contact type
	 *
	 * @method	getActiveType
	 * @return	{String}		'company' / 'person'
	 */
	getActiveTypeKey: function() {
		return	Todoyu.Tabs.getActiveKey('contact');
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
	}

};