<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2010, snowflake productions gmbh
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

TodoyuExtManager::addRecordConfig('contact', 'jobtype', array(
	'label'		=> 'LLL:contact.record.jobtype',
	'list'		=> 'TodoyuJobtypeManager::getRecords',
	'form'		=> 'ext/contact/config/form/admin/jobtype.xml',
	'object'	=> 'TodoyuJobType',
	'delete'	=> 'TodoyuJobtypeManager::deleteJobtype',
	'save'		=> 'TodoyuJobtypeManager::saveJobType',
	'table'		=> 'ext_contact_jobtype'
));

// add holidaysets to record area of the sysadmin
TodoyuExtManager::addRecordConfig('contact', 'contactinfotype', array(
	'label'		=> 'LLL:contact.record.contactinfotype',
	'list'		=> 'TodoyuContactInfoTypeManager::getRecords',
	'form'		=> 'ext/contact/config/form/admin/contactinfotype.xml',
	'object'	=> 'TodoyuContactInfoType',
	'delete'	=> 'TodoyuContactInfoTypeManager::deleteContactTypeInfo',
	'save'		=> 'TodoyuContactInfoTypeManager::saveContactInfoType',
	'table'		=> 'ext_contact_contactinfotype'
));

?>