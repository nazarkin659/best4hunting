/**
 * Support for editable admin lists.
 */

var editableRows = new Class ({

	addNewRow: function(table) //add new empty row (clone last one) with array input names incremented by 1
	{
		lastRow = $(table).getElement('tbody').getLast('tr'); //get model row from last row in table
		
		var footer = $(table).getElement('tfoot');
		var modelRow=null;
		
		if (footer!=null)
			modelRow = footer.getElement('tr');
		
		if (modelRow!=null) //get new row from special "model row" in tfooter
			newRow = modelRow.clone();
		else{
			if (lastRow==null)
				return false;
			else
				newRow = lastRow.clone(); //get model row from last row in table
		}
		
		var index = 0;
		
		//get index of new input names
		if (lastRow==null)
			index = 0; //1.st row
		else{
			lastRow.getElements('input').each( function(el){ //get inputs index of last row
				parts = el.name.match(/^(.+)\[(\d+)\]$/);
				if (parts!=null)
					index = parts[2]*1+1;
			});
		}
	
		newRow.getElements('input,select,textarea').each( //incerement inputs keys indexes
			function(el){
				
				parts = el.name.match(/^(.+)\[(.+)\]$/); //already have indexes (not model) = rewrite it
				if (parts!=null){
					el.name=parts[1]+'['+index+']';
	
					if (el.type!=undefined && el.type=="text") //reset values. but only if not from model row. (?)
						el.value="";
				}
				else { //from "model" row
					parts = el.name.match(/^(.+)_model$/);
					if (parts!=null){
						el.name=parts[1]+'['+index+']';
						el.name=el.name.replace(/\(/g,'['); //replace () by [] - reason is to not overewite  multi-dimensional inputs on php side
						el.name=el.name.replace(/\)/g,']'); //like when name of model row is "name[5]_model", use "name(5)_model"
					}
				}			
			}
		);
		
		newRow.inject($(table).getElement('tbody'),'bottom');
	}, 
	
	moveRowUp: function(row)
	{
		prev = row.getPrevious('tr');
		if (!prev)
			return false;
		
		this.swapRowsIds(row,prev);

		prev.inject(row,'after'); //swap rows
	}, 
	
	moveRowDown: function(row)
	{
		next = row.getNext('tr');
		if (!next)
			return false;
		
		this.swapRowsIds(row,next);

		next.inject(row,'before'); //swap rows
	}, 
	
	swapRowsIds: function(first,second) {
		
		var firstId = null;
		if (input = first.getElement('input[name$=\]],select[name$=\]],textarea[name$=\]]')){
			inputNameParts = input.name.match(/^(.+)\[(.+)\]$/);
			firstId = inputNameParts[2];
		}
		var secondId=null;
		if (secondInput = second.getElement('input[name$=\]],select[name$=\]],textarea[name$=\]]')){
			secondInputNameParts = secondInput.name.match(/^(.+)\[(.+)\]$/);
			secondId = secondInputNameParts[2];
		}
		
		//swap input ids
		first.getElements('input,select,textarea').each( 
			function(el){
				parts = el.name.match(/^(.+)\[(.+)\]$/);
				if (parts!=null)
					el.name=parts[1]+'['+secondId+']';
		});
		
		second.getElements('input,select,textarea').each( 
				function(el){
					parts = el.name.match(/^(.+)\[(.+)\]$/); 
					if (parts!=null)
						el.name=parts[1]+'['+firstId+']';
		});
	}
});

vmiRows = new editableRows();