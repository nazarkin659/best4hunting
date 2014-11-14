/*======================================================================*\
|| #################################################################### ||
|| # Copyright �2006-2009 Youjoomla LLC. All Rights Reserved.           ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- THIS IS NOT FREE SOFTWARE ---------------- #      ||
|| # http://www.youjoomla.com | http://www.youjoomla.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
var AjaxForm = new Class({
	Implements: [Options],
	options:{
		form: null,
		categorySelect: null,
		categoryOptions: null,
		errorMessageClass: null,
		messageTextarea: null,
		disableElements: null,
		selectedOption: null,
		responseParent: null,
		responseLoadClass: null,
		responseClass: null,
		uploadLink: null,
		uploadElem: null,
		uploadMessage:null,
		uploadLoading:null,
		LiveSite:null,
		imageId:null,
		sessionId:null,
		inputId:null,
		selectDepartmentTxt:null,
		selectDepartmentError:null,			
		uploadZipTxt:null,
		uploadSuccesTxt:null
	},
	
	
	initialize: function(options) {
		this.setOptions(options);		
		if(!$(this.options.form)) return;		
		this.form = $(this.options.form);	
		this.select = $(this.options.categorySelect);
		this.textarea = $(this.options.messageTextarea);
		this.selectOptions = new Hash(this.options.categoryOptions);
		this.disableElements = new Hash(this.options.disableElements);
		this.start();
	},
	
	start: function(){
		/* upload separately */
		$(this.options.uploadElem).addEvent('change', function(){
			var originalAction = this.form.get('action');
			this.form.set({'action': this.options.uploadLink});
			this.form.submit();
			this.form.set({'action': originalAction});
			
			$(this.options.uploadElem).setStyle('display','none');
			$('input_file_wrapper').setStyle('display','none');
			this.uploadMessage.set({'class':this.options.uploadLoading}).setStyle('display','block').empty();
		}.bind(this));	
		
		/* set the AJAX submit on the form */
		this.form.addEvent('submit', this.sendForm.bind(this));	
			
		/* reset the select and set the new options */
		this.select.length = 0;
		/* inject the additional containers */
		this.inject();
		/* set the first option */
		this.select[0] = new Option( this.options.selectDepartmentTxt, '');	
		
		/* populate with the options from categoryOptions */
		var index = 1;
		this.selectOptions.each(function(option, i){
			if(this.selectOptions.getLength() > 1){
				var selected = 	i == this.options.selectedOption.toInt() ? true :false;
			}else{
				var selected = 	true;
			}
			this.select[index] = new Option( option.option , option.id , selected , selected );	
			index++;
		}.bind(this));
		/* add onchange event */
		this.select.addEvent('change', this.showMessages.bind(this));	
		
		//*
		$('submit_btn').setStyle('display','none');
		$('captcha_row').setStyle('display','none');
		$('upload_row').setStyle('display','none');
		$('name').set({'disabled': 'disabled'});
		$('email').set({'disabled': 'disabled'});
		$('subject').set({'disabled': 'disabled'});
		$('captcha').set({'disabled': 'disabled'});
		$('message').set({'disabled': 'disabled'});
		//*/
		
		this.response = new Element('div',{
			'class':this.options.responseClass,
			'styles':{
				'display':'block'
			},
			'html':this.htmlspecialchars_decode(this.options.selectDepartmentError, 'ENT_QUOTES')
		}).injectInside($(this.options.responseParent));
		
		if( this.options.selectedOption || this.selectOptions.getLength() <= 1) {
			this.showMessages();
		}
	},
	
	sendForm: function(event){
		new Event(event).stop();
		this.response.set({'class':this.options.responseLoadClass}).setStyle('display','block').empty();
		
		new Request.JSON({
			url:this.form.get('action'),
			onComplete: function(json){
				this.response.set({'class':this.options.responseClass, 'html':json.error||json.success});	
				// if fields are set, remove class error from them. It will get added later if errors were generated
				if( json.fields ){
					var err_fields = new Hash(json.fields);
					err_fields.each(function(value, key){
						$(key).erase('disabled').removeClass('YJC_error');
					});
				}
				
				if(json.success){					
					this.options.disableElements.each(function(elem){
						$(elem).set({'disabled': 'disabled'});
						//this.reload_captcha_js(this.options.LiveSite,this.options.imageId,this.options.sessionId,this.options.inputId);
					});	
				}else{
					//this.reload_captcha_js(this.options.LiveSite,this.options.imageId,this.options.sessionId,this.options.inputId);		
					// highlight error fields. they come is json under fields as field_id=>error ( 0 - not error, 1 - is error )
					var err_fields = new Hash(json.fields);
					err_fields.each(function(value, key){
						if(value==1)
							$(key).addClass('YJC_error');
					});		
				}
			}.bind(this)
		}).post(this.form);
	},	
	
	showMessages: function(){
		
		var disables = [];
		disables[0] = 'name';
		disables[1] = 'email';
		disables[2] = 'subject';
		disables[3] = 'captcha';
		disables[4] = 'message';
		
		var displays = [];
		displays[0] = 'submit_btn';
		displays[1] = 'captcha_row';
		displays[2] = 'upload_row';
		
		//remove error class and field values from previous sendForm action
		disables.each(function(e){
			$(e).set({'disabled':'disabled'}).removeClass('YJC_error');
		})
		
		if( $(this.options.categorySelect).value !== '' ){
			this.reload_captcha_js(this.options.LiveSite,this.options.imageId,this.options.sessionId,this.options.inputId);
		}else{
			displays.each(function(e){
				$(e).setStyle('display', 'none');
			})
		
			$(this.options.responseParent).empty();
			this.response = new Element('div',{
				'class':this.options.responseClass,
				'styles':{
					'display':'block'
				},
				'html':this.htmlspecialchars_decode(this.options.selectDepartmentError, 'ENT_QUOTES')
			}).injectInside($(this.options.responseParent));
			this.messages.setStyle('display','none');
			
			return true;
		}
		
		var element = this.selectOptions.get(this.select.value.toInt());
		
		if(element.disabled){
			displays.each(function(e){
				$(e).setStyle('display', 'none');
			})
			
			disables.each(function(e){
				$(e).set({'disabled':'disabled'}).removeClass('YJC_error');
			})
			
		}else{
			displays.each(function(e){
				$(e).setStyle('display', '');
			})	
			disables.each(function(e){
				$(e).erase('disabled').removeClass('YJC_error');
			})
		}
		
		this.messages.empty();
		this.response.empty();
		this.uploadMessage.setStyle('display','none').empty();
		$(this.options.uploadElem).setStyle('display','');
		
		this.disableElements.each(function(elem){
			$(elem).erase('disabled');
		});	
		this.textarea.set({'value':''});
		this.messages.setStyle('display','none');	
		if(!element) return;
		
		element.message = this.htmlspecialchars_decode(element.message, 'ENT_QUOTES');

		if( element.message )
			this.messages.set({'html':element.message}).setStyle('display','block');	
		else
			this.messages.setStyle('display','none');
			
		this.textarea.set({'value':element.disabled_message||''});
		this.disableElements.each(function(elem){
			if( element.disabled ){
				$(elem).set({'disabled':'disabled'});
			}else{
				$(elem).erase('disabled');
			}
		});	
		if(element.disable_fields){
			element.disable_fields.each(function(elem){
				if(elem == 'captcha') $('captcha_row').setStyle('display','none');
				
				if(elem == 'archive'){
					$('upload_row').setStyle('display','none');
				}else{
					$(elem).set({'disabled': 'disabled'});
				}
			})
		}
	},
	
	inject: function(){
		this.messages = new Element('div',{
			'class':this.options.errorMessageClass,
			'styles':{
				'display':'none'
			},
			'html':''
		}).injectAfter(this.select);
		
		this.response = new Element('div',{
			'class':this.options.responseClass,
			'styles':{
				'display':'none'
			}
		}).injectInside($(this.options.responseParent));
		
		var injectUpAfter = $('input_file_wrapper') ? 'input_file_wrapper' : this.options.uploadElem;		
		this.uploadMessage = new Element('div',{
			'class':this.options.uploadMessage,
			'styles':{
				'display':'none'
			}
		}).injectAfter($(injectUpAfter));
	},
	
	stopUpload: function(response){
		if(response==0){
			$(this.options.uploadElem).setStyle('display','');
			$('input_file_wrapper').setStyle('display','');
			this.uploadMessage.set({'class':this.options.uploadMessage, 'html':this.options.uploadZipTxt});
		}	
		else{
			this.uploadMessage.set({'class':this.options.uploadMessage, 'html':this.options.uploadSuccesTxt});
		}
	},
	
	get_html_translation_table: function (table, quote_style){
		var entities = {}, hash_map = {}, decimal = 0, symbol = '';
		var constMappingTable = {}, constMappingQuoteStyle = {};
		var useTable = {}, useQuoteStyle = {};
		
		// Translate arguments
		constMappingTable[0]      = 'HTML_SPECIALCHARS';
		constMappingTable[1]      = 'HTML_ENTITIES';
		constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
		constMappingQuoteStyle[2] = 'ENT_COMPAT';
		constMappingQuoteStyle[3] = 'ENT_QUOTES';
		
		useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
		useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';
		
		if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
			throw new Error("Table: "+useTable+' not supported');
			// return false;
		}
		
		entities['38'] = '&amp;';
		if (useTable === 'HTML_ENTITIES') {
			entities['160'] = '&nbsp;';
			entities['161'] = '&iexcl;';
			entities['162'] = '&cent;';
			entities['163'] = '&pound;';
			entities['164'] = '&curren;';
			entities['165'] = '&yen;';
			entities['166'] = '&brvbar;';
			entities['167'] = '&sect;';
			entities['168'] = '&uml;';
			entities['169'] = '&copy;';
			entities['170'] = '&ordf;';
			entities['171'] = '&laquo;';
			entities['172'] = '&not;';
			entities['173'] = '&shy;';
			entities['174'] = '&reg;';
			entities['175'] = '&macr;';
			entities['176'] = '&deg;';
			entities['177'] = '&plusmn;';
			entities['178'] = '&sup2;';
			entities['179'] = '&sup3;';
			entities['180'] = '&acute;';
			entities['181'] = '&micro;';
			entities['182'] = '&para;';
			entities['183'] = '&middot;';
			entities['184'] = '&cedil;';
			entities['185'] = '&sup1;';
			entities['186'] = '&ordm;';
			entities['187'] = '&raquo;';
			entities['188'] = '&frac14;';
			entities['189'] = '&frac12;';
			entities['190'] = '&frac34;';
			entities['191'] = '&iquest;';
			entities['192'] = '&Agrave;';
			entities['193'] = '&Aacute;';
			entities['194'] = '&Acirc;';
			entities['195'] = '&Atilde;';
			entities['196'] = '&Auml;';
			entities['197'] = '&Aring;';
			entities['198'] = '&AElig;';
			entities['199'] = '&Ccedil;';
			entities['200'] = '&Egrave;';
			entities['201'] = '&Eacute;';
			entities['202'] = '&Ecirc;';
			entities['203'] = '&Euml;';
			entities['204'] = '&Igrave;';
			entities['205'] = '&Iacute;';
			entities['206'] = '&Icirc;';
			entities['207'] = '&Iuml;';
			entities['208'] = '&ETH;';
			entities['209'] = '&Ntilde;';
			entities['210'] = '&Ograve;';
			entities['211'] = '&Oacute;';
			entities['212'] = '&Ocirc;';
			entities['213'] = '&Otilde;';
			entities['214'] = '&Ouml;';
			entities['215'] = '&times;';
			entities['216'] = '&Oslash;';
			entities['217'] = '&Ugrave;';
			entities['218'] = '&Uacute;';
			entities['219'] = '&Ucirc;';
			entities['220'] = '&Uuml;';
			entities['221'] = '&Yacute;';
			entities['222'] = '&THORN;';
			entities['223'] = '&szlig;';
			entities['224'] = '&agrave;';
			entities['225'] = '&aacute;';
			entities['226'] = '&acirc;';
			entities['227'] = '&atilde;';
			entities['228'] = '&auml;';
			entities['229'] = '&aring;';
			entities['230'] = '&aelig;';
			entities['231'] = '&ccedil;';
			entities['232'] = '&egrave;';
			entities['233'] = '&eacute;';

			entities['234'] = '&ecirc;';
			entities['235'] = '&euml;';
			entities['236'] = '&igrave;';
			entities['237'] = '&iacute;';
			entities['238'] = '&icirc;';
			entities['239'] = '&iuml;';
			entities['240'] = '&eth;';
			entities['241'] = '&ntilde;';
			entities['242'] = '&ograve;';
			entities['243'] = '&oacute;';
			entities['244'] = '&ocirc;';
			entities['245'] = '&otilde;';
			entities['246'] = '&ouml;';
			entities['247'] = '&divide;';
			entities['248'] = '&oslash;';
			entities['249'] = '&ugrave;';
			entities['250'] = '&uacute;';
			entities['251'] = '&ucirc;';
			entities['252'] = '&uuml;';
			entities['253'] = '&yacute;';
			entities['254'] = '&thorn;';
			entities['255'] = '&yuml;';
		}
		
		if (useQuoteStyle !== 'ENT_NOQUOTES') {
			entities['34'] = '&quot;';
		}
		if (useQuoteStyle === 'ENT_QUOTES') {
			entities['39'] = '&#39;';
		}
		entities['60'] = '&lt;';
		entities['62'] = '&gt;';
		
		
		// ascii decimals to real symbols
		for (decimal in entities) {
			symbol = String.fromCharCode(decimal);
			hash_map[symbol] = entities[decimal];
		}
		
		return hash_map;
	},
	
	htmlspecialchars_decode	: function(string, quote_style){
		var hash_map = {}, symbol = '', tmp_str = '', entity = '';
		tmp_str = string.toString();
		
		if (false === (hash_map = this.get_html_translation_table('HTML_SPECIALCHARS', quote_style))) {
			return false;
		}
		
		for (symbol in hash_map) {
			entity = hash_map[symbol];
			tmp_str = tmp_str.split(entity).join(symbol);
		}
		tmp_str = tmp_str.split('&#039;').join("'");
		
		return tmp_str;
	},
	
	reload_captcha_js : function(live_site,id,session_id,input_id){
		$(id).src = live_site+"components/com_yjcontactus/helpers/captcha.php"+session_id + "&refresh=" + Math.random();
		$(input_id).value = '';
	}
	
});