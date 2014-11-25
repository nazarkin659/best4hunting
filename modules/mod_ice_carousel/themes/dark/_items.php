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
				
                  <h<?php echo $item_heading; ?>>
					
					<?php if ($params->get('link_titles') == 1) : ?>
					
                        <a class="mod-ice-carousel-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
                        <?php echo $item->title; ?>
                        </a>
                        <?php else :?>
                        <?php echo $item->title; ?>
                        <?php endif; ?>
                    
					</h<?php echo $item_heading; ?>>
                    
                    
                    <p class="iteminfo">
						<?php if ($params->get('show_author')) :?>
                        <span class="mod-articles-category-writtenby"><?php echo JText::_('AUTHOR');?>: 
                        <?php echo $item->displayAuthorName; ?>
                        </span>
                        <?php endif;?>
                        
                        <?php if ($item->displayCategoryTitle) :?>
                        <span class="mod-articles-category-category"><?php echo JText::_('CATEGORY');?>: 
                        <?php echo $item->displayCategoryTitle; ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($item->displayDate) : ?>
                            <span class="mod-articles-category-date"><?php echo JText::_('DATE');?>: 
                            <?php echo $item->displayDate; ?></span>
                        <?php endif; ?>
                        
                        <?php if ($item->displayHits) :?>
                        <span class="mod-ice-carousel-hits"><?php echo JText::_('HITS');?>: 
                        <?php echo $item->displayHits; ?>  </span>
                        <?php endif; ?>
                      
                      </p>
                
			<?php if($item->mainImage): ?>
				<?php echo $item->mainImage; ?>
			<?php endif; ?>
			<?php if ($params->get('show_introtext')) :?>
				<p class="mod-articles-category-introtext">
				<?php echo $item->displayIntrotext; ?>
				</p>
			<?php endif; ?>

			<?php if ($params->get('show_readmore')) :?>
				<p class="mod-articles-category-readmore">
					<a class="mod-articles-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
					<?php if ($item->params->get('access-view')== FALSE) :
							echo JText::_('MOD_CAROUSEL_REGISTER_TO_READ_MORE');
						elseif ($readmore = $item->alternative_readmore) :
							echo $readmore;
							echo JHTML::_('string.truncate', $item->title, $params->get('readmore_limit'));
						elseif ($params->get('show_readmore_title', 0) == 0) :
							echo JText::sprintf('MOD_CAROUSEL_READ_MORE_TITLE');	
						else :
							echo JText::_('MOD_CAROUSEL_READ_MORE');
							echo JHTML::_('string.truncate', $item->title, $params->get('readmore_limit'));
						endif; ?>
				</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>
 </div> 