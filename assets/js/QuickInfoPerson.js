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

Todoyu.Ext.contact.QuickInfoPerson = {

	/**
	 * Ext shortcut
	 *
	 * @var	{Object}	ext
	 */
	ext:		Todoyu.Ext.contact,


	/**
	 * Init person quickinfo
	 */
	init: function() {

	},



	/**
	 * Install quickinfo for person
	 *
	 * @param	{Element}		element
	 */
	install: function(element) {
		var idPerson = $(element).id.split('-').last();

		$(element).observe('mouseover', this.onMouseOver.bindAsEventListener(this, idPerson));
		$(element).observe('mouseout', this.onMouseOut.bindAsEventListener(this, idPerson));
	},



	/**
	 * Evoked upon mouseOver event upon person element. Shows quick-info tooltip.
	 *
	 * @param	{Object}	event		the DOM-event
	 * @param	{Integer}	idPerson
	 */
	onMouseOver: function(event, idPerson) {
		Todoyu.QuickInfo.show('contact', 'person', idPerson, event.pointerX(), event.pointerY());
	},



	/**
	 * Evoked upon mouseOut event upon person element. Hides quick-info tooltip.
	 *
	 * @param	{Object}	event			the DOM-event
	 * @param	{Integer}	idPrson
	 */
	onMouseOut: function(event, idPerson) {
		Todoyu.QuickInfo.hide();
	}

};