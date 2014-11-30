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
JHtml::_('behavior.formvalidation');
$editor = &JFactory::getEditor();
$helper = new YjContactUSHelpers();
?>
<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
	Joomla.submitbutton = function(task)
	{
		if (task == 'yjdepartment.cancel' || document.formvalidator.isValid(document.id('yjms-form'))) {
			Joomla.submitform(task, document.getElementById('yjms-form'));
		}
	}
}
//-->
</script>            
<form action="index.php" method="post" name="adminForm" id="yjcontactusform-form" class="form-validate">
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<!--<legend><?php echo empty($this->item->id) ? JText::_('COM_YJCONTACTUS_EDIT_FORM') : JText::_('COM_YJCONTACTUS_NEW_FORM'); ?></legend>-->
			<ul class="adminformlist">

				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name', NULL, $this->item->name !='' ? $this->item->name : ''); ?></li>
			</ul>
            
			<ul class="adminformlist">
				<div class="clr"></div>
				<?php echo $this->form->getLabel('description'); ?>
                <div class="clr"></div>
				<?php echo $this->form->getInput('description', NULL, $this->item->description !='' ? $this->item->description : ''); ?>

				<div class="clr"></div>
                <?php echo $this->form->getLabel('message'); ?>
                <div class="clr"></div>
				<?php echo $this->form->getInput('message', NULL, $this->item->message !='' ? $this->item->message : ''); ?>
			</ul>
            
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('upload'); ?>
				<?php echo $this->form->getInput('upload', NULL, $this->item->upload !='' ? $this->item->upload : 1); ?></li>

				<li><?php echo $this->form->getLabel('enabled'); ?>
				<?php echo $this->form->getInput('enabled', NULL, $this->item->enabled !='' ? $this->item->enabled : 1); ?></li>
                
				<li><?php echo $this->form->getLabel('published'); ?>
				<?php echo $this->form->getInput('published', NULL, $this->item->published !='' ? $this->item->published : 1); ?></li>
                
				<li><?php echo $this->form->getLabel('email'); ?>
				<?php echo $this->form->getInput('email', NULL, $this->item->email !='' ? $this->item->email : ''); ?></li>

                <?php echo $this->form->getInput('old_id', NULL, $this->item->id > 0 ? $this->item->id : NULL); ?>

                <?php echo $this->form->getInput('id', NULL, $this->item->id > 0 ? $this->item->id : NULL); ?>

			</ul>
			<div class="clr"> </div>

		</fieldset>
	</div>
    <input type="hidden" name="option" value="com_yjcontactus"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="id" value="<?php echo isset($this->item->id) ? $this->item->id : NULL ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>    
</form>
<p><center><?php echo JText::_( 'COM_YJCONTACTUS_YJ_POWERED_BY', true )?> <a href='http://www.youjoomla.com' target='_blank'>YouJoomla.com</a></center></p>