<?php
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

class TodoyuContactViewHelper {

	/**
	 * Get label for a person in a form
	 *
	 * @param	TodoyuFormElement	$field
	 * @param	Array				$data
	 * @return	String
	 */
	public static function getPersonLabel(TodoyuFormElement $field, array $data) {
		$idPerson	= intval($data['id']);

		return TodoyuPersonManager::getLabel($idPerson);
	}


	public static function getPersonOptions(TodoyuFormElement $field) {
		$options	= array();
		$persons	= TodoyuPersonManager::getAllActivePersons();

		foreach($persons as $person) {
			$options[] = array(
				'value'	=> $person['id'],
				'label'	=> TodoyuPersonManager::getLabel($person['id'])
			);
		}

		return $options;
	}


	/**
	 * Get options array with all persons being employees of internal company
	 *
	 * @return	Array
	 */
	public static function getInternalPersonOptions(TodoyuFormElement $field) {
		$options	= array();
		$persons	= TodoyuPersonManager::getInternalPersons();

		if( sizeof($persons) > 0 ) {
				// List internal persons
			foreach($persons as $person) {
				$options[] = array(
					'value'	=> $person['id'],
					'label'	=> TodoyuPersonManager::getLabel($person['id'])
				);
			}
		} else {
				// No internal persons / firm defined? inform about that
			$options[] = array(
				'value'		=> 0,
				'label'		=> Label('contact.form.error.nointernalpersons'),
				'disabled'	=> true,
				'classname'	=> 'error'
			);
		}

		return $options;
	}





	/**
	 * Get persons address label
	 *
	 * @param	TodoyuFormElement	$formElement
	 * @param	Array				$valueArray
	 * @return	String
	 */
	public static function getPersonAddressLabel(TodoyuFormElement $formElement, array $valueArray) {
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
		$options	= TodoyuJobtypeManager::getJobtypeOptions();

		if ( count($options) == 0 ) {
			$options[]	= array(
				'value'		=> 'disabled',
				'label'		=> 'LLL:contact.error.noJobtypes',
				'disabled'	=> true,
				'classname'	=> 'error'
			);
		}

		return $options;
	}



	/**
	 * Get label of employee (person) item
	 *
	 * @param	TodoyuFormElement	$field
	 * @param	Array				$record
	 * @return	String
	 */
	public static function getEmployeeLabel(TodoyuFormElement $field, array $record) {
		$idPerson	= intval($record['id']);
		$label		= '';

		if( $idPerson !== 0 ) {
			$label	= TodoyuPersonManager::getLabel($idPerson);
		}

		return $label;
	}



	/**
	 * Get label of address (concatenated info summary)
	 *
	 * @param	TodoyuFormElement	$field
	 * @param	Array				$record
	 * @return	String
	 */
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
				$label .= ( $label !== '' ? ', ' : '' ) . $record['city'];
			}
		}

		return $label;
	}



	/**
	 * Get label of contact info record ('title')
	 *
	 * @param	TodoyuFormElement	$field
	 * @param	Array				$record
	 * @return	String
	 */
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



	/**
	 * Get options config array for contact infotype category selector
	 *
	 * @param	TodoyuFormElement $field
	 * @return	Array
	 */
	public static function getContactInfoTypeCategoryOptions(TodoyuFormElement $field) {
		$categoriesConf	= TodoyuContactManager::getContactinfotypeCategories();
		$reform		= array(
			'index'	=> 'value',
			'label'	=> 'label'
		);
		$options		= TodoyuArray::reform($categoriesConf, $reform);

		return $options;
	}



	/**
	 * Get options config array for address type selector
	 *
	 * @param	TodoyuFormElement $field
	 * @return	Array
	 */
	public static function getAddressTypeOptions(TodoyuFormElement $field) {
		$addressTypesConf	= TodoyuContactManager::getAddressTypes();
		$reform		= array(
			'index'	=> 'value',
			'label'	=> 'label'
		);
		$options		= TodoyuArray::reform($addressTypesConf, $reform);

		return $options;
	}



	/**
	 * Get options for country selector
	 *
	 * @param	TodoyuFormElement $field
	 * @return	Array
	 */
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

			array_unshift($countryOptions, array(
				'value' => 'disabled',
				'label' => '------------------------',
				'disabled' => true)
			);

			foreach($favoriteCountries as $favoriteCountry) {
				array_unshift($countryOptions, $favoriteCountry);
			}
		}

		return $countryOptions;
	}



	/**
	 * Wrapper Method to get Address records (company-person form)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getWorkaddressOptionsCompany(TodoyuFormElement $field)	{
		$idCompany = intval($field->getForm()->getVar('parent'));

		return self::getWorkaddressOptions($idCompany);
	}



	/**
	 * Wrapper Method to get Address records (person-company form)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getWorkaddressOptionsPerson(TodoyuFormElement $field)	{
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
		$options	= array();

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
					'disabled'	=> true,
					'classname'	=> 'error'
				);
			}
		}

		return $options;
	}



	/**
	 * Get selector options config array for info types
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getContactInfoTypeOptions(TodoyuFormElement $field) {
		$types	= TodoyuContactInfoTypeManager::getContactInfoTypes(true);
		$reform	= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($types, $reform);
	}

}

?>