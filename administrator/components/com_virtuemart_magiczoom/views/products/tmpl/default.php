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

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root().'media/com_virtuemart_magiczoom/backend.css');

?>
<div class="magictoolbox <?php echo JVERSION_30 ? 'joomla3' : ''; ?>">
<form action="<?php echo JRoute::_('index.php?option=com_virtuemart_magiczoom'); ?>" method="post" name="adminForm" id="adminForm" >
    <input type="hidden" name="option" value="com_virtuemart_magiczoom" />
    <input type="hidden" name="view" value="products" />
    <input type="hidden" name="task" value="displayProducts" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="actions">
        <label for="what">Search (name): </label><input name="what" value="<?php echo $this->what; ?>"/>
        <label for="category">Category: </label><select name="category"><?php
            foreach($this->categories as $id => $name) {
                ?><option<?php echo ($name == $this->category ? ' selected="selected"' : ''); ?> value="<?php echo $id; ?>"><?php echo $name; ?></option><?php
            }
        ?></select>
        <input type="submit" value="Go"/>
    </div>
</form>
<?php if($this->productCount) { ?>
<table class="adminlist table">
    <tr>
        <th class="title">ID</th>
        <th class="title">Image</th>
        <th class="title">Additional</th>
        <th class="title">Name</th>
        <th class="title">Description</th>
    </tr>
    <?php
        for($i = 0; $i < count($this->items); $i++) {
            $product = $this->items[$i];
    ?>
        <tr class="row<?php echo $i%2; ?>">
            <td><?php echo $product->id; ?></td>
            <td>
                <?php if($product->imageUrl) { ?>
                    <img height="24" src='<?php echo $product->imageUrl; ?>' alt="product image"/>
                <?php } else { ?>
                    <i>No image</i>
                <?php } ?>
            </td>
            <td>
                <?php if($product->imageUrl === false) { ?>
                    <i>Upload images for this product first</i><br/>
                <?php } else if($product->images_count > 1) {
                    $link = JRoute::_("index.php?option=com_virtuemart_magiczoom&view=products&task=alternates&id={$product->id}");
                    $text = "Edit alternates/hotspots";
                ?>
                    <b>
                        <?php echo ($product->images_count-1).' additional image'.($product->images_count > 1 ? 's' : ''); ?>
                        <br/>
                        <a href="<?php echo $link; ?>"><?php echo $text; ?></a>
                    </b>
                <br/>
                <?php } else {
                    ?>
                    <i>Upload more images first</i>
                <br/>
                <?php }
                    $link = 'index.php?view=media&virtuemart_product_id='.$product->id.'&no_menu=1&option=com_virtuemart';
                    //$link = JRoute::_('index.php?view=media&virtuemart_product_id='.$product->id.'&option=com_virtuemart');
                    echo $this->getPopupLinkHTML($link, "Manage files", 800, 540, '_blank', 'Upload additional images', 'screenX=100,screenY=100');
                ?>
            </td>
            <td><?php echo $product->name; ?></td>
            <td><?php echo $product->description; ?></td>
        </tr>
    <?php } ?>
</table>
<div>
    <?php echo $this->getPaginationHTML($this->productCount, $this->from); ?>
</div>

<?php } ?>
</div>
