<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<?php $unpubclass = ""; if ($faq['published'] == 0) $unpubclass = "content_edit_unpublished"; ?>
	<?php if ($this->view_mode == 'allononepage') : ?>
	<div class='fsf_faq_inline'>
		<div class="fsf_faq_question <?php echo $unpubclass; ?>">
			<?php echo $this->content->EditPanel($faq); ?>
			<strong><?php echo $faq['question']; ?></strong>
		</div>
		<div class='fsf_faq_answer'>
			<?php 
			if (FSF_Settings::get( 'glossary_faqs' )) {
				echo FSF_Glossary::ReplaceGlossary($faq['answer']); 
				if ($faq['fullanswer'])
				{
					echo FSF_Glossary::ReplaceGlossary($faq['fullanswer']); 
				}
			} else {
				echo $faq['answer']; 
				if ($faq['fullanswer'])
				{
					echo $faq['fullanswer']; 
				}
			}		
			?>
			<?php if (array_key_exists($faq['id'], $this->tags)): ?>
			<div class='fsf_faq_tags'>
	
				<span><?php echo JText::_('TAGS'); ?>:</span>
				<?php echo implode(", ", $this->tags[$faq['id']]); ?>
			</div>
			<?php endif; ?>
		</div>	
	</div>	
	<?php elseif ($this->view_mode == 'questionwithtooltip'): ?>	
	<div class='fsf_faq'>
		<div class="fsf_faq_question <?php echo $unpubclass; ?>">
			<?php echo $this->content->EditPanel($faq); ?>
			<?php $text = $faq['answer']; 
			if ($faq['fullanswer'])
				$text .= "<div class='fsf_faq_more'><a href='#'>click for more...</a></div>";
				
			$text = str_replace("'", "", $text);
			$question = $faq['question'];
			
			$question = str_replace("'", "", $question);
			
			$output = '<div class="fsf_faq_question_tip">' . $question;
			$output .= '</div><div class="fsf_faq_answer_tip">';
			$output .= $text;
			if (array_key_exists($faq['id'], $this->tags))
			{
				$output .= '<div class="fsf_faq_tags">';
				$output .= '<span>' . JText::_('TAGS') . ':</span> ';
				$output .= str_replace("'","\"",implode(", ", $this->tags[$faq['id']]));
				$output .= '</div>';
			}
			$output .= '</div>'	
			
			?>
			<a href='<?php echo FSFRoute::x( '&faqid=' . $faq['id'] ); ?>' class='fsj_tip_wide fsf_highlight' title='<?php echo $output ?>'>
				<?php echo $faq['question']; ?>
			</a>
		</div>  
	</div>	
	<?php elseif ($this->view_mode == 'questionwithlink'): ?>	
		<div class='fsf_faq'>
			<div class="fsf_faq_question <?php echo $unpubclass; ?>">
				<?php echo $this->content->EditPanel($faq); ?>
				<a class='fsf_highlight' href='<?php echo FSFRoute::x( '&faqid=' . $faq['id'] ); ?>'>
					<?php echo $faq['question']; ?>
				</a>
			</div>
		</div>	
	<?php elseif ($this->view_mode == 'questionwithpopup'): ?>	
		<div class='fsf_faq'>
			<div class="fsf_faq_question <?php echo $unpubclass; ?>">
				<?php echo $this->content->EditPanel($faq); ?>
				<a class="fsf_modal fsf_highlight" href='<?php echo FSFRoute::x( '&tmpl=component&faqid=' . $faq['id'] ); ?>' rel="{handler: 'iframe', size: {x: <?php echo FSF_Settings::get('faq_popup_width'); ?>, y: <?php echo FSF_Settings::get('faq_popup_height'); ?>}}">
					<?php echo $faq['question']; ?>
				</a>
			</div>		
		</div>	
	<?php elseif ($this->view_mode == 'accordian'): ?>	
		<div class='fsf_faq'>
			<div class="fsf_faq_question <?php echo $unpubclass; ?> <?php if ($this->view_mode == "accordian") echo "accordion_toggler_$acl"; ?>" style='cursor: pointer;'>
				<?php echo $this->content->EditPanel($faq); ?>
				<a class='fsf_highlight' href="#" onclick='return false;'><?php echo $faq['question']; ?></a>
			</div>
			<div class='fsf_faq_answer <?php if ($this->view_mode == "accordian") echo "accordion_content_$acl"; ?>'>
			<?php 
				if (FSF_Settings::get( 'glossary_faqs' )) {
					echo FSF_Glossary::ReplaceGlossary($faq['answer']); 
					if ($faq['fullanswer'])
					{
						echo FSF_Glossary::ReplaceGlossary($faq['fullanswer']); 
					}
				} else {
					echo $faq['answer']; 
					if ($faq['fullanswer'])
					{
						echo $faq['fullanswer']; 
					}
				}		
			?>
				<?php if (array_key_exists($faq['id'], $this->tags)): ?>
				<div class='fsf_faq_tags'>
	
					<span><?php echo JText::_('TAGS'); ?>:</span>
					<?php echo implode(", ", $this->tags[$faq['id']]); ?>
				</div>
				<?php endif; ?>
			</div>
			
		</div>
	<?php endif; ?>

