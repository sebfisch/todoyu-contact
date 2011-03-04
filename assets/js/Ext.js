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

	},



	/**
	 * Handler to be called on selecting tabs of contact
	 *
	 * @method	onTabSelect
	 * @param	{Event}		event
	 * @param	{String}	tab
	 */
	onTabSelect: function(event, tab) {
		this[tab.capitalize()].showList(Todoyu.Ext.contact.PanelWidget.ContactSearch.getValue());
	},



	/**
	 * Update contact page content with response of AJAX request with given URL + options
	 *
	 * @method	updateContent
	 * @param	{String}		url
	 * @param	{Array}		options
	 */
	updateContent: function(url, options) {
		Todoyu.Ui.updateContent(url, options);
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

		this[objName].showList(Todoyu.Ext.contact.PanelWidget.ContactSearch.getValue());
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