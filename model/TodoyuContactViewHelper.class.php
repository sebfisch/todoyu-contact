<?php

class TodoyuContactViewHelper {

	public static function getUserAddressLabel(TodoyuFormElement $formElement, array $valueArray) {
		$idAddressType	= intval($valueArray['id_addresstype']);
		$addressType	= TodoyuAddressTypeManager::getAddressType($idAddressType);

		return TodoyuDiv::getLabel($addressType['label']);
	}


	/**
	 * Get availabe jobtypes (to render selector)
	 *
	 * @param	TodoyuForm	$formObject
	 * @return	Array
	 */
	public static function getJobtypeOptions(TodoyuFormElement $field) {
		$jobtypeOptions	= TodoyuJobtypeManager::getJobtypeOptions();
		$prefix		= array(
			'label'	=> Label('core.pleaseChoose'),
			'value'	=> 0
		);

		array_unshift($jobtypeOptions, $prefix);

		return $jobtypeOptions;
	}



	/**
	 * Get user type options
	 * Default are internal and external, but this list can be extended by configuration
	 *
	 * @param	TodoyuFormElement		$field
	 * @return	Array
	 */
	public static function getUserTypeOptions(TodoyuFormElement $field) {
		$types	= TodoyuUserManager::getUserTypes();
		$reform	= array(
			'key'	=> 'value',
			'label'	=> 'label'
		);

		$types	= TodoyuDiv::reformArray($types, $reform, true);

		return $types;
	}

}

?>