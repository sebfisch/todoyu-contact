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

		$types	= TodoyuArray::reform($types, $reform, true);

		return $types;
	}





	public static function getEmployeeLabel(TodoyuFormElement $field, array $record) {
		$idUser	= intval($record['id']);
		$label	= '';

		if( $idUser !== 0 ) {
			$label	= TodoyuUserManager::getLabel($idUser);
		}

		return $label;
	}


	public static function getAddressLabel(TodoyuFormElement $field, array $record) {
		$idAddress	= intval($record['id']);
		$label		= '';

		if( $idAddress !== 0 ) {
			$addressType	= TodoyuAddressManager::getAddresstypeLabel($record['id_addresstype']);

			if( ! empty($addressType) ) {
				$label .= $addressType . ': ';
			}

			if( ! empty($record['street']) ) {
				$label .= $record['street'];
			}

			if( ! empty($record['city']) ) {
				$label .= ', ' . $record['city'];
			}
		}

		return $label;
	}


	public static function getContactinfoLabel(TodoyuFormElement $field, array $record) {
		$idContactInfo	= intval($record['id']);
		$label			= '';

		if( $idContactInfo !== 0 ) {
			$idContactInfoType	= intval($record['id_contactinfotype']);
			$contactInfoType	= TodoyuContactInfoTypeManager::getContactInfoType($idContactInfoType);

			$label	= $contactInfoType->getTitle() . ': ' . $record['info'];
		}

		return $label;
	}


	public static function getCustomerLabel(TodoyuFormElement $field, array $record) {
		$idCustomer	= intval($record['id']);
		$label		= '';

		if( $idCustomer !== 0 ) {
			$customer	= TodoyuCustomerManager::getCustomer($idCustomer);

			$label		= $customer->getTitle();
		}

		return $label;
	}


	public static function getAddressTypeOptions(TodoyuFormElement $field) {
		$addressTypes	= TodoyuContactManager::getAddressTypes();
		$reform		= array(
			'index'	=> 'value',
			'label'	=> 'label'
		);
		$options		= TodoyuArray::reform($addressTypes, $reform);

		return $options;
	}

	public static function getCountryOptions(TodoyuFormElement $field) {
		$countryOptions		= TodoyuDatasource::getCountryOptions();
		$favoriteCountryIDs = TodoyuAddressManager::getMostUsedCountryIDs();
		$favoriteCountries	= array();

		if( sizeof($favoriteCountryIDs) > 0 ) {
			foreach($countryOptions as $countryOption) {
				if( in_array($countryOption['id'], $favoriteCountryIDs) ) {
					$favoriteCountries[] = $countryOption;
				}
			}

			$favoriteCountries = array_reverse($favoriteCountries);

			array_unshift($countryOptions, array('value' => '', 'label' => '------------------------'));

			foreach($favoriteCountries as $favoriteCountry) {
				array_unshift($countryOptions, $favoriteCountry);
			}
		}

		return $countryOptions;
	}



}

?>