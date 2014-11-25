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
?>
<div id="icecarousel<?php echo $module->id; ?>" class="ice-carousel-dark" style="height:<?php echo $moduleHeight;?>; width:<?php echo $moduleWidth;?>">
<div class="ice-horizontal ice-container ice-container">
<?php if( $totalPages  > 1  ) : ?>
    <a class="ice-previous"  href="" onclick="return false;"><?php echo JText::_('Previous');?></a>
    <a class="ice-next" href="" onclick="return false;"><?php echo JText::_('Next');?></a>
<?php endif; ?>
<?php if(  $params->get('navigator_pos','top') && $totalPages  > 1 ) : ?>
    <!-- NAVIGATOR -->    
      <div class="ice-navigator-outer <?php echo $params->get('navigator_pos','top'); ?>">
            <ul class="ice-navigator ice-bullets">
            <?php foreach( $pages as $key => $row ): ?>
                <li><span><?php echo  $key+1;?></span></li>
             <?php endforeach; ?> 		
            </ul>
        </div>
   <?php endif; ?>


 <!-- MAIN CONTENT of ARTICLESCROLLER MODULE --> 
  <div class="ice-main-wapper" style="height:<?php echo $moduleHeight;?>;width:<?php echo $moduleWidth;?>">
		<div class="ice-loading"></div>
		<?php
			if( $isAjax ){
				if(isset($pages[ $limitstart ])){
					$list = $pages[ $limitstart ];
					$key = $limitstart;
					 require(  $itemLayoutPath );
				 }
			}
			else{
				foreach( $pages as $key => $list ): 
					 require(  $itemLayoutPath ); 
				endforeach;
			}; ?>
  </div>
 </div> 
  <!-- END MAIN CONTENT of ARTICLESCROLLER MODULE --> 
 </div> 

