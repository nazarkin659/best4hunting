<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
	    	<div class='faq_category <?php if ($this->view_mode_cat == "accordian") echo "accordion_toggler_$acl"; ?>' <?php if ($this->view_mode_cat == "accordian") echo " style='cursor: pointer;' "; ?> >

	    		<?php if ($cat['image']) : ?>
	    		<div class='faq_category_image'>
					<?php if (substr($cat['image'],0,1) == "/") : ?>
	    			<img src='<?php echo JURI::root( true ); ?><?php echo $cat['image']; ?>' width='64' height='64'>
	    			<?php else: ?>
	    			<img src='<?php echo JURI::root( true ); ?>/images/fsf/faqcats/<?php echo $cat['image']; ?>' width='64' height='64'>
	    			<?php endif; ?>
	    		</div>
	    		<?php endif; ?>

				<div class='faq_category_head'>
					<?php if ($cat['id'] == $this->curcatid) : ?><b><?php endif; ?>
					
					<?php if ($this->view_mode_cat == "popup") : ?>
	
						<a class="fsf_modal fsf_highlight" href='<?php echo FSFRoute::x( '&tmpl=component&limitstart=&catid=' . $cat['id'] . '&view_mode=' . $this->view_mode_incat ); ?>' rel="{handler: 'iframe', size: {x: 650, y: 375}}">
						   <?php echo $cat['title'] ?>
						</a>

						   <?php elseif ($this->view_mode_cat == "accordian"): ?>

						<A class="fsf_highlight" href="#" onclick='return false;'><?php echo $cat['title'] ?></a>
						
					<?php else: ?>

						<A class="fsf_highlight" href='<?php echo FSFRoute::x( '&limitstart=&catid=' . $cat['id'] );?>'><?php echo $cat['title'] ?></a>

					<?php endif; ?>
					
					<?php if ($cat['id'] == $this->curcatid) : ?></b><?php endif; ?>
				</div>

				<div class='faq_category_desc'><?php echo $cat['description']; ?></div>

			</div>

			<!-- INLINE FAQS -->
			<?php if ($this->view_mode_cat == "inline" || $this->view_mode_cat == "accordian") : ?>
			<div class='faq_category_faqlist <?php if ($this->view_mode_cat == "accordian") echo "accordion_content_$acl"; ?>' id="faq_category_faqlist">
				<?php if ($this->view_mode_cat == "accordian") $acl = 2; ?>
				<?php $this->view_mode = $this->view_mode_incat; ?>
				<?php if (array_key_exists('faqs',$cat) && count($cat['faqs']) > 0): ?>
					<?php foreach ($cat['faqs'] as &$faq) : ?>
						<?php include JPATH_SITE.DS.'components'.DS.'com_fsf'.DS.'views'.DS.'faq'.DS.'snippet'.DS.'_faq.php' ?>
					<?php endforeach; ?>	
				<?php else: ?>
					<div class="fsf_faq_question" style="padding-bottom:5px;"><?php echo JText::_("NO_FAQS_FOUND_IN_THIS_CATEGORY");?></div>
					<div class="fsf_faq_answer"></div>
				<?php endif; ?>
				<?php if ($this->view_mode_cat == "accordian") $acl = 1; ?>
			</div>
			<?php endif; ?>				
			<!-- END INLINE FAQS -->

			<!--<div class='fsf_clear'></div>-->

