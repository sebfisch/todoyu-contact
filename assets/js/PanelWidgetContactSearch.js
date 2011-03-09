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

/**
 * PanelWidget contact search
 */
Todoyu.Ext.contact.PanelWidget.ContactSearch = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.contact,

	/**
	 * Widget ID
	 *
	 * @property	id
	 * @type		String
	 */
	id:		'contactSearch',

	/**
	 * Element reference for search input
	 *
	 * @property	input
	 * @type		Element
	 */
	input:			null,

	/**
	 * Element reference for search form
	 *
	 * @property	form
	 * @type		Element
	 */
	form:			null,

	/**
	 * Element reference for search clearButton
	 *
	 * @property	clearButton
	 * @type		Element
	 */
	clearButton:	null,

	/**
	 * Delay time before sending search request
	 *
	 * @property	delayTime
	 * @type		Number
	 */
	delayTime:	0.5,

	/**
	 * Timeout ID
	 *
	 * @property	timeout
	 * @type		Object
	 */
	timeout:	null,



	/**
	 * Init the widget: install observers, initialize UI
	 *
	 * @method	init
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
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
		this.input.observe('keyup', this.onKeyup.bindAsEventListener(this));
		this.form.observe('submit', this.onFormSubmit.bindAsEventListener(this))
	},



	/**
	 * KeyUp handler (on text entered)
	 *
	 * @method	onKeyup
	 * @param	{Event}	event
	 */
	onKeyup: function(event) {
		this.toggleClearButton();

		this.clearTimeout();

		this.timeout = this.search.bind(this).delay(this.delayTime);
	},



	/**
	 * Form submit handler (prevent normal submit)
	 *
	 * @method	onFormSubmit
	 * @param	{Event}			event
	 */
	onFormSubmit: function(event) {
		event.stop();
		this.search();
	},



	/**
	 * Get current selected contact type (tab)
	 *
	 * @method	getType
	 * @return	{String}		e.g. 'person' / 'company'
	 */
	getType: function() {
		return Todoyu.Tabs.getActive('contact').id.replace('contact-tab-','');
	},



	/**
	 * Execute search request
	 *
	 * @method	search
	 */
	search: function() {
		this.toggleClearButton();

		var type	= this.getType().capitalize();

		this.ext[type].showList(this.getValue());
	},



	/**
	 * Get current search value
	 *
	 * @method	getValue
	 */
	getValue: function() {
		return $F(this.input);
	},



	/**
	 * Toggle clear button. Only visible if search text entered
	 *
	 * @method	toggleClearButton
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
	 *
	 * @method	clearTimeout
	 */
	clearTimeout: function() {
		if( this.timeout !== null ) {
			window.clearTimeout(this.timeout);
			this.timeout = null;
		}
	},



	/**
	 * Clear input field
	 *
	 * @method	clear
	 */
	clear: function() {
		this.input.clear();
		this.toggleClearButton();
		this.search();
	}

};