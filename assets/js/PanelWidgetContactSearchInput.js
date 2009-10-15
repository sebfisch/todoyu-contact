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

Todoyu.Ext.contact.PanelWidget.ContactSearchInput = {

	ext: Todoyu.Ext.contact,

	sendRequest:	true,
	sentValue:    	'',
	freq:			0.4,
	value:			'',

	InputField:		{},

	mainContentID:	'contact-main',

	searchFormID:	'panelwidget-searchinput',



	/**
	 *	General init of search input panel widget
	 */
	init: function()	{
		this.initSearchInput();
	},



	/**
	 *	Init search input
	 */
	initSearchInput: function()	{
		this.InputField = $('searchinput');

		if(this.InputField)	{
			this.InputField.clear();
			this.InputField.observe('keyup', this.inputFieldListener.bind(this));
			this.InputField.observe('blur', this.inputFieldListener.bind(this));
		}
	},



	/**
	 *	Init field listener
	 */
	inputFieldListener: function()	{
		if(this.value == this.sentValue)	{
			this.sendRequest = false;
		}

		if(this.value != this.InputField.getValue() && this.sendRequest == true)	{
			this.sendRequest = false;
		}

		if(this.sendRequest == false)	{
			this.sendRequest = true;
			this.value = this.InputField.getValue();
			if(this.timeout) clearTimeout(this.timeout);
			this.timeout = setTimeout(this.inputFieldListener.bind(this), this.freq*1000);
		} else if(this.InputField.getValue() != '') {
			this.sendRequest = false;
			this.sentValue = this.value;

			this.searchRequest('searchcontacts');
		}
	},



	/**
	 *	Search request
	 *
	 *	@param	String	cmd
	 */
	searchRequest: function(cmd)	{
		this.changeButtonLabelToShowAll();

		var url = Todoyu.getUrl('contact', 'contactlist');

		var options = {
			'parameters': {
				'cmd': cmd,
				'sword': this.value
			}
		};

		Todoyu.Ui.update(this.mainContentID, url, options);
	},



	/**
	 *	Show all
	 */
	showAll: function()	{
		var url = Todoyu.getUrl('contact', 'contactlist');

		this.value = '';

		this.changeButtonLabelToShowAll();

		var options = {
			'parameters': {
				'cmd': 'showAll'
			}
		}

		this.InputField.clear();

		Todoyu.Ui.update(this.mainContentID, url, options);
	},



	/**
	 *	Change button label to 'show all'
	 */
	changeButtonLabelToShowAll: function()	{
		var iconSpan = $$('#searchinput-showall span')[0];

		if(iconSpan.hasClassName('hideAll')) {
			iconSpan.replaceClassName('hideAll', 'showAll');
		} else {
			iconSpan.replaceClassName('showAll', 'hideAll');
		}
		
		var showallCheckbox	= $$('#searchinput-showall input')[0];
		showallCheckbox.checked	= ! showallCheckbox.checked;
	},



	/**
	 *	Refresh
	 */
	refresh: function()	{
		var url = Todoyu.getUrl('contact', 'panelwidgetcontactsearchinput');

		var options = {
			'parameters': {
				'cmd': 'refresh'
			},
			'onComplete': function(response)	{
				this.init();
			}.bind(this)
		};

		if($('panelwidget-contactSearchInput'))	{
			Todoyu.Ui.replace('panelwidget-contactSearchInput', url, options);
		}
	}

};