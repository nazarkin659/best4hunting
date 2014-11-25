<?php
/**
 * IceVmCategory Extension for Joomla 2.5 By IceTheme
 * 
 * 
 * @copyright	Copyright (C) 2008 - 2012 IceTheme.com. All rights reserved.
 * @license		GNU General Public License version 2
 * 
 * @Website 	http://www.icetheme.com/Joomla-Extensions/iceaccordion.html
 * @Support 	http://www.icetheme.com/Forums/IceVmCategory/
 *
 */
  ?>
 
 <div class="lofmenu_virtuemart">
	<?php echo $categories; ?>
</div>
<script language="javascript">
	if(jQuery('.lofmenu_virtuemart .lofmenu .lofitem1') ){
		jQuery('.lofmenu_virtuemart .lofmenu .lofitem1').find('ul').css({'visibility':'hidden'});
	}
	jQuery(document).ready(function(){
		jQuery('.lofmenu_virtuemart .lofmenu .lofitem1 ul').each(function(){
			jQuery(this).find('li:first').addClass('loffirst');
		})
		jQuery('.lofmenu_virtuemart .lofmenu li').each(function(){
			jQuery(this).mouseenter(function(){
				jQuery(this).addClass('lofactive');
				jQuery(this).find('ul').css({'visibility':'visible'});
				jQuery(this).find('ul li ul').css({'visibility':'hidden'});
			});
			jQuery(this).mouseleave(function(){
				jQuery(this).removeClass('lofactive');
				jQuery(this).find('ul').css({'visibility':'hidden'});
			});
		});
	});
</script>