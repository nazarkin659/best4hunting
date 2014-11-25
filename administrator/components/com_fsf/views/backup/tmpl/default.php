<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php if ($this->log) : ?>
<h1>Your upgrade has been completed.</h1>
<h4>The log of this process is below.</h4>
<?php $logno = 1; ?>
<?php foreach ($this->log as &$log): ?>
	<div>
	<div style="margin:4px;font-size:115%;"><a href="#" onclick="ToggleLog('log<?php echo $logno; ?>');return false;">+<?php echo $log['name']; ?></a></div>
	<div id="log<?php echo $logno; ?>" style="display:none;">
	<pre style="margin-left: 20px;border: 1px solid black;padding: 2px;background-color: ghostWhite;"><?php echo $log['log']; ?></pre>
	</div>
</div>
	<?php $logno++; ?>
<?php endforeach; ?>

<script>
function ToggleLog(log)
{
	if (document.getElementById(log).style.display == "inline")
	{
		document.getElementById(log).style.display = 'none';
	} else {
		document.getElementById(log).style.display = 'inline';
	}
}
</script>
<?php else: ?>

<!--  -->

<h1><?php echo JText::_("UPDATE"); ?></h1>
<a href='<?php echo FSFRoute::x("index.php?option=com_fsf&view=backup&task=update"); ?>'><?php echo JText::_("PROCESS_FREESTYLE_JOOMLA_INSTALL_UPDATE"); ?></a><br />&nbsp;<br />

<h1><?php echo JText::_("BACKUP_DATABASE"); ?></h1>
<a href='<?php echo FSFRoute::x("index.php?option=com_fsf&view=backup&task=backup"); ?>'><?php echo JText::_("DOWNLOAD_BACKUP_NOW"); ?></a><br />&nbsp;<br />

<h1><?php echo JText::_("RESTORE_DATABASE"); ?></h1>
<div style="color:red; font-size:150%"><?php echo JText::_("PLEASE_NOTE_THE_WILL_OVERWRITE_AND_EXISTING_DATA_FOR_FREESTYLE_FAQS"); ?></div>

<?php //  ?>

<form action="<?php echo FSFRoute::x("index.php?option=com_fsf&view=backup&task=restore"); ?>"  method="post" name="adminForm2" id="adminForm2" enctype="multipart/form-data"></::>
<input type="file" id="filedata" name="filedata" /><input type="submit" name="Restore" value="<?php echo JText::_("RESTORE"); ?>">
</form>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_fsf" />
<input type="hidden" name="task" id="task" value="" />
<input type="hidden" name="view" value="backup" />
</form>
<?php endif; ?>