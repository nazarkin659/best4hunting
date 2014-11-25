/*----------------------------------------------------------------------
# Scratch2Win - Joomla System Plugin 
# ----------------------------------------------------------------------
# Copyright © 2014 VirtuePlanet Services LLP. All rights reserved.
# License - http://www.virtueplanet.com/policies/licenses
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Website:  http://www.virtueplanet.com
----------------------------------------------------------------------*/
var jq = jQuery.noConflict();

jq(document).ready(function()
{	
	jq('.button-set').buttonset();
	jq('.pane-toggler, .pane-toggler-down').click(function(){
			jq('.jquery-chosen').chosen('destroy');
			jq('.jquery-chosen').chosen();
	});
	jq('form#style-form').find('div.width-60').addClass('width-50').removeClass('width-60');
	jq('form#style-form').find('div.width-40').addClass('width-50').removeClass('width-40');
	
	jq('#PROMO_1-options').find('span').text('Item-1 Options');
	jq('#PROMO_2-options').find('span').text('Item-2 Options');
	jq('#PROMO_3-options').find('span').text('Item-3 Options');
	
	for (var i = 1; i <= 3; i++) 
	{
		var page = jq('select#jform_params_page_'+i);
		page.parent('li').next('li').show();
		var pageType = page.val();
		if(pageType == 1)
		{
			page.parent('li').next('li').hide();
		}
		
		var scratchPad = jq('select#jform_params_coupon_'+i);
		var scratchPadSettings = scratchPad.val();
		var lastSettingElement = jq('input#jform_params_scratchpad_cursor_'+i).parents('li').next('li');
		
		if(scratchPadSettings == 0)
		{
			scratchPad.parent('li').nextUntil(lastSettingElement, 'li').hide();
		}		
	}
});

jq(window).load(function(){
	jq('.jquery-chosen, select#jformordering, select#jform_access, select#jform_enabled').chosen();
});

function S2W_pageSelect(element)
{
	var value = jq(element).val();
	if(value == 1)
	{
		jq(element).parent('li').next('li').hide();
	}
	else{
		jq(element).parent('li').next('li').show();
		jq(element).parent('li').next('li').find('.jquery-chosen').chosen('destroy');
		jq(element).parent('li').next('li').find('.jquery-chosen').chosen();		
	}
}

function S2W_scratchPadShow(element)
{
	var scratchPad = jq(element);
	var scratchPadSettings = scratchPad.val();
	var lastSettingElement = scratchPad.parents('ul').find('input.scratchpad_settings:last').parents('li').next('li');	

	if(scratchPadSettings == 0)
	{
		scratchPad.parent('li').nextUntil(lastSettingElement, 'li').hide();
	}
	else{
		scratchPad.parent('li').nextUntil(lastSettingElement, 'li').show();
	}
}