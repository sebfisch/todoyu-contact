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


	timeoutSave: null,

	/**
	 * Constructor Initialize with search word
	 *
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

		this.selection	= $('panelwidget-staffselector-selection');
		this.selection.on('click', 'li', this.onSelectionClick.bind(this));

		this.addAddIconsToList();
		this.addRemoveIconsToList();
	},



	/**
	 * Handler when clicked on item
	 *
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onItemClick: function(event, item) {
		this.addItemToSelection(item);
		this.saveSelection();
	},



	/**
	 * Add an item to the selection list
	 *
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

			// Highlight new item
		new Effect.Highlight(item, {
			duration: 2.0
		});
	},



	/**
	 * Handler when clicked on a selection item
	 * Remove it from selection
	 *
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onSelectionClick: function(event, item) {
		new Effect.SlideUp(item, {
			duration: 0.5,
			afterFinish: function() {
				item.remove();
				this.addMessageIfSelectionEmpty();
				this.saveSelection();
			}.bind(this)
		});
	},



	/**
	 * When selection contains no item, add place holder label
	 */
	addMessageIfSelectionEmpty: function() {
		if( this.isSelectionEmpty() ) {
			this.selection.update('<p>[LLL:contact.panelwidget-staffselector.selection.empty]</p>');
		}
	},


	/**
	 * Check whether selection contains any items
	 *
	 * @return	{Boolean}
	 */
	isSelectionEmpty: function() {
		return this.selection.down('li') === undefined;
	},



	/**
	 * Save selected items
	 */
	saveSelection: function(noDelay) {
		clearTimeout(this.timeoutSave);
		if( noDelay !== true ) {
			this.timeoutSave = this.saveSelection.bind(this, true).delay(1);
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
	 * @param	{Array}			items
	 * @param	{Ajax.Response}	response
	 */
	onSelectionSaved: function(items, response) {
		Todoyu.PanelWidget.fire('staffselector', items);
	},



	/**
	 * Get item IDs from selection list
	 */
	getSelectedItems: function() {
		return this.selection.select('li').collect(function(item){
			return item.id.split('-').last();
		});
	},



	/**
	 * Handler when list was updated
	 */
	onUpdated: function() {
		this.addAddIconsToList();
	},



	/**
	 * Add adding icons to all items in the search list
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
	 * @param	{Array}	items
	 */
	addRemoveIconsToList: function(items) {
		items	= items || this.selection.select('li');

		items.each(function(item){
			item.down('a').insert(new Element('span', {
				'class': 'remove'
			}));
		});
	}
});




Todoyu.Ext.contact.PanelWidget.StaffSelectorOLD = {

	/**
	 * Extension backlink
	 *
	 * @var	{Object}	ext
	 */
	ext:			Todoyu.Ext.contact,

	/**
	 * Person list element
	 */
	list:			null,

	/**
	 * Jobtype element
	 */
	jobType:		null,

	/**
	 * Jobtype togglebox element
	 */
	jobTypeToggle:	null,

	/**
	 * Jobtype to persons linking object
	 */
	jobType2Persons:	{},



	/**
	 * Initialize widget. Link elements and set jobtype mapping
	 *
	 * @method	init
	 * @param	{Object}		jobType2Persons
	 */
	init: function(jobType2Persons) {
		this.list			= $('panelwidget-staffselector-persons');
		this.jobType		= $('panelwidget-staffselector-jobtype');
		this.jobTypeToggle	= $('panelwidget-staffselector-jobtypetoggle-label');
		this.jobType2Persons= jobType2Persons;

		this.installObservers();
	},



	/**
	 * Install observers
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
		this.list.observe('change', this.onSelectionChange.bindAsEventListener(this));
		this.jobType.observe('change', this.onJobtypeSelected.bindAsEventListener(this));
		this.jobTypeToggle.observe('click', this.onJobtypeToggleChange.bindAsEventListener(this));
	},



	/**
	 * Handler of person selection changes
	 *
	 * @method	onSelectionChange
	 * @param	{Event}		event
	 */
	onSelectionChange: function(event) {
		this.selectAllJobtypes(false);
		this.onUpdate();
	},



	/**
	 * Handler of jobtype selection changes
	 *
	 * @method	onJobtypeSelected
	 * @param	{Event}		event
	 */
	onJobtypeSelected: function(event) {
		this.selectPersonsByJobtype();
		this.onUpdate();
	},



	/**
	 * Handler of jobtype toggler changes
	 *
	 * @method	onJobtypeToggleChange
	 * @param	{Event}		event
	 */
	onJobtypeToggleChange: function(event) {
		var toggler	= event.findElement('div').down('input');

		if( ! toggler.checked ) {
			this.jobType.multiple	= 'multiple';
			this.jobType.size		= this.jobType.options.length;
		} else {
			this.jobType.multiple	= false;
			this.jobType.size		= 1;
		}

		$(toggler.id + '-label').toggleClassName('expand');

			// Fix for IE. Label click doesn't update input if input is hidden
		if( Prototype.Browser.IE ) {
			toggler.checked = !toggler.checked;
		}

		this.onUpdate();
	},



	/**
	 * If widget has changed inform all listeners which persons are selected
	 *
	 * @method	onUpdate
	 */
	onUpdate: function() {
		this.savePrefs();
	},



	/**
	 * Select all/no jobTypes
	 *
	 * @method	selectAllJobtypes
	 * @param	{Boolean}		select
	 */
	selectAllJobtypes: function(select) {
		var selected = select === true;
		this.jobType.select('option').each(function(option){
			option.selected = selected;
		});
	},



	/**
	 * Select all/no persons
	 *
	 * @method	selectAllPersons
	 * @param	{Boolean}			select
	 */
	selectAllPersons: function(select) {
		var selected = select === true;
		this.list.select('option').each(function(option){
			option.selected = selected;
		});
	},



	/**
	 * Select (one or multiple) given persons
	 *
	 * @method	selectPersons
	 * @param	{Array}			personIDs
	 */
	selectPersons: function(personIDs) {
		this.list.select('option').each(function(option){
			if( personIDs.include(option.value) ) {
				option.selected = true;
			}
		});
	},



	/**
	 * Select persons having selected job type assigned
	 *
	 * @method	selectPersonsByJobtype
	 */
	selectPersonsByJobtype: function() {
		this.selectAllPersons(false);

		var jobTypes	= this.getSelectedJobtypes();

		if( jobTypes.first() == 0 ) {
				// Select all employees
			this.selectAllPersons(true);
		} else {
				// Select all employees with selected jobtypes
			jobTypes.each(function(jobType){
				var personIDs = this.getJobtypePersons(jobType);
				this.selectPersons(personIDs);
			}.bind(this));
		}
	},



	/**
	 * Get all persons which have the requested job type
	 *
	 * @method	getJobtypePersons
	 * @param	{Number}		jobType
	 * @return	Array
	 */
	getJobtypePersons: function(jobType) {
		return this.jobType2Persons[jobType];
	},



	/**
	 * Get IDs of currently selected persons
	 *
	 * @method	getSelectedPersons
	 * @return	Array
	 */
	getSelectedPersons: function() {
		return $F(this.list);
	},



	/**
	 * Get selected jobtypes
	 *
	 * @method	getSelectedJobtypes
	 * @return	Array
	 */
	getSelectedJobtypes: function() {
		var jobTypes	= $F(this.jobType);

		return Object.isArray(jobTypes) ? jobTypes : [jobTypes];
	},



	/**
	 * Get number of select persons
	 *
	 * @method	getNumberOfSelectedPersons
	 * @return	{Number}
	 */
	getNumberOfSelectedPersons: function() {
		return this.getSelectedPersons().size();
	},



	/**
	 * Check if any person is currently selected
	 *
	 * @method	isAnyPersonSelected
	 * @return	{Boolean}
	 */
	isAnyPersonSelected: function() {
		return this.getSelectedPersons().size() > 0;
	},



	/**
	 * Check if jobtype is in multiselect mode
	 *
	 * @method	isMultiJobtypes
	 * @return	{Boolean}
	 */
	isMultiJobtypes: function() {
		return this.jobType.multiple;
	},



	/**
	 * Store prefs
	 *
	 * @method	savePrefs
	 */
	savePrefs: function() {
		var pref = Object.toJSON({
			'multiple': this.isMultiJobtypes(),
			'jobtypes': this.getSelectedJobtypes(),
			'persons': this.getSelectedPersons()
		});

		Todoyu.Pref.save('contact', 'panelwidgetstaffselector', pref, 0, this.onPrefsSaved.bind(this));
	},



	/**
	 * Handler being called after saving of prefs
	 *
	 * @method	onPrefsSaved
	 * @param	{Ajax.Response}		response
	 */
	onPrefsSaved: function(response) {
		Todoyu.PanelWidget.fire('staffselector', this.getSelectedPersons());
	}

};