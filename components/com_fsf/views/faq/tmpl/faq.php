<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>

<?php echo $this->tmpl ? FSF_Helper::PageStylePopup() : FSF_Helper::PageStyle(); ?>

<?php $width = ""; if (FSF_Settings::get('faq_popup_inner_width') > 0) $width = " style='width:".FSF_Settings::get('faq_popup_inner_width')."px;' "; ?>
<?php if ($this->tmpl) echo "<div $width>"; ?>

<?php echo $this->tmpl ? FSF_Helper::PageTitlePopup("FREQUENTLY_ASKED_QUESTION") : FSF_Helper::PageTitle("FREQUENTLY_ASKED_QUESTION"); ?>

<?php $unpubclass = ""; if ($this->faq['published'] == 0) $unpubclass = "content_edit_unpublished"; ?>
<div class="<?php echo $unpubclass; ?>">
	<?php echo $this->content->EditPanel($this->faq); ?>

<div class="fsf_faq_question ">
	<strong><?php echo $this->faq['question']; ?></strong>
</div>

<?php if ($this->faq['featured']): ?>
	<div class="fsf_faq_featured"><img src="<?php echo JURI::root( true ); ?>/components/com_fsf/assets/images/featuredfaq.png"	width="16" height="16" /><?php echo JText::_('Featured_faq'); ?></div>
<?php endif; ?>

<div class='fsf_faq_answer_single'>	
 
<?php 
if (FSF_Settings::get( 'glossary_faqs' )) {
	echo FSF_Glossary::ReplaceGlossary($this->faq['answer']); 
	if ($this->faq['fullanswer'])
	{
		echo FSF_Glossary::ReplaceGlossary($this->faq['fullanswer']); 
	}
} else {
	echo $this->faq['answer']; 
	if ($this->faq['fullanswer'])
	{
		echo $this->faq['fullanswer']; 
	}
}		
?>
	<?php if (count($this->tags) > 0): ?>
	<div class='fsf_faq_tags'>
	
		<span><?php echo JText::_('TAGS'); ?>:</span>
		<?php echo implode(", ", $this->tags); ?>
	</div>
	<?php endif; ?>
</div>	

</div>
<?php if ($this->tmpl) echo "</div>"; ?>

<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'_powered.php'; ?>
<?php if (FSF_Settings::get( 'glossary_faqs' )) echo FSF_Glossary::Footer(); ?>

<?php if ($this->tmpl) echo "</div>"; ?>

<?php echo $this->tmpl ? FSF_Helper::PageStylePopupEnd() :FSF_Helper::PageStyleEnd(); ?>

<script>
<?php if ($this->tmpl): ?>
jQuery(document).ready( function ()
{
	jQuery('a').click( function (ev) {
		ev.preventDefault();
		var href = jQuery(this).attr('href');
		window.parent.location.href = href;
	});		
});
<?php endif; ?>

/**/
</script>

