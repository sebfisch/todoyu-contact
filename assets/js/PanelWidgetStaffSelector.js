/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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
 */

Todoyu.Ext.contact.PanelWidget.StaffSelector = {

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
	 * @param	{Object}		jobType2Persons
	 */
	init: function(jobType2Persons) {
		this.list			= $('panelwidget-staffselector-persons');
		this.jobType		= $('panelwidget-staffselector-jobtype');
		this.jobTypeToggle	= $('panelwidget-staffselector-jobtypetoggle');
		this.jobType2Persons= jobType2Persons;

		this.installObservers();
	},



	/**
	 * Install observers
	 */
	installObservers: function() {
		this.list.observe('change', this.onSelectionChange.bindAsEventListener(this));
		this.jobType.observe('change', this.onJobtypeSelected.bindAsEventListener(this));
		this.jobTypeToggle.observe('change', this.onJobtypeToggleChange.bindAsEventListener(this));
	},



	/**
	 * Handler of person selection changes
	 *
	 * @param	{Event}		event
	 */
	onSelectionChange: function(event) {
		this.selectAllJobtypes(false);
		this.onUpdate();
	},



	/**
	 * Handler of jobtype selection changes
	 * 
	 * @param	{Event}		event
	 */
	onJobtypeSelected: function(event) {
		this.selectPersonsByJobtype();
		this.onUpdate();
	},



	/**
	 * Handler of jobtype toggler changes
	 * 
	 * @param	{Event}		event
	 */
	onJobtypeToggleChange: function(event) {
		var toggler	= event.findElement('input');

		if( toggler.checked ) {
			this.jobType.multiple	= 'multiple';
			this.jobType.size		= '';
		} else {
			this.jobType.multiple	= '';
			this.jobType.size		= 1;
		}

		$(toggler.id + '-label').toggleClassName('expand');

		this.onUpdate();
	},



	/**
	 * If widget has changed inform all listeners which persons are selected
	 */
	onUpdate: function() {
		this.savePrefs();
	},



	/**
	 * Select all/no jobTypes
	 *
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
	 * @param	{Boolean}		select
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
	 * @param	{Array}		personIDs
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
	 * Get all perosons which have the requested job type
	 *
	 * @param	{Number}		jobType
	 * @return	Array
	 */
	getJobtypePersons: function(jobType) {
		return this.jobType2Persons[jobType];
	},



	/**
	 * Get IDs of currently selected persons
	 *
	 * @return	Array
	 */
	getSelectedPersons: function() {
		return $F(this.list);
	},



	/**
	 * Get selected jobtypes
	 *
	 * @return	Array
	 */
	getSelectedJobtypes: function() {
		var jobTypes	= $F(this.jobType);

		return Object.isArray(jobTypes) ? jobTypes : [jobTypes];
	},



	/**
	 * Get number of select persons
	 *
	 * @return	{Number}
	 */
	getNumberOfSelectedPersons: function() {
		return this.getSelectedPersons().size();
	},



	/**
	 * Check if any person is currently selected
	 *
	 * @return	{Boolean}
	 */
	isAnyPersonSelected: function() {
		return this.getSelectedPersons().size() > 0;
	},



	/**
	 * Check if jobtype is in multiselect mode
	 * 
	 * @return	{Boolean}
	 */
	isMultiJobtypes: function() {
		return this.jobType.multiple;
	},



	/**
	 * Store prefs
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
	 * @param	{Object}	response
	 */
	onPrefsSaved: function(response) {
		Todoyu.PanelWidget.fire('staffselector', this.getSelectedPersons());
	}
};