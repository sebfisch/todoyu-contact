/**
 * @author ferni
 */
Todoyu.Ext.contact.Company =  {
	
	ext: Todoyu.Ext.contact,
	
	add: function() {
		this.edit(0);
	},
	
	edit: function(idCompany) {
		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			'parameters': {
				'company': idCompany,
				'cmd': 'edit'
			},
			'onComplete': this.onEdit.bind(this, idCompany)
		};
		
		this.ext.update(url, options);
	},
	
	onEdit: function(idCompany, response) {
		
	},

		
	remove: function(idCompany) {
		if( confirm('[LLL:contact.confirmRemoving]') )	{
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
				'parameters': {
					'cmd':		'remove',
					'company':	idCompany
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
			Todoyu.notify('error', 'Form invalid', 2);
			Todoyu.notify('info', 'Ich bin eine Info', 20);
			$('contact-form-content').update(response.responseText);
		} else {
				// Notify (implement)
			Todoyu.notify('success', 'Company saved', 3);
			this.showList();
		}
	},
	
	showList: function(sword) {
		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			'parameters': {
				'cmd': 'list',
				'sword': sword
			}
		};
		
		this.ext.updateContent(url, options);
	},
		
	
	show: function(idCompany) {
		var url		= Todoyu.getUrl('contact', 'company')
		var options	= {
			'parameters': {
				'cmd': 'detail',
				'company': idCompany				
			}			
		};
		
		Todoyu.Popup.openWindow('popupRecordInfo', 'Info', 420, 340, 810, 200, url, options);
	}	
};