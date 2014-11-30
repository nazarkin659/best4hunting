<?php

/*------------------------------------------------------------------------
# mod_virtuemart_magiczoom - Magic Zoom for Joomla with VirtueMart
# ------------------------------------------------------------------------
# Magic Toolbox
# Copyright 2011 MagicToolbox.com. All Rights Reserved.
# @license - http://www.opensource.org/licenses/artistic-license-2.0  Artistic License 2.0 (GPL compatible)
# Website: http://www.magictoolbox.com/magiczoom/modules/joomla/
# Technical Support: http://www.magictoolbox.com/contact/
/*-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access.');

//NOTE: load tooltip behavior
JHtml::_('behavior.tooltip');

//JHtml::stylesheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');
//JHtml::stylesheet('backend.css', 'media/com_virtuemart_magiczoom/');
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');

?>
<form action="<?php echo JRoute::_('index.php?option=com_virtuemart_magiczoom'); ?>" method="post" name="adminForm" id="adminForm" >
    <input type="hidden" name="option" value="com_virtuemart_magiczoom" />
    <input type="hidden" name="view" value="products" />
    <input type="hidden" name="task" value="alternates" />
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="productId" value="<?php echo $this->productId; ?>" />

    <div class="alternates <?php echo JVERSION_30 ? 'joomla3' : ''; ?>" style="margin: 20px">
        <div style="background: #F9F9F9; padding: 20px">

            <div style="overflow: hidden;">
                <div style="float: left;">
                    <?php
                        $link = JRoute::_("index.php?option=com_virtuemart_magiczoom&view=products&task=hotspots&id={$this->productId}&target={$this->mainImage->virtuemart_media_id}");
                        $text = "<img src=\"".$this->mainImage->imageUrl."\" alt=\"product image\" {$this->dimentions[3]} />";
                    ?>
                    <a href="<?php echo $link; ?>"><?php echo $text; ?></a>
                </div>
                <div style="float: left; margin: 10px; padding: 10px;">
                    <h2><?php echo $this->mainImage->name; ?></h2>
                    <?php echo $this->mainImage->description; ?>
                </div>
            </div>

            <div style="margin: 20px 0">
                <h2>Alternates</h2>
                Select images that are alternates to the main image
                <table class="adminlist table">
                    <tr>
                        <th class="title">Image</th>
                        <th class="title">Is alternate</th>
                        <th class="title">Advanced Zoom Options</th>
                    </tr>
                <?php
                for($i = 0; $i < count($this->items); $i++) {
                    $im = $this->items[$i];
                    $link = JRoute::_("index.php?option=com_virtuemart_magiczoom&view=products&task=hotspots&id={$this->productId}&target={$im->virtuemart_media_id}");
                    $text = "<img src=\"{$im->imageUrl}\" alt=\"{$im->file_title}\" title=\"{$im->name}\" height=\"64\" />";
                ?>
                    <tr class="row<?php echo $i%2; ?>">
                        <td>
                            <a href="<?php echo $link; ?>"><?php echo $text; ?></a>
                        </td>
                        <td>
                            <input type="checkbox" name="alts[<?php echo $im->file_id; ?>][checked]" <?php if($im->is_alternate != '0') echo 'checked'; ?>/>
                        </td>
                        <td colspan="2">
                            <input class="stretch" name="alts[<?php echo $im->file_id ; ?>][advanced]" value="<?php echo $im->advanced_option; ?>">
                        </td>
                    </tr>
                <?php } ?>
                </table>
            </div>

        </div>
    </div>

</form>
