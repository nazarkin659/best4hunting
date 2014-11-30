<?php
/**
 * IceCarousel Extension for Joomla 1.6 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2011 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/icecarousel.html
 * @Support 	http://www.icetheme.com/Forums/IceCarousel/
 *
 */
 
 

/* no direct access*/
defined('_JEXEC') or die;
if( modIceCarousel::checkIceAjax() ):
$list = $pages[ $page - 1];
$key = $page - 1;
endif;
?>

<div class="ice-main-item page-<?php echo $key+1;?>">
    <?php foreach( $list as $i => $item ): ?>
     <div class="ice-row" style="width:<?php echo $itemWidth;?>%">
		<div class="lof-inner">
        	<h4 class="heading">
			<a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>"><?php echo $item->title ?></a></h4>
				<div class="carousel_content">
                	<div class="padding k2_padding">
					  <?php if($params->get('itemAuthor')): ?>
							<div class="moduleItemAuthor">
						  <?php echo K2HelperUtilities::writtenBy($item->authorGender); ?>
					
								<?php if(isset($item->authorLink)): ?>
								<a rel="author" title="<?php echo K2HelperUtilities::cleanHtml($item->author); ?>" href="<?php echo $item->authorLink; ?>"><?php echo $item->author; ?></a>
								<?php else: ?>
								<?php echo $item->author; ?>
								<?php endif; ?>
							</div>
							<?php endif; ?>
					 <?php if($params->get('itemDateCreated')): ?>
						<span class="moduleItemDateCreated"><?php echo JText::_('K2_WRITTEN_ON') ; ?> <?php echo JHTML::_('date', $item->created, JText::_('K2_DATE_FORMAT_LC2')); ?></span>
					  <?php endif; ?>
					 <?php if($params->get('itemCategory')): ?>
					  <?php echo JText::_('K2_IN') ; ?> <a class="moduleItemCategory" href="<?php echo $item->categoryLink; ?>"><?php echo $item->categoryname; ?></a>
					  <?php endif; ?>
					
					<div class="<?php echo $show_k2_preview_image?'iceTip':''; ?>" rel="<?php echo $show_k2_preview_image?$item->thumbnail:'';?>" title="<?php echo  $show_k2_preview_image?$item->title:'';?>">
					
					<!-- Show k2 items-->
					<?php if($item->mainImage): ?>
					  <a <?php echo $target;?>  href="<?php echo $item->link;?>" title="<?php echo $item->title;?>">
						<?php echo $item->mainImage; ?>
					  </a>
					<?php endif; ?>
                    </div>
					<?php if($params->get('itemIntroText')): ?>
						 <div class="moduleItemIntrotext">
								<?php echo $item->text; ?>
						</div>
					<?php endif; ?>
					<?php if($params->get('show_readmore') ): ?>
					<a class="moduleItemReadMore" href="<?php echo $item->link; ?>">
						<?php echo JText::_('K2_READ_MORE'); ?>
					</a>
					<br/>
					<?php endif; ?>
					 <?php if($params->get('itemTags') && count($item->tags)>0): ?>
					  <div class="moduleItemTags">
						<b><?php echo JText::_('K2_TAGS'); ?>:</b>
						<?php foreach ($item->tags as $tag): ?>
						<a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a>
						<?php endforeach; ?>
					  </div>
					  <?php endif; ?>
					  <?php if($params->get('itemCommentsCounter') && $componentParams->get('comments')): ?>		
						<?php if(!empty($item->event->K2CommentsCounter)): ?>
							<!-- K2 Plugins: K2CommentsCounter -->
							<?php echo $item->event->K2CommentsCounter; ?>
						<?php else: ?>
							<?php if($item->numOfComments>0): ?>
							<a class="moduleItemComments" href="<?php echo $item->link.'#itemCommentsAnchor'; ?>">
								<?php echo $item->numOfComments; ?> <?php if($item->numOfComments>1) echo JText::_('K2_COMMENTS'); else echo JText::_('K2_COMMENT'); ?>
							</a>
							<?php else: ?>
							<a class="moduleItemComments" href="<?php echo $item->link.'#itemCommentsAnchor'; ?>">
								<?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?>
							</a>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
					 <?php if($params->get('itemHits')): ?>
						<span class="moduleItemHits">
							<?php echo JText::_('K2_READ'); ?> <?php echo $item->hits; ?> <?php echo JText::_('K2_TIMES'); ?>
						</span>
					<?php endif; ?>
                    </div>
                </div>
		</div>
	</div>
    <?php endforeach; ?>
</div>