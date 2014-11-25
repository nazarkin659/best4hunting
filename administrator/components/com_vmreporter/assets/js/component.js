jQuery(document).ready(function($){	
	$(".chzn-select").chosen();
	
	var cSelect = $('.chzn-select.first').chosen();
	var allItem = cSelect.find("option[value='0']"); //reference to the "ALL" option
	var rest = cSelect.find("option[value!='0']"); //reference for the rest of the options
	var allItemAlreadySelected = true; //set a flag for the "ALL" option's previous state
	cSelect.change(function(event) {   
		if ($(this).find("option:selected").length == 0) {
	        allItem.prop('selected', true); //select "ALL" option
		} else {
			if (allItem.is(':selected')) {
				if (allItemAlreadySelected == false) {
				    rest.prop('selected', false); //deselect rest
				    allItem.prop('selected', true); //select "ALL" option
				} else //if "ALL" option is previously selected (already), it means we have selected smthelse
						allItem.prop('selected', false); //so deselect "ALL" option
			}
		}
    allItemAlreadySelected = allItem.is(':selected'); //update the flag
    $('.chzn-select.first').trigger("liszt:updated"); //update the control
	});	
	cSelect.ready(function(event) {   
		if ($(this).find("option:selected").length == 0) {
	        allItem.prop('selected', true); //select "ALL" option
		} else {
			if (allItem.is(':selected')) {
				if (allItemAlreadySelected == false) {
				    rest.prop('selected', false); //deselect rest
				    allItem.prop('selected', true); //select "ALL" option
				} 
			}
		}
    allItemAlreadySelected = allItem.is(':selected'); //update the flag
    $('.chzn-select.first').trigger("liszt:updated"); //update the control
	});	
	
	
	var sSelect = $('.chzn-select.second').chosen();
	var sallItem = sSelect.find("option[value='0']"); //reference to the "ALL" option
	var srest = sSelect.find("option[value!='0']"); //reference for the rest of the options
	var sallItemAlreadySelected = true; //set a flag for the "ALL" option's previous state
	sSelect.change(function(event) {   
		if ($(this).find("option:selected").length == 0) {
	        sallItem.prop('selected', true); //select "ALL" option
		} else {
			if (sallItem.is(':selected')) {
				if (sallItemAlreadySelected == false) {
				    srest.prop('selected', false); //deselect rest
				    sallItem.prop('selected', true); //select "ALL" option
				} else //if "ALL" option is previously selected (already), it means we have selected smthelse
						sallItem.prop('selected', false); //so deselect "ALL" option
			}
		}
    sallItemAlreadySelected = sallItem.is(':selected'); //update the flag
    $('.chzn-select.second').trigger("liszt:updated"); //update the control
	});		
	sSelect.ready(function(event) {   
		if ($(this).find("option:selected").length == 0) {
	        sallItem.prop('selected', true); //select "ALL" option
		} else {
			if (sallItem.is(':selected')) {
				if (sallItemAlreadySelected == false) {
				    srest.prop('selected', false); //deselect rest
				    sallItem.prop('selected', true); //select "ALL" option
				} 
			}
		}
    sallItemAlreadySelected = sallItem.is(':selected'); //update the flag
    $('.chzn-select.second').trigger("liszt:updated"); //update the control
	});			
	
	$(".calendar").wrap('<i class="calendar-cont"></i>');
	$('.date input[type="text"]').click(function(){
		$(this).siblings('.calendar-cont').find('img').click();
	});
	
	$("li.show-chart > a").click(function() {
		$(this).parent("li").addClass("active");
		$("li.show-table, .plot-menu a").removeClass("active");
		$(".plot-menu .default-active a").addClass("active");
	 	$("#table-area").addClass('hide');
	 	$("#chart-area").removeClass('hide');
		return false;		
	});
	$("li.show-table > a").click(function() {
	 	$("#plotChart").unbind();
	 	$(this).parent("li").addClass("active");
		$("li.show-chart").removeClass("active");
		$("#chart-area").addClass('hide');
	 	$("#table-area").removeClass('hide');	
		return false;		
	});
	$(".plot-menu a").click(function() {
	 	$(".plot-menu a").removeClass('active');
		$(this).addClass('active');
	}); 
});


function orderTotal(id, view, Plotlabel) {
	var $ = jQuery;
	$(".chart-loading").removeClass('hide');
	$.getJSON('index.php?option=com_vmreporter&view='+view+'&json=true&id='+id,
		function(datas, textStatus) {
			var orderValue = {};
			var orderNumber = [];
			var createdOn = [];
			var totalOrderValue = [];
			$.each(datas.reports, function( key, value ) {	
				if($.inArray(value['order_number'], orderNumber) == -1) {
					orderNumber.push(value['order_number']);
					var date = value['created_on'].split(" ");
					if($.inArray(date[0], createdOn) == -1) {
						orderValue[strtotime(date[0]+' 00:00:00')*1000] = parseFloat(value['order_total']);
						createdOn.push(date[0]);
					} else {
						orderValue[strtotime(date[0]+' 00:00:00')*1000] = parseFloat(orderValue[strtotime(date[0]+' 00:00:00')*1000]) + parseFloat(value['order_total']);
					}
				}					
			});
			$.each(orderValue, function( OKey, OValue ) {	
				totalOrderValue.push([OKey, OValue]);
			});
			$.plot("#plotChart", [{ label: Plotlabel, data: totalOrderValue }], {
				series: {
					lines: {
						show: true,
						barWidth: 50000*1000,
						fillColor: { colors: [ { opacity: 0.8 }, { opacity: 0.6 } ] },
						lineWidth: 1,
						fill: true,
						align: 'center',
						horizontal: false
					},
					points: {
						show: true
					}
				},
				grid: {
					hoverable: true
				},				
				xaxis: { 
					mode: "time",
					minTickSize: [1, "day"]
				},
				colors: ["#cb4b4b"],
				tooltip: true,
				tooltipOpts: {
					content: "%s: %y.00", // show percentages, rounding to 2 decimal places
					shifts: {
						x: 20,
						y: 0
					},
					defaultTheme: false
				}
			});
		}
	).error(function(){ 
    	alert("Error occurred getting plot data!");
	}).complete(function() { 
		$(".chart-loading").addClass('hide'); 
	});
}

function ProductsPerformance(id, view, Plotlabel) {
	var $ = jQuery;	
	$(".chart-loading").removeClass('hide');
	$.getJSON('index.php?option=com_vmreporter&view='+view+'&json=true&id='+id,
		function(datas, textStatus) {
			var DataSeries = {};
			var productName = [];
			var createdOn = [];
			var totalOrderValue = [];
			$.each(datas.reports, function( key, value ) {	
				var attr = '';	
				if(value['product_attribute'] !== null) {	
					$.each($.parseJSON(value['product_attribute']), function(AKey, iAttr) {
						if(attr.length > 0) {
							attr = attr+',';
						}
						attr = attr+iAttr;
					});
					attr = attr.replace(/\<span class="costumTitle">/g, '');
					attr = attr.replace(/\<span class="costumValue" >/g, ': ');
					attr = attr.replace(/\<\/span>/g, '');
					attr = attr.replace(/\<p>/g, '');
					attr = attr.replace(/\<\/p>/g, '');
					var productNamewithAttr = value['order_item_name']+' - '+attr;
				} else {
					var productNamewithAttr = value['order_item_name'];
				}
				if($.inArray(productNamewithAttr, productName) == -1) {
					productName.push(productNamewithAttr);
					DataSeries[productNamewithAttr] = parseFloat(value['product_subtotal_with_tax']);			
				} else {
					DataSeries[productNamewithAttr] = parseFloat(DataSeries[productNamewithAttr]) + parseFloat(value['product_subtotal_with_tax']);	
				}				
			});
			//console.log(DataSeries);
			var i = 0;
			var ProductData = [];
			$.each(DataSeries, function( OKey, OValue ) {	
				totalOrderValue.push([OKey, OValue]);
				ProductData[i] = {
					label: OKey,
					data: OValue
				}
				i++;
				
			});
			var placeholder = $("#plotChart")
			$.plot(placeholder, ProductData, {
				series: {
					pie: {
						show: true
					}
				},
				grid: {
					hoverable: true
				},
				tooltip: true,
				tooltipOpts: {
					content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
					shifts: {
						x: 20,
						y: 0
					},
					defaultTheme: false
				}
			});			
		}
	).error(function(){ 
    	alert("Error occurred getting plot data!");
	}).complete(function() { 
		$(".chart-loading").addClass('hide'); 
	});
}