
/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

var AUtility = {
	ajaxSend : function(url, callback, name) {
		var http_zadost = false;
		if (window.XMLHttpRequest) { // Mozilla, Safari, Opera, Konqueror...
			http_zadost = new XMLHttpRequest();
			if (http_zadost.overrideMimeType) {
				http_zadost.overrideMimeType('text/xml');
			}
		} else if (window.ActiveXObject) { // Internet Explorer
			try {
				http_zadost = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					http_zadost = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					alert('Send failed');
				}
			}
		}
		if (!http_zadost) {
			alert('Send failed');
			return false;
		}
		var d = '&';
		if (url.search(/\?/) == -1) {
			d = '?';
		}
		var r = AUtility.getRandomValue();
		url += d + 'randomParam' + '=' + r;
		http_zadost.open('GET', url, true);
		if (typeof callback == 'function') {
			http_zadost.onreadystatechange = function() {
				if (http_zadost.readyState == 4) {
					if (http_zadost.status == 200) {
						callback(http_zadost.responseText, name);
					} else {
						alert('Send failed');
					}
				}
			}
		}
		http_zadost.send(null);
		return true;
	},
	issetBooleanCookie : function(name) {
		var values = document.cookie.split(';');
		if (values.length) {
			for ( var i in values) {
				if ((typeof values[i] == 'string')
						&& (values[i].search(name + '=true') >= 0)) {
					return true;
				}
			}
		}
		return false;
	},
	setBooleanCookie : function(name) {
		var vyprs = new Date();
		vyprs.setDate(vyprs.getDate() + 365);
		document.cookie = name + "=true; expires=" + vyprs.toGMTString()
				+ "; path=/";
		return true;
	},
	ajaxSendPost : function(url, params, callback, name) {
		var http_ask = false;
		if (window.XMLHttpRequest) { // Mozilla, Safari, Opera, Konqueror...
			http_ask = new XMLHttpRequest();
			if (http_ask.overrideMimeType) {
				http_ask.overrideMimeType('text/xml');
			}
		} else if (window.ActiveXObject) { // Internet Explorer
			try {
				http_ask = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					http_ask = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					alert('Send failed');
					return false;
				}
			}
		}
		if (!http_ask) {
			alert('Send failed');
			return false;
		}
		var r = AUtility.getRandomValue();
		params += '&randomParam=' + r;
		http_ask.open('POST', url, true);

		http_ask.setRequestHeader("Content-type",
				"application/x-www-form-urlencoded");
		
		// 13.1.2014 dajo (#8306): Fixed the "Refused to set unsafe header" warnings
		//http_ask.setRequestHeader("Content-length", params.length);
		//http_ask.setRequestHeader("Connection", "close");

		if (typeof callback == 'function') {
			http_ask.onreadystatechange = function() {
				if (http_ask.readyState == 4) {
					if (http_ask.status == 200) {
						result = http_ask.responseText;
						callback(result, name);
					} else {
						alert('Send failed');
						return false;
					}
				}
			}
		}
		http_ask.send(params);
	},
	getDivElement : function(id) {
		var element = document.getElementById(id);
		return element;
	},
	getRandomValue : function() {
		var r = Math.random();
		r = r.toString();
		return r.replace(/(\.)/, "");
	}
}