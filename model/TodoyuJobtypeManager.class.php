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

class TodoyuJobtypeManager {

	/**
	 * Jobtype DB table
	 *
	 */
	const TABLE = 'ext_contact_jobtype';



	/**
	 * Get jobtype object
	 *
	 * @param	Integer		$idJobtype
	 * @return	TodoyuJobtype
	 */
	public static function getJobtype($idJobtype) {
		return TodoyuCache::getRecord('TodoyuJobtype', $idJobtype);
	}



	/**
	 * Get all jobtypes
	 *
	 * @return	Array
	 */
	public static function getAllJobtypes() {
		$fields	= '*';
		$table	= self::TABLE;
		$where	= 'deleted = 0';
		$order	= 'title';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get job types
	 *
	 * @param	Array	$typeIDs	optional
	 * @return	Array
	 */
	public static function getJobtypes($typeIDs = array()) {
		$fields	= 'id, title';
		$table	= self::TABLE;

		$where	= 'deleted = 0';
		if ( count($typeIDs) > 0 ) {
			$typeIDs	= TodoyuArray::intval($typeIDs);
			$where		.= ' AND id IN (' . TodoyuArray::intImplode($typeIDs, ',') . ') ';
		}

		$order	= 'title';

		$jobTypes	=	Todoyu::db()->getIndexedArray('id', $fields, $table, $where, '', '');
		foreach($jobTypes as $id => $jobType) {
			$jobTypes[$id]['title'] = TodoyuDiv::getLabel($jobType['title']);
			$jobTypes[$id]['id']	= $id;
		}

		return $jobTypes;
	}


	public static function getRecords() {
		$jobtypes	= TodoyuJobtypeManager::getAllJobtypes();
		$reform		= array(
			'title'	=> 'label'
		);

		return TodoyuArray::reform($jobtypes, $reform, true);
	}



	/**
	 * Get job type options
	 *
	 * @return	Array
	 */
	public static function getJobtypeOptions() {
		$jobTypes	= self::getJobtypes();
		$reform		= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);
		$options	= TodoyuArray::reform($jobTypes, $reform);

		return $options;
	}



	/**
	 * Get internal users with jobtype
	 *
	 * @return	Array
	 */
	public static function getInternalUsersWithJobtype() {
		$internalUsers	= TodoyuPersonManager::getInternalUsers(true, true);
		$jobTypes		= TodoyuDiv::useFieldAsIndex(self::getJobtypes(), 'id');

		foreach($internalUsers as $index => $user) {
			$internalUsers[$index]['jobtype']	= $user['id_jobtype'] != 0 ? $jobTypes[$user['id_jobtype']]['title'] : $internalUsers[$index]['jobtype']	= 'LLL:user.noJobDefined';
		}

		return $internalUsers;
	}



	/**
	 * Search in job types
	 *
	 * @param	Array	$searchFieldsArray
	 * @param	String	$search
	 * @return	Resource
	 */
	public static function searchJobTypes(array $searchFieldsArray, $search)	{
		$table = self::TABLE;

		if( $search != '*' )	{
			$searchArray = TodoyuArray::trimExplode(' ',$search);
			if(count($searchArray) > 0)	{
				$where = Todoyu::db()->buildLikeQuery($searchArray, $searchFieldsArray);
			} else {
				return false;
			}
		} else {
			$where = '';
		}

		$where = strlen($where) > 0 ? $where.' AND deleted = 0':' deleted = 0';

		return Todoyu::db()->doSelect('id', $table, $where, '', 'title');
	}



	/**
	 * Save job type
	 *
	 * @param	Array	$jobTypeData
	 */
	public static function saveJobType(array $data)	{
		$idJobtype	= intval($data['id']);
		$xmlPath	= 'ext/contact/config/form/admin/jobtype.xml';

		if( $idJobtype === 0 ) {
			$idJobtype = self::addJobtype();
		}

		$data	= TodoyuFormHook::callSaveData($xmlPath, $data, $idJobtype);

		self::updateJobtype($idJobtype, $data);


		return $idJobtype;
	}



	/**
	 * Add new jobtype
	 *
	 * @param	Array		$data
	 * @return	Integer
	 */
	public static function addJobtype(array $data = array()) {
		unset($data['id']);

		return Todoyu::db()->addRecord(self::TABLE, $data);
	}


	/**
	 * Update jobtype
	 *
	 * @param	Integer		$idJobtype
	 * @param	Array		$data
	 * @return	Bool
	 */
	public static function updateJobtype($idJobtype, array $data) {
		$idJobtype	= intval($idJobtype);
		unset($data['id']);

		return Todoyu::db()->updateRecord(self::TABLE, $idJobtype, $data);
	}



	/**
	 * Remove job type
	 *
	 * @param	Integer	$idJobtype
	 */
	public static function deleteJobtype($idJobtype)	{
		$idJobtype	= intval($idJobtype);

		return Todoyu::db()->deleteRecord(self::TABLE, $idJobtype);
	}



	/**
	 * Sum amount of users by job function, add jobtitles, assign users into job groups
	 *
	 * @param	Array	$staffUsers
	 */
	public static function summUpJobGroups(array &$staffUsers, array $prefs = array()) {
		$jobTypes	= array();

		foreach($staffUsers as $idUser => $userData) {
				// Sum amount of users by job function, add jobtitles, assign users into job groups
			$jobTypes[ $userData['id_jobtype'] ]['amount_users']++;
			$jobTypes[ $userData['id_jobtype'] ]['title']		= $userData['jobtype'];
			$jobTypes[ $userData['id_jobtype'] ]['user_ids'][]	= $userData['id'];

				// Set users selected as stored in user prefs
			if ( is_array( $prefs['selectedUserIDs'] ) && in_array($idUser, $prefs['selectedUserIDs']) ) {
				$staffUsers[$idUser]['selected'] = 1;
			}
		}

		return $jobTypes;
	}
}

?>