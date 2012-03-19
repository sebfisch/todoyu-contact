<?php
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
 * Person filter data source
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonFilterDataSource {

	/**
	 * Get autocomplete list for person
	 *
	 * @param	String		$input
	 * @param	Array		$formData
	 * @param	String		$name
	 * @return	Array
	 */
	public static function autocompletePersons($input, array $formData = array(), $name = '') {
		$data = array();

		$fieldsToSearchIn = array(
			'firstname',
			'lastname',
			'shortname'
		);

		$persons = TodoyuContactPersonManager::searchPersons($input, $fieldsToSearchIn);

		foreach($persons as $person) {
			$data[$person['id']] = TodoyuContactPersonManager::getLabel($person['id']);
		}

		return $data;
	}



	/**
	 * Get person filter definition label
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getLabel($definitions) {
		$definitions['value_label'] = TodoyuContactPersonManager::getLabel($definitions['value']);

		return $definitions;
	}



	/**
	 * Prepare options of salutations for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getSystemRoleOptions(array $definitions) {
		$options	= array();
		$roles	= TodoyuRoleManager::getAllRoles();

		foreach($roles as $role) {
			$label  = $role['title'] . ($role['is_active'] ? '' : ' (inactive)');

			$options[] = array(
				'label'		=> $label,
				'value'		=> $role['id']
			);
		}

		$definitions['options'] = $options;

		return $definitions;
	}



	/**
	 * Prepare options of salutations for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getJobTypeOptions(array $definitions) {
		$options	= array();
		$jobtypes	= TodoyuContactJobTypeManager::getAllJobTypes();
		foreach($jobtypes as $jobtype) {
			$label	= $jobtype['title'];

			$options[] = array(
				'label'		=> $label,
				'value'		=> $jobtype['id']
			);
		}

		$definitions['options'] = $options;

		return $definitions;
	}



	/**
	 * Prepare options of salutations for rendering in widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getSalutationOptions(array $definitions) {
		$definitions['options'] = array(
			array(
				'index' => '1',
				'value' => 'm',
				'key'   => 'mr',
				'class' => 'salutationMale',
				'label' => Todoyu::Label('contact.ext.person.attr.salutation.m')
			),
			array(
				'index' => '2',
				'value' => 'f',
				'key'   => 'mrs',
				'class' => 'salutationFemale',
				'label' => Todoyu::Label('contact.ext.person.attr.salutation.f')
			)
		);

		return $definitions;
	}

}

?>