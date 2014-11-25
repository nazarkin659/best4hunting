<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php echo JHTML::_( 'form.token' ); ?>

<script language="javascript" type="text/javascript">
<!--
function submitbutton(pressbutton) {
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
                submitform( pressbutton );
                return;
        }

        <?php
        $editor =& JFactory::getEditor();
        echo $editor->save( 'description' );
        echo $editor->save( 'longdesc' );
        ?>
        submitform(pressbutton);
}
//-->
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
	<fieldset class="adminform">
		<legend><?php echo JText::_("DETAILS"); ?></legend>

		<table class="admintable">
		<tr>
			<td width="135" align="right" class="key">
				<label for="word">
					<?php echo JText::_("WORD"); ?>:
				</label>
			</td>
			<td>
				<input class="text_area" type="text" name="word" id="word" size="32" maxlength="250" value="<?php echo JViewLegacy::escape($this->glossary->word);?>" />
			</td>
		</tr>
		<?php FSFAdminHelper::LA_Form($this->glossary); ?>
		<tr>
			<td width="135" align="right" class="key">
				<label for="description">
					<?php echo JText::_("DESCRIPTION"); ?>:
				</label>
			</td>
			<td>
				<?php echo $editor->display('description', htmlspecialchars($this->glossary->description, ENT_COMPAT, 'UTF-8'), '550', '400', '60', '20', array('pagebreak')); ?>
            </td>

		</tr>
		<tr>
			<td width="135" align="right" class="key">
				<label for="longdesc">
					<?php echo JText::_("LONG_DESCRIPTION"); ?>:
				</label>
			</td>
			<td>
				<?php echo $editor->display('longdesc', htmlspecialchars($this->glossary->longdesc, ENT_COMPAT, 'UTF-8'), '550', '400', '60', '20', array('pagebreak')); ?>
            </td>

		</tr>
	</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="id" value="<?php echo $this->glossary->id; ?>" />
<input type="hidden" name="published" value="<?php echo $this->glossary->published; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="glossary" />
</form>

