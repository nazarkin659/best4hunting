

/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

var lastSelected=null;
var productID = null;
var url = null;

function showUserData(url){	
	var user_id = $('user_id').value;
	var as_guest = ($('as_guest') != null && $('as_guest').checked) ? 1 : 0;
	
	if (url=='') //get base url from form
		url = $('baseurl').value;
	
	if (user_id != null){
		url += 'index.php?option=com_vminvoice&controller=order&task=userajax&uid='+encodeURIComponent(user_id)+'&as_guest='+as_guest+'&tmpl=component';
		
		if ((typeof Request != "undefined") && (typeof Request.HTML != "undefined")){ //use request HTML which separates scripts and allows to evaluate them
		
			var req = new Request.HTML({ 
				url:url,
				evalScripts: false, 
				noCache: true, 
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript){
					updateUserInfo(responseHTML);
					eval(responseJavaScript);
				}
			}).send();
		}
		else
			AUtility.ajaxSend (url, updateUserInfo, null);
	}
}


function updateUserInfo(response,name){	
	document.getElementById('userInfo').innerHTML = response;
	
	//window.JTooltips.initialize();
	
	/*
	//eval all <scrips> on the page again - to load editors again (caused problems with editors not loaded and order cannot be saved)
	//EDIT: editors disabled completely in tmpl/userinfo.php
	var re = new RegExp('textarea', "g");
	if (response.match(re))
	{
		$each (document.getElements('script'),function (script){
			eval(script.innerHTML);
		});
	}
	*/
	
	//attach "onchange" events on input again
	changed_userinfo=false;
	addUserInfoCheck(); 
}

function copyBillingToDelivery()
{
	var s_name;
	
	//copy inputs
	$each($('billing_address').getElements('input'),function (b_input){
		
		s_name = 'S_'+b_input.getProperty('name').substring(2);
		
		//checkbox or radio
		if (b_input.getProperty('type')=='checkbox' || b_input.getProperty('type')=='radio')
		{
			//find collection of opposite inputs by name
			if (s_name.substr(-2)=='[]') //by this[] mootools is confused - use only begining
				s_inputs =  $('shipping_address').getElements('input[name^='+s_name.substr(0,s_name.length-2)+']');
			else
				s_inputs =  $('shipping_address').getElements('input[name='+s_name+']');

			$each(s_inputs,function (s_input){ //find input with same value

				if (s_input.value==b_input.value){
					//check or uncheck him
					if (b_input.checked) s_input.checked=true; 
					else s_input.checked=false;
				}
			});
		}
		//text input
		else {
			s_input =  $('shipping_address').getElement('input[name='+s_name+']');
			if (s_input)
				s_input.value=b_input.value;
		}
	});
	
	//copy selects
	$each($('billing_address').getElements('select'),function (b_input){
		
		//find selected options
		var selected = new Array();
		$each(b_input.getElements('option'),function (b_option){ //find selected options
			if (b_option.selected==true)
				selected[b_option.value]=b_option.value;
		});
		
		s_name = 'S_'+b_input.getProperty('name').substring(2);

		if (s_name.substr(s_name.length-2,2)=='[]') //by this[] mootools is confused - use only begining.  btw IE bug with substr http://rapd.wordpress.com/2007/07/12/javascript-substr-vs-substring/
			s_input =  $('shipping_address').getElement('select[name^='+s_name.substr(0,s_name.length-2)+']'); 
		else
			s_input =  $('shipping_address').getElement('select[name='+s_name+']');
		
		if (s_input)
		{
			$each(s_input.getElements('option'),function (s_option){ //iterate through options
				if (typeof selected[s_option.value] == 'undefined' ) //and select/unselect them
					s_option.selected=false
				else
					s_option.selected=true;
			});
		}
	});
	
	//copy textareas
	$each($('billing_address').getElements('textarea'),function (b_input){
		
		s_name = 'S_'+b_input.getProperty('name').substring(2);
		s_input = $('shipping_address').getElement('textarea[name='+s_name+']');
		
		if (s_input)
			s_input.value = b_input.value;
		
	});
	
	//copy iframe (editor)
	$each($('billing_address').getElements('iframe'),function (b_input){
		
		s_name = 'S_'+b_input.getProperty('id').substring(2);
		s_input = $('shipping_address').getElement('iframe[id='+s_name+']');
		
		if (s_input)
			s_input.contentWindow.document.body.innerHTML = b_input.contentWindow.document.body.innerHTML;
		
	});
	
	changed_userinfo=true;
	
	$('billing_is_shipping').checked = false;
	enableAllShipping();
}

function disableAllShipping()
{
	$each($('shipping_address').getElements('textarea,input,select'),function (el){
		if (el.name != 'billing_is_shipping')
			el.disabled = true;
	});
}

function enableAllShipping()
{
	$each($('shipping_address').getElements('textarea,input,select'),function (el){
		if (el.name != 'billing_is_shipping')
			el.disabled = false;
	});
}

function showOrderData(url, newProduct, overrideShipping, overridePayment, recomputeByRulesI) 
{	
	var params = '';
	
	if (url === undefined || !url) //get base url from form
		url = $('baseurl').value;	
	
	if (newProduct === undefined)
		newProduct = false;
	
	if (overrideShipping === undefined)
		overrideShipping = false;
	
	if (overridePayment === undefined)
		overridePayment = false;

	url += 'index.php?option=com_vminvoice&controller=order&task=orderajax&tmpl=component';
	
	if (newProduct){
		if ($('newproduct_id').value != '' || $('newproduct').value.trim() != ''){
			params = appendParams(params, 'pid', 'newproduct_id');
			params = appendParams(params, 'pname', 'newproduct');
			params = appendParams(params, 'pprice', 'newproduct_price');
			params = appendParams(params, 'pquantity', 'newproduct_quantity');
		}
		else {
			alert(AddProduct);
			return ;
		}
	}
	
	url += appendQuery('cid','cid');
	url += appendQuery('status','status');
	url += appendQuery('payment_method_id','payment_method_id');
	url += appendQuery('shipment_method_id','shipment_method_id'); //only for vm2
	
	url += appendQuery('coupon_discount','coupon_discount');
	url += appendQuery('order_discount','order_discount'); //only for vm1
	
	url += appendQuery('order_payment','order_payment'); 
	url += appendQuery('order_payment_tax','order_payment_tax'); //only for vm2
	//vm1 does not have payment separated and VM2 does not pass tax rate (its only guessed)
	
	url += appendQuery('order_shipping','order_shipping');
	url += appendQuery('order_shipping_tax','order_shipping_tax');
	url += appendQuery('order_shipping_taxrate','order_shipping_taxrate'); //only for vm1

	if (overrideShipping)
		url += '&override_shipping=1';
		
	if (overridePayment) //only for vm2. vm1 is overriden every time
		url += '&override_payment=1';
	
	if (recomputeByRulesI!==false) //only for VM2. force recompute prices for item by selected calculation rules
		url += '&recompute_rules_i='+recomputeByRulesI;
		
	params = appendProperty(params, 'orderInfo', 'input', 'product_quantity');
	params = appendProperty(params, 'orderInfo', 'textarea', 'product_attribute');
	params = appendProperty(params, 'orderInfo', 'select', 'order_status');
	params = appendProperty(params, 'orderInfo', 'input', 'product_item_price');
	params = appendProperty(params, 'orderInfo', 'input', 'product_tax');
	params = appendProperty(params, 'orderInfo', 'input', 'product_price_discount');
	params = appendProperty(params, 'orderInfo', 'input', 'product_id');
	params = appendProperty(params, 'orderInfo', 'input', 'order_item_id');
	params = appendProperty(params, 'orderInfo', 'input', 'order_item_name');
	params = appendProperty(params, 'orderInfo', 'input', 'order_item_sku');
	params = appendProperty(params, 'orderInfo', 'select', 'tax_rate');	//only for vm1
	params = appendProperty(params, 'orderInfo', 'input', 'tax_rate_guessed');	//only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'opened_attrs');
	params = appendProperty(params, 'orderInfo', 'input', 'product_price_with_tax'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'product_subtotal_with_tax'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'product_discountedPriceWithoutTax'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'product_priceWithoutTax'); //only for vm2
	
	params = appendProperty(params, 'orderInfo', 'input', 'virtuemart_order_calc_rule_id'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'select', 'calc_mathop'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'calc_value'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'select', 'calc_currency'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'calc_currency'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'opened_rules'); //only for vm2
	
	params = appendProperty(params, 'orderInfo', 'input', 'calc_rule_name'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'select', 'calc_kind'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'calc_kind'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'calc_amount'); //only for vm2
	params = appendProperty(params, 'orderInfo', 'input', 'calc_baseprice'); //only for vm2. on prices recompute again.
	params = appendProperty(params, 'orderInfo', 'select', 'calc_basecurrency'); //only for vm2. on prices recompute again.
	params = appendProperty(params, 'orderInfo', 'input', 'calc_override'); //only for vm2. on prices recompute again.
	params = appendProperty(params, 'orderInfo', 'input', 'calc_override_price'); //only for vm2. on prices recompute again.
	
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_key'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_name'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_value'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'select', 'product_attribute_value'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_plugin_key'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_plugin_key2'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_plugin_val2'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_textvalue'); //only for vm2. attributes rows.
	
	params = appendProperty(params, 'orderInfo', 'input', 'product_attribute_custom'); //only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'select', 'product_attribute_custom');//only for vm2. attributes rows.
	params = appendProperty(params, 'orderInfo', 'textarea', 'product_attribute_custom');//only for vm2. attributes rows.
	
	params = appendProperty(params, 'orderInfo', 'input', 'product_weight'); //because... its simpler to pass this for new product
	params = appendProperty(params, 'orderInfo', 'input', 'product_weight_uom');
	
    params = appendProperty(params, 'userInfo', 'input', 'user_id');
    params = appendProperty(params, 'userInfo', 'input', 'as_guest'); //only for vm2
    
	if (newProduct){ //for VM2 and new product: append new product's custom fields
		
		params = appendProperty(params, 'orderInfo', 'input', 'customPlugin');
		params = appendProperty(params, 'orderInfo', 'select', 'customPlugin');
		params = appendProperty(params, 'orderInfo', 'textarea', 'customPlugin');
		
		params = appendProperty(params, 'orderInfo', 'input', 'customPrice');
		params = appendProperty(params, 'orderInfo', 'select', 'customPrice');
		params = appendProperty(params, 'orderInfo', 'textarea', 'customPrice');
		
		$('addProductIcon').style.backgroundImage = 'url('+$('baseurl').value+'components/com_vminvoice/assets/images/ajax-loader-white.gif)';
	}
	else if (recomputeByRulesI!==false){
		$('ajaxLoaderRules'+recomputeByRulesI).style.display = 'inline';
	}
	else
		$('refreshOrderIcon').style.backgroundImage = 'url('+$('baseurl').value+'components/com_vminvoice/assets/images/ajax-loader-white.gif)';

	AUtility.ajaxSendPost(url, params, updateOrderInfo, null);

	return;
}

function deleteProduct(a) {
	if (confirm(AreYouSure)){
		tr = a.getParent('tr');
		tr.destroy();
	}
	return;
}

function appendQuery(varName,inputId){
	if ($(inputId))
		return '&'+varName+'='+encodeURIComponent($(inputId).value);
	return '';
}

function appendParams(url, varname, inputId) {
    if ($(inputId)) {
        url += (url == '' ? '' : '&') + varname + '=' + encodeURIComponent($(inputId).value);
    }
    
    return url;
}

function appendProperty(url, parent, type, name) {
	var elements = $(parent).getElements(type + '[name^=' + name + ']');
	for (var i = 0; i < elements.length; i++)
		if (elements[i].name.substring(elements[i].name.length-6)!='_model'){
			
			//skip non-checked checkboxes and radios
			if (type=='input' && (elements[i].type=='radio' || elements[i].type=='checkbox') && !elements[i].checked)
				continue;

			value = type=='textarea' ? elements[i].innerHTML : elements[i].value;
			
			url += (url == '' ? '' : '&') + elements[i].name + '=' + encodeURIComponent(value);
		}

	return url;
}

function updateOrderInfo(response, name) 
{	
	$('orderInfo').innerHTML = response;		
	
	//attach tooltips again
	if (typeof Tips == "function"){ //note: JTooltips is not visible here, cannot ask for it!!
		delete JTooltips;
		$$('.tip-wrap').destroy(); //this seems to work. we need to remove sometimes ramining old tooltip
		var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});  
		
	}
    
    // Run scripts inside the new element HTML
    exec_body_scripts($('orderInfo'));
}

function exec_body_scripts(body_el) {
  // Finds and executes scripts in a newly added element's body.
  // Needed since innerHTML does not run scripts.
  //
  // Argument body_el is an element in the dom.

  function nodeName(elem, name) {
    return elem.nodeName && elem.nodeName.toUpperCase() ===
              name.toUpperCase();
  };

  function evalScript(elem) {
    var data = (elem.text || elem.textContent || elem.innerHTML || "" ),
        head = document.getElementsByTagName("head")[0] ||
                  document.documentElement,
        script = document.createElement("script");

    script.type = "text/javascript";
    try {
      // doesn't work on ie...
      script.appendChild(document.createTextNode(data));      
    } catch(e) {
      // IE has funky script nodes
      script.text = data;
    }

    head.insertBefore(script, head.firstChild);
    head.removeChild(script);
  };

  // main section of function
  var scripts = [],
      script,
      children_nodes = body_el.childNodes,
      child,
      i;

  for (i = 0; children_nodes[i]; i++) {
    child = children_nodes[i];
    if (nodeName(child, "script" ) &&
      (!child.type || child.type.toLowerCase() === "text/javascript")) {
          scripts.push(child);
      }
  }

  for (i = 0; scripts[i]; i++) {
    script = scripts[i];
    if (script.parentNode) {script.parentNode.removeChild(script);}
    evalScript(scripts[i]);
  }
};

function GetKeyCode(e)
{
	var event = new Event(e);
	return event.code;
} 

function generateWhisper(element,e,url,type)
{
	var unicode = GetKeyCode(e);
	var str = element.value;
	if (unicode != 38 && unicode != 40 && str != lastSelected) {
		if (unicode != 13) {	
			url += 'index.php?option=com_vminvoice&controller=order&task=whisper&tmpl=component&type='+element.name;
            var params = 'str=' + encodeURIComponent(str);
			AUtility.ajaxSendPost (url, params, processRequest, element);
		} else     
			setWhisperVisibility(element,'none');
	}
}

function moveWhisper(element,e) {
	var unicode = GetKeyCode(e);
	var naseptavac = $('naseptavac');
	if (unicode == 40) {
		naseptavac.options.selectedIndex = ((naseptavac.options.selectedIndex >= 0) && (naseptavac.options.selectedIndex < (naseptavac.options.length-1)) ? (naseptavac.options.selectedIndex+1) : 0);
		getChangeHandler(element);
	} else if (unicode == 38) {
		naseptavac.options.selectedIndex = ((naseptavac.options.selectedIndex > 0) ? (naseptavac.options.selectedIndex-1) : (naseptavac.options.length-1));
		getChangeHandler(element);
	}
	else if (unicode == 13) { //enter
		getClickHandler(element);
		lastSelected = element.value;
		if (window.event)
			e.returnValue = false;
		else
			e.preventDefault();
		setWhisperVisibility(element,'none');
	}
} 

function processRequest(response, element) {
	var name = element.name + 'whisper';
    $(name).innerHTML = response;
    $('naseptavac').size = $('naseptavac').options.length;
    setWhisperVisibility(element,'block');
}

function getChangeHandler(element) {
	var select = $('naseptavac');
	var nazev = select.options[select.selectedIndex].innerHTML;	
	element.value = nazev.replace(/\&amp;/g,'&');
	document.getElementById(element.name+'_id').value = select.value;
}

function getClickHandler(element) {
	getChangeHandler(element);
	if (element.name=='user')
		showUserData('');
	if (element.name=='newproduct')
		showOrderData(null,true,false,false,false);
}

function generateParams(response,name){
	$('params').innerHTML = response;
}

function setWhisperVisibility(element,value){
	var name = element.name + 'whisper';
	$(name).style.display = value;
}

function processShippingChange(el)
{
	var shipping = $('ship_method_id').options[$('ship_method_id').selectedIndex].value.split('|');
	
	if (shipping.length > 3)
	{
		$('custom_shipping_class').value=shipping[0];
		$('custom_shipping_carrier').value=shipping[1];
		$('custom_shipping_ratename').value=shipping[2];
		$('custom_shipping_costs').value=shipping[3];
		$('custom_shipping_id').value=shipping[4];
		$('custom_shipping_taxrate').value=shipping[5];
	}
}

function applyShipping() //only for VM1 (i VM2 is applied in php based on selected shipping_method_id)
{
	$('order_shipping').value = $('custom_shipping_costs').value;
	$('order_shipping_taxrate').value = $('custom_shipping_taxrate').value;
	
	showOrderData(null,false,false,false,false);
}

function applyStatus()
{
	$$('select[name^=order_status[]').set('value',$('status').value);
}

var last_coupon = "";

function getCouponInfo(coupon,currency,url)
{
	if (last_coupon != coupon)
	{
		last_coupon = coupon;
		$('coupon_info').innerHTML='...';
		
		if (url === undefined) //get base url from form
			url = $('baseurl').value;

		url += 'index.php?option=com_vminvoice&controller=order&task=couponajax&coupon='+encodeURIComponent(coupon)+'&currency='+encodeURIComponent(currency);
		AUtility.ajaxSend (url, updateCouponInfo);
	}
}

function updateCouponInfo(text)
{
	document.getElementById('coupon_info').innerHTML=text;
}

function passCouponDiscount(type,discount)
{
	if (type=="percent")	{
		
		if ($('order_salesPrice')) /* VM2 */
			var base = $('order_salesPrice').value*1;
		else /* VM1 */
			var base = $('order_subtotal').value*1+$('order_tax').value*1;
		
		discount = Math.round(discount*base)/100;
	}	
		
	$('coupon_discount').value = (-(discount*1))*1;

	showOrderData(null,false,false,false,false);
}

function populateStates(countrySelectName,stateSelectName)
{
	var country = $(document.body).getElement('select[name='+countrySelectName+']').value;
	
	var stateSelect = $(document.body).getElement('select[name='+stateSelectName+']');
	
	if (country && stateSelect){
		stateSelect.innerHTML = '<option>...</option>';
		AUtility.ajaxSend('index.php?option=com_vminvoice&controller=order&task=statesajax&country_id='+country, populateStatesCallback);
	}
}

function populateStatesCallback(text)
{
	stateSelect.innerHTML = text;
}