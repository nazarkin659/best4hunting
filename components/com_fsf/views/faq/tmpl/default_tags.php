<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php echo FSF_Helper::PageStyle(); ?>
<?php echo FSF_Helper::PageTitle("FREQUENTLY_ASKED_QUESTIONS","TAGS"); ?>

	<div class="fsf_spacer"></div>
	<?php echo FSF_Helper::PageSubTitle("PLEASE_SELECT_A_TAG"); ?>

	<div class='faq_category'>
	    <div class='faq_category_image'>
			<img src='<?php echo JURI::root( true ); ?>/components/com_fsf/assets/images/tags-64x64.png' width='64' height='64'>
	    </div>
	    <div class='fsf_spacer contentheading' style="padding-top:6px;padding-bottom:6px;">
			<?php if (FSF_Settings::Get('faq_cat_prefix')): ?>
			<?php echo JText::_("FAQS"); ?> 
			<?php endif; ?>
			<?php echo JText::_('TAGS'); ?>
		</div>
	</div>
	<div class='fsf_clear'></div>
	
	<div class='fsf_faqs' id='fsf_faqs'>
	<?php if (count($this->tags)) foreach ($this->tags as $tag) : ?>
		<div class='fsf_faq fsf_faq_tag'>
			<div class="fsf_faq_question">
				<a class='fsf_highlight' href='<?php echo FSFRoute::_('index.php?option=com_fsf&view=faq&tag=' . urlencode($tag->tag) . '&Itemid=' . JRequest::getVar('Itemid')); ?>'>
					<?php echo $tag->tag; ?>
				</a>
			</div>
		</div>	
	<?php endforeach; ?>
	<?php if (count($this->tags) == 0): ?>
	<div class="fsf_no_results"><?php echo JText::_("NO_TAGS_FOUND");?></div>
	<?php endif; ?>
	</div>
	
<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'_powered.php'; ?>

<?php echo FSF_Helper::PageStyleEnd(); ?>
