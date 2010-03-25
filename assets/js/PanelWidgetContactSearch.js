/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSC License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * PanelWidget contact search
 */
Todoyu.Ext.contact.PanelWidget.ContactSearch = {

	/**
	 * Ext shortcut
	 */
	ext:	Todoyu.Ext.contact,

	/**
	 * Widget ID
	 */
	id:		'contactSearch',

	/**
	 * Element references
	 */
	input:			null,
	form:			null,
	clearButton:	null,

	/**
	 * Delay time before sending search request
	 */
	delayTime:	0.5,

	/**
	 * Timeout ID
	 */
	timeout:	null,



	/**
	 * Init the widget: install observers, initialize UI
	 */
	init: function() {
		this.input		= $('panelwidget-' + this.id + '-sword');
		this.form		= $('panelwidget-' + this.id + '-form');
		this.clearButton= $('panelwidget-' + this.id + '-clear');

		this.installObservers();
		this.toggleClearButton();
	},



	/**
	 * Install observers on input field and form
	 */
	installObservers: function() {
		this.input.observe('keyup', this.onKeyup.bindAsEventListener(this));
		this.form.observe('submit', this.onFormSubmit.bindAsEventListener(this))
	},



	/**
	 * KeyUp handler (on text entered)
	 * 
	 * @param	Event	event
	 */
	onKeyup: function(event) {
		this.toggleClearButton();
		
		this.clearTimeout();
		
		this.timeout = this.search.bind(this).delay(this.delayTime);		
	},



	/**
	 * Form submit handler (prevent normal submit)
	 *
	 * @param	Event	event
	 */
	onFormSubmit: function(event) {
		event.stop();
		this.search();
	},



	/**
	 * Get current selected contact type (tab)
	 *
	 * @return	String		e.g. 'person' / 'company'
	 */
	getType: function() {
		return Todoyu.Tabs.getActive('contact-tabs').id.replace('contact-tab-','');
	},



	/**
	 * Execute search request
	 */
	search: function() {
		this.toggleClearButton();

		var type	= this.getType().capitalize();

		this.ext[type].showList(this.getValue());
	},



	/**
	 * Get current search value
	 */
	getValue: function() {
		return $F(this.input);
	},



	/**
	 * Toggle clear button. Only visible if search text entered
	 */
	toggleClearButton: function() {
		if( this.getValue().strip() === '' ) {
			this.clearButton.hide();
		} else {
			this.clearButton.show();
		}
	},



	/**
	 * Clear current timeout if set
	 */
	clearTimeout: function() {
		if( this.timeout !== null ) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}
	},



	/**
	 * Clear input field
	 */
	clear: function() {
		this.input.clear();
		this.toggleClearButton();
		this.search();
	}

};