<?php
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
 * Contact rights
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactRights {

	/**
	 * Deny access because if given right is not given
	 *
	 * @param	String	$right
	 */
	private static function deny($right) {
		deny('contact', $right);
	}



	/**
	 * Restricts add of contact record
	 *
	 * @static
	 * @param	String		$record
	 */
	public static function restrictRecordAdd($record) {
		$record	= trim($record);
		if( $record === 'person' ) {
			TodoyuContactPersonRights::restrictAdd();
		} else if( $record === 'company' ) {
			TodoyuContactCompanyRights::restrictAdd();
		} else {
			self::deny('contact:unkonwnrecord');
		}
	}



	/**
	 * Restricts edit of contact record
	 *
	 * @static
	 * @param	String		$record
	 */
	public static function restrictRecordEdit($record, $idRecord) {
		$idRecord	= intval($idRecord);
		$record	= trim($record);

		if( $record === 'person' ) {
			TodoyuContactPersonRights::restrictEdit($idRecord);
		} else if( $record === 'company' ) {
			TodoyuContactCompanyRights::restrictEdit($idRecord);
		} else {
			self::deny('contact:unkonwnrecord');
		}


	}



	/**
	 * Check if see of contact info type of given person is allowed for current person
	 *
	 * @static
	 * @param	Integer		$idPerson
	 * @param	Integer		$idContactinfotype
	 * @return	Boolean
	 */
	public static function isContactinfotypeOfPersonSeeAllowed($idPerson, $idContactinfotype) {
		$idPerson			= intval($idPerson);
		$idContactinfotype	= intval($idContactinfotype);

		if( TodoyuAuth::isAdmin() || $idPerson === personid() ) {
			return true;
		}

		return self::isContactinfotypeSeeAllowed($idContactinfotype);
	}



	/**
	 * Check if see of contact info type of given company is allowed for current person
	 *
	 * @static
	 * @param	Integer		$idCompany
	 * @param	Integer		$idContactinfotype
	 * @return	Boolean
	 */
	public static function isContactinfotypeOfCompanySeeAllowed($idCompany, $idContactinfotype) {
		$idCompany			= intval($idCompany);
		$idContactinfotype	= intval($idContactinfotype);

		$employers	= TodoyuContactPersonManager::getPerson(personid())->getCompanyIDs();

		if( TodoyuAuth::isAdmin() || in_array($idCompany, $employers) ) {
			return true;
		}

		return self::isContactinfotypeSeeAllowed($idContactinfotype);
	}



	/**
	 * Check if see of contact info type is allowed for current person
	 *
	 * @static
	 * @param	Integer		$idPerson
	 * @param	Integer		$idContactInfoType
	 * @return	Boolean
	 */
	public static function isContactinfotypeSeeAllowed($idContactInfoType) {
		$idContactInfoType	= intval($idContactInfoType);

		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		if( allowed('contact', 'relation:seeAllContactinfotypes') ) {
			return true;
		}

		return TodoyuContactContactInfoTypeManager::getContactInfoType($idContactInfoType)->isPublic();
	}



	/**
	 * Checks if see of address type of given person is allowed for current person
	 *
	 * @static
	 * @param	Integer		$idPerson
	 * @param	Integer		$idAddresstype
	 * @return	Boolean
	 */
	public static function isAddresstypeOfPersonSeeAllowed($idPerson, $idAddresstype) {
		$idPerson			= intval($idPerson);
		$idAddresstype		= intval($idAddresstype);

		if( TodoyuAuth::isAdmin() || $idPerson === personid() ) {
			return true;
		}

		return self::isAddresstypeSeeAllowed($idAddresstype);
	}



	/**
	 * Checks if see of address type of given company is allowed for current person
	 *
	 * @static
	 * @param	Integer		$idPerson
	 * @param	Integer		$idAddresstype
	 * @return	Boolean
	 */
	public static function isAddresstypeOfCompanySeeAllowed($idCompany, $idAddresstype) {
		$idCompany			= intval($idCompany);
		$idAddresstype		= intval($idAddresstype);

		$employers	= TodoyuContactPersonManager::getPerson(personid())->getCompanyIDs();


		if( TodoyuAuth::isAdmin() || in_array($idCompany, $employers) ) {
			return true;
		}

		return self::isAddresstypeSeeAllowed($idAddresstype);
	}



	/**
	 * Check if see of address type is allowed for current person
	 *
	 * @static
	 * @param	Integer		$idAddressType
	 * @return	Boolean
	 */
	public static function isAddresstypeSeeAllowed($idAddressType) {
		$idAddressType	= intval($idAddressType);

		if( TodoyuAuth::isAdmin() ) {
			return true;
		}

		switch($idAddressType) {
			case 1:
				return allowed('contact', 'relation:seeHomeAddress');
				break;
			case 2:
				return allowed('contact', 'relation:seeBusinessAddress');
				break;
			case 3:
				return allowed('contact', 'relation:seeBillingAddress');
				break;
		}

		return false;
	}



	/**
	 * Restricts usage of contact export
	 *
	 * @static
	 */
	public static function restrictExport() {
		if( ! allowed('contact', 'panelwidgets:export') ) {
			self::deny('panelwidgets:export');
		}
	}
}

?>