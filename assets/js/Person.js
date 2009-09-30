/**
 * @author ferni
 */
Todoyu.Ext.contact.Person =  {
	
	ext: Todoyu.Ext.contact,
	
	add: function() {
		this.edit(0);
	},
	
	edit: function(idPerson) {
		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			'parameters': {
				'person': idPerson,
				'cmd': 'edit'
			},
			'onComplete': this.onEdit.bind(this, idPerson)
		};
		
		this.ext.update(url, options);
	},
	
	onEdit: function(idPerson, response) {
		this.observeFieldsForShortname(idPerson);
	},
	
	observeFieldsForShortname: function(idPerson) {
		$('person-' + idPerson + '-field-lastname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
		$('person-' + idPerson + '-field-firstname').observe('keyup', this.generateShortName.bindAsEventListener(this, idPerson));
	},
	
	generateShortName: function(event, idPerson) {
		var lastname	= $F('person-' + idPerson + '-field-lastname');
		var firstname	= $F('person-' + idPerson + '-field-firstname');
		
		if( lastname.length >= 2 && firstname.length >= 2 ) {
			$('person-' + idPerson + '-field-shortname').value = firstname.substr(0,2).toUpperCase() + lastname.substr(0,2).toUpperCase();
		}
	},	
	
	remove: function(idPerson) {
		if( confirm('[LLL:contact.confirmRemoving]') )	{
			var url = Todoyu.getUrl('contact', 'person');
			var options = {
				'parameters': {
					'cmd':		'remove',
					'person':	idPerson
				},
				'onComplete': this.onRemoved.bind(this)
			};

			Todoyu.send(url, options);
		}
	},
	
	onRemoved: function(response) {
		this.showList();
	},
	
	save: function(form) {
		$(form).request ({
				'parameters': {
					'cmd': 'save'
				},
				'onComplete': this.onSaved.bind(this)
			});

		return false;
	},
	
	onSaved: function(response) {
		var error	= response.hasTodoyuError();
		
		if( error ) {
			$('contact-form-content').update(response.responseText);
		} else {
				// Notify (implement)
			alert('Person saved: ' + response.responseText);
			this.showList();
		}
	},
	
	showList: function() {
		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			'parameters': {
				'cmd': 'list'
			}
		};
		
		this.ext.update(url, options);
	},
	
	editUserRecord: function(idUser) {
		var params	= {
			'ext': 'admin',
			'mod': 'user',
			'cmd': 'edit',
			'user': idUser		
		}
		
		location.href = '?' + Object.toQueryString(params);
	},
	
	
	show: function(idPerson) {
		var url		= Todoyu.getUrl('contact', 'person')
		var options	= {
			'parameters': {
				'cmd': 'detail',
				'person': idPerson				
			}			
		};
		
		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, 340, 810, 200, url, options);
		/*
		var contentUrl = Todoyu.getUrl('contact', 'contactlist');
		contentUrl = contentUrl + '&cmd=infoPopupContent';

		var requestOptions	= {
			'parameters': {
				'type':		type,
				'idRecord':	idRecord
			}
		};

		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, 340, 810, 200, contentUrl, requestOptions);
		*/
	}
	
};