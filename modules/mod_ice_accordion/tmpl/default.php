<?php
/**
 * IceAccordion Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/iceaccordion.html
 * @Support 	http://www.icetheme.com/Forums/IceAccordion/
 *
 */
 

/* no direct access*/
defined('_JEXEC') or die;
?>
<div  id="iceaccordion<?php echo $module->id;?>">
<div class="iceaccordion">
        <?php require $item_path; ?>       
</div>
<div class="iceaccordion-hidden">
</div>
<?php echo $paging ;?>
 <?php
	if(!empty($link_all)){
		?>
	<div class="link_all">
		<?php echo $link_all; ?>
	</div>
		<?php
	}
?>
</div>
