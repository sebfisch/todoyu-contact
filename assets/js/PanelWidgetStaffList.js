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

Todoyu.Ext.contact.PanelWidget.StaffList = {

	/**
	 * Extension backlink
	 *
	 * @var	{Object}	ext
	 */
	ext: Todoyu.Ext.contact,

	fulltextTimeout: null,

	filters: {},



	/**
	 * Initialize panelWidget
	 *
	 * @method	init
	 * @param	{Object}		filters		Filter hash. Because of JSON, an (empty) array means no data
	 */
	init: function(filters) {
			// If filters are given as parameters, add them to internal storage
		if( typeof(filters) === 'object' && ! Object.isArray(filters) ) {
			$H(filters).each(function(pair){
				this.applyFilter(pair.key, pair.value, false);
			}, this);
		}

		this.observeFulltext();
		this.observePersons();
	},



	/**
	 * Install keyup event observer on full-text search input field
	 *
	 * @method	observeFulltext
	 */
	observeFulltext: function() {
		$('panelwidget-stafflist-field-fulltext').observe('keyup', this.onFulltextKeyup.bindAsEventListener(this));
	},



	/**
	 * Install click event observer on items of persons list
	 *
	 * @method	observePersons
	 */
	observePersons: function() {
		$('panelwidget-stafflist-list').observe('click', this.onPersonClick.bindAsEventListener(this));
	},



	/**
	 * Handler for keyup events of full-text search input field
	 *
	 * @method	onFulltextKeyup
	 * @param	{Event}		event
	 */
	onFulltextKeyup: function(event) {
		this.clearTimeout();
		this.applyFilter('fulltext', this.getFulltext());

		this.startTimeout();
	},



	/**
	 * Click event handler for person: save pref, execute callbacks
	 *
	 * @method	onPersonClick
	 * @param	{Event}			event
	 */
	onPersonClick: function(event) {
		var listElement = event.findElement('li');

		if( Object.isElement(listElement) ) {
			var idPerson = listElement.id.split('-').last();

			this.ext.savePref('panelwidgetstafflist', idPerson);
			Todoyu.Hook.exec('panelwidget.stafflist.onPersonClick', idPerson);
		}
	},



	/**
	 * Clear (full-text) timeout
	 *
	 * @method	clearTimeout
	 */
	clearTimeout: function() {
		clearTimeout(this.fulltextTimeout);
	},



	/**
	 * Install full-text timeout
	 *
	 * @method	startTimeout
	 */
	startTimeout: function() {
		this.fulltextTimeout = this.update.bind(this).delay(0.3);
	},



	/**
	 * Get full-text input field value
	 *
	 * @method	getFulltext
	 */
	getFulltext: function() {
		return $F('panelwidget-stafflist-field-fulltext');
	},



	/**
	 * Apply filter to staff list panelwidget
	 *
	 * @method	applyFilter
	 * @param	{String}		name
	 * @param	{String}		value
	 * @param	{Boolean}		update
	 */
	applyFilter: function(name, value, update) {
		this.filters[name] = value;

		if( update === true ) {
			this.clearTimeout();
			this.update();
		}
	},



	/**
	 * Refresh staff list panelWidget
	 *
	 * @method	update
	 */
	update: function() {
		var url		= Todoyu.getUrl('contact', 'panelwidgetstafflist');
		var options	= {
			'parameters': {
				'action':	'list',
				'filters':	Object.toJSON(this.filters)
			},
			'onComplete':	this.onUpdated.bind(this)
		};
		var target	= 'panelwidget-stafflist-list';

		Todoyu.Ui.replace(target, url, options);
	},



	/**
	 * Handler to be evoked after refresh of project list panelWidget
	 *
	 * @method	onUpdated
	 * @param	{Ajax.Response}		response
	 */
	onUpdated: function(response) {
		this.observePersons();
	},



	/**
	 * Check whether given project is listed in panelWidget's project list
	 *
	 * @method	isPersonListed
	 * @param	{Number}		idPerson
	 * @return  {Boolean}
	 */
	isPersonListed: function(idPerson) {
		return Todoyu.exists('panelwidget-stafflist-person-' + idPerson);
	}

};