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

if (!class_exists ('getNames')) {
	require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_vmreporter' . DS .'helpers' . DS . 'getname.php');
}
if (!class_exists( 'VmConfig' )) 
   require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
   
VmConfig::loadConfig();
VmConfig::loadJLang('com_virtuemart',true);
VmConfig::loadJLang('com_virtuemart_orders',true);  
 
$cache = & JFactory::getCache();
$cache->setCaching( 1 );
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$pageNav = $this->pageNav;
$report = $this->report["reports"];
$this->form->getInput('jscss'); // Load JS and CSS files.
$document = JFactory::getDocument();
$document->addScriptDeclaration("
js = jQuery.noConflict();
js(document).ready(function(){	
	js('#submenu').find('a').each(function() {
		var menu_href = js(this).attr('href');
		if (menu_href.indexOf('bycategories') >= 0) {
			js(this).addClass('active');
		}
	})
});
");

$coloums = $this->form->getInput('column_filter');
foreach($coloums as $key=>$value):
		if($filterOrder = $value):
			$getColoums[] = JHTML::_('select.option', $key, $value);
		endif;
endforeach;
?>
<div class="report-switch">
	<?php echo $this->form->getInput('report_switch'); ?>
</div>

<div id="chart-area" class="hide">
	<div class="plot-menu">
		<?php echo $this->form->getInput('plot_menu'); ?>
	</div>
	
	<div class="plot-container">
		<div id="plotChart" class="demo-placeholder"></div>
		<div class="chart-loading hide"></div>	
	</div>
</div>
<div id="table-area">
	<form action="" method="post" enctype="multipart/form-data" name="adminForm" id="bycategory-form" class="form-validate">
		<div class="generate-report">
			<h3><?php echo JText::_('COM_VIRTUEMART_COLUMNS') ?></h3>
			<div class="reporter-inner">	
				<table class="column-selection">
					<tr>
						<td align="left" width="90%">
							<?php echo  JHTML::_ ('select.genericlist',$getColoums , 'coloums[]','class="inputbox chzn-select" multiple="true"', 'value', 'text' ,$this->SelectedColoumns);?>
						</td>
						<td width="10%" style="text-align: center">
							<button class="report-button" onclick="this.form.period.value='';this.form.submit();"><?php echo JText::_('JSUBMIT'); ?></button>
						</td>
					</tr>
				</table>
			</div>	
		</div>

		<div class="available-reports">
			<h3><?php echo  JText::_('COM_VMREPORTER_REPORTS') ?></h3>
			<div class="reporter-inner">  
				<table class="adminlist">
					<thead>
						<tr>
							<th width="2%"><?php echo '#'; ?></th>
							<?php 
							$pID = JRequest::getInt('id',0);
							$Getdir = JRequest::getVar('dir','asc');
							$getFilterby = JRequest::getVar('filterby', 'order_number');
							if($Getdir == 'asc') $dir='desc';
							else $dir='asc';
							foreach($this->SelectedColoumns as $th): 
							$pDir = ($getFilterby == $th)? $dir:'asc';
							?>										
							<th>
								<a href="<?php echo JRoute::_('index.php?option=com_vmreporter&view=bycategory&id='.$pID.'&filterby='.$th.'&dir='.$pDir)?>"><?php echo $coloums[$th] ?>
								<?php
								if($Getdir == 'desc') $image = "sort_desc.png";
								else $image = "sort_asc.png";
								if($getFilterby == $th)
									echo '<img src="'.JURI::root(true).'/media/system/images/'.$image.'" alt="'.$pDir.'"/>';
								?>					
								</a>			
							</th>
							<?php endforeach;?>
						</tr>
					</thead>
					<tbody>
						<?php 
						$Nav = array();
						foreach($pageNav as $key=>$value){ $Nav[$key] = $value; }
						$limit = $Nav['limit']*$Nav['pages.current'];
						if($Nav['total'] < $limit) {
							$limit = $Nav['total'];
						} 
						for($i=$Nav['limitstart'];$i<$limit;$i++) {
							$num = $i+1;
							echo "<tr class='sortable'>";
							echo "<td>".$num."</td>";
							foreach($this->SelectedColoumns as $item):
									echo "<td class=".$item.">";
									if($item == 'product_attribute') {
										$attrs = json_decode('['.$report[$i]->$item.']');
										if(count($attrs) > 0) {
											foreach($attrs[0] as $attr) {
												if(is_object($attr)) {
													foreach($attr as $atr) {
														if(is_object($atr)) {
															foreach($atr as $opt) {
																echo str_replace('</p>', '', str_replace('<p>','', str_replace('<span class="costumValue" >',': ', str_replace('<span class="costumTitle">','', str_replace('</span>','', $opt))))).'; ';
															}
														}
													}
												} else {
													echo str_replace('</p>', '', str_replace('<p>','', str_replace('<span class="costumValue" >',': ', str_replace('<span class="costumTitle">','', str_replace('</span>','', $attr))))).'; ';
												}	
											} 											
										}											
									} else if($item == 'user_currency_id' || $item == 'order_currency') {
										echo $cache->call(array('getNames','currency'),$report[$i]->$item);
									} else if($item == 'virtuemart_user_id' || $item == 'created_by') {
										echo $cache->call(array('getNames','user'),$report[$i]->$item);
									} else if($item == 'virtuemart_shipmentmethod_id') {
										echo $cache->call(array('getNames','shipment'),$report[$i]->$item);
									} else if($item == 'virtuemart_paymentmethod_id') {	
										echo $cache->call(array('getNames','payment'),$report[$i]->$item);
									} else if($item == 'order_status_name') {	
										echo JText::_($report[$i]->$item);											
									} else {
										echo $report[$i]->$item;
									}				
									echo "</td>";		

							endforeach;
							echo "</tr>";
						}?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="<?php echo (count($this->SelectedColoumns)+1)?>">
								<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
					</tfoot>				
				</table>
			</div>
		</div>
		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="task" value="" />
		<input  type="hidden" name="option" value="com_vmreporter"/>
		<input  type="hidden" name="view" value="bycategory"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>