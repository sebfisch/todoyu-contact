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

Todoyu.Ext.contact.QuickInfoPerson = {

	/**
	 * Ext shortcut
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
	 * @param	Element	element
	 */
	install: function(element) {
		var idPerson = $(element).id.split('-').last();

		$(element).observe('mouseover', this.onMouseOver.bindAsEventListener(this, idPerson));
		$(element).observe('mouseout', this.onMouseOut.bindAsEventListener(this, idPerson));
	},


	/**
	 * Evoked upon mouseOver event upon person element. Shows quick-info.
	 *
	 * @param	Object	event		the DOM-event
	 * @param	Integer	idPerson
	 */
	onMouseOver: function(event, idPerson) {
		Todoyu.QuickInfo.show('contact', 'person', idPerson, event.pointerX(), event.pointerY())
	},


	/**
	 * Evoked upon mouseOut event upon person element. Show quick info.
	 *
	 * @param	Object	event			the DOM-event
	 * @param	Integer	idPrson
	 */
	onMouseOut: function(event, idPerson) {
		Todoyu.QuickInfo.hide();
	}

};