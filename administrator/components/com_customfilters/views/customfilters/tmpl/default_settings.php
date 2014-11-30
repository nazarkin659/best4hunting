<?php
/**
 *
 * The basic view file
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2010 - 2013 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: view.html.php 1 2011-10-21 19:19 sakis $
 * @since		1.8.0
 */

?>

<div class="bdpopup cf_advacned_settings" id="window<?php echo $item->id?>">
<a id="hide_popup<?php echo $item->id?>" class="hide_popup"></a>
	<h3><?php echo JText::_('COM_CUSTOMFILTERS_ADV_SETTINGS')?></h3>
	<ul class="adminformlist">
	<li>
	<label class="cflabel" for="slider_min_value_<?php echo $item->id?>"><?php echo JText::_('COM_CUSTOMFILTERS_SLIDER_MIN_VALUE_LABEL');?></label>
	<input type="text" name="slider_min_value[<?php echo $item->id?>]" id="slider_min_value_<?php echo $item->id?>" value="<?php echo $item->slider_min_value ?>" class="inputbox" size="4" maxlength="8"/>
	</li>
	<li>
	<label class="cflabel" for="slider_max_value_<?php echo $item->id?>"><?php echo JText::_('COM_CUSTOMFILTERS_SLIDER_MAX_VALUE_LABEL');?></label>
	<input type="text" name="slider_max_value[<?php echo $item->id?>]" id="slider_max_value_<?php echo $item->id?>" value="<?php echo $item->slider_max_value ?>" class="inputbox" size="4" maxlength="8"/>
	</ul>
	<button class="btn bdokbutton" id="close_btn<?php echo $item->id?>" onclick="return false;">OK</button>
</div>