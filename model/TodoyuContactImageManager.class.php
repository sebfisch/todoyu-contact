<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
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
	 * Check whether the contact of given type and ID has an image (real file, not the displayed fallback dummy) assigned
	 *
	 * @param	String		$imageKey	ID of TodoyuContactPerson / TodoyuContactCompany
	 * @param	String		$typeKey	'person' / 'company'
	 * @return	Boolean
	 */
	public static function hasImage($imageKey, $typeKey) {
		$pathContactImage	= self::getPathContactImage($imageKey, $typeKey);

		return TodoyuFileManager::isFile($pathContactImage);
	}



	/**
	 * Returns the preview image of a person
	 *
	 *
	 * @param	TodoyuFormElement_Comment	$formElement
	 * @param	String						$typeKey		'person' / 'company'
	 * @return	String
	 */
	public static function renderImageForm(TodoyuFormElement_Comment $formElement, $typeKey) {
		$idRecord	= intval($formElement->getForm()->getHiddenField('id'));
		$idImage	= $formElement->getForm()->getHiddenField('image_id');

		if( !$idImage ) {
			$idImage	= $idRecord;
		}

		$dummy	= $idRecord === 0 || !self::hasImage($idRecord, $typeKey);

		return self::getImage($idImage, $typeKey, $dummy);
	}



	/**
	 * Returns the image tag of the given contact type (person / company)
	 *
	 * @param	Integer		$idImage
	 * @param	String		$typeKey		'person' / 'company'
	 * @param	Boolean		$isDummy
	 * @return	String
	 */
	public static function getImage($idImage, $typeKey, $isDummy = true) {
		$params	= array(
			'ext'			=> 'contact',
			'controller'	=> $typeKey,
			'action'		=> 'renderimage',
			'idImage'		=> $idImage,
			'hash'			=> NOW
		);

		$imgSrc	= TodoyuString::buildUrl($params);
		$alt	= $isDummy ? 'none' : '';

		return TodoyuString::getImgTag($imgSrc, 0, 0, $alt);
	}



	/**
	 * Renders the Image. Needed because the files folder is .htaccess protected.
	 * If no picture of an user is found, one of randomly 7 images is taken
	 *
	 * @param	Integer		$imageKey		ID of TodoyuContactPerson / TodoyuContactCompany
	 * @param	String		$typeKey		'person' / 'company'
	 */
	public static function renderImage($imageKey, $typeKey) {
			// Image ID === 0 => get random dummy image
		$imageKey	=  self::hasImage($imageKey, $typeKey) ? $imageKey : 0;
		$filePath	=  self::getPathContactImage($imageKey, $typeKey);

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
	 * @param	Integer		$idRecord		ID of TodoyuContactPerson / TodoyuContactCompany
	 * @param	String		$typeKey		'person' / 'company'
	 * @return	Integer
	 */
	public static function store($path, $name, $mime, $idRecord, $typeKey) {
		$idRecord	= intval($idRecord);

		$storageDir	= self::getPathStorageDir($typeKey);

		if( $idRecord == 0 ) {
			$idRecord	= md5(NOW);
			$new		= true;
		} else {
			$new	= false;
		}

		$dimension			= self::getDimension();
		$pathResizedImage	= $storageDir . '/' . $idRecord . '/' . self::$destFileName;

		TodoyuImageManager::saveResizedImage($path, $pathResizedImage, $dimension['x'], $dimension['y'], null, true);

		return $new ? $idRecord : 0;
	}



	/**
	 * Get path to storage directory
	 *
	 * @param	String	$typeKey		'person' / 'company'
	 * @return	String
	 */
	public static function getPathStorageDir($typeKey) {
		return TodoyuFileManager::pathAbsolute(Todoyu::$CONFIG['EXT']['contact']['contactimage']['path' . $typeKey]);
	}



	/**
	 * Gets the web-path to the image
	 *
	 * @param	String	$typeKey		'person' / 'company'
	 * @return	String
	 */
	public static function getPathWebDir($typeKey) {
		return TodoyuFileManager::pathWeb(Todoyu::$CONFIG['EXT']['contact']['contactimage']['path' . $typeKey]);
	}



	/**
	 * Get path to profile image of contact record of given type + ID
	 *
	 * @param	Integer		$imageKey		ID of TodoyuContactPerson / TodoyuContactCompany
	 * @param	String		$typeKey		'person' / 'company'
	 * @return	String
	 */
	public static function getPathContactImage($imageKey, $typeKey) {
		if( $imageKey === 0 ) {
				// Get random dummy image
			$path	= PATH . '/ext/contact/asset/img/persondefault/user0' . rand(1, 6) . '.png';
		} else {
			$path	= self::getPathStorageDir($typeKey)  . '/' . $imageKey . '/' . self::$destFileName;
		}

		return $path;
	}



	/**
	 * Rename storage folder.
	 *
	 * @param	String		$typeKey 			'person' / 'company'
	 * @param	String		$temporaryImageKey
	 * @param	Integer		$idRecord
	 */
	public static function renameStorageFolder($typeKey, $temporaryImageKey, $idRecord) {
		$storagePath	= self::getPathStorageDir($typeKey);

		if( is_dir($storagePath) ) {
			TodoyuFileManager::rename($storagePath . '/' . $temporaryImageKey, $storagePath . '/' . $idRecord);
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
	 * @param	String		$typeKey		'person' / 'company'
	 */
	public static function removeImage($idImage, $typeKey) {
		$storageDir	= self::getPathStorageDir($typeKey);
		$dir		= $storageDir . '/' . $idImage;

		if( is_file($dir . '/contactimage.png') ) {
			unlink($dir . '/contactimage.png');
			rmdir($dir);
		}
	}



	/**
	 * Check whether the type of the uploaded file is in the allowed types
	 *
	 * @param	String	$typeKey	'person' / 'company'
	 * @return	Boolean
	 */
	public static function checkFileType($typeKey) {
		$allowedTypes	= Todoyu::$CONFIG['EXT']['contact']['contactimage']['allowedTypes'];

		if( !in_array($typeKey, $allowedTypes) ) {
			return false;
		}

		return true;
	}

}

?>