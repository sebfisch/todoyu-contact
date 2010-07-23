/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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

Todoyu.Ext.contact.QuickInfoPerson = {

	/**
	 * Ext shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext:		Todoyu.Ext.contact,



	/**
	 * Selector for event quickinfo
	 */
	selector:	'.quickInfoPerson',


	/**
	 * Install quickinfo for events
	 */
	install: function() {
		//Todoyu.QuickInfo.install('person', this.selector, this.getID.bind(this));
	},



	/**
	 * Uninstall quickinfo for events
	 */
	uninstall: function() {
		Todoyu.QuickInfo.uninstall(this.selector);
	},



	/**
	 * Add a quickinfo to a single element
	 *
	 * @param	{String}	idElement
	 */
	add: function(idElement) {
		Todoyu.QuickInfo.install('person', '#' + idElement, this.getID.bind(this));
	},



	/**
	 * Remove a quickinfo from a single element
	 *
	 * @param	{String}	idElement
	 */
	remove: function(idElement) {
		Todoyu.QuickInfo.uninstall('#' + idElement);
	},



	/**
	 * Get ID form observed element
	 *
	 * @param	{Element}	element
	 * @param	{Event}		event
	 */
	getID: function(element, event) {
		return $(element).id.split('-').last();
	},



	/**
	 * Remove given calendar event quickinfo element from cache
	 *
	 * @param	{Number}	idPerson
	 */
	removeFromCache: function(idPerson) {
		Todoyu.QuickInfo.removeFromCache('person' + idPerson);
	}

};