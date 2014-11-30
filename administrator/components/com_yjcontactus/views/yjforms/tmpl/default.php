<?php 
/**
 * Departments tmpl for YjContactUS Component
 * 
 * @package    
 * @subpackage Components
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die(); 

JHTML::_('behavior.tooltip'); 
$user		= JFactory::getUser();
$userId		= $user->get('id');

//Ordering allowed ?
$ordering = ($this->state->get('list.ordering') == 'ordering');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$saveOrder	= $listOrder == 'ordering';
$helper 	= new YjContactUSHelpers();
?>
<form name="adminForm" method="post" action="index.php">
<table class="adminlist" width="100%">
	<thead>
		<tr>
			<th width="25">
				<?php echo JText::_( 'COM_YJCONTACTUS_NUM' ); ?>
			</th>
			<th width="25">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);"/>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_YJCONTACTUS_NAME'), 'name', $listDirn, $listOrder); ?>
			</th>
<!--		<th>
				<?php echo JText::_('COM_YJCONTACTUS_EMAIL'); ?>
			</th>-->
			<th>
				<?php echo JText::_('COM_YJCONTACTUS_DEPARTMENTS'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_YJCONTACTUS_MENU_NAME'); ?>
			</th>            
			<th width="70">
				<?php echo JHTML::_('grid.sort', JText::_('COM_YJCONTACTUS_PUBLISHED'), 'published', $listDirn, $listOrder); ?>
			</th>
			<th width="100" class="no-border-left">
				<?php echo JHTML::_('grid.sort', JText::_('COM_YJCONTACTUS_ORDER'), 'ordering', $listDirn, $listOrder)."&nbsp;&nbsp;".JHTML::_('grid.order', $this->rows); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if(count($this->rows) > 0):	
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i]; 
			$checked 		= JHTML::_('grid.checkedout',   $row, $i);
			//$published 	= JHTML::_('grid.published', $row, $i);
			//$link 		= JRoute::_('index.php?option=com_yjcontactus&task=edit&cid[]='. $row->id);
			$item->max_ordering = 0; //??
			$ordering	= ($listOrder == 'ordering');
			//$canCreate	= $user->authorise('core.create',		'com_miaflv.movie.'.$item->catid);
			//$canEdit	= $user->authorise('core.edit',			'com_miaflv.movie.'.$item->id);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			//$canEditOwn	= $user->authorise('core.edit.own',		'com_miaflv.movie.'.$item->id) && $item->created_by == $userId;
			$canChange	= $user->authorise('core.edit.state',	'com_yjcontactus.form.'.$row->id) && $canCheckin;			
			?>
            <tr class="<?php echo "row$k"; ?>">
	            <td style="text-align:center;">
                    <?php echo $this->pagination->getRowOffset( $i ) ?>
                </td>
                <td style="text-align:center;">
                    <?php echo $checked; ?>
                </td>
                <td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_FORM_NAME') ?>"> 
                    <a title="" href="<?php echo JRoute::_('index.php?option=com_yjcontactus&task=yjform.edit&id=' . $row->id); ?>">
                        <?php echo strip_tags($row->name); ?>
                    </a>
                </td>
    <!--		<td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_FORM_EMAIL') ?>">
                        <?php echo strip_tags($row->email);?>
                </td>-->
                <td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_FORM_DEPART') ?>">
                        <?php echo $helper->get_departments_name(strip_tags($row->departments));?>
                </td>
                <td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_MENU_NAME') ?>">
                        <?php echo $helper->get_menu_name(strip_tags($row->item_id));?>
                </td>            
				<td class="center"><?php echo JHtml::_('jgrid.published', $row->published, $i, 'yjforms.', $canChange, 'cb'); ?></td>
                <td class="order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) : ?>
                            <?php if ($listDirn == 'asc') :?>
                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'yjforms.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'yjforms.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php elseif ($listDirn == 'desc') : ?>
                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'yjforms.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); //($item->catid == @$this->items[$i-1]->catid)?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'yjforms.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); //($item->catid == @$this->items[$i+1]->catid)?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                        <input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                    <?php else : ?>
                        <?php echo $row->ordering; ?>
                    <?php endif; ?>        
                    <?php //echo $item->ordering; ?>
                </td>
            </tr>
			<?php
    	$k = 1 - $k; 
		} 
	else: ?>
        <tr class="row0">
            <td colspan="7" class="center"><?php echo JText::_('COM_YJCONTACTUS_NO_RECORDS'); ?></td>
        </tr>
	<?php endif; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="13">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
</table>
	<input type="hidden" name="option" value="com_yjcontactus"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />    
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<p><center><?php echo JText::_( 'COM_YJCONTACTUS_YJ_POWERED_BY', true )?> <a href='http://www.youjoomla.com' target='_blank'>YouJoomla.com</a></center></p>