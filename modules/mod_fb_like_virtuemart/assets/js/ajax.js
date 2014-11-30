jsonAjax = new jsonAjaxConstructor();
function jsonAjaxConstructor(){
	this.async = true;
	this.onError = onError;
	this.onWait = popWait;
	this.onWaitEnd = killWait;
}
// perform the XMLHttpRequest();
function vmHttp(verb,url,rm,qry) {
    //reference our arguments
	var callback = rm;
	var qryStr = (!qry) ? '' : toQueryString(qry);
	var calledOnce = false; //this is to prevent a bug in onreadystatechange... "state 1" gets called twice.
	try{//this should work for most modern browsers excluding: IE Mac
		var req = ( window.XMLHttpRequest ) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP") ;
			req.onreadystatechange = function(){
			switch(req.readyState){
				case 1: 
					if(!calledOnce){
						jsonAjax.onWait();
						calledOnce = true;
					}
					break;
				case 2: break;
				case 3: break;
				case 4:
					jsonAjax.onWaitEnd();
					if ( req.status == 200 ){// only if "OK"
						try{
							rObj = parseResponse( req ) ;
							success = true;
						}catch(e){ 
							jsonAjax.onError('Parsing Error: The value returned could not be evaluated.');
							success = false;
						}
						if(success) callback( rObj );
					}else{ 
						jsonAjax.onError("There was a problem retrieving the data:\n" + req.statusText);
					}
					break;
				}
			}
			req.open( verb , noCache(url) , jsonAjax.async );
			if(verb.toLowerCase() == 'post')	
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.send(qryStr);
	}catch(e){//a browser not equiped to handle XMLHttp
		//jsonAjax.onError("There was a problem retrieving the data:");
	}
}

/*--- BEGIN: RESPONSE PARSING FUNCTIONS ---*/
function parseResponse(rO){
	//FIRST TRY IT AS XML
	if(rO.getResponseHeader("Content-Type").split(';')[0] == 'text/xml'){	
		return rO.responseXML;
	}	
	
	var str = rO.responseText;
	// jan.jannek@cetecom.de, 2006-02-16, weird error: some IEs show the responseText followed by the complete response (header and body again) 
	var i = str.indexOf("HTTP/1");
	if (i > -1) {
		str = str.substring(i, str.length);
		i = str.indexOf(String.fromCharCode(13, 10, 13, 10));
		if (i > -1) {
			str = str.substring(i + 2, str.length);
		}
	}		
	
	//NEXT TRY IT AS WDDX
	if(str.indexOf("<wddxPacket") > -1){
		return parseWDDX(str);	
	}
	
	
	//DO THE STRING EVAL
	try{//next try JSON eval();
		return parseJSON(str);
	}catch(e){//then try Classic eval();
		return parseJS(str);
	}
}	
function parseJS(str){ 
	eval(str);
	var r = eval(str.split('=')[0].replace(/\s/g,''));
	return r;
}
function parseJSON(str){
	return  eval('('+str+')');;
}

function parseWDDX(str){ var wddx = xmlStr2Doc(str); var data = wddx.getElementsByTagName("data"); return __parseWDDXnode(data[0].firstChild); } function xmlStr2Doc(str){ var xml; if(typeof(DOMParser) == 'undefined'){ xml=new ActiveXObject("Microsoft.XMLDOM"); xml.async="false"; xml.loadXML(str); }else{ var domParser = new DOMParser(); xml = domParser.parseFromString(str, 'application/xml'); } return xml; } function __parseWDDXnode(n){ var val; switch(n.tagName){ case 'string': val = __parseWDDXstring(n); break; case 'number': val = parseInt(n.firstChild.data); break; case 'boolean': val = n.getAttribute('value'); break; case 'dateTime': val = Date(n.firstChild.data); break; case 'array': val = __parseWDDXarray(n); break; case 'struct': val = __parseWDDXstruct(n); break; case 'recordset': val = __parseWDDXrecordset(n); break; case 'binary': val = n.firstChild.data; break; case 'char': val = __parseWDDXchar(n);; break; case 'null': val = ''; break; default: val = n.tagName; break; } return val; } function __parseWDDXstring(node){ var items = node.childNodes; var str = ''; for(var x=0;x < items.length;x++){ if(typeof(items[x].data) != 'undefined') str += items[x].data; else str += __parseWDDXnode(items[x]); } return str; } function __parseWDDXchar(node){ switch(node.getAttribute('code')){ case '0d': return '\r'; case '0c': return '\f'; case '0a': return '\n'; case '09': return '\t'; } } function __parseWDDXarray(node){ var items = node.childNodes; var arr = new Array(); for(var i=0;i < items.length;i++){ arr[i] = __parseWDDXnode(items[i]); } return arr; } function __parseWDDXstruct(node){ var items = node.childNodes; var obj = new Object(); for(var i=0;i < items.length;i++){ obj[items[i].getAttribute('name').toLowerCase()] = __parseWDDXnode(items[i].childNodes[0]); } return obj; } function __parseWDDXrecordset(node){ var qry = new Object(); var fields = node.getElementsByTagName("field"); var items; var dataType; var values; for(var x = 0; x < fields.length; x++){ items = fields[x].childNodes; values = new Array(); for(var i = 0; i < items.length; i++){ values[values.length] = __parseWDDXnode(items[i]); } qry[fields[x].getAttribute('name').toLowerCase()] = values; } return qry; }
//END: WDDX deserializer functions

/*--- END: RESPONSE PARSING FUNCTIONS ---*/


/*--- BEGIN: REQUEST PARAMETER FUNCTIONS ---*/
	function toQueryString(obj){
		//determine the variable type
		if(typeof(obj) == 'string')
			return obj;
		if(typeof(obj) == 'object'){
			if(typeof(obj.elements) == 'undefined')//It's an Object()!
				return object2queryString(obj);
			else //It's a form!
				return form2queryString(obj);
		}	
	}
	
	function object2queryString(obj){
		var ar = new Array();
		for(x in obj) ar[ar.length] = x+'='+obj[x];
		return ar.join('&');
	}
	
	function form2queryString(form){
		var obj = new Object();
		var ar = new Array();
		for(var i=0;i<form.elements.length;i++){
			try {
				elm = form.elements[i];
				nm = elm.name;
				if(nm != ''){
					switch(elm.type.split('-')[0]){
						case "select":
							for(var s=0;s<elm.options.length;s++){
								if(elm.options[s].selected){
									if(typeof(obj[nm]) == 'undefined') obj[nm] = new Array();
									obj[nm][obj[nm].length] = escape(elm.options[s].value);
								}	
							}
							break;
						
						case "radio":
							if(elm.checked){
								if(typeof(obj[nm]) == 'undefined') obj[nm] = new Array();
								obj[nm][obj[nm].length] = escape(elm.value);
							}	
							break;
						
						case "checkbox":
							if(elm.checked){
								if(typeof(obj[nm]) == 'undefined') obj[nm] = new Array();
								obj[nm][obj[nm].length] = escape(elm.value);
							}	
							break;
						
						default:
							if(typeof(obj[nm]) == 'undefined') obj[nm] = new Array();
							obj[nm][obj[nm].length] = escape(elm.value);
							break;
					}
				}
			}catch(e){}
		}
		for(x in obj) ar[ar.length] = x+'='+obj[x].join(',');
	return ar.join('&');
	}
/*--- END: REQUEST PARAMETER FUNCTIONS ---*/


//IE likes to cache so we will fix it's wagon!
function noCache(url){
	var qs = new Array();
	var arr = url.split('?');
	var scr = arr[0];
	if(arr[1]) qs = arr[1].split('&');
	qs[qs.length]='nocache='+new Date().getTime();
return scr+'?'+qs.join('&');
}

function popWait(){ 
	proc = document.getElementById("JSMX_loading");
	if( proc == null ){
		var p = document.createElement("div");
		p.id = "JSMX_loading";
		document.body.appendChild(p);
	}
}

function killWait(){
	proc = document.getElementById("JSMX_loading");
	if( proc != null ) document.body.removeChild(proc);
}

function onError(str){ alert(str); }