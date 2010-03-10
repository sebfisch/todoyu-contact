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
	 * Initialize
	 */
	init: function() {

	},



	/**
	 * Handler to be called on selecting tabs of contact
	 * 
	 * @param	Object		event
	 * @param	String		tab
	 */
	onTabSelect: function(event, tab) {
		this[tab.capitalize()].showList();
	},



	/**
	 * Update contact page content with response of AJAX request with given URL + options
	 * 
	 * @param	String		url
	 * @param	Array		options 
	 */
	updateContent: function(url, options) {
		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Switch display of contacts type to given type
	 * 
	 * @param	String		type
	 */
	changeType: function(type) {
		Todoyu.Tabs.setActive('contact', type);

		objName	= type.capitalize();

		this[objName].showList();
	}

};