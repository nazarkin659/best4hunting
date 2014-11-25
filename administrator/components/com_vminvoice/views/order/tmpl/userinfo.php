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

defined('_JEXEC') or ('Restrict Access');

/* @var $this VMInvoiceViewOrder */

JFilterOutput::objectHTMLSafe($this->billingData);
JFilterOutput::objectHTMLSafe($this->shippingData);

//TODO: remove all scripts!!!
//http://forum.virtuemart.net/index.php?topic=59255.15
function sanitiseUserInput($input)
{
	$input = preg_replace('#<\s*script\b[^>]*>.*<\s*\/\s*script\s*>#isU','',$input); //remove script tags
	
	//if it is only one text input, make him 100% long to look nice
	
	//match text input but only if its only one
	if (preg_match('/(<\s*input.*type\s*=\s*"\s*text\s*".*)>(?!.*<\s*input)$/iUsx',$input,$matchesInput)){

		if (preg_match('/class\s*=\s*".*"/iUsx',$matchesInput[1])) //has class defined, append fullWidth
			$modifInput = preg_replace('/class\s*=\s*"(.*)"/iUsx','class="$1 fullWidth"',$matchesInput[1]);
		else
			$modifInput =  $matchesInputs[1].' class="fullWidth"'; //add new class
			
		$input = preg_replace('#'.preg_quote($matchesInput[1]).'#is', $modifInput, $input);
	}
	
	//if it contains editor, keep only raw text area (editors makes JS bugs on reloading page)
	if (preg_match('/<!--\s*Start Editor\s*-->.*(<\s*textarea.*>.*<\s*\/\s*textarea\s*>)/isU',$input,$match))
		$input = preg_replace('/(<textarea.*)class\s*=\s*(?:"|\').*(?:"|\')(.*>)/isU','$1 $2',$match[1]);
		
	return $input;
}

?>

<?php if (! $this->userajax) { ?><div id="userInfo"><?php } ?>

<h2 class="customer"><?php echo JText::_('COM_VMINVOICE_CUSTOMER_INFORMATION'); ?></h2>

<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->billingData->user_id; ?>" autocomplete="off"/> 
<input type="hidden" name="update_userinfo" id="update_userinfo" value="0" autocomplete="off"/> 
<input type="hidden" name="B_user_info_id" id="B_user_info_id" value="<?php if (isset($this->billingData->user_info_id)) echo $this->billingData->user_info_id; ?>" autocomplete="off"/> 
<input type="hidden" name="S_user_info_id" id="S_user_info_id" value="<?php  if (isset($this->shippingData->user_info_id)) echo $this->shippingData->user_info_id; ?>" autocomplete="off"/> 


<?php if ($this->orderData->as_guest){ // blby je ze to pri udeslani vymaze user formular.. ?>
<table class="admintable registration">
  <tr>
  	<td align="left">
  	<b><?php echo JText::_('COM_VMINVOICE_CHECKOUT_AS_GUEST_INFO') ?></b>
  	</td>
  	<td align="right">
  	<label title="<?php echo JText::_('COM_VMINVOICE_CHECKOUT_AS_GUEST_INFO') ?>"> 
		<input type="checkbox" name="as_guest" id="as_guest" value="1" onclick="showUserData('');" checked /> <?php echo JText::_('COM_VMINVOICE_CHECKOUT_AS_GUEST')?> </label>
		</td>
	</tr>
</table>
<?php } else { ?>

<table class="admintable registration">
  <tr>
  	<td width="20%" align="right"><span class="customerid hasTip" title="<?php echo JText::_('COM_VMINVOICE_CUSTOMER_ID_DESC'); ?>"><?php echo JText::_('COM_VMINVOICE_CUSTOMER_ID'); ?></span></td>
  	<td align="left">
			
  	<?php
  	if (empty($this->billingData->user_id)){

		echo '<span class="hasTip" style="display:inline" id="new_customer_info" title="'.$this->escape(JText::_('COM_VMINVOICE_NEW_CUSTOMER_ADDITIONAL')).'"><b>'.JText::_('COM_VMINVOICE_NEW_CUSTOMER_DESC').'</b></span>';
  	}
  	else {
  		
  		if (!empty($this->billingData->user_info_id))
  		{
	  		if (COM_VMINVOICE_ISVM2)
	  			$url = 'index.php?option=com_virtuemart&view=user&task=edit&cid[]='.$this->billingData->user_id.'&virtuemart_user_id[]='.$this->billingData->user_id;
	  		else
	  			$url = 'index.php?option=com_virtuemart&page=admin.user_form&user_id='.$this->billingData->user_id;
	  		
	  		echo '<b><a href="'.JRoute::_($url).'" target="_blank">'.$this->billingData->user_id.'</a></b>';
	  		echo ': '.$this->billingData->first_name.' '.$this->billingData->last_name;
  		}
  		else
  		{
	  		if (COM_VMINVOICE_ISVM2)
	  			$url = 'index.php?option=com_users&task=user.edit&id='.$this->billingData->user_id;
	  		else
	  			$url = 'index.php?option=com_users&view=user&task=edit&cid[]='.$this->billingData->user_id;
	  			
	  		echo '<b><a href="'.JRoute::_($url).'" target="_blank">'.JText::_('COM_VMINVOICE_JOOMLA_USER').' '.$this->billingData->user_id.'</a></b>';
	  		$user = JFactory::getUser($this->billingData->user_id);
	  		echo ': '.$user->name;
  			echo ' - <b class="hasTip" title="'.$this->escape(JText::_('COM_VMINVOICE_NEW_SHOPPER_ADDITIONAL')).'">'.JText::_('COM_VMINVOICE_NEW_SHOPPER_DESC').'.</b>';
  		}
  	}


  	?></td>
  	<td align="right">
  	<?php if (COM_VMINVOICE_ISVM2) {?>
  	<label title="<?php echo JText::_('COM_VMINVOICE_CHECKOUT_AS_GUEST_INFO') ?>"> 
		<input type="checkbox" name="as_guest" id="as_guest" value="1" onclick="showUserData('');" /> <?php echo JText::_('COM_VMINVOICE_CHECKOUT_AS_GUEST')?> </label>
	<?php } ?>
	</td>
	</tr>
	<?php  if (empty($this->billingData->user_id) || empty($this->billingData->user_info_id)) { ?>
  <tr>
    <td align="right"><span class="shoppergroup"><?php echo JText::_('COM_VMINVOICE_NEW_SHOPPER_GROUP') ?></span></td>
    <td colspan="2"><?php 
    echo JHTML::_('select.genericlist',invoiceGetter::getShopperGroups(), 'shopper_group','', 'id', 'name', InvoiceGetter::getDefaultShopperGroup());
    
    ?></td>
  </tr>
  <?php } ?>
  <tr>
    <td width="20%"><span class="registration hasTip" title="<?php echo $this->escape(JText::_('COM_VMINVOICE_REGISTRATION_INFO_TITLE')); ?>::<?php echo $this->escape(JText::_('COM_VMINVOICE_REGISTRATION_INFO_DESC')); ?>"><?php echo JText::_('COM_VMINVOICE_EXISTING_NEW_CUSTOMER'); ?></span>
    <td colspan="2">
      <input type="text" id="user" name="user" class="fullWidth" autocomplete="off" onkeyup="generateWhisper(this,event,'<?php echo JURI::base(); ?>');" onkeydown="moveWhisper(this,event);"  onclick="generateWhisper(this,event,'<?php echo JURI::base(); ?>');" />
      <div class="clr"></div>
      <div id="userwhisper"></div>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td colspan="2"><strong><?php echo $this->escape(JText::_('COM_VMINVOICE_REGISTRATION_INFO_TITLE')); ?></strong><br /><?php echo $this->escape(JText::_('COM_VMINVOICE_REGISTRATION_INFO_DESC')); ?></td>
  </tr>
</table>
<?php } ?>

<table width="100%" class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="50%" valign="top"><?php echo JText::_('COM_VMINVOICE_BILLING_ADDRESS'); ?></th>
			<th width="50%" valign="top"><?php echo JText::_('COM_VMINVOICE_SHIPPING_ADDRESS'); ?>: <?php echo $this->shippingData->address_type_name ? $this->shippingData->address_type_name : JText::_('COM_VMINVOICE_DEFAULT'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td valign="top">
				<table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0" id="billing_address">
					<tbody>
						<tr>
							<td colspan="4">								
								<a href="javascript:copyBillingToDelivery()" title="<?php echo JText::_('COM_VMINVOICE_COPY_BILLING_INTO_DELIVERY'); ?>" class="fill"></a>			
							</td>
						</tr>
						<tr>
						  <td>
        		    <fieldset class="adminform">
        		      <legend><?php echo JText::_('COM_VMINVOICE_CUSTOMER_NAME') ?></legend>
        		      <table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0">
        		        <tbody>
          						<tr>
          							<td width="20%" align="right" class="key"><?php if (empty($this->billingData->user_id)) echo '* ';?><?php echo JText::_('COM_VMINVOICE_FIRST_NAME'); ?></td>
          							<td width="30%" align="left"><input id="B_first_name" type="text" class="fullWidth" name="B_first_name" value="<?php echo $this->billingData->first_name; ?>" /></td>
          							<td width="20%" align="right" class="key"><?php if (empty($this->billingData->user_id)) echo '* ';?><?php echo JText::_('COM_VMINVOICE_LAST_NAME'); ?></td>
          							<td width="30%" align="left"><input id="B_last_name" type="text" class="fullWidth" name="B_last_name" value="<?php echo $this->billingData->last_name; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_TITLE'); ?></td>
          							<td align="left"><input id="B_title" type="text" class="fullWidth" name="B_title" value="<?php echo $this->billingData->title; ?>" /></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_MIDDLE_NAME'); ?></td>
          							<td align="left"><input id="B_middle_name" type="text" class="fullWidth" name="B_middle_name" value="<?php echo $this->billingData->middle_name; ?>" /></td>
          						</tr>
          						<tr>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_COMPANY_NAME'); ?></td>
          							<td width="80%" colspan="3" align="left"><input type="text" class="fullWidth" name="B_company" value="<?php echo $this->billingData->company; ?>" /></td>
          						</tr>
                    </tbody>
                  </table>
                </fieldset>	
        		    <fieldset class="adminform">
        		      <legend><?php echo JText::_('COM_VMINVOICE_ADDRESS') ?></legend>
        		      <table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0">
        		        <tbody>
          						<tr>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_ADDRESS_1'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="B_address_1" value="<?php echo $this->billingData->address_1; ?>" /></td>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_ADDRESS_2'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="B_address_2" value="<?php echo $this->billingData->address_2; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_CITY'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="B_city" value="<?php echo $this->billingData->city; ?>" /></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_ZIP_POSTAL_CODE'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="B_zip" value="<?php echo $this->billingData->zip; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_COUNTRY'); ?></td>
          							<td align="left"><?php echo JHTML::_('select.genericlist',$this->countries, 'B_country', array('class' => 'fullWidth','onchange' => 'populateStates(\'B_country\',\'B_state\')','onkeyup' => 'populateStates(\'B_country\',\'B_state\')'), 'id', 'name', $this->billingData->country); ?></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_STATE_PROVINCE_REGION'); ?></td>
          							<td align="left"><?php echo JHTML::_('select.genericlist',$this->b_states, 'B_state', array('class' => 'fullWidth'), 'id', 'name', $this->billingData->state); ?></td>
          						</tr>
                    </tbody>
                  </table>
                </fieldset>	
        		    <fieldset class="adminform">
        		      <legend><?php echo JText::_('COM_VMINVOICE_CONTACTS') ?></legend>
        		      <table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0">
        		        <tbody>
          						<tr>
          							<td width="20%" align="right" class="key"><?php if (empty($this->billingData->user_id)) { echo '* '; } ?><?php echo JText::_('COM_VMINVOICE_MAIL'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="B_email" value="<?php echo $this->billingData->email; ?>" /></td>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_PHONE'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="B_phone_1" value="<?php echo $this->billingData->phone_1; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_MOBILE_PHONE'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="B_phone_2" value="<?php echo $this->billingData->phone_2; ?>" /></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_FAX'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="B_fax" value="<?php echo $this->billingData->fax; ?>" /></td>
          						</tr>
          						
          						<?php 
          						//display custom user fields
          						if (isset($this->billingData->userFields)) {
          						  $countUF = 0;
          							foreach ($this->billingData->userFields as $userField)
          							{

          								
                          $countUF++;
                          if ($countUF == 1) {
                            ?>
            						    
                						<tr>
                						  <td colspan="4">&nbsp;</td>
                						</tr>
                						<tr>
                						  <td colspan="4" align="left" class="title"><h3><?php echo JText::_('COM_VMINVOICE_ADDITIONAL_FIELDS') ?></h3></td>
                						</tr>
                						
                						<?php
              						}
          								//display label (with tip)
          								echo '<tr><td align="right">';
          								$titleTrans = InvoiceGetter::getVMTranslation($userField['title']);
          								if (isset($userField['desc']) && $userField['desc']!=$userField['title'])
          									echo '<span class="hasTip" title="'.$titleTrans.'::'.InvoiceGetter::getVMTranslation($userField['desc']).'">'.$titleTrans.'</span></td>';
          								else
          									echo $titleTrans.'</td>';
          								
          								//display inputs
          								echo '<td align="left">'.sanitiseUserInput($userField['input']).'</td></tr>';
          							}
          						}
          						?>
          					</tbody>
          				</table>
          			</fieldset>
						  </td>
						</tr>
					</tbody>
				</table>
			</td>
			<td valign="top">
				<table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0" id="shipping_address">
					<tbody>
						<tr>
							<td align="left" style="height:24px;padding-left:10px!important;">
							<label style="font-weight:bold;vertical-align:middle;" class="hasTip" title="<?php echo JText::_('COM_VMINVOICE_SAME_AS_BILLING'); ?>::<?php echo JText::_('COM_VMINVOICE_SAME_AS_BILLING_DESC'); ?>">
							<input style="margin:0px;" type="checkbox" id="billing_is_shipping" name="billing_is_shipping" value="1" onclick="if (this.checked) disableAllShipping(); else enableAllShipping(); changed_userinfo=true;" <?php if ($this->shippingData->billing_is_shipping) echo 'checked' ?>/> <?php echo JText::_('COM_VMINVOICE_SAME_AS_BILLING'); ?></label>
							</td>
							<td align="left"> &nbsp;</td>
							<td align="right">&nbsp; </td>
							<td align="left"> &nbsp;</td>
						</tr>
						<tr>
						  <td colspan="4">
        		    <fieldset class="adminform">
        		      <legend><?php echo JText::_('COM_VMINVOICE_CUSTOMER_NAME') ?></legend>
        		      <table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0">
        		        <tbody>
          						<tr>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_FIRST_NAME'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="S_first_name" value="<?php echo $this->shippingData->first_name; ?>" /></td>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_LAST_NAME'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="S_last_name" value="<?php echo $this->shippingData->last_name; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_TITLE'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="S_title" value="<?php echo $this->shippingData->title; ?>" /></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_MIDDLE_NAME'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="S_middle_name" value="<?php echo $this->shippingData->middle_name; ?>" /></td>
          						</tr>
          						<tr>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_COMPANY_NAME'); ?></td>
          							<td width="80%" colspan="3" align="left"><input type="text" class="fullWidth" name="S_company" value="<?php echo $this->shippingData->company; ?>" /></td>
          						</tr>
          					</tbody>
          				</table>
          			</fieldset>
          			<fieldset class="adminform">
          		    <legend><?php echo JText::_('COM_VMINVOICE_ADDRESS') ?></legend>
          		    <table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0">
          		      <tbody>
          						<tr>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_ADDRESS_1'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="S_address_1" value="<?php echo $this->shippingData->address_1; ?>" /></td>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_ADDRESS_2'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="S_address_2" value="<?php echo $this->shippingData->address_2; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_CITY'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="S_city" value="<?php echo $this->shippingData->city; ?>" /></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_ZIP_POSTAL_CODE'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="S_zip" value="<?php echo $this->shippingData->zip; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_COUNTRY'); ?></td>
          							<td align="left"><?php echo JHTML::_('select.genericlist',$this->countries, 'S_country', array('class' => 'fullWidth','onchange' => 'populateStates(\'S_country\',\'S_state\')','onkeyup' => 'populateStates(\'S_country\',\'S_state\')'), 'id', 'name', $this->shippingData->country); ?></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_STATE_PROVINCE_REGION'); ?></td>
          							<td align="left"><?php echo JHTML::_('select.genericlist',$this->s_states, 'S_state', array('class' => 'fullWidth'), 'id', 'name', $this->shippingData->state); ?></td>
          						</tr>
          					</tbody>
        					</table>
        				</fieldset>
          			<fieldset class="adminform">
          				<legend><?php echo JText::_('COM_VMINVOICE_CONTACTS') ?></legend>
                  <table class="admintable" width="100%" cellspacing="0" cellpadding="1" border="0">
                    <tbody>    
          						<tr>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_MAIL'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="S_email" value="<?php echo $this->shippingData->email; ?>" /></td>
          							<td width="20%" align="right" class="key"><?php echo JText::_('COM_VMINVOICE_PHONE'); ?></td>
          							<td width="30%" align="left"><input type="text" class="fullWidth" name="S_phone_1" value="<?php echo $this->shippingData->phone_1; ?>" /></td>
          						</tr>
          						<tr>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_MOBILE_PHONE'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="S_phone_2" value="<?php echo $this->shippingData->phone_2; ?>" /></td>
          							<td align="right" class="key"><?php echo JText::_('COM_VMINVOICE_FAX'); ?></td>
          							<td align="left"><input type="text" class="fullWidth" name="S_fax" value="<?php echo $this->shippingData->fax; ?>" /></td>
          						</tr>
          						
          						<?php 
          						//display custom user fields
          						if (isset($this->shippingData->userFields)) {
          							$countUF = 0;
                        foreach ($this->shippingData->userFields as $userField)
          							{ 
          						    
                          $countUF++;
                          if ($countUF == 1) {
                            ?>
            						    
                						<tr>
                						  <td colspan="4">&nbsp;</td>
                						</tr>
                						<tr>
                						  <td colspan="4" align="left" class="title"><h3><?php echo JText::_('COM_VMINVOICE_ADDITIONAL_FIELDS') ?></h3></td>
                						</tr>
                						
                						<?php
              						}
          								//display label (with tip)
          								echo '<tr><td align="right">';
          								$titleTrans = InvoiceGetter::getVMTranslation($userField['title']);
          								if (isset($userField['desc']) AND $userField['desc']!=$userField['title'])
          									echo '<span class="hasTip" title="'.$titleTrans.'::'.InvoiceGetter::getVMTranslation($userField['desc']).'">'.$titleTrans.'</span></td>';
          								else
          									echo $titleTrans.'</td>';
          								
          								//display inputs
          								echo '<td align="left">'.sanitiseUserInput($userField['input']).'</td></tr>';
          							}
          						}
          
          						?>
          					</tbody>
          				</table>
        				</fieldset>
						  </td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

							<?php if ($this->shippingData->billing_is_shipping){ ?>
							<script type="text/javascript">disableAllShipping();</script>
							<?php }?>
							
<?php if (! $this->userajax) { ?></div><?php } ?>