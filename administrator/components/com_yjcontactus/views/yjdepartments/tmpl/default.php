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
			<th>
				<?php echo JText::_('COM_YJCONTACTUS_DESCRIPTION'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_YJCONTACTUS_MESSAGE'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_YJCONTACTUS_EMAIL'); ?>
			</th>            
			<th width="70">
				<?php echo JHTML::_('grid.sort', JText::_('COM_YJCONTACTUS_PUBLISHED'), 'published', $listDirn, $listOrder); ?>
			</th>
			<th width="100">
				<?php //echo JHTML::_('grid.sort', JText::_('COM_YJCONTACTUS_ORDER'), 'ordering', @$this->lists['order_Dir'], @$this->lists['order'])."&nbsp;&nbsp;".JHTML::_('grid.order', $this->rows); ?>
				<?php echo JHtml::_('grid.sort',  'COM_YJCONTACTUS_ORDER', 'ordering', $listDirn, $listOrder); ?>
                <?php if ($saveOrder) :?>
                    <?php echo JHtml::_('grid.order',  $this->rows, 'filesave.png', 'yjdepartments.saveorder'); ?>
                <?php endif; ?>                
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	if(count($this->rows) > 0):		
		$k = 0;
		for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
			$row = &$this->rows[$i]; 
			$checked 	= JHTML::_('grid.checkedout',   $row, $i);

			$item->max_ordering = 0; //??
			$ordering	= ($listOrder == 'ordering');
			$canCreate	= $user->authorise('core.create',		'com_yjcontactus.yjdepartment.'.$row->id);
			$canEdit	= $user->authorise('core.edit',			'com_yjcontactus.yjdepartment.'.$row->id);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			//$canEditOwn	= $user->authorise('core.edit.own',		'com_yjcontactus.yjdepartment.'.$item->id) && $item->created_by == $userId;
			$canChange	= $user->authorise('core.edit.state',	'com_yjcontactus.yjdepartment.'.$row->id) && $canCheckin;

			$link 		= JRoute::_('index.php?option=com_yjcontactus&controller=yjdepartments&task=yjdepartment.edit&id='. $row->id);
	?>
            <tr class="<?php echo "row$k"; ?>">
            	<td style="text-align:center;">
                    <?php echo $this->pagination->getRowOffset( $i ) ?>
                </td>
                <td style="text-align:center;">
                    <?php echo $checked; ?>
                </td>
                <td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_DEPART_NAME') ?>" > 
                    <a title="" href="<?php echo $link ?>">
                        <?php echo strip_tags($row->name); ?>
                    </a>
                </td>
                <td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_DEPART_DESCRIPTION') ?>" > 
                        <?php echo substr(strip_tags($row->description), 0, 100);?>
                </td>
                <td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_DEPART_MESSAGE') ?>" > 
                        <?php echo $row->message;?>
                </td>
                <td style="text-align:left;">
                    <span class="editlinktip hasTip" title="<?php echo JText::_('COM_YJCONTACTUS_TIP_FORM_EMAIL') ?>" > 
                        <?php echo $row->email != "" ? $row->email : JText::_('COM_YJCONTACTUS_DEFAULT_FORM_EMAIL');?>
                </td>
				<td class="center"><?php echo JHtml::_('jgrid.published', $row->published, $i, 'yjdepartments.', $canChange, 'cb'); ?></td>
                <td class="order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) : ?>
                            <?php if ($listDirn == 'asc') :?>
                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'yjdepartments.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'yjdepartments.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php elseif ($listDirn == 'desc') : ?>
                                <span><?php echo $this->pagination->orderUpIcon($i, true, 'yjdepartments.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); //($item->catid == @$this->items[$i-1]->catid)?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'yjdepartments.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); //($item->catid == @$this->items[$i+1]->catid)?></span>
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
            <td colspan="8" class="center"><?php echo JText::_('COM_YJCONTACTUS_NO_RECORDS'); ?></td>
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
	<input type="hidden" name="option" value="com_yjcontactus" />
	<input type="hidden" name="view" value="yjdepartments" />    
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="yjdepartments" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->get('list.ordering'); ?>" />    
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>      
</form>
<p><center><?php echo JText::_( 'COM_YJCONTACTUS_YJ_POWERED_BY', true )?> <a href='http://www.youjoomla.com' target='_blank'>YouJoomla.com</a></center></p>