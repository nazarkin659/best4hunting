<?php

/**
 * PDF Invoicing Solution for VirtueMart & Joomla!
 * 
 * @package   VM Invoice
 * @version   2.0.31
 * @author    ARTIO http://www.artio.net
 * @copyright Copyright (C) 2011 ARTIO s.r.o. 
 * @license   GNU/GPLv3 http://www.artio.net/license/gnu-general-public-license
 */

defined('_JEXEC') or die('Restrict Access');

JHTML::_('behavior.tooltip');
JHtml::_('behavior.calendar');

global $mainframe;

$delivery_note = $this->delivery_note;

//build string with neccesay statuses for invice cration (from invoice config)
$orderStatuses = (array)InvoiceHelper::getParams()->get('order_status');

foreach ($orderStatuses as &$orderStatus)
	$orderStatus = isset($this->statuses[$orderStatus]) ? $this->statuses[$orderStatus]->name : $orderStatus;
        
if (count($orderStatuses)==1)
	$sendStatuses = $orderStatuses[0];
elseif (count($orderStatuses)>1)  {
	$sendStatuses = ' '.JText::_('COM_VMINVOICE_OR').' '.array_pop($orderStatuses);
	$sendStatuses = implode(', ',$orderStatuses).$sendStatuses;
}
else
	$sendStatuses = '';

$params = InvoiceHelper::getParams();

if ($params->get('allow_order_notes', 0)){
	$document = JFactory::getDocument();
	$document->addScript(JURI::root().'administrator/components/com_vminvoice/assets/js/mootools.outerclick.js');
}


            
?>
<script language="javascript" defer="defer">

function show_change(div_id)
{
	var div = document.getElementById(div_id);

	if (div.style.display=='none')
		div.style.display='block';
	else 
		div.style.display='none';
}

function show_change_date(order_id)
{
	var div = document.getElementById('change_invoice_date_'+order_id);

	if (div.style.display=='none'){
		div.style.display='block';
		div.getElement('img.calendar').fireEvent('click');}
	else {
		div.style.display='none'; 
		calendar.hide();}
}

<?php if ($params->get('allow_order_notes', 0)){ ?>
function show_order_note(order_id)
{
	var div = $('order_note_'+order_id);
	var img = div.getParent('td').getElement('img');
	div.style.display='block';
	div.getElement('textarea').focus();
	
	div.addEvent('outerClick',function(e) {  //hide on clicking outside
		if (e.target!=img)
			this.style.display='none';
	}.bind(div));
}

<?php }?>

function reset_search()
{
	$('filter_orders').getElements('input[type=text]').set('value','');
	$('filter_orders').getElements('input[type=checkbox]').set('checked',false);
	$('filter_orders').getElements('option').set('selected',false);
}


//before batch sending
function clicked_batch()
{
	if ($('batch_select_selected_list').checked && document.adminForm.boxchecked.value==0){
		alert('<?php echo JText::_('COM_VMINVOICE_CHECK_AT_LEAST_ONE_ORDER')?>');
		document.adminForm.task.value='';
		return false;}

	document.adminForm.target = '';
	document.adminForm.task.value='batch';
	
	//download pdfs - open form target in new window
	if ($('batch_download').checked){ //generator_order_by.value
		newwindow = window.open('index.php?option=com_vminvoice&controller=invoices','win2', 'status=yes,toolbar=yes,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');
		document.adminForm.target = 'win2';
		if (window.focus) newwindow.focus()
		return true;
	}
	
	return true;
}

//before batch sending
function clicked_filter()
{
	document.adminForm.task.value='';
	document.adminForm.target = '';

	return true;
}

</script>

<?php 
$total =  $this->get('Total'); 
JHTML::_('behavior.calendar');

$options = array();
$options[]=JHTML::_('select.option','invoice', JText::_('COM_VMINVOICE_INVOICES'));
$options[]=JHTML::_('select.option','dn', JText::_('COM_VMINVOICE_DELIVERY_NOTES'));

$starting_order = $params->get('starting_order',0);

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

<table class="adminheading" width="100%">
	<tr>
		<td valign="top">
			<fieldset id="filter_orders"><legend><?php echo JText::_('COM_VMINVOICE_FILTER_ORDERS')?></legend>

				<table style="width:100%" cellpadding="0" cellspacing="0" class="admintable">
				<tr><td style="width:120px">
					<label for="start_date">
		           	<?php echo JText::_('COM_VMINVOICE_DATE_FROM'); ?>: </label>
		       		</td><td>
					<?php echo JHTML::calendar(JRequest::getVar('filter_start_date'), 'filter_start_date', 'start_date', '%d-%m-%Y'); ?>
		           	<label for="end_date"><?php echo JText::_('COM_VMINVOICE_DATE_TO'); ?>: </label>
					<?php echo JHTML::calendar(JRequest::getVar('filter_end_date'), 'filter_end_date', 'end_date', '%d-%m-%Y'); ?>
				</td></tr><tr><td>
					<label for="order_status"> <?php echo JText::_('COM_VMINVOICE_STATUS')?></label>
					</td><td>					
					<?php echo JHTML::_('select.genericlist', $this->statuses, 'filter_order_status[]', 'multiple="multiple" size="5"', 'id', 'name', JRequest::getVar('filter_order_status',array()));  ?>
				</td></tr><tr><td>
					<label for="filter_id"> <?php echo JText::_('COM_VMINVOICE_ORDER_ID')?></label>
					</td><td>
					<input type="text" name="filter_id" id="filter_id" value="<?php echo JRequest::getVar('filter_id')?>"/>
				</td></tr><tr><td>
				<?php if ($this->invoice_numbering=='own') { ?>
					<label for="filter_inv_prefix"> <?php echo JText::_('COM_VMINVOICE_INVOICE_PREFIX'); ?>: </label>
					</td><td>
					<input type="text" name="filter_inv_prefix" id="filter_inv_prefix" value="<?php echo JRequest::getVar('filter_inv_prefix')?>" />
					</td></tr><tr><td>
				<?php } ?>
					<label for="filter_inv_no"> <?php echo JText::_('COM_VMINVOICE_INVOICE_NO'); ?>: </label>
					</td><td>
					<input type="text" name="filter_inv_no" id="filter_inv_no" value="<?php echo JRequest::getVar('filter_inv_no')?>" />
	           	</td></tr><tr><td>
					<label for="start_inv_date">
		           	<?php echo JText::_('COM_VMINVOICE_INVOICE_DATE_FROM'); ?>: </label>
		       		</td><td>
					<?php echo JHTML::calendar(JRequest::getVar('filter_start_inv_date'), 'filter_start_inv_date', 'start_inv_date', '%d-%m-%Y'); ?>
		           	<label for="end_inv_date"><?php echo JText::_('COM_VMINVOICE_INVOICE_DATE_TO'); ?>: </label>
					<?php echo JHTML::calendar(JRequest::getVar('filter_end_inv_date'), 'filter_end_inv_date', 'end_inv_date', '%d-%m-%Y'); ?>
				</td></tr><tr><td>
		           	<label for="filter_name"> <?php echo JText::_('COM_VMINVOICE_NAME'); ?>: </label>
					</td><td>
					<input type="text" name="filter_name" id="filter_name" value="<?php echo JRequest::getVar('filter_name')?>"/>
				</td></tr><tr><td>
					<label for="filter_email"> <?php echo JText::_('COM_VMINVOICE_MAIL'); ?>: </label>
					</td><td>
                    
					<input type="text" name="filter_email" id="filter_email" value="<?php echo JRequest::getVar('filter_email')?>" />
				</td></tr>
                
                <tr><td>
					<label for="filter_email"> <?php echo JText::_('COM_VMINVOICE_SHIPPING'); ?>: </label>
					</td><td>
					<input class="btn" type="button" value="<?php echo JText::_('COM_VMINVOICE_CLEAR'); ?>" style="float:right!important;margin-left:1px" onclick="reset_search();this.form.submit();">
					<input class="btn" type="submit" value="<?php echo JText::_('COM_VMINVOICE_FILTER_ORDERS'); ?>" style="float:right!important" onclick="clicked_filter();">
                    <?php
                    if (COM_VMINVOICE_ISVM2) {
                        $opts = array();
                        $opts[] = JHtml::_('select.option', '', JText::_('COM_VMINVOICE_SHIPPING_ANY'));
                        foreach ($this->shippings as $id => $shipping) {
                            $opts[] = JHtml::_('select.option', $id, $shipping->name);
                        }
                        echo JHtml::_('select.genericlist', $opts, 'filter_shipping', null, 'value', 'text', JRequest::getVar('filter_shipping'));
                    }
                    else if (COM_VMINVOICE_ISVM1) {
                        ?>
                        <input type="text" name="filter_shipping" id="filter_shipping" value="<?php echo JRequest::getVar('filter_shipping'); ?>" />
                        <?php
                    }
                    ?>
				</td></tr>
                
				</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset><legend><?php echo JText::_('COM_VMINVOICE_PROCESS_ORDERS')?></legend>
				<table width="100%" cellpadding="0" cellspacing="0" class="admintable">
				<tr>
					<td>
					<label><input type="radio" id="batch_select_selected_list" name="batch_select" value="selected_list"<?php if (JRequest::getVar('batch_select','selected_list')=='selected_list') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_ORDERS_CHECKED_IN_LIST')?></label>
					</td>
				</tr>
				<tr>
					<td>
					<label><input type="radio" name="batch_select" value="all_filtered"<?php if (JRequest::getVar('batch_select')=='all_filtered') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_ORDERS_MATCHING_FILTER')?></label>	
					</td>
				</tr>
			</table>
			</fieldset>
		
			<fieldset><legend><?php echo JText::_('COM_VMINVOICE_BATCH_ACTION')?></legend>
			
			<table width="100%" cellpadding="0" cellspacing="0" class="admintable">
				<tr>
					<td>
					<label><input type="radio" id="batch_download" name="batch" value="download"<?php if (JRequest::getVar('batch','download')=='download') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_DOWNLOAD')?></label>
					<?php if ($params->get('delivery_note')==1) echo JHTML::_('select.genericlist', $options, 'batch_download_option', null, 'value', 'text', JRequest::getVar('batch_download_option'));  ?>
                    <?php echo JHTML::_('select.genericlist', $this->sort_options, 'batch_sort_by', null, 'value', 'text', JRequest::getVar('batch_sort_by')); ?>
                    <?php echo JHTML::_('select.genericlist', $this->sort_dir_opts, 'batch_sort_dir', null, 'value', 'text', JRequest::getVar('batch_sort_dir')); ?>
					</td>
				</tr>
				<tr>
					<td>
				<label><input type="radio" name="batch" value="mail"<?php if (JRequest::getVar('batch')=='mail') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_SEND_EMAIL')?></label>
				<?php 
				if ($params->get('delivery_note') && !$params->get('send_both',1))
					echo JHTML::_('select.genericlist', $options, 'batch_mail_option', null, 'value', 'text', JRequest::getVar('batch_mail_option'));
				elseif ($params->get('delivery_note') && $params->get('send_both',1))
					echo '<label>&nbsp; '.JString::strtolower(JText::_('COM_VMINVOICE_INVOICES')).' & '.JString::strtolower(JText::_('COM_VMINVOICE_DELIVERY_NOTES')).'</label>';
				else
					echo '<label>&nbsp;  '.JString::strtolower(JText::_('COM_VMINVOICE_INVOICES')).'</label>';
					?>
				<label><input type="checkbox" name="batch_mail_force" value="1"<?php if (JRequest::getVar('batch_mail_force','0')==1) echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_ALSO_ALREADY_SENT')?></label>
					</td>
				</tr>
				<?php if ($params->get('invoice_number')=='own') { ?>
				<tr>
					<td>
					<label><input type="radio" name="batch" value="create_invoice"<?php if (JRequest::getVar('batch')=='create_invoice') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_CREATE_INVOICE_NUMBERS')?></label>
					</td>
				</tr>
				<?php } ?>
				<?php if ($params->get('cache_pdf')) { ?>
				<tr>
					<td>
				<label><input type="radio" name="batch" value="generate"<?php if (JRequest::getVar('batch')=='generate') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_PRE-GENERATE_PDFS')?></label>
				<label><input type="checkbox" name="batch_generate_force" value="1"<?php if (JRequest::getVar('batch_generate_force','0')==1) echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_ALSO_ALREADY_GENERATED')?></label>
					</td>
				</tr>
				<?php } ?>
				
				<tr>
					<td>
				<label><input type="radio" name="batch" value="change_status"<?php if (JRequest::getVar('batch')=='change_status') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_CHANGE_STATUS')?></label>
				<?php echo JHTML::_('select.genericlist', $this->statuses, 'batch_status', null, 'id', 'name', JRequest::getVar('batch_status')); ?>
				<label><input type="checkbox" name="batch_notify_customer" value="Y"<?php if (JRequest::getVar('batch_notify_customer')==1) echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_NOTIFY_CUSTOMER')?></label>
					</td>
				</tr>
				
				<tr>
					<td>
				<input class="btn" type="submit" value="<?php echo JText::_('COM_VMINVOICE_PROCESS')?>" style="float:right!important" onclick="return clicked_batch();">
				<label><input type="radio" name="batch" value="delete"<?php if (JRequest::getVar('batch')=='delete') echo ' checked';?>> <?php echo JText::_('COM_VMINVOICE_DELETE_CREATED_INVOICES')?></label>
					</td>
				</tr>
			</table>
			</fieldset>
			<!--  
				<fieldset style="text-align:right">
					<input class="btn" type="button" value="<?php echo JText::_('COM_VMINVOICE_GENERATE_ALL'); ?>" class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_GENERATE_ALL_DESC'); ?>"
					onclick="javascript:void window.open('../index.php?option=com_vminvoice&view=vminvoice&task=cronGenerate', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');" >
				</fieldset>
			-->
		</td>
	</tr>
</table>

<div id="editcell">
<table class="adminlist table table-striped">
	<thead>
		<tr>
		<?php 
        // Get sorting
        $filterOrder = JRequest::getVar('filter_order', 'order_id');
        $filterOrderDir = JRequest::getVar('filter_order_Dir', 'desc');
        
		//build header array to pass it to plugin
		$header = array();
		$header['id'] = '<th class="center" width="5">'.JText::_('ID').'</th>';	
		$header['check'] = '<th class="center" width="20">'.(COM_VMINVOICE_ISJ30 ? JHtml::_('grid.checkall') : '<input type="checkbox" name="toggle" value="" onclick="checkAll('.count($this->invoices).');" />').'</th>';
		$header['order_id'] = '<th class="center" width="60">'.JHtml::_('grid.sort', 'COM_VMINVOICE_ORDER_ID', 'order_id', $filterOrderDir, $filterOrder, null, 'desc').'</th>';
		$header['invoice_no'] = '<th class="center" width="60">'.JHtml::_('grid.sort', 'COM_VMINVOICE_INVOICE_NO', 'invoice_num', $filterOrderDir, $filterOrder, null, 'desc').'</th>';
		$header['edit'] = '<th class="center" width="1%">'.JText::_('COM_VMINVOICE_EDIT_ORDER').'</th>';
		$header['name'] = '<th class="center">'.JText::_('COM_VMINVOICE_CLIENT_NAME').'</th>';
		$header['company'] = '<th class="center">'.JText::_('COM_VMINVOICE_COMPANY').'</th>';
		$header['email'] = '<th class="center">'.JText::_('COM_VMINVOICE_MAIL').'</th>';
        
        $header['shipping'] = '<th class="center">'.JText::_('COM_VMINVOICE_SHIPPING').'</th>';
        
		$header['status'] = '<th class="center" width="180">'.JText::_('COM_VMINVOICE_STATUS').'</th>';
		
		$header['created_date'] = '<th class="center" width="80">'.JHtml::_('grid.sort', 'COM_VMINVOICE_CREATED_DATE', 'created_date', $filterOrderDir, $filterOrder, null, 'desc').'</th>';
		$header['modified_date'] = '<th class="center" width="80">'.JHtml::_('grid.sort', 'COM_VMINVOICE_LAST_MODIFIED', 'modified_date', $filterOrderDir, $filterOrder, null, 'desc').'</th>';
		
		$header['total'] = '<th class="center" width="80">'.JHtml::_('grid.sort', 'COM_VMINVOICE_TOTAL', 'order_total', $filterOrderDir, $filterOrder, null, 'desc').'</th>';
		
		$header['invoice_date'] = '<th class="center" width="80">'.JHtml::_('grid.sort', 'COM_VMINVOICE_INVOICE_DATE', 'invoice_date', $filterOrderDir, $filterOrder, null, 'desc').'</th>';
		
		if ($params->get('allow_order_notes', 0))
			$header['order_note'] = '<th width="20" class="center hasTip" title="'.JText::_('COM_VMINVOICE_ORDER_NOTES').'" rel="'.JText::_('COM_VMINVOICE_ORDER_NOTES_DESC').'">'.JText::_('COM_VMINVOICE_ORDER_NOTES').'</th>';

		$header['invoice_sent'] = '<th class="center" width="45">'.JText::_('COM_VMINVOICE_INVOICE_SENT').'</th>';
		if ($delivery_note)
			$header['dn_sent'] = '<th class="center" width="45" title="'.JText::_('COM_VMINVOICE_DELIVERY_NOTE_SENT').'">'.JText::_('COM_VMINVOICE_DN_SENT').'</th>';
		$header['invoice_mail'] = '<th class="center" width="45" title="'.JText::_('COM_VMINVOICE_MAIL_INVOICE').'">'.JText::_('COM_VMINVOICE_MAIL').'</th>';
		if ($delivery_note)
			$header['dn_mail'] = '<th class="center" width="45" title="'.JText::_('COM_VMINVOICE_MAIL_DELIVERY_NOTE_ONLY').'">'.JText::_('COM_VMINVOICE_MAIL_DN').'</th>';
		$header['generate_invoice'] = '<th class="center" width="45" title="'.JText::_('COM_VMINVOICE_GENERATE_INVOICE_PDF').'">'.JText::_('COM_VMINVOICE_INVOICE_PDF').'</th>';
		if ($delivery_note)
			$header['generate_dn'] = '<th class="center" width="45" title="'.JText::_('COM_VMINVOICE_GENERATE_DELIVERY_NOTE_PDF').'">'.JText::_('COM_VMINVOICE_DN_PDF').'</th>';
		
		//support for custom plugins
		$this->dispatcher->trigger('onInvoicesListHeader', array(&$header, $this));
		echo implode(PHP_EOL, $header);
		?>
		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i = 0, $n = count($this->invoices); $i < $n; $i++) {
	    //$tm1 = microtime(true);
	    //echo '<tr><td colspan="14">' . ($tm1 - $tm2) . '</td></tr>';
	    //$tm2 = $tm1; 
		$row = $this->invoices[$i];
		$checked 	= JHTML::_('grid.id', $i, $row->order_id);
		
		$editOrder_url = "index.php?option=com_vminvoice&controller=invoices&task=editOrder&cid=". $row->order_id;
		
		
		$pdf_url = "index.php?option=com_vminvoice&controller=invoices&task=pdf&cid=". $row->order_id;
		$pdf_dn_url = "index.php?option=com_vminvoice&controller=invoices&task=pdf_dn&cid=". $row->order_id;
		$pdf_link = "&nbsp;<a href=\"javascript:void window.open('$pdf_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\">";
		$pdf_dn_link = "&nbsp;<a href=\"javascript:void window.open('$pdf_dn_url', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no');\">";
        $mail_url = "index.php?option=com_vminvoice&controller=invoices&task=send_mail&cid=". $row->order_id;
		$mail_dn_url = "index.php?option=com_vminvoice&controller=invoices&task=send_delivery_note&cid=". $row->order_id;

		$onclick="onclick=\"return confirm('".JText::_('COM_VMINVOICE_RESEND_INVOICE_PROMPT')."');\"";
		$mail_link = "&nbsp;<a href='$mail_url' ".($row->invoice_mailed ? $onclick : '').">";
		$onclick="onclick=\"return confirm('".JText::_('COM_VMINVOICE_RESEND_DN_PROMPT')."');\"";
        $mail_dn_link = "&nbsp;<a href='$mail_dn_url' ".($row->dn_mailed ? $onclick : '').">";       
		
        $item = array();
        $item['id'] = '<td>'./*($i+1)*/$row->order_id.'</td>';
        $item['check'] = '<td>'.$checked.'</td>';
        $item['order_id'] = '<td>'.$row->order_id.'</td>';
        
        if ($this->invoice_numbering!='own')
        	$item['invoice_no'] = '<td>'.$row->invoiceNoFull.'</td>';
        else{

			if ($row->invoiceNoFull){
        		$item['invoice_no'] = '
				<td><a href="javascript:void(0)" onclick="show_change(\'change_invoice_no_'.$row->order_id.'\');" title="'.JText::_('COM_VMINVOICE_EDIT').'">
				'.$row->invoiceNoFull.'
				</a>
				<div style="display:none" id="change_invoice_no_'.$row->order_id.'">'
				.($this->prefix_editing==1 ? '
					<input style="width:100%" type="text" size="8" name="invoice_prefix['.$row->order_id.']" value="'.$row->invoiceNoPrefix.'" title="'.JText::_('COM_VMINVOICE_INVOICE_PREFIX').'">
					<input style="width:100%" type="text" size="8" name="invoice_no['.$row->order_id.']" value="'.$row->invoiceNoDb.'" title="'.JText::_('COM_VMINVOICE_INVOICE_NO').'">
				' : '
					<input style="width:100%" type="text" size="8" name="invoice_no['.$row->order_id.']" value="'.$row->invoiceNoDb.'" title="'.JText::_('COM_VMINVOICE_INVOICE_NO').'">
				').'
				<input style="width:100%" class="btn" type="submit" name="update_inv_no['.$row->order_id.']" value="'.JText::_('COM_VMINVOICE_EDIT').'">
				</div></td>';
        	}
        	else {
        		$item['invoice_no'] = '<td><a href="javascript:void(0)" onclick="show_change(\'change_invoice_no_'.$row->order_id.'\');">
				<span class="hasTip" title="'.JText::_('COM_VMINVOICE_INVOICE_NUMBER_NOT_GENERATED_YET').'">'.JText::_('COM_VMINVOICE_CREATE').'</span>
				</a>
				<div style="display:none" id="change_invoice_no_'.$row->order_id.'">'
				.($this->prefix_editing==1 ? '
					<input style="width:100%" type="text" size="8" name="invoice_prefix['.$row->order_id.']" value="'.$this->default_prefix.'" title="'.JText::_('COM_VMINVOICE_INVOICE_PREFIX').'">
					<input style="width:100%" type="text" size="8" name="invoice_no['.$row->order_id.']" value="'.$this->newInoviceNo.'" title="'.JText::_('COM_VMINVOICE_INVOICE_NO').'">			
				' : '
					<input style="width:100%" type="text" size="8" name="invoice_no['.$row->order_id.']" value="'.$this->newInoviceNo.'" title="'.JText::_('COM_VMINVOICE_INVOICE_NO').'">
				').'
				<input style="width:100%" class="btn" type="submit" name="update_inv_no['.$row->order_id.']" value="'.JText::_('COM_VMINVOICE_CREATE').'">
				</div></td>';
        	}
       } 
        
        $item['edit'] = '<td align="center"><a class="editOrder" href="'.$editOrder_url.'" title="'.JText::_("Edit order").'"><span class="unseen">'.JText::_("Edit order").'</span></a></td>';
        
        
        $item['name'] = '<td>'.stripslashes($row->last_name) . ' ' . stripslashes($row->first_name).'</td>';
        
        
        
        $item['company'] = '<td>'.stripslashes($row->company).'</td>';
        $item['email'] = '<td>'.$row->email.'</td>';
        
        
        $item['shipping'] = '<td>'.htmlspecialchars($row->shipment_name).'</td>';
        $item['status'] = '<td>'.JHTML::_('select.genericlist', $this->statuses, 'status['.$row->order_id.']', null, 'id', 'name', $row->order_status).'
	    <input class="btn" type="submit" name="update['.$row->order_id.']" value="'.JText::_('COM_VMINVOICE_UPDATE').'">
		<span style="white-space: nowrap;"><input type="checkbox" name="notify['.$row->order_id.']" value="YF">'.JText::_('COM_VMINVOICE_NOTIFY_CUSTOMER').'</span></td>';
        
        
        
      	
        $item['created_date'] = '<td>'.($row->cdate ? JHTML::_('date',  $row->cdate, JText::_('DATE_FORMAT_LC3')) : '-').'</td>';
        $item['modified_date'] = '<td>'.($row->mdate ? JHTML::_('date',  $row->mdate, JText::_('DATE_FORMAT_LC3')): '-').'</td>';
        
        
        $item['total'] = '<td>'.InvoiceCurrencyDisplay::getFullValue($row->order_total, $row->order_currency).'</td>';
        
        
        
        if ($row->invoiceNoFull==false){

	        if ($row->order_id<$starting_order)
	        	$reason=JText::_('COM_VMINVOICE_INVOICES AREN\'T AUTOMATICALLY CREATED BEFORE ORDER').' '.$starting_order;
	        else
	        	$reason=JText::_('COM_VMINVOICE_NEEDS_TO_GET_IN_STATE').' '.$sendStatuses.'.';
								
	        $colspan = ($delivery_note ? 7 : 4)+($params->get('allow_order_notes', 0) ? 1 : 0);

       		$item['invoice_date'] = '<td align="center" colspan="'.$colspan.'">
				<span class="hasTip" title="'.JText::_('COM_VMINVOICE_INVOICE_NUMBER_NOT_GENERATED_YET').'::'.$reason.'">
				'.JText::_('COM_VMINVOICE_INVOICE_NUMBER_NOT_GENERATED_YET').'.</span>
			</td>';
       		
       		$item['invoice_sent'] = '';
       		if ($delivery_note)
       			$item['dn_sent'] = '';
       		$item['invoice_mail'] = '';
       		if ($delivery_note)
       			$item['dn_mail'] = '';
       		$item['generate_invoice'] = '';
       		if ($delivery_note)
       			$item['generate_dn'] = '';
	        
	    } else {
	        
	    	$defDate = date('d-m-Y',$row->invoiceDate>0 ? $row->invoiceDate : time());
	        $item['invoice_date'] = '<td>
				<a href="javascript:void(0)" onclick="show_change_date(\''.$row->order_id.'\');">
				'.($row->invoiceDate>0 ? JHTML::_('date',  $row->invoiceDate, JText::_('DATE_FORMAT_LC3')): JText::_('COM_VMINVOICE_CREATE')).'</a>
				<div style="display:none" id="change_invoice_date_'.$row->order_id.'">
				'.JHTML::calendar($defDate, 'invoice_date['.$row->order_id.']', 'invoice_date_'.$row->order_id, '%d-%m-%Y','style="width:70%"').
				'<input class="btn" style="width:100%" type="submit" name="update_inv_date['.$row->order_id.']" value="'.JText::_('COM_VMINVOICE_CHANGE').'">
				</div>
			</td>';
		
	        if ($params->get('allow_order_notes', 0)){

				$orderParams = json_decode($row->params);
				$orderNote = (is_object($orderParams) AND !empty($orderParams->order_note)) ? $orderParams->order_note : '';
				
				$item['order_note'] = '<td style="position:relative;"><img align="center" ';
							
				if (is_object($orderParams) AND !empty($orderParams->order_note))
	        		$item['order_note'] .= 'src="components/com_vminvoice/assets/images/icon-16-article.png" class="hasTip" title="'.JText::_('COM_VMINVOICE_ORDER_NOTES').'" rel="'.nl2br(str_replace('"', "'", $orderNote)).'" ';
				else
					$item['order_note'] .= 'src="components/com_vminvoice/assets/images/icon-16-newarticle.png" '; 
				
				$item['order_note'] .= ' style="cursor:pointer" onclick="show_order_note('.$row->order_id.');">';
				$item['order_note'] .= '<div style="display:none;position:absolute;right:20px;top:20px;width:200px;background:white;padding:5px;border:solid black 1px;z-index:100" id="order_note_'.$row->order_id.'">';
				
				$item['order_note'] .= '<textarea name="order_note['.$row->order_id.']" style="width:100%;height:60px;box-sizing:border-box">'.$orderNote.'</textarea><br>';
				$item['order_note'] .= '<input class="btn" type="submit" name="update_order_note['.$row->order_id.']" value="'.JText::_('COM_VMINVOICE_SAVE').'">';
				$item['order_note'] .= '</div></td>';
	        }
	        
	        $item['invoice_sent'] = '<td align="center">
					<img src="'.InvoiceHelper::imgSrc('mail_' . ($row->invoice_mailed ? 'y' : 'n') . '.png').'" />
				</td>';
			if ($delivery_note) 
	        	$item['dn_sent'] = '<td align="center">
						<img src="'.InvoiceHelper::imgSrc('mail_' . ($row->dn_mailed ? 'y' : 'n') . '.png').'" />
					</td>';
	        	
	        $item['invoice_mail'] = '<td align="center">
				'.$mail_link.'<img src="'.InvoiceHelper::imgSrc('email.png').'" /></a>
			</td>';
	        
			if ($delivery_note)
	        	$item['dn_mail'] = '<td align="center">
					'.$mail_dn_link.'<img src="'.InvoiceHelper::imgSrc('email.png').'" /></a>
				</td>';
			        	
	        $item['generate_invoice'] = '<td align="center"'.($row->generated!=false ? ' class="generated"' : '').'>
	            '.$pdf_link.'<img src="'.InvoiceHelper::imgSrc('pdf.png').'" title="'.($row->generated!=false ? JText::_('COM_VMINVOICE_ALREADY_GENERATED') : '').'"/></a>
			</td>';
	        
			if ($delivery_note)
	        	$item['generate_dn'] = '<td align="center"'.($row->generatedDN!=false ? ' class="generated"' : '').'>
	            '.$pdf_dn_link.'<img src="'.InvoiceHelper::imgSrc('pdf.png').'" title="'.($row->generatedDN!=false ? JText::_('COM_VMINVOICE_ALREADY_GENERATED') : '').'"/></a>
			</td>';
			
       	}
        
        $results = $this->dispatcher->trigger('onInvoicesListItem', array(&$item, $i, $row, $this));
        foreach ($results as $result)
        	if ($result===false) //false = not display row
        		continue;
        
        //display row
        ?>
        <tr class="<?php echo "row$k"; ?>">
        <?php  echo implode(PHP_EOL, $item); ?>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
    <tr>
		<td colspan="<?php echo count($item)+($params->get('allow_order_notes', 0) ? 1 : 0) ?>"><?php echo $this->pagination->getListFooter(); ?></td>
	</tr>

</table>
</div>

    <input type="hidden" name="total" id="total" value="<?php echo $n ;?>" />
    <input type="hidden" name="option" value="com_vminvoice" />
    <input type="hidden" name="task" value="" autocomplete="off"/>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="controller" value="invoices" />
    <input type="hidden" name="filter_order" value="<?php echo $filterOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $filterOrderDir; ?>" />
	
</form>

