<?php
/*------------------------------------------------------------------------------------------------------------
# VP One Page Checkout! Joomla 2.5 Plugin for VirtueMart 2.0 / VirtueMart 2.6
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 - 2014 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
$comUserOption=shopfunctionsF::getComUserOption();
$url = JRoute::_('index.php?option=com_virtuemart&view=cart');
$user = JFactory::getUser();
if ($user->id) { ?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="login" id="form-login">
	<div class="proopc-loggedin-user"><?php echo JText::sprintf('COM_VIRTUEMART_WELCOME_USER', $user->name ); ?> <b class="caret"></b></div>
	<div class="proopc-logout-cont hide">
		<div class="proopc_arrow_box">
			<div class="proopc-arrow-inner">
				<button class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; ?>" type="submit"><?php echo JText::_( 'JLOGOUT'); ?></button>
			</div>
		</div>
	</div>
	<input type="hidden" name="option" value="<?php echo $comUserOption ?>" />
	<input type="hidden" name="task" value="user.logout" />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="return" value="<?php echo base64_encode($url) ?>" />
	<input type="hidden" name="ctask" value="login" />
</form>
<?php } ?>