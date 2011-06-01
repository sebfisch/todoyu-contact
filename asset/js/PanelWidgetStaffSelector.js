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
 * @module	Contact
 */

/**
 * Panel widget: staff selector
 *
 * @class
 * @extends		Todoyu.PanelWidgetSearchList
 */
Todoyu.Ext.contact.PanelWidget.StaffSelector = Class.create(Todoyu.PanelWidgetSearchList, {

	/**
	 * Selection element
	 * @var	{Element}	selection
	 */
	selection: null,

	/**
	 * Timeout for save request
	 */
	timeoutSave: null,



	/**
	 * Constructor Initialize with search word
	 *
	 * @method	initialize
	 * @param	{Function}	$super
	 * @param	{String}	search
	 */
	initialize: function($super, search) {
		$super({
			id:			'staffselector',
			search:		search,
			ext:		'contact',
			controller:	'panelwidgetstaffselector',
			action:		'list'
		});

		this.config.actionSelection	= 'save';
			// Save reference to selection list
		this.selection	= $('panelwidget-staffselector-selection');

			// Observe selection list for disable and remove clicks
		this.selection.on('click', 'li', this.onSelectionItemClick.bind(this));
		this.selection.on('click', 'li span.remove', this.onRemoveClick.bind(this));

			// Observe input for return clicks
		this.input.on('keyup', this.onInputKeyUps.bind(this));

		this.addAddIconsToList();
		this.addRemoveIconsToList();

		this.markFirstAsHot();
	},



	/**
	 * Handler when clicked on item
	 *
	 * @method	onItemClick
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onItemClick: function(event, item) {
		this.addItemToSelection(item);
		this.saveSelection();
	},



	/**
	 * Handler for keyup event in search input
	 * Handle return key press to add the hot element
	 * to the selection
	 *
	 * @method	onInputKeyUps
	 * @param	{Event}		event
	 */
	onInputKeyUps: function(event) {
		if( event.keyCode === Event.KEY_RETURN ) {
			var firstItem	= this.list.down('li');
			if( firstItem !== undefined ) {
				this.addItemToSelection(firstItem);
				this.input.select();
				this.saveSelection();
				this.markFirstAsHot();
			}
		}
	},



	/**
	 * Mark first item in result list as hot
	 * Hot means, when the user presses return,
	 * this item will be added to the selection
	 *
	 * @method	markFirstAsHot
	 */
	markFirstAsHot: function() {
		this.list.select('li').invoke('removeClassName', 'hot');

		var first	= this.list.down('li');

		if( first ) {
			first.addClassName('hot');
		}
	},



	/**
	 * Add an item to the selection list
	 *
	 * @method	addItemToSelection
	 * @param	{Element}	item
	 */
	addItemToSelection: function(item) {
			// Remove 'no items' label if no items are in selection
		if( this.isSelectionEmpty() ) {
			this.selection.update('');
		}

			// Move item to selection
		this.selection.insert({
			bottom: item
		});

			// Add remove button
		this.addRemoveIconsToList([item]);

			// Remove add button
		item.down('span.add').remove();

			// Sort items
		this.sortSelection();

			// Highlight new item
		new Effect.Highlight(item, {
			duration: 2.0,
			afterFinish: function() {
				this.removeAttribute('style');
			}.bind(item)
		});
	},


	/**
	 * Sort selection items
	 * First groups then persons by alphabet
	 */
	sortSelection: function() {
		var nodes		= this.selection.select('li');
		var hashPersons	= {};
		var hashGroups	= {};

		nodes.each(function(item){
			var hash = item.hasClassName('person') ? hashPersons : hashGroups;
			var label	= item.down('a').innerHTML.stripTags().strip();
			hash[label] = item;
		});

		var sortedKeysPersons	= Object.keys(hashPersons).sort();
		var sortedKeysGroups	= Object.keys(hashGroups).sort();

		this.selection.update('');

		sortedKeysGroups.each(function(key){
			this.selection.insert(hashGroups[key]);
		}, this);

		sortedKeysPersons.each(function(key){
			this.selection.insert(hashPersons[key]);
		}, this);
	},



	/**
	 * Handler when clicked on a selection item
	 * Remove it from selection
	 *
	 * @method	onSelectionItemClick
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onSelectionItemClick: function(event, item) {
		if( ! event.element().hasClassName('remove') ) {
			item.toggleClassName('disabled');
			this.saveSelection();
		}
	},



	/**
	 * Handler when clicked on remove icon
	 *
	 * @method	onRemoveClick
	 * @param	{Event}		event
	 * @param	{Element}	removeIcon
	 */
	onRemoveClick: function(event, removeIcon) {
		event.stop();

		var item	= removeIcon.up('li');

		new Effect.SlideUp(item, {
			duration: 0.3,
			afterFinish: function() {
				if( item.parentNode ) {
					item.remove();
				}
				this.addMessageIfSelectionEmpty();
				this.saveSelection();
			}.bind(this)
		});
	},



	/**
	 * When selection contains no item, add place holder label
	 *
	 * @method	addMessageIfSelectionEmpty
	 */
	addMessageIfSelectionEmpty: function() {
		if( this.isSelectionEmpty() ) {
			this.selection.update('<p>[LLL:contact.panelwidget-staffselector.selection.empty]</p>');
		}
	},



	/**
	 * Check whether selection contains any items
	 *
	 * @method	isSelectionEmpty
	 * @return	{Boolean}
	 */
	isSelectionEmpty: function() {
		return this.selection.down('li') === undefined;
	},



	/**
	 * Save selected items
	 *
	 * @method	saveSelection
	 */
	saveSelection: function(noDelay) {
		clearTimeout(this.timeoutSave);
		if( noDelay !== true ) {
			this.timeoutSave = this.saveSelection.bind(this, true).delay(0.5);
			return ;
		}

		var items	= this.getSelectedItems();
		var url		= Todoyu.getUrl(this.config.ext, this.config.controller);
		var options	= {
			parameters: {
				action:		this.config.actionSelection,
				selection:	items.join(',')
			},
			onComplete: this.onSelectionSaved.bind(this, items)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when selection was saved
	 * Fire change event to notify other about the change
	 *
	 * @method	onSelectionSaved
	 * @param	{Array}			items
	 * @param	{Ajax.Response}	response
	 */
	onSelectionSaved: function(items, response) {
		Todoyu.PanelWidget.fire('staffselector', items);
	},



	/**
	 * Get item IDs from selection list
	 * Disabled items are prefixed with a minus
	 *
	 * @method	getSelectedItems
	 * @return	{Array}				selected items' IDs
	 */
	getSelectedItems: function() {
		return this.selection.select('li').collect(function(item){
			var itemKey	= item.id.split('-').last();
			var prefix	= item.hasClassName('disabled') ? '-' : '';

			return prefix + itemKey;
		});
	},



	/**
	 * Handler when list was updated
	 *
	 * @method	onUpdated
	 */
	onUpdated: function() {
		this.addAddIconsToList();
		this.markFirstAsHot();
	},



	/**
	 * Add adding icons to all items in the search list
	 *
	 * @method	addAddIconsToList
	 */
	addAddIconsToList: function() {
		this.list.select('li a').each(function(item){
			item.insert(new Element('span', {
				'class': 'add'
			}));
		});
	},



	/**
	 * Add removing icons to items. If no items are provided,
	 * add to all in the selection list
	 *
	 * @method	addRemoveIconsToList
	 * @param	{Array}	items
	 */
	addRemoveIconsToList: function(items) {
		items	= items || this.selection.select('li');

		items.each(function(item){
			item.down('a').insert(new Element('span', {
				'class': 'remove'
			}));
		});
	},



	/**
	 * Check whether any item is selected or not
	 */
	isAnyPersonSelected: function() {
		return this.getSelectedItems().size() > 0;
	},



	/**
	 * Get all selected elements (persons)
	 * Gets also group and other types
	 */
	getSelectedPersons: function() {
		var items	= this.getSelectedItems();

		return this.getSelectedItems().findAll(function(item){
			return item.substr(0, 1) !== '-';
		});
	}

});