/**
 * @author Sakis Terzis
 * @license GNU/GPL v.2
 * @copyright Copyright (C) 2013 breakDesigns.net. All rights reserved
 */

window.addEvent('domready',function(){
	var displayTypesDropDown=$$('.cfDisplayTypes');
	
	displayTypesDropDown.addEvent('change',function(){
		var selected_val=this.getElement(':selected').value;
		var dropdown_id=this.getProperty('id');
		var filterid=dropdown_id.substring(7);
		var advSettingLink=document.id('show_popup'+filterid);
		
		//display advanced
		if(display_types_advanced.indexOf(selected_val)>=0){			
			advSettingLink.setStyle('display','block');
		}else{
			advSettingLink.setStyle('display','none');
		}
	});
	
})