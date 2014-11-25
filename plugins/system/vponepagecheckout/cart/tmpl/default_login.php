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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
vmJsApi::jPrice();

if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
$comUserOption=shopfunctionsF::getComUserOption();
if (empty($this->url)){
	$uri = JFactory::getURI();
	$url = $uri->toString(array('path', 'query', 'fragment'));
} else{
	$url = $this->url;
}
$action_url = JRoute::_('index.php?view=cart',$this->useXHTML,$this->useSSL);
$user = JFactory::getUser();
if(!isset($this->show)) $this->show = true;
if ($this->show and $user->id == 0  ) {

?>
<h4 class="proopc-subtitle"><?php echo JText::_('PLG_VPONEPAGECHECKOUT_ASK_FOR_LOGIN'); ?></h4>
<form action="<?php echo $action_url; ?>" method="post" name="proopc-login" id="UserLogin" autocomplete="off">
	<div class="proopc-group">
		<div class="proopc-input-group-level">
	    	<label class="full-input" for="proopc-username"><?php echo JText::_('COM_VIRTUEMART_USERNAME'); ?></label>
		</div>
		<div class="proopc-input proopc-input-append">
			<input type="text" id="proopc-username" name="username" class="inputbox input-medium" size="18" />	
			<i class="status hover-tootip"></i>
		</div>			
	</div>	
	<div class="proopc-group">
		<div class="proopc-input-group-level">
			<label class="full-input" for="proopc-passwd"><?php echo JText::_('COM_VIRTUEMART_PASSWORD'); ?></label>
		</div>
		<div class="proopc-input proopc-input-append">
			<input id="proopc-passwd" type="password" name="password" class="inputbox input-medium" size="18" placeholder="" />
			<i class="status hover-tootip"></i>
		</div>			
	</div>			
		

	<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
	<div class="proopc-group">
		<div class="proopc-input proopc-input-append">
			<label for="proopc-remember" class="proopc-checkbox inline">			
				<input type="checkbox" id="proopc-remember" name="remember" class="inputbox" value="yes" alt="Remember Me" />
				<?php echo $remember_me = JVM_VERSION===1? JText::_('Remember me') : JText::_('JGLOBAL_REMEMBER_ME') ?>
			</label>
		</div>			
	</div>		
	<?php endif; ?>		
	
	<div class="proops-login-inputs">	
		<div class="proopc-group">
			<div class="proopc-input proopc-input-prepend">				
				<button type="submit" class="proopc-btn<?php if($this->params->get('color', 1) == 2) echo ' proopc-btn-danger'; else echo ' proopc-btn-inverse'; ?>" onclick="return ProOPC.loginAjax()"><i id="proopc-login-process" class="proopc-button-process"></i><?php echo JText::_('PLG_VPONEPAGECHECKOUT_LOGIN_AND_CHECKOUT') ?></button>
			</div>			
		</div>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" /> 
		<input type="hidden" name="return" id="proopc-return"	value="<?php echo base64_encode($url); ?>" />
		<?php echo JHtml::_('form.token');?>
	</div>
	<div class="proops-login-inputs">	
		<div class="proopc-group">
			<div class="proopc-input">	
				<ul class="proopc-ul">			
					<li><a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=remind'); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_USERNAME'); ?></a></li>
					<li><a href="<?php echo JRoute::_('index.php?option='.$comUserOption.'&view=reset'); ?>"><?php echo JText::_('COM_VIRTUEMART_ORDER_FORGOT_YOUR_PASSWORD'); ?></a></li>
				</ul>
			</div>			
		</div>
	</div>			
</form>

<?php  } else if ($user->id  ){ ?>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="login" id="form-login">
	<?php echo JText::sprintf( 'COM_VIRTUEMART_HINAME', $user->name ); ?>
	<input type="submit" name="Submit" class="button btn" value="<?php echo JText::_( 'COM_VIRTUEMART_BUTTON_LOGOUT'); ?>" />
	<input type="hidden" name="option" value="<?php echo $comUserOption ?>" />
	<?php if ( JVM_VERSION===1 ) { ?>
	<input type="hidden" name="task" value="logout" />
	<?php } else { ?>
	<input type="hidden" name="task" value="user.logout" />
	<?php } ?>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="return" value="<?php echo base64_encode($url) ?>" />
	<input type="hidden" name="ctask" value="login" />
</form>
<?php }?>

