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
 * class to upload profile images & company logos
 */
Todoyu.Ext.contact.Upload = {

	/**
	 * Shows upload form on button click
	 *
	 * @param	{String}	form
	 * @param	{String}	recordType		(person / company)
	 */
	showUploadForm: function(form, recordType) {
		var idRecord = $(form).id.split('-')[1];

		this.addUploadForm(idRecord, recordType);

		$$('button.buttonUploadContactImage')[0].hide();
	},



	/**
	 * Remove upload form and unhide button making it shown
	 */
	removeUploadForm: function() {
		if( Object.isElement( $('contactimage-uploadform') ) ) {
			$('contactimage-uploadform').remove();
		}
		$$('button.buttonUploadContactImage').first().show();
	},



	/**
	 * Add upload form for contact images
	 *
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType	(person / company)
	 */
	addUploadForm: function(idRecord, recordType) {
		var url		= Todoyu.getUrl('contact', 'formhandling');
		var options	= {
			'parameters': {
				'action':		'contactimageuploadform',
				'idRecord':		idRecord,
				'recordType':	recordType
			}
		};
		var target	= $$('button.buttonUploadContactImage')[0].id;
		Todoyu.Ui.append(target, url, options);
	},



	/**
	 * Upload contact image
	 *
	 * @param	{String}	form
	 * @todo	clean up
	 */
	upload: function(form) {
		var field	= $(form).down('input[type=file]');

		if( field.value !== '' ) {
				// Create iFrame for contact image upload
			Todoyu.Form.addIFrame('contactimage');

			$(form).submit();
		}
	},



	/**
	 * Contact image upload finished handler
	 * 
	 * @param	{String}	recordType		(person / company)
	 * @param	{Number}	idContact
	 * @param	{Number}	idReplace
	 */
	uploadFinished: function(recordType, idContact, idReplace) {
		var form = $(recordType + '-' + idContact + '-form');

		this.refreshPreviewImage(form, idContact ? idContact : idReplace, recordType);
		this.setReplaceIdToHiddenField(form, idReplace, recordType);

		this.removeUploadForm();
		Todoyu.notifySuccess('[LLL:contact.contactimage.upload.success]');
	},



	/**
	 * Refreshes the preview image in the form
	 *
	 * @param	{String}	form
	 * @param	{Number}	idImage
	 * @param	{String}	recordType		(person / company)
	 */
	refreshPreviewImage: function(form, idImage, recordType) {
		var url		= Todoyu.getUrl('contact', recordType);
		var options	= {
			'parameters': {
				'action':		'loadimage',
				'idImage':		idImage
			}
		};
		var target	= form.down('div.fieldnamePreview img');

		Todoyu.Ui.replace(target, url, options);
	},



	/**
	 * Sets the temporary id (folder-name) of the uploaded image to the hidden field 
	 *
	 * @param	{String}	form
	 * @param	{Number}	idReplace
	 * @param	{String}	recordType		(person / company)
	 */
	setReplaceIdToHiddenField: function(form, idReplace, recordType) {
		var field = $(form).down('[name = ' + recordType +'[image_id]]');
		field.setValue(idReplace);
	},



	/**
	 * Check whether upload failed, determine reason (file too big / failure) and notify
	 *
	 * @method	uploadFailed
	 * @param	{Number}		error		1 = filesize exceeded, 2 = failure
	 * @param	{String}		filename
	 * @param	{Number}		maxFileSize
	 */
	uploadFailed: function(error, filename, maxFileSize) {
		this.removeUploadForm();

		var info	= {
			'filename': 	filename,
			'maxFileSize':	maxFileSize
		};

		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:contact.contactimage.upload.maxFileSizeExceeded]';
		} else {
			msg	= '[LLL:contact.contactimage.upload.uploadFailed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 10);
	},



	/**
	 * Send Request to remove image of current user
	 *
	 * @param	{String}	form
	 * @param	{String}	recordType
	 */
	removeImage: function(form, recordType) {
		var url = Todoyu.getUrl('contact', 'formhandling');
		var idImage = this.getImageId(form, recordType);

		var options = {
			'parameters': {
				'action':		'removeimage',
				'idRecord':		idImage,
				'recordType':	recordType
			},
			'onComplete': this.refreshPreviewImage.bind(this, form, idImage, recordType)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Returns the id of the image.
	 *
	 * @param	{String}	form
	 * @param	{String}	recordType
	 */
	getImageId: function(form, recordType) {
		var field = $(form).down('[name = ' + recordType +'[image_id]]');

		if( field && field.getValue() ) {
			return field.getValue()
		} else {
			return $(form).id.split('-')[1];
		}
	}
};