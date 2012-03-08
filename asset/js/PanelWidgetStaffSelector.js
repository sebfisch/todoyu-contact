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
	 * Save group button
	 * @var	{Element}	buttonSaveGroup
	 */
	buttonSaveGroup: null,


	/**
	 * Constructor Initialize with search word
	 *
	 * @method	initialize
	 * @param	{Function}	$super		Parent constructor: Todoyu.PanelWidgetSearchList.initialize
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

		this.buttonSaveGroup	= $('panelwidget-staffselector-button-savegroup');

		this.initStaffSelectorObservers();

		this.addItemsIcons(true, true, true);

		this.markFirstAsHot();
	},



	/**
	 * Initialize search input and list observers
	 *
	 * @method	initObservers
	 */
	initObservers: function() {
		this.input.on('keyup', this.onSearchKeyUp.bind(this));
		this.list.on('click', '', this.onItemClick.bind(this));
	},




	/**
	 * Init staff selector specific observers
	 *
	 * @method  initStaffSelectorObservers
	 */
	initStaffSelectorObservers: function() {
			// Observe selection list for disable and remove clicks
		this.selection.on('click', 'li', this.onSelectionItemClick.bind(this));
		this.selection.on('click', 'li span.remove', this.onRemoveClick.bind(this));
		this.selection.on('click', 'li span.deletegroup', this.onDeleteGroupClick.bind(this));

			// Observe search result item clicks
		this.list.on('click', 'li span.deletegroup', this.onDeleteGroupClick.bind(this));

			// Observe input for return clicks
		this.input.on('keyup', this.onInputKeyUps.bind(this));

			// Observe "save selection as group" button
		this.buttonSaveGroup.on('click', 'button', this.onSaveGroupButtonClick.bind(this));
	},


	/**
	 * Add icons to listed items of search results and selection
	 *
	 * @param	{Boolean}	addDeleteIcons
	 * @param	{Boolean}	addAddIcons
	 * @param	{Boolean}	addRemoveIcons
	 * @method	addItemsIcons
	 */
	addItemsIcons: function(addDeleteIcons, addAddIcons, addRemoveIcons) {
			// Add (+) select icon to all selectable virtual group, group and person items
		if( addAddIcons ) {
			this.addAddIconsToList();
		}

			// Add (-) delete icon to all virtual group items
		if( addDeleteIcons ) {
			this.addDeleteIconsToList();
		}

			// Add (X) removal icon to all active selection items
		if( addRemoveIcons ) {
			this.addRemoveIconsToList();
		}
	},



	/**
	 * Handler when clicked on item
	 *
	 * @method	onItemClick
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onItemClick: function(event, item) {
			// Click on anchor or (+) add icon
		if( ! item.hasClassName('deletegroup') ) {
			this.addItemToSelection(item);
			this.saveSelection();
		}
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
			this.onReturnKey();
		}
	},



	/**
	 * Handle return key press in search field
	 *
	 */
	onReturnKey: function() {
			// Add "hot" item to selection
		var firstItem	= this.list.down('li');
		if( firstItem ) {
			this.addItemToSelection(firstItem);
			this.input.select();
			this.saveSelection();
			this.markFirstAsHot();
		} else {
				// Activate first highlighted item from selection
			this.toggleMatchingElementsInSelection();
			this.saveSelection();
		}
	},



	/**
	 * Toggle all status for all elements which match the search text
	 *
	 * @method	toggleMatchingElementsInSelection
	 */
	toggleMatchingElementsInSelection: function() {
		this.getMatchingSelectionElements(this.getSearchText()).invoke('toggleClassName', 'disabled');
	},



	/**
	 * Get first highlighted (if any) item from persons selection
	 *
	 * @method	getAllSelectedAndHighlightedItems
	 * @return	{Array}
	 */
	getAllSelectedAndHighlightedItems: function() {
		return this.selection.select('li').findAll(function(item) {
			return item.style.backgroundColor !== '';
		});
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
			// "normalize" item - get LI tag of clicked item
		var itemTagName = item.tagName ? item.tagName.toLowerCase() : '';
		if( itemTagName !== 'li') {
			item	= item.up('li');
		}

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
		if( item.down('span.add') ) {
			item.down('span.add').remove();
		}

			// Sort items
		this.sortSelection();

			// Highlight new item
		new Effect.Highlight(item, {
			duration:		2.0,
			afterFinish:	function() {
				this.removeAttribute('style');
			}.bind(item)
		});
	},



	/**
	 * Sort selection items (virtual groups, groups then persons by alphabet)
	 *
	 * @method	sortSelect
	 */
	sortSelection: function() {
		var nodes		= this.selection.select('li');

			// Collect nodes grouped by type
		var hashPersons			= {};
		var hashVirtualGroups	= {};
		var hashGroups			= {};

		nodes.each(function(item) {
			var hash = item.hasClassName('person') ? hashPersons : ( item.hasClassName('group') ? hashGroups : hashVirtualGroups);
			var label	= item.down('a').innerHTML.stripTags().strip();
			hash[label] = item;
		});

			// Update selection with sorted item nodes
		this.selection.update('');

		this.insertSelectionNodesSorted(hashVirtualGroups);
		this.insertSelectionNodesSorted(hashGroups);
		this.insertSelectionNodesSorted(hashPersons);
	},



	/**
	 * Insert given items alphabetically sorted into selection
	 *
	 * @method	insertSelectionNodesSorted
	 * @param	{Object}	hashItems
	 */
	insertSelectionNodesSorted: function(hashItems) {
		var sortedItems	= Object.keys(hashItems).sort();

		sortedItems.each(function(key){
			this.selection.insert(hashItems[key]);
		}, this);
	},



	/**
	 * Handler when clicked on a selection item: remove item from selection
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
	 * Handler when clicked on delete group icon
	 *
	 * @method	onDeleteGroupClick
	 * @param	{Event}		event
	 * @param	{Element}	deleteIcon
	 */
	onDeleteGroupClick: function(event, deleteIcon) {
		event.stop();

		if( confirm('[LLL:contact.panelwidget-staffselector.confirm.deletegroup.confirm') ) {
			var item	= deleteIcon.up('li');
			var idPref = item.id.split('-')[3].replace('v', '');

			this.deleteGroup(idPref);
		}
	},



	/**
	 * Delete group preference with given ID
	 *
	 * @method	deleteGroup
	 * @param	{Number}		idPref
	 */
	deleteGroup: function(idPref) {
		var url = Todoyu.getUrl('contact', 'panelwidgetstaffselector');
		var options	= {
			parameters: {
				action:	'deleteGroup',
				group:	idPref
			},
			onComplete: this.onGroupDeleted.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler after "virtual" group (pref) has been deleted
	 *
	 * @method	onGroupDeleted
	 * @param	{Ajax.Response}  response
	 */
	onGroupDeleted: function(response) {
		if( !response.hasTodoyuError() ) {
			Todoyu.notifySuccess('[LLL:contact.panelwidget-staffselector.deletegroup.success');
			this.update();
		} else {
			Todoyu.notifySuccess('[LLL:contact.panelwidget-staffselector.deletegroup.error');
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
		return !this.selection.down('li');
	},



	/**
	 * Save preference of selected items
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
	 * Handler when selection preference was saved
	 * Fire change event to notify other about the change
	 *
	 * @method	onSelectionSaved
	 * @param	{Array}			items
	 * @param	{Ajax.Response}	response
	 */
	onSelectionSaved: function(items, response) {
		Todoyu.PanelWidget.fire('staffselector', response.responseJSON);
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
	 * @param	{Ajax.Response}	response
	 */
	onUpdated: function(response) {
		this.addItemsIcons(true, true, false);
		this.markFirstAsHot();
	},



	/**
	 * Handler on an empty result
	 *
	 * @method	onEmptyResult
	 * @param	{Function}			$super		Todoyu.PanelWidgetSearchList.onEmptyResult
	 * @param	{Ajax.Response}		response
	 */
	onEmptyResult: function($super, response) {
		if( this.getSearchText().strip() !== '' ) {
			this.highlightMatchingSelectedItems(this.getSearchText());
		}
	},



	/**
	 * Highlight all already selected items which match the current search word
	 *
	 * @method	highlightMatchingSelectedItems
	 * @param	{String}	search
	 */
	highlightMatchingSelectedItems: function(search) {
		this.getMatchingSelectionElements(search).each(function(li){
			li.highlight({
				duration:3.0
			});
		});
	},



	/**
	 * Get elements in selection which match the search word
	 *
	 * @param	{String}	search
	 * @return	{Element[]}
	 */
	getMatchingSelectionElements: function(search) {
		var pattern	= new RegExp(search, 'i');

		return this.selection.select('li').findAll(function(li){
			var name = li.down('a').innerHTML.stripTags().strip();
			return pattern.test(name);
		});
	},



	/**
	 * Add deletion icons to all virtual group items in the search results list
	 *
	 * @method	addDeleteIconsToList
	 */
	addDeleteIconsToList: function() {
		this.list.select('li.virtualgroup a').each(function(item){
			item.insert(new Element('span', {
				'class':	'deletegroup',
				title:		'[LLL:contact.panelwidget-staffselector.icon.deletegroup]'
			}));
		});
	},



	/**
	 * Add icons for adding to active selection to all items in the search results list
	 *
	 * @method	addAddIconsToList
	 */
	addAddIconsToList: function() {
		this.list.select('li a').each(function(item){
			item.insert(new Element('span', {
				'class':	'add',
				title:		'[LLL:contact.panelwidget-staffselector.icon.additem]'
			}));
		});
	},



	/**
	 * Add removal icons to all selected items. If no items are provided,
	 * add to all in the selection list
	 *
	 * @method	addRemoveIconsToList
	 * @param	{Array}	items
	 */
	addRemoveIconsToList: function(items) {
		items	= /*items ||*/ this.selection.select('li');

		items.each(function(item){
			var anchor = item.down('a');
			if( ! anchor.down('span.remove') ) {
				anchor.insert(new Element('span', {
					'class':	'remove',
					title:		'[LLL:contact.panelwidget-staffselector.icon.removefromselection]'
				}));
			}
		});
	},



	/**
	 * Check whether any item is selected or not
	 *
	 * @method	isAnyPersonSelected
	 */
	isAnyPersonSelected: function() {
		return this.getSelectedItems().size() > 0;
	},



	/**
	 * Check whether any item is selected or not
	 *
	 * @method	isAnyItemSelected
	 */
	isAnyItemSelected: function() {
		return this.getSelectedItems().size() > 0;
	},



	/**
	 * Get all selected elements (persons). Gets also group and other types
	 *
	 * @method	getSelectedPersons
	 * @return	{Array}
	 */
	getSelectedPersons: function() {
		var items	= this.getSelectedItems();

		return this.getSelectedItems().findAll(function(item){
			return item.substr(0, 1) !== '-';
		});
	},



	/**
	 * Save persons and groups as "virtual" group preference
	 *
	 * @method	onSaveGroupButtonClick
	 */
	onSaveGroupButtonClick: function() {
			// No persons selected
		if( !this.isAnyItemSelected() ) {
			alert('LLL:contact.panelwidget-staffselector.selection.empty');
			return;
		}

		var title 	= prompt('[LLL:contact.panelwidget-staffselector.newGroupLabel.prompt]', '[LLL:search.ext.newSeparatorLabel.preset]');

			// Canceled saving
		if( title === null ) {
			return;
		}
			// No name entered
		if( title.strip() === '' ) {
			alert('[LLL:contact.panelwidget-staffselector.newGroupLabel.error.saveEmptyName]');
			return;
		}

			// Save group items (persons and groups, as type-prefixed IDs e.g. g1 g2 g3 p1 p2 p3...)
		var url		= Todoyu.getUrl('contact', 'panelwidgetstaffselector');
		var options	= {
			parameters: {
				action:	'saveGroup',
				title:	title,
				items:	Object.toJSON(this.getSelectedItems())
			},
			onComplete:	this.onSavedVirtualGroup.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * @method  onSavedGroup
	 */
	onSavedVirtualGroup: function() {
		Todoyu.notifySuccess('[LLL:contact.panelwidget-staffselector.saved.success');

		this.update();
	}

});