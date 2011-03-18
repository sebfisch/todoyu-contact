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
 * Contact Address
 *
 * @class		Address
 * @namespace	Todoyu.Ext.contact
 */
Todoyu.Ext.contact.Address = {

	/**
	 * Sends Ajax request when the a country is selected to get its country zones
	 *
	 * @method	onChangeCountry
	 * @param	{Object}	inputField
	 * @param	{String}	referencedFieldName
	 * @param	{String}	fieldNameToReplace
	 */
	onChangeCountry: function(inputField, referencedFieldName, fieldNameToReplace) {
		var selectedValue		= inputField.value;
		var idInputFieldArr		= inputField.id.split('-').without('fulltext');
		var idTarget = idInputFieldArr.join('-').replace(fieldNameToReplace, referencedFieldName);

		if( $(idTarget) ) {
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
					'parameters': {
						'action':		'getRegionOptions',
						'idCountry':	selectedValue
					},
					'onComplete': this.onUpdateCompanyAddressRecords.bind(this, idTarget)
			};

			Todoyu.Ui.update(idTarget, url, options);
		}
	},



	/**
	 * Fills the found options to the selector
	 * Highlights the selector for 2 seconds
	 *
	 * @method	onUpdateCompanyAddressRecords
	 * @param	{String}			idTarget
	 * @param	{Ajax.Response}		response
	 */
	onUpdateCompanyAddressRecords: function(idTarget, response) {
		$(idTarget).innerHTML = response.responseText;

		new Effect.Highlight($(idTarget), {
			'startcolor':	'#fffe98',
			'endcolor':		'#ffffff',
			'duration':		2.0
		});
	}

};