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
			'label'	=> 'LLL:form.select.pleaseChoose',
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
//	public static function getUserTypeOptions(TodoyuFormElement $field) {
//		$types	= TodoyuUserManager::getUserTypes();
//		$reform	= array(
//			'key'	=> 'value',
//			'label'	=> 'label'
//		);
//
//		$types	= TodoyuArray::reform($types, $reform, true);
//
//		return $types;
//	}





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



	/**
	 * Get label of a company
	 *
	 * @param	TodoyuFormElement	$field
	 * @param	Array		$record
	 * @return	String
	 */
	public static function getCompanyLabel(TodoyuFormElement $field, array $record) {
		$idCompany	= intval($record['id']);
		$label		= '';

		if( $idCompany !== 0 ) {
			$company	= TodoyuCompanyManager::getCompany($idCompany);

			$label		= $company->getTitle();
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
		$countryOptions				= TodoyuDatasource::getCountryOptions();
		$favoriteCountryIDs 		= TodoyuAddressManager::getMostUsedCountryIDs();
		$favoriteCountries			= array();
		$favoriteCountrySortOrder	= array_flip($favoriteCountryIDs);
		
		if( sizeof($favoriteCountryIDs) > 0 ) {
			foreach($countryOptions as $countryOption) {
				if( in_array($countryOption['id'], $favoriteCountryIDs) ) {
					$sortOrderKey						= $favoriteCountrySortOrder[$countryOption['id']];
					$favoriteCountries[$sortOrderKey]	= $countryOption;
				}
			}
			
			krsort($favoriteCountries);
			
			array_unshift($countryOptions, array('value' => 'disabled', 'label' => '------------------------', 'disabled' => true));

			foreach($favoriteCountries as $favoriteCountry) {
				array_unshift($countryOptions, $favoriteCountry);
			}
		}
		
		array_unshift($countryOptions, array('value' => 0, 'label'	=> 'LLL:form.select.pleaseChoose'));
		
		return $countryOptions;
	}

	
	
	/**
	 * Wrapper Method to get Address records (company-user form)
	 * 
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getWorkaddressOptionsCompany(TodoyuFormElement $field)	{
		$idCompany = intval($field->getForm()->getVar('parent'));
		return self::getWorkaddressOptions($idCompany);
	}
	
	
	
	/**
	 * Wrapper Method to get Address records (user-company form)
	 * 
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getWorkaddressOptionsUser(TodoyuFormElement $field)	{
		$idCompany = intval($field->getForm()->getField('id')->getValue());
		return self::getWorkaddressOptions($idCompany);
	}
	
	

	/**
	 * Gets address(es) of employer firm
	 *
	 * @param	Integer	$idCompany
	 * @return	Array
	 */
	public static function getWorkaddressOptions($idCompany) {
		$idCompany	= intval($idCompany);
		$options	= array(array('value' => 0, 'label' => 'LLL:form.select.pleaseChoose'));

		if( $idCompany !== 0 ) {
			$addresses	= TodoyuCompanyManager::getCompanyAddressRecords($idCompany);
			
			if(count($addresses) > 0)	{
				foreach($addresses as $address) {
					$options[] = array(
						'value'	=> $address['id'],
						'label'	=> $address['street'] . ', ' . $address['city']
					);
				}
			} else {
				$options[]	= array(
					'value'		=> 'disabled',
					'label'		=> 'LLL:contact.company.noAddress',
					'disabled'	=> true
				);
			}
		}

		return $options;
	}

}

?>