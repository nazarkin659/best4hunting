/**
 * bdpopup v.2.0 2013-5-13 18:14
 * Creates a popup window from a part of a page.
 * Usefull for forms as it does not open a new window 
 * and what is set here can be submited with the rest form
 * @author Sakis Terzis
 * @license GNU/GPL v.2
 * @copyright Copyright (C) 2013 breakDesigns.net. All rights reserved
 */

function displayPopup(id) {
	var link = 'show_popup' + id;
	var hideTags = 'hide_popup' + id;
    var closeBtn='close_btn'+id;
    var closeElements=new Array(document.id(hideTags),document.id(closeBtn));



	document.id(link).addEvent('click', function() {
	    closeOpenPopups();
		var elname = 'window' + id;
		document.id(elname).setStyle('display', 'block');
	})

	closeElements.each(function (e) {
	    e.addEvent('click', function() {
		var elname = 'window' + id;
		document.id(elname).setStyle('display', 'none');
	});
    });
}

function closeOpenPopups(){
   var windows=$$windows=$$('.bdpopup');
   var winLength=windows.length;
   for(var i=0; i<winLength; i++){
       windows[i].setStyle('display', 'none');
   }
}
