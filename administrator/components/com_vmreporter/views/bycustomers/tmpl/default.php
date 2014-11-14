<?php
/**
 * @version     1.0.0
 * @package     com_vmreporter
 * @copyright   Copyright (C) 2013 VirtuePlanet Services LLP. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      VirtuePlanet Services LLP <info@virtueplanet.com> - http://www.virtueplanet.com
 */


// no direct access
defined('_JEXEC') or die;

if (!class_exists( 'VmConfig' )) 
   require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/components/com_vmreporter/assets/css/chosen.css');
$document->addStyleSheet(JURI::base(true).'/components/com_vmreporter/assets/css/vmreporter.css');
$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/jquery.min.js');
$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/chosen.jquery.min.js');
$document->addScript(JURI::base(true).'/components/com_vmreporter/assets/js/component.js');

VmConfig::loadConfig();
VmConfig::loadJLang('com_virtuemart',true);
VmConfig::loadJLang('com_virtuemart_orders',true);
JHTML::_('Behavior.calendar');
$pageNav = $this->pageNav;
$todate = date('Y-m-d');
$from_date = date('Y-m-d' ,strtotime($todate . '-30 days'));
$getCustomers = array();
$getOrders = array();
$getCustomers[0] = JText::_('JALL');
$getOrders[0] = JText::_('JALL');
foreach($this->getCustomers as $Customer):
	$getCustomers[$Customer->virtuemart_user_id] = $Customer->name;
endforeach;
foreach($this->getOrders as $orders):
	$getOrders[$orders->order_status_code] = $orders->order_status_name;
endforeach;
$html = array();
$orders = array();
foreach($getCustomers as $id => $Customers) {
	$html[] = JHTML::_('select.option', $id, $Customers);
}
foreach($getOrders as $status => $name) {
	$orders[] = JHTML::_('select.option', $status, JText::_($name));
}
?>
<div class="generate-report">
	<h3><?php echo  JText::_('COM_VMREPORTER_GENERATE') ?></h3>
	<div class="reporter-inner">		
		<form action="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bycustomers');?>" method="post" name="adminForm1" id="adminForm1">
			<table class="report-table">
				<thead>
					<tr>
						<th width="45%">
							<?php echo JText::_('COM_VMREPORTER_USERS')?>
						</th>
						<th width="25%">
							<?php echo JText::_('COM_VIRTUEMART_ORDER_LIST_STATUS')?>					
						</th>
						<th width="15%">
							<?php echo JText::_('COM_VMREPORTER_STARTDATE') ?>
						</th>
						<th width="15%">
							<?php echo JText::_('COM_VMREPORTER_ENDDATE') ?>
						</th>						
					</tr>					
				</thead>
				<tbody>					
					<tr>
						<td>						
							<?php echo  JHTML::_ ('select.genericlist', $html, 'users[]','class="inputbox first chzn-select" multiple="true" size="1"', 'value', 'text', $this->SelectedUsers);?>
						</td>
						<td>
							<?php echo  JHTML::_ ('select.genericlist', $orders,'status[]', 'class="inputbox second chzn-select" multiple="true" size="1"', 'value', 'text', $this->SelectedStauses);?>	
						</td>					
						<td class="date">
							<?php echo JHTML::_('calendar',$from_date,'start','start','%Y-%m-%d','readonly="yes"');?>
						</td>
						<td class="date">
							<?php echo JHTML::_('calendar',$todate,'end','end','%Y-%m-%d','readonly="yes"');?>
						</td>							
					</tr>					
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4">
							<input class="report-button" type="submit" name="submit" value="<?php echo JText::_('COM_VMREPORTER_GENERATE'); ?>"/>
						</td>						
					</tr>
				</tfoot>
			</table>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="generate" value="1" />
			<input  type="hidden" name="option" value="com_vmreporter"/>
			<input  type="hidden" name="task" value=""/>
			<input  type="hidden" name="view" value="bycustomers"/>
		</form>		
	</div>	
</div>

<?php if($this->reports): ?>  
<div class="available-reports">
	<h3><?php echo  JText::_('COM_VMREPORTER_AVAILABLE_REPORTS') ?></h3>
	<div class="reporter-inner">  
		<form action="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bycustomers');?>" method="post" name="adminForm" id="adminForm">			
			<table class="adminlist">
				<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th width="20%">
							<?php echo JText::_('COM_VMREPORTER_DATETIME') ?>
						</th>
						<th width="69%" class="left" style="padding-left: 8px;">
							<?php echo JText::_('COM_VMREPORTER_REPORT_DESCRIPTION') ?>
						</th>
						<th width="10%">
							<?php echo JText::_('COM_VMREPORTER_REPORT_DOWNLOAD_CSV') ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="4">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach($this->reports as $key=>$value):?>
						<tr class="row<?php echo $key % 2; ?>">
							<td class="center">
							<?php echo JHtml::_('grid.id', $key, $value->id); ?>
						</td>
						<td class="center">
							<a href="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bycustomer&id='.$value->id)?>"><?php echo $value->created_on?></a>
						</td>
						<td class="left">
							<div class="queries">
							<?php $decoded_customers = json_decode($value->report_query);
							
								  //Customers//							
								  echo '<div class="query"><strong>'.JText::_("COM_VMREPORTER_USERS").':</strong> ';
								  $pCount = count($decoded_customers->user);
								  $p = 1;
								  if($pCount):
								  	foreach($decoded_customers->user as $customer):
									echo $customer->name;
									if($p !== $pCount) echo ", ";
									$p++;
									endforeach;
								  else:
								 	echo JText::_('JALL');
								  endif;
								  echo "</div>";
								  
								  /*************Status***************/
								  
								  echo '<div class="query"><strong>'.JText::_("JSTATUS").':</strong> ';
								  $sCount = count($decoded_customers->status);
								  $s = 1;
								  if($sCount):
								  	foreach($decoded_customers->status as $status):
									echo JText::_($status->order_status_name);
									if($s !== $sCount) echo ", ";
									$s++;
									endforeach;
								 else:
								 	echo JText::_('JALL');
								 endif;
								  echo "</div>";
								  
								  // DATE RANGE//
								  
								  echo '<div class="query"><strong>'.JText::_("COM_VMREPORTER_DATERANGE").':</strong> ';
								  echo $decoded_customers->from.' '.JText::_("COM_VMREPORTER_TO").' '.$decoded_customers->to;
								  echo "</div>";
							?>
							</div>
						</td>
						<td class="center" width="10%">
							<a href ="<?php echo JRoute::_('index.php?option=com_vmreporter&task=bycustomers.createCSV&id='.$value->id)?>"><?php echo JText::_('COM_VMREPORTER_REPORT_DOWNLOAD') ?></a>
						</td>
						</tr>
					<?php endforeach;?>
				</tbody>
			</table>			
			<?php echo JHtml::_('form.token'); ?>
			<input type="hidden" name="boxchecked" value="0" />			
			<input  type="hidden" name="option" value="com_vmreporter"/>
			<input  type="hidden" name="task" value=""/>
			<input  type="hidden" name="view" value="bycustomers"/>
		</form>
	</div>
</div>
<?php endif;?>