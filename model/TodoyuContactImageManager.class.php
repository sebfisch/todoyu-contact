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
 * Handler for contact (person / company) images
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactImageManager {

	/**
	 * Filename of the uploaded image in the files - folder
	 *
	 * @var	String
	 */
	protected static $destFileName = 'contactimage.png';



	/**
	 * Returns the preview image of a person
	 *
	 *
	 * @param	TodoyuFormElement_Comment	$formElement
	 * @param	String						$type
	 * @return	String
	 */
	public static function renderImageForm(TodoyuFormElement_Comment $formElement, $type) {
		$idImage	= $formElement->getForm()->getHiddenField('image_id') ? $formElement->getForm()->getHiddenField('image_id') : intval($formElement->getForm()->getHiddenField('id'));

		return self::getImage($idImage, $type);
	}



	/**
	 * Returns the person-image tag.
	 *
	 * @param	Integer		$idImage
	 * @param	String		$type
	 * @return	String
	 */
	public static function getImage($idImage, $type) {
		$params	= array(
			'ext'			=> 'contact',
			'controller'	=> $type,
			'action'		=> 'renderimage',
			'idImage'		=> $idImage,
			'hash'			=> time()
		);

//		$dimension	= TodoyuContactImageManager::getDimension();
		$imgSrc	= TodoyuString::buildUrl($params);

		return TodoyuString::getImgTag($imgSrc);
	}



	/**
	 * Renders the Image. Needed because the files folder is .htaccess protected.
	 * If no picture of an user is found, one of randomly 7 images is taken
	 *
	 * @param	Integer		$idImage
	 * @param	String		$type
	 */
	public static function renderImage($idImage, $type) {
		$filePath	= PATH . '/ext/contact/asset/img/persondefault/user0' . rand(1, 6) . '.png';

		if( is_file( self::getStorageDir($type)  . '/' . $idImage . '/' . self::$destFileName) ) {
			$filePath	=  self::getStorageDir($type)  . '/' . $idImage . '/' . self::$destFileName;
		}

		header('Content-Type: image/png');
		header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
		echo file_get_contents($filePath);
		exit();
	}



	/**
	 * Store a file
	 * - Add file to storage folder
	 * - Update the contact record
	 *
	 * @param	String		$path
	 * @param	String		$name
	 * @param	String		$mime
	 * @param	Integer		$idContact
	 * @param	String		$recordType
	 * @return	Integer
	 */
	public static function store($path, $name, $mime, $idContact, $recordType) {
		$idContact	= intval($idContact);

		$storageDir	= self::getStorageDir($recordType);

		if( $idContact == 0 ) {
			$idContact	= md5(NOW);
			$new		= true;
		} else {
			$new	= false;
		}

		$dimension	= self::getDimension();

		TodoyuImageManager::saveResizedImage($path, $storageDir . '/' . $idContact . '/' . self::$destFileName, $dimension['x'], $dimension['y'], null, true);
		return $new ? $idContact : 0;
	}



	/**
	 * Get path to storage directory
	 *
	 * @param	String	$type		e.g. 'person' / 'company'
	 * @return	String
	 */
	public static function getStorageDir($type) {
		return TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['contact']['contactimage']['path'.$type]);
	}



	/**
	 * Gets the web-path to the image
	 *
	 * @param	String	$type
	 * @return	String
	 */
	public static function getWebDir($type) {
		return TodoyuFileManager::pathWeb(Todoyu::$CONFIG['EXT']['contact']['contactimage']['path' . $type]);
	}



	/**
	 * Rename storage folder.
	 *
	 * @param	String	$type (person/company)
	 * @param	String	$old
	 * @param	Integer	$new
	 */
	public static function renameStorageFolder($type, $old, $new) {
		$storagePath	= self::getStorageDir($type);

		if( is_dir($storagePath) ) {
			rename($storagePath . '/' . $old, $storagePath . '/' . $new);
		}
	}



	/**
	 * Returns the dimension of a picture (set in contact/config/init.php)
	 *
	 * @return	Array
	 */
	protected static function getDimension() {
		return Todoyu::$CONFIG['EXT']['contact']['contactimage']['dimension'];
	}



	/**
	 * Removes the Image from the file folder
	 *
	 * @param	Integer		$idImage
	 * @param	String		$type		e.g. 'person' / 'company'
	 */
	public static function removeImage($idImage, $type) {
		$storageDir	= self::getStorageDir($type);
		$dir		= $storageDir . '/' . $idImage;

		if( is_file($dir . '/contactimage.png') ) {
			unlink($dir . '/contactimage.png');
			rmdir($dir);
		}
	}



	/**
	 * Check whether the type of the uploaded file is in the allowed types
	 *
	 * @param	String	$type	e.g. 'person' / 'company'
	 * @return	Boolean
	 */
	public static function checkFileType($type) {
		$allowedTypes	= Todoyu::$CONFIG['EXT']['contact']['contactimage']['allowedTypes'];

		if( !in_array($type, $allowedTypes) ) {
			return false;
		}

		return true;
	}
}

?>