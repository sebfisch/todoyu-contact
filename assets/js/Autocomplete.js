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

Todoyu.Ext.contact.Autocomplete = {

	/**
	 * Handler when parenttask field is autocompleted
	 *
	 * @param	Ajax.Response			response
	 * @param	Todoyu.Autocompleter	autocompleter
	 */
	onRegionAutocomplete: function(response, autocompleter) {
		if( response.getTodoyuHeader('acElements') == 0 ) {
			Todoyu.notifyInfo('[LLL:contact.ac.region.notFoundInfo]');
		}
	},



	/**
	 * Handler when user field is autocompleted
	 *
	 * @param	Ajax.Response			response
	 * @param	Todoyu.Autocompleter	autocompleter
	 */
	onUserAutocomplete: function(response, autocompleter) {
		if( response.getTodoyuHeader('acElements') == 0 ) {
			Todoyu.notifyInfo('[LLL:contact.ac.user.notFoundInfo]');
		}
	},



	/**
	 * Handler when company field is autocompleted
	 *
	 * @param	Ajax.Response			response
	 * @param	Todoyu.Autocompleter	autocompleter
	 */
	onCompanyAutocomplete: function(response, autocompleter) {
		if( response.getTodoyuHeader('acElements') == 0 ) {
			Todoyu.notifyInfo('[LLL:user.ac.company.notFoundInfo]');
		}
	}

};