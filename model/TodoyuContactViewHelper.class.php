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
	 * Get user address label
	 *
	 * @param	TodoyuFormElement	$formElement
	 * @param	Array				$valueArray
	 * @return	String
	 */
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





	/**
	 * Get label of employee (user) item
	 *
	 * @param	TodoyuFormElement	$field
	 * @param	Array				$record
	 * @return	String
	 */
	public static function getEmployeeLabel(TodoyuFormElement $field, array $record) {
		$idUser	= intval($record['id']);
		$label	= '';

		if( $idUser !== 0 ) {
			$label	= TodoyuUserManager::getLabel($idUser);
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
	 * Get options config array for address type selector
	 *
	 * @param	TodoyuFormElement $field
	 * @return	Array
	 */
	public static function getAddressTypeOptions(TodoyuFormElement $field) {
		$addressTypes	= TodoyuContactManager::getAddressTypes();
		$reform		= array(
			'index'	=> 'value',
			'label'	=> 'label'
		);
		$options		= TodoyuArray::reform($addressTypes, $reform);

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