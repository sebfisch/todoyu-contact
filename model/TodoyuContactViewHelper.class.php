<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions GmbH, Switzerland
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
 * Contact specific view helpers
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
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



	/**
	 * Get options config array for persons selector
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
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

		return TodoyuString::getLabel($addressType['label']);
	}



	/**
	 * Get available job types (to render selector)
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getJobTypeOptions(TodoyuFormElement $field) {
		$options	= TodoyuJobTypeManager::getJobTypeOptions();

		if( count($options) == 0 ) {
			$options[]	= array(
				'value'		=> 'disabled',
				'label'		=> 'LLL:contact.company.attr.person.jobtype.noJobtypes',
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
		$countryOptions				= TodoyuStaticRecords::getCountryOptions();
		$favoriteCountryIDs 		= TodoyuAddressManager::getMostUsedCountryIDs();
		$favoriteCountries			= array();
		$favoriteCountrySortOrder	= array_flip($favoriteCountryIDs);
		
//		TodoyuDebug::printInFireBug($countryOptions, '$countryOptions');

		if( sizeof($favoriteCountryIDs) > 0 ) {
			foreach($countryOptions as $countryOption) {
				if( in_array($countryOption['value'], $favoriteCountryIDs) ) {
					$sortOrderKey						= $favoriteCountrySortOrder[$countryOption['value']];
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



	/**
	 * Get selector options config array for regions in address form
	 *
	 * @param	TodoyuFormElement	$field
	 */
	public static function getRegionOptions(TodoyuFormElement $field)	{
		$country	= $field->getForm()->getField('id_country')->getValue();
		$idCountry	= intval($country[0]);

		return TodoyuStaticRecords::getCountryZoneOptions($idCountry);
	}



	/**
	 * Get selector options config array for timezones, grouped into optGroups of continents
	 *
	 * @param	TodoyuFormElement $field
	 * @return	Array
	 */
	public static function getTimezoneOptionsGrouped(TodoyuFormElement $field) {
		$timezones	= TodoyuStaticRecords::getAllTimezones();
		$optGroups	= array();

		foreach($timezones as $timezone) {
			$tzParts	= explode('/', $timezone['timezone']);
			$optGroups[$tzParts[0]][] = array(
				'value'	=> $timezone['id'],
				'label'	=> $timezone['timezone']
			);
		}

		return $optGroups;
	}
}

?>