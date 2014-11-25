<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<table width="100%">
	<tr>
		<td width="55%" valign="top">
		
			<fieldset class="adminform">
				<legend><?php echo JText::_("GENERAL"); ?></legend>
		
				<?php $this->Item("SETTINGS","index.php?option=com_fsf&view=settings","settings","FSF_HELP_SETTINGS"); ?>
				<?php $this->Item("TEMPLATES","index.php?option=com_fsf&view=templates","templates","FSF_HELP_TEMPLATES"); ?>
				<?php $this->Item("VIEW_SETTINGS","index.php?option=com_fsf&view=settingsview","viewsettings","FSF_HELP_VIEWSETTINGS"); ?>
<!--  -->
			</fieldset>
		
<!-- ##NOT_TEST_START## -->
<!--  -->		

			<fieldset class="adminform">
				<legend><?php echo JText::_("FAQS"); ?></legend>
				<?php $this->Item("FAQ_CATS","index.php?option=com_fsf&view=faqcats","categories","FSF_HELP_FAQ_CATS"); ?>
				<?php $this->Item("FAQS","index.php?option=com_fsf&view=faqs","faqs","FSF_HELP_FAQS"); ?>
	
			</fieldset>
		
<!--  -->	

			<fieldset class="adminform">
				<legend><?php echo JText::_("GLOSSARY"); ?></legend>
				<?php $this->Item("GLOSSARY_ITEMS","index.php?option=com_fsf&view=glossarys","glossary","FSF_HELP_GLOSSARY_ITEMS"); ?>
	
			</fieldset>
<!-- ##NOT_TEST_END## -->
		
<!--  -->	


<!--  -->	
	
		</td>
		<td width="45%" valign="top">


<?php
if (FSFAdminHelper::Is16())
{

JHTML::addIncludePath(array(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsf'.DS.'html'));	 ?>	

<?php

echo JHTML::_( 'fsjtabs.start' );

$title = "Version";
echo JHTML::_( 'fsjtabs.panel', $title, 'cpanel-panel-'.$title, true );

$ver_inst = FSFAdminHelper::GetInstalledVersion();
$ver_files = FSFAdminHelper::GetVersion();

?>
<?php if (FSFAdminHelper::IsFAQs()) :?>
	<h3>If you like Freestyle FAQs please vote or review us at the <a href='http://extensions.joomla.org/extensions/directory-a-documentation/faq/11910' target="_blank">Joomla extensions directory</a></h3>
<?php elseif (FSFAdminHelper::IsTests()) :?>
<h3>If you like Freestyle Testimonials please vote or review us at the <a href='http://extensions.joomla.org/extensions/contacts-and-feedback/testimonials-a-suggestions/11911' target="_blank">Joomla extensions directory</a></h3>
<?php else: ?>
<h3>If you like Freestyle FAQs, please vote or review us at the <a href='http://extensions.joomla.org/extensions/clients-a-communities/help-desk/11912' target="_blank">Joomla extensions directory</a></h3>
<?php endif; ?>
<?php
echo "<h4>Currently Installed Verison : <b>$ver_files</b></h4>";
if ($ver_files != $ver_inst)
	echo "<h4>".JText::sprintf('INCORRECT_VERSION',FSFRoute::x('index.php?option=com_fsf&view=backup&task=update'))."</h4>";

?>
<div id="please_wait">Please wait while fetching latest version information...</div>

<iframe id="frame_version" height="300" width="100%" frameborder="0" border="0"></iframe>	
<?php

$title = "Announcements";
echo JHTML::_( 'fsjtabs.panel', $title, 'cpanel-panel-'.$title );
?>
<iframe id="frame_announce" height="600" width="100%" frameborder="0" border="0"></iframe>
<?php

$title = "Help";
echo JHTML::_( 'fsjtabs.panel', $title, 'cpanel-panel-'.$title );
?>
<iframe id="frame_help" height="600" width="100%" frameborder="0" border="0"></iframe>
<?php
echo JHTML::_( 'fsjtabs.end' );

} else {
?>
	
<?php

$pane = &JPane::getInstance('tabs', array('allowAllClose' => true));
echo $pane->startPane("content-pane");


$title = "Version";
echo $pane->startPanel( $title, 'cpanel-panel-'.$title );

$ver_inst = FSFAdminHelper::GetInstalledVersion();
$ver_files = FSFAdminHelper::GetVersion();

?>
<?php if (FSFAdminHelper::IsFAQs()) :?>
	<h3>If you like Freestyle FAQs please vote or review us at the <a href='http://extensions.joomla.org/extensions/directory-a-documentation/faq/11910' target="_blank">Joomla extensions directory</a></h3>
<?php elseif (FSFAdminHelper::IsTests()) :?>
<h3>If you like Freestyle Testimonials please vote or review us at the <a href='http://extensions.joomla.org/extensions/contacts-and-feedback/testimonials-a-suggestions/11911' target="_blank">Joomla extensions directory</a></h3>
<?php else: ?>
<h3>If you like Freestyle FAQs, please vote or review us at the <a href='http://extensions.joomla.org/extensions/clients-a-communities/help-desk/11912' target="_blank">Joomla extensions directory</a></h3>
<?php endif; ?>
<?php
echo "<h4>Currently Installed Verison : <b>$ver_files</b></h4>";
if ($ver_files != $ver_inst)
	echo "<h4>".JText::sprintf('INCORRECT_VERSION',FSFRoute::x('index.php?option=com_fsf&view=backup&task=update'))."</h4>";

?>
<div id="please_wait">Please wait while fetching latest version information...</div>

<iframe id="frame_version" height="300" width="100%" frameborder="0" border="0"></iframe>	
<?php
echo $pane->endPanel();

$title = "Announcements";
echo $pane->startPanel( $title, 'cpanel-panel-'.$title );
?>
<iframe id="frame_announce" height="600" width="100%" frameborder="0" border="0"></iframe>
<?php
echo $pane->endPanel();

$title = "Help";
echo $pane->startPanel( $title, 'cpanel-panel-'.$title );
?>
<iframe id="frame_help" height="600" width="100%" frameborder="0" border="0"></iframe>
<?php
echo $pane->endPanel();

echo $pane->endPane();

}
?>

		</td>	
	</tr>
</table>

<script>
jQuery(document).ready(function () {
	jQuery('#frame_version').attr('src',"http://freestyle-joomla.com/latestversion-fsf?ver=<?php echo FSFAdminHelper::GetVersion();?>");
	jQuery('#frame_version').load(function() 
    {
        jQuery('#please_wait').remove();
    });

	jQuery('.fsf_main_item').mouseenter(function () {
		jQuery(this).css('background-color', '<?php echo FSF_Settings::get('css_hl'); ?>');
	});
	jQuery('.fsf_main_item').mouseleave(function () {
		jQuery(this).css('background-color' ,'transparent');
	});

	jQuery('#frame_announce').attr('src',"http://freestyle-joomla.com/support/announcements?tmpl=component");
	jQuery('#frame_help').attr('src',"http://freestyle-joomla.com/comhelp/fsf-main-help");
});
</script>