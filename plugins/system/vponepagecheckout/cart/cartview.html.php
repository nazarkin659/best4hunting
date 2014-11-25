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

jimport( 'joomla.html.parameter' );
jimport('joomla.application.component.view');
if (!class_exists('VmConfig'))
{
  require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
}
if(!class_exists('VmView'))
{
  require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'vmview.php');
}

VmConfig::loadJLang('com_virtuemart', true);
VmConfig::loadJLang('com_virtuemart_shoppers', true);

class VirtueMartViewCart extends VmView 
{

	public function display($tpl = null) 
	{
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();
		$document->setMetaData('robots','NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET');
		$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
		$params = new JParameter($plugin->params);		
		
		if (JPluginHelper::isEnabled('system', 'bonus')) 
		{
			JLoader::discover('VmbonusHelperFront', JPATH_SITE . '/components/com_vm_bonus/helpers');
		}
		
		$this->addTemplatePath(dirname(__FILE__).DS. 'tmpl'.DS); /* New Layout Path */
    if($templatePath = $this->getTemplatePath())
    {
      $this->addTemplatePath($templatePath); /* Add Template Layout Path */
    }    
		
		$layoutName = $this->getLayout();
		if($layoutName == 'select_payment' or $layoutName == 'select_shipment')
		{
			$this->setLayout('default');
			$layoutName = 'default';
			JRequest::setVar('task', 'procheckout');
		}
		if(!$layoutName)
		{
			$layoutName = JRequest::getWord('layout', 'default');
		}			
		$this->assignRef('layoutName', $layoutName);
		$format = JRequest::getWord('format');		
		
		if (!class_exists('VirtueMartCart')) require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		$this->assignRef('cart', $cart);
		
		/* Start of ProOPC Script */
		$ProOpcSession = JFactory::getSession();
		if($params->get('ajax_validation', 0)) 
		{
			if(!class_exists('ProOPCHelper')) require(dirname(__FILE__) .DS.'helper.php');			
			$users = $ProOpcSession->get('ProOPC', array(), 'users');	
				
			if(count($users) < 1) 
			{
				ProOPCHelper::getUsers();
				$users = $ProOpcSession->get('ProOPC', array(), 'users');	
			}			
		}
		
		// Add user details to cart if user logs in using other modules
		$juser = JFactory::getUser();
		if($juser->guest)
		{
			$mainframe->setUserState('proopc.guest.user', 1);			
		}
		else if($mainframe->getUserState('proopc.guest.user'))
		{
			$mainframe->setUserState('proopc.guest.user', 0);
			if($cart->STsameAsBT == 1) 
			{
				$cart->ST = 0;
			}									
			if($this->getUserBTinfo($juser->id) != false) 
			{
				if(!is_array($cart->BT)) 
				{
					$cart->BT = array();
				}	
				foreach($this->getUserBTinfo($juser->id) as $field=>$value) 
				{
					$cart->BT[$field] = $value;
				}
			}
			if($this->getUserSTinfo($juser->id) != false and $cart->STsameAsBT != 1) 
			{
				if(!is_array($cart->ST)) 
				{
					$cart->ST = array();
				}					
				foreach($this->getUserSTinfo($juser->id) as $field=>$value) 
				{
					$cart->ST[$field] = $value;
				}					
			}	
			if($this->getUserBTinfo($juser->id) != false || $this->getUserSTinfo($juser->id) != false) 
			{
				$cart->setCartIntoSession();
			}				
		}
		
		$post = JRequest::get('request');
		if(isset($post['ctask'])) 
    {
			$checkoutTask = $post['ctask'];
		} 
    else 
    {
			$checkoutTask = '';
		}
		
		if($checkoutTask == "checkemail" and $params->get('ajax_validation', 0)) 
    {
			$email = $post['email'];
			$valid = 1;						
			foreach($users as $user) 
			{
				if($user->email == $email) 
				{
					$valid = 0;
					break;
				}
			}
			$return = array('valid'=>$valid);
			$this->jsonReturn($return);					
		}	
		else if($checkoutTask == "getpaymentscripts") 
    {
			$cart->prepareCartViewData();					
			$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
			$this->assignRef('currencyDisplay',$currencyDisplay);
			$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();	
			$this->checkPaymentMethodsConfigured();
			$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);			
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);		
			$this->assignRef('params', $params);			
			
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');			
			$this->lSelectPayment();	
			$payments = $this->renderPlgLayout('default_payment');
      $dom = new domDocument;
		  $dom->loadHTML($payments);
      $scripts = $dom->getElementsByTagName('script');
      $scriptURI = array();
      $_scripts = array();
      foreach ($scripts as $script) 
      {			  
        if(!$script->getAttribute('src')) 
        {
          $tempScripts = str_replace('//-->','', str_replace('<!--', '', $script->textContent));
          if(strpos($tempScripts, 'jQuery(function ()') !== false) 
          {
            $tempScripts = str_replace('jQuery(function () {', '', $this->str_lreplace('});', '',$tempScripts));
          } 
          $_scripts[] = trim($tempScripts);          
        } 
        else 
        {
          $scriptURI[] = $script->getAttribute('src');
        }
      } 		
			
			$return = array('payments'=>$payments, 'payment_script'=>$scriptURI, 'payment_scripts'=>$_scripts);
			$this->jsonReturn($return);			
		}
		else if($checkoutTask == "getshipmentscripts") 
    {
			$cart->prepareCartViewData();					
			$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
			$this->assignRef('currencyDisplay',$currencyDisplay);
			$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();	
			$this->checkPaymentMethodsConfigured();
			$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);			
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);		
			$this->assignRef('params', $params);				
		
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$this->lSelectShipment();	
			$shipments = $this->renderPlgLayout('default_shipment');
      $dom = new domDocument;
		  $dom->loadHTML($shipments);
      $shipment_scripts = $dom->getElementsByTagName('script');
      $shipment_inline_scripts = array();
      foreach ($shipment_scripts as $shipment_script) 
      {			  
        if(!$shipment_script->getAttribute('src')) 
        {
          $shipment_inline_scripts[] = trim($shipment_script->textContent);          
        } 
      } 					
			
			$return = array('shipments'=>$shipments, 'shipment_scripts'=>$shipment_inline_scripts);
			$this->jsonReturn($return);			
		}		
		else if($checkoutTask == "checkuser" and $params->get('ajax_validation', 0)) 
    {				
			$username = $post['username'];
			$valid = 1;	
			foreach($users as $user) 
      {
				if($user->username == $username) 
        {
					$valid = 0;
					break;					
				}
			}
			$return = array('valid'=>$valid);
			$this->jsonReturn($return);
		}			
		else if($checkoutTask == "savebtaddress") 
    {	
			$error = 0;
			if(isset($cart->tosAccepted)) {
				if($cart->tosAccepted) 
        {
					$post['agreed'] = 1;
				} 
        else 
        {
					$post['agreed'] = 0;
				}	
			}			
			$cart->saveAddressInCart($post,'BT');
			
			if($cart->STsameAsBT) 
      {
				$cart->ST = 0;
			}
			$cart->prepareCartViewData();
			
			$currentUser = JFactory::getUser();
			$stage = isset($post['stage']) ? $post['stage'] : '';
			if($stage == 'final' && ($currentUser->id > 0)) 
			{
				$post['virtuemart_user_id'] = $currentUser->id;
				$post['agreed'] = 1;
				$post['address_type'] = 'BT';
				$user = VmModel::getModel('user');
				$savereturn = $user->store($post);	
				if(!$savereturn) 
				{
					$error = 1;
				}
			}

			$ret = array();
			foreach($cart->BTaddress['fields'] as $item)
			{
				$ret[$item['name']] = $this->escape($item['value']);
			} 
						
			$messages=array();
			foreach($mainframe->getMessageQueue() as $message) 
			{
				$messages[]=$message["message"];
			}
			$messages = implode('<br/>', $messages);
			$return = array('error' => $error, 'info' => $ret, 'msg' => $messages);
			$this->jsonReturn($return);
		}
		else if($checkoutTask == "savestaddress") 
		{	
			$error = 0;		
			$cart->saveAddressInCart($post,'ST');
			if(isset($post['shipto_virtuemart_userinfo_id'])) 
      {
				$cart->ST['virtuemart_userinfo_id'] = $post['shipto_virtuemart_userinfo_id'];
			}			
			$cart->STsameAsBT=0;
			$cart->prepareCartViewData();

			$currentUser = JFactory::getUser();
			$stage = isset($post['stage']) ? $post['stage'] : '';
			if($stage == 'final' && $currentUser->id) 
      {
				$post['shipto_virtuemart_user_id'] = $currentUser->id;
				$post['address_type'] = 'ST';
				$user = VmModel::getModel('user');
				$savereturn = $user->storeAddress($post);	
				if(!$savereturn) 
        {
					$error = 1;
				}
			}

			$ret = array();
			foreach($cart->STaddress['fields'] as $item)
			{
				$ret[$item['name']] = $this->escape($item['value']);
			} 
						
			$messages=array();
			foreach($mainframe->getMessageQueue() as $message) 
			{
				$messages[]=$message["message"];
			}
			
			$messages = implode('<br/>', $messages);
			$return = array('error' => $error, 'info' => $ret, 'msg' => $messages);
			$this->jsonReturn($return);
		}	
		else if($checkoutTask == 'selectstaddress') 
    {
			$virtuemart_userinfo_id = $post['virtuemart_userinfo_id'];
			$selectedStateID = '';
			if(!is_array($cart->ST)) 
      {
				$cart->ST = array();
			}
			if($this->getUserSTaddress($virtuemart_userinfo_id) != false && $virtuemart_userinfo_id) 
      {					
				foreach($this->getUserSTaddress($virtuemart_userinfo_id) as $field=>$value) 
        {
					$cart->ST[$field] = $value;
					if($field == 'virtuemart_state_id') 
          {
						$selectedStateID = $value;
					}
				}									
			}					
			else 
      {
				$cart->ST = array();
				$cart->ST['virtuemart_userinfo_id'] = 0;
			}
			$cart->setCartIntoSession();
			$cart->prepareCartViewData();
			$cart->prepareAddressRadioSelection();
			$cart->prepareAddressDataInCart("ST",false);
			$EditSTaddress = $cart->STaddress;
			$this->assignRef('EditSTaddress', $EditSTaddress);			
			$this->assignRef('params', $params);
			$selectSTName = $this->getUserSTList();			
			$this->assignRef('selectSTName', $selectSTName);		
			$editST = $this->renderPlgLayout('default_staddress');	
			$return = array('editst'=>$editST, 'stateid'=>$selectedStateID);
			$this->jsonReturn($return);								
		}
		else if($checkoutTask == "btasst") 
    {
			$cart->STsameAsBT=1;
			$cart->ST=0;
			$cart->setCartIntoSession();
			$mainframe->setUserState('proopc.btasst', 1);	
			$mainframe->close();
		}	
		else if($checkoutTask == "btnotasst") 
    {
			$cart->STsameAsBT=0;
			$post['address_type_name']='ST';
			$cart->setCartIntoSession();
			$mainframe->setUserState('proopc.btasst', 0);			
			$mainframe->close();		
		}	
		else if($checkoutTask == "test") 
    {
			$cart->prepareCartViewData();
			$cart->setCartIntoSession();
			$this->jsonReturn($cart);	
		}				
		else if($checkoutTask == "register") 
    {			
			$userModel = VmModel::getModel('user');
			$user_params = JComponentHelper::getParams('com_users');
			$captcha = $user_params->get('captcha', 0);
			$captchaEnabled =  FALSE;
			if($captcha === 'recaptcha' && $params->get('enable_recaptcha', 0)) 
      {
				JPluginHelper::importPlugin('captcha');
				$dispatcher = JDispatcher::getInstance();
				$res = $dispatcher->trigger('onCheckAnswer',$post['recaptcha_response_field']);
				if(!$res[0])
        {
					$messages[] = $dispatcher->getError();
					$return = array("error"=> 1, "info" => $ret, "msg" => $messages);
					$this->jsonReturn($return);		
				}			
			}	
			$data = JRequest::get('post');
			$ret = $this->registerJUser($data);
			
			$savedUser = $ret['user'];	
			// If user activation is turned on, we need to set the activation information
			$useractivation = $user_params->get( 'useractivation' );
			$doUserActivation = false;
			if (JVM_VERSION===1)
      {
				if ($useractivation == '1' ) 
        {
					$doUserActivation=true;
				}
			} 
      else 
      {
				if ($useractivation == '1' or $useractivation == '2') 
        {
					$doUserActivation=true;
				}
			}

			$messages = array();			
			if($ret == false) 
      {
				foreach($mainframe->getMessageQueue() as $message) 
        {
					$messages[]=$message["message"];
				} 
				$return = array("error"=> 1, "info" => $ret, "msg" => $messages);
				$this->jsonReturn($return);				
			}

			$this->sendRegistrationEmail($savedUser,$savedUser->password_clear, $doUserActivation);
			if ($doUserActivation ) 
      {
				$messages[] = JText::_('COM_VIRTUEMART_REG_COMPLETE_ACTIVATE');
			} 
      else 
      {
				$messages[] = JText::_('COM_VIRTUEMART_REG_COMPLETE');
			}      
			$currentUser = JFactory::getUser();
			$stop = 1;     
			if($currentUser->id > 0) 
      {
				$stop = 0;
			}
			
			if(isset($cart->tosAccepted)) 
      {
				if($cart->tosAccepted) 
        {
					$ret['data']['agreed']=1;
				} 
        else 
        {
					$ret['data']['agreed']=0;
				}	
			}			
			$cart->saveAddressInCart($ret['data'],'BT');
			if($cart->STsameAsBT) 
      {
				$cart->ST = 0;
			}
			$cart->prepareCartViewData();
						
			$return = array("error"=> 0, "info" => $ret, "msg" => $messages, "stop"=>$stop);
			$this->jsonReturn($return);
			
		} 	
		else if($checkoutTask == 'login')
    {		
			if ($return = JRequest::getVar('return', '', 'method', 'base64')) 
      {
				$return = base64_decode($return);
				if (!JURI::isInternal($return)) 
        {
					$return = '';
				}
			}		
			$options = array();				
			$options['remember'] = JRequest::getBool('remember', false);				
			$options['return'] = $return;		
			$credentials = array();				
			$credentials['username'] = JRequest::getVar('username', '', 'method', 'username');				
			$credentials['password'] = JRequest::getString('passwd', '', 'post', JREQUEST_ALLOWRAW);				
			//preform the login action
			$error = $mainframe->login($credentials, $options);
			$user = JFactory::getUser();
			if($user->id > 0) 
      {
				if (JPluginHelper::isEnabled('system', 'bonus')) 
        {
					VmbonusHelperFrontBonus::ParseCart();
				}		
			}		
			self::ajaxResponse($error); // ajaxResponse static function is added at the bottom
		}
		else if($checkoutTask == "setshipments") 
    {
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$this->lSelectShipment();
			$cart->setShipment(JRequest::getInt('virtuemart_shipmentmethod_id'));
			$dispatcher = JDispatcher::getInstance();	

			$temp_st_zip=false;
			$temp_bt_zip=false;
			if($cart->STsameAsBT==0 && empty($cart->ST["zip"])) 
      {
        $cart->ST["zip"]=1;
        $temp_st_zip=true;
			}
			if($cart->STsameAsBT==1 && empty($this->cart->BT["zip"])) 
      {
        $this->cart->BT["zip"]=1;
        $temp_bt_zip=true;
			}
			$rets = $dispatcher->trigger('plgVmOnSelectCheckShipment',array(&$cart));
			if($temp_st_zip) 
      {
			    $this->cart->ST["zip"]='';
			}
			if($temp_bt_zip) 
      {
			    $this->cart->BT["zip"]='';
			}
			foreach($rets as $ret) 
      {
				if($ret === false) {
					$msgs = JFactory::getApplication()->getMessageQueue();
					$messages = array();
					foreach($msgs as $msg) 
          {
						$messages[] = str_replace("<br/>","\n",$msg["message"]);
					}
					$messages = implode($messages, '<br/>');		
					$return = array('error'=>1, 'msg'=>$messages);
					$this->jsonReturn($return);	
				}
			}
			$cart->setCartIntoSession();
			//$this->lSelectShipment();
			$return = array('error'=>0, 'msg'=>'');
			$this->jsonReturn($return);				
		}	
		else if($checkoutTask == "setpayment") 
    {
			$mainframe->setUserState('virtuemart.paypal.express.url', false);
			if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
			JPluginHelper::importPlugin('vmpayment');
			$cart->setPaymentMethod(JRequest::getInt('virtuemart_paymentmethod_id'));
			$dispatcher = JDispatcher::getInstance();			
			$msg = "";
			$rets = $dispatcher->trigger('plgVmOnSelectCheckPayment',array($cart,&$msg));
			
			$messages = array();
			foreach($rets as $ret) 
      {
				if($ret===false) 
        {
					$msgs = JFactory::getApplication()->getMessageQueue();					
					foreach($msgs as $msg) 
          {
						$messages[]=str_replace("<br/>","\n",$msg["message"]);
					}					
				}
			}	
			$paymentExpresssURL = $mainframe->getUserState('virtuemart.paypal.express.url', false);
			
			$messages = implode($messages, '<br/>');
			$saveCC = JRequest::getInt('savecc',0);
			$payment_data = JRequest::getInt('payment_data',0);
			if($payment_data == 1 and $saveCC == 1) 
      {
				$cart->setCartIntoSession();	
				$cart->prepareCartViewData();
				if(empty($messages)) 
        {
					$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
				} 
        else if($paymentExpresssURL) 
        {
					$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);					
				} 
        else 
        {
					$return = array('error'=>1, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
				}				
			}
			else if($payment_data == 1 and $saveCC != 1) 
      {
				$cart->setCartIntoSession();				
				$return = array('error'=>0, 'msg'=>$messages);
			} 
      else 
      {
				if($paymentExpresssURL) 
        {
					$cart->setCartIntoSession();
					$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
				}
				else if(empty($messages)) 
        {
					$cart->setCartIntoSession();
					$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
				} 
        else 
        {
					$return = array('error'=>1, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);	
				}				
			}
			$this->jsonReturn($return);	
		}
		else if ($checkoutTask == "setdefaultsp") 
    {
			$mainframe->setUserState('virtuemart.paypal.express.url', false);
			$shipment_error = false;
			if(JRequest::getInt('virtuemart_shipmentmethod_id')) 
      {
				if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
				JPluginHelper::importPlugin('vmshipment');
				$cart->setShipment(JRequest::getInt('virtuemart_shipmentmethod_id'));
				$dispatcher = JDispatcher::getInstance();	

				$temp_st_zip=false;
				$temp_bt_zip=false;
				if($cart->STsameAsBT==0 && empty($cart->ST["zip"])) 
        {
				    $cart->ST["zip"]=1;
				    $temp_st_zip=true;
				}
				if($cart->STsameAsBT==1 && empty($this->cart->BT["zip"])) 
        {
				    $this->cart->BT["zip"]=1;
				    $temp_bt_zip=true;
				}
				$rets = $dispatcher->trigger('plgVmOnSelectCheckShipment',array(&$cart));
				if($temp_st_zip) 
        {
				    $this->cart->ST["zip"]='';
				}
				if($temp_bt_zip) {
				    $this->cart->BT["zip"]='';
				}
				foreach($rets as $ret) 
        {
					if($ret===false) 
          {
						$msgs=JFactory::getApplication()->getMessageQueue();
						$messages=array();
						foreach($msgs as $msg) 
            {
							$messages[]=str_replace("<br/>","\n",$msg["message"]);
						}
						$messages = implode($messages, '<br/>');		
						$return = array('error'=>1, 'msg'=>$messages, 'redirect'=> false);
						$shipment_error = true;
						$this->jsonReturn($return);	
					}
				}
			}
			
			if(!$shipment_error and JRequest::getInt('virtuemart_paymentmethod_id')) 
      {
				if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
				JPluginHelper::importPlugin('vmpayment');
				$cart->setPaymentMethod(JRequest::getInt('virtuemart_paymentmethod_id'));
				$dispatcher = JDispatcher::getInstance();			
				$msg = "";
				$rets = $dispatcher->trigger('plgVmOnSelectCheckPayment',array($cart,&$msg));
				
				$paymentExpresssURL = $mainframe->getUserState('virtuemart.paypal.express.url', false);
				
				$messages = array();
				foreach($rets as $ret) 
        {
					if($ret===false) 
          {
						$msgs = JFactory::getApplication()->getMessageQueue();					
						foreach($msgs as $msg) 
            {
							$messages[]=str_replace("<br/>","\n",$msg["message"]);
						}					
					}
				}	
				$messages = implode($messages, '<br/>');
				$saveCC = JRequest::getInt('savecc',0);
				$payment_data = JRequest::getInt('payment_data',0);
				if($payment_data == 1 and $saveCC == 1) 
        {
					$cart->setCartIntoSession();	
					$cart->prepareCartViewData();
					if(empty($messages)) 
          {
						$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
					} 
          else if($paymentExpresssURL) 
          {
						$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
					}	
          else 
          {
						$return = array('error'=>1, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
					}
					$this->jsonReturn($return);				
				}
				else if($payment_data == 1 and $saveCC != 1) 
        {
					$cart->setCartIntoSession();				
					$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
				} 
				else {
					$cart->setCartIntoSession();
					if($paymentExpresssURL) 
          {
						$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
					}
					else if(empty($messages)) 
          {
						$return = array('error'=>0, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);
					} 
          else 
          {
						$return = array('error'=>1, 'msg'=>$messages, 'redirect'=>$paymentExpresssURL);	
					}				
				}
				$this->jsonReturn($return);	
			} 
      else 
      {
				$return = array('error'=>0, 'msg'=>'');
				$this->jsonReturn($return);	
			}			
		}
		else if($checkoutTask == "getcartsummery") 
    {
			$cart->prepareCartViewData();
      $this->lSelectCoupon();
			$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
			$this->assignRef('currencyDisplay',$currencyDisplay);
			$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');			
			$this->lSelectShipment();
			$this->lSelectPayment();      
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
			$cart->setCartIntoSession();
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);		
			$this->assignRef('params', $params);
			$pricelist = $this->renderPlgLayout('default_pricelist');		
			$return = array('cartsummery' => $pricelist);
			$this->jsonReturn($return);
		}									
		else if($checkoutTask == "getcartlist") 
    {
			$time_start = microtime(true);
			$cart->prepareCartViewData();
			$this->lSelectCoupon();
			$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
			$this->assignRef('currencyDisplay',$currencyDisplay);
			$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
			$cart->setCartIntoSession();
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);		
			$this->assignRef('params', $params);
			$cartlist = $this->renderPlgLayout('default_cartlist');	
			$time_end = microtime(true);
			$execution_time = ($time_end - $time_start);
			$return = array('cartlist'=>$cartlist, 'time_taken'=>'Total Execution Time: '.$execution_time.' Sec');
			$this->jsonReturn($return);
		}
		else if($checkoutTask == "getshipmentpaymentcartlist") 
    {
			$time_start = microtime(true);
			$cart->prepareCartViewData();					
			
			$this->lSelectCoupon();
			$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
			$this->assignRef('currencyDisplay',$currencyDisplay);
			$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();	
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);			
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);		
			$this->assignRef('params', $params);			
			$cartlist_only = $this->renderPlgLayout('default_cartlist');	
			
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$this->lSelectShipment();	
			$shipments = $this->renderPlgLayout('default_shipment');	
      $dom = new domDocument;
		  $dom->loadHTML($shipments);
      $shipment_scripts = $dom->getElementsByTagName('script');
      $shipment_inline_scripts = array();
      foreach ($shipment_scripts as $shipment_script) 
      {			  
        if(!$shipment_script->getAttribute('src')) 
        {
          $shipment_inline_scripts[] = trim($shipment_script->textContent);          
        } 
      } 
													
			$this->lSelectPayment();	
			$payments = $this->renderPlgLayout('default_payment');
      $dom = new domDocument;
		  $dom->loadHTML($payments);
      $scripts = $dom->getElementsByTagName('script');
      $scriptURI = array();
      $_scripts = array();
      foreach ($scripts as $script) 
      {			  
        if(!$script->getAttribute('src')) 
        {
          $tempScripts = str_replace('//-->','', str_replace('<!--', '', $script->textContent));
          if(strpos($tempScripts, 'jQuery(function ()') !== false) 
          {
            $tempScripts = str_replace('jQuery(function () {', '', $this->str_lreplace('});', '',$tempScripts));
          } 
          $_scripts[] = trim($tempScripts);
        } 
        else 
        {
          $scriptURI[] = $script->getAttribute('src');
        }
      } 		

			$cart->setCartIntoSession();
			
			$time_end = microtime(true);
			$execution_time = ($time_end - $time_start);
			
			$return = array('shipments'=>$shipments, 'shipment_scripts'=>$shipment_inline_scripts, 'payments'=>$payments, 'payment_script'=>$scriptURI, 'payment_scripts'=>$_scripts, 'cartlist'=>$cartlist_only, 'time_taken'=>'<b>Total Execution Time:</b> '.$execution_time.' Sec');
			$this->jsonReturn($return);
		}	
		else if($checkoutTask == "getpaymentlist") 
    {
			$time_start = microtime(true);
			$cart->prepareCartViewData();					
			
			$this->lSelectCoupon();
			$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
			$this->assignRef('currencyDisplay',$currencyDisplay);
			$totalInPaymentCurrency = $this->getTotalInPaymentCurrency();	
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);			
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);		
			$this->assignRef('params', $params);			
								
			$this->lSelectPayment();	
			$payments = $this->renderPlgLayout('default_payment');
      $dom = new domDocument;
		  $dom->loadHTML($payments);
      $scripts = $dom->getElementsByTagName('script');
      $scriptURI = array();
      $_scripts = array();
      foreach ($scripts as $script) 
      {			  
        if(!$script->getAttribute('src')) 
        {
          $tempScripts = str_replace('//-->','', str_replace('<!--', '', $script->textContent));
          if(strpos($tempScripts, 'jQuery(function ()') !== false) 
          {
            $tempScripts = str_replace('jQuery(function () {', '', $this->str_lreplace('});', '',$tempScripts));
          } 
          $_scripts[] = trim($tempScripts);
        } 
        else 
        {
          $scriptURI[] = $script->getAttribute('src');
        }
      } 		

			$cart->setCartIntoSession();
			
			$time_end = microtime(true);
			$execution_time = ($time_end - $time_start);
			
			$return = array('payments'=>$payments, 'payment_script'=>$scriptURI, 'payment_scripts'=>$_scripts, 'time_taken'=>'<b>Total Execution Time:</b> '.$execution_time.' Sec');
			$this->jsonReturn($return);
		}	    
		else if($checkoutTask == "deleteproduct") 
    {
			$cart->removeProductCart(JRequest::getString('id'));
			if (JPluginHelper::isEnabled('system', 'bonus')) 
      {
				VmbonusHelperFrontBonus::ParseCart();
			}
			$cart->prepareCartViewData();
			$totalProduct = 0;
			if(!empty($cart->products)) 
      {
				foreach($cart->products as $product) 
        {
					$totalProduct = $totalProduct + $product->quantity;
				}
			}			
			$return = array('pqty'=>$totalProduct);
			$this->jsonReturn($return);	
		}	
		else if($checkoutTask == "updateproduct") 
    {
			VMConfig::loadConfig(true,false);
			$cart->updateProductCart(JRequest::getString('id'));
			if (JPluginHelper::isEnabled('system', 'bonus')) 
      {				
				VmbonusHelperFrontBonus::ParseCart();
			}			
			$cart->prepareCartViewData();
			$totalProduct = 0;
			if(!empty($cart->products)) 
      {
				foreach($cart->products as $product) 
        {
					$totalProduct = $totalProduct + $product->quantity;
				}
			}				
			if(($err = $cart->getError())!=null) 
      {
				$return = array('error'=>1,'msg'=>$err);
			} 
      else 
      {
				$return = array('error'=>0,'msg'=>'', 'pqty'=>$totalProduct);
			}
			$this->jsonReturn($return);			
		}			
		else if($checkoutTask == "setcoupon") 
    {	
			$msg = $cart->setCouponCode(JRequest::getString('coupon_code'));
			if (JPluginHelper::isEnabled('system', 'bonus')) 
      {
				VmbonusHelperFrontBonus::ParseCart();
			}			
			$cart->getCartPrices();
			$systemMsgs = JFactory::getApplication()->getMessageQueue();			
			if(!empty($systemMsgs)) 
      {				
				$msg = array();
				foreach($systemMsgs as $systemMsg) 
        {
					$msg[] = str_replace("<br/>", "", $systemMsg["message"]);
				}
				$return = array('error'=>1,'msg'=>implode('<br/>', $msg));
				$this->jsonReturn($return);					
			}
			$lang = JFactory::getLanguage();
			$lang->load('com_virtuemart');			
			if(strlen($msg)) 
      {
				if($msg == 'COM_VIRTUEMART_CART_COUPON_VALID' || JText::_($msg) == JText::_('COM_VIRTUEMART_CART_COUPON_VALID')) 
        {
					$return =array('error'=>0,'msg'=>JText::_($msg));										
				} 
        else 
        {
					$return = array('error'=>1,'msg'=>JText::_($msg));
				}
				$this->jsonReturn($return);
			} 
			$return = array('error'=>1, 'msg'=>'Ooops..something went wrong. Try again.');	
			$this->jsonReturn($return);
		}
		else if($checkoutTask == "settos") 
    {	
			$cart->saveAddressInCart($post,'ST');
			$cart->prepareCartViewData();		
		}
		else if($checkoutTask == "verifycheckout") 
    {
			$cart->prepareCartViewData();					
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);
			$error = 0;				
			$verifyMsg = array();
			//Test Shipment and show shipment plugin

			if (empty($cart->virtuemart_shipmentmethod_id) and $params->get('disable_shipment', 0) !== 1) 
      {
				$error = 1;
				$verifyMsg[] = JText::_('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
			} 
      else 
      {
				if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
				JPluginHelper::importPlugin('vmshipment');
				//Add a hook here for other shipment methods, checking the data of the choosed plugin
				$dispatcher = JDispatcher::getInstance();
				$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataShipment', array($cart));
				//vmdebug('plgVmOnCheckoutCheckDataShipment CART', $retValues);
				foreach ($retValues as $retVal) 
        {
					if ($retVal === true) 
          {
						$verifyMsg[] = $retVal;
						break; // Plugin completed succesfull; nothing else to do
					} 
          elseif ($retVal === false) 
          {
					// Missing data, ask for it (again)
						$error = 1;
						$msgs = JFactory::getApplication()->getMessageQueue();
						foreach($msgs as $msg) 
            {
							$verifyMsg[]=str_replace("<br/>","\n",$msg["message"]);
						}					
						// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
					}
				}
			}
			//Test Payment and show payment plugin
			if($cart->pricesUnformatted['salesPrice']>0.0)
      {
				if (empty($cart->virtuemart_paymentmethod_id)) 
        {
					$error = 1;
					$verifyMsg[] = JText::_('COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED');
				} 
        else 
        {
					if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
					JPluginHelper::importPlugin('vmpayment');								
					//Add a hook here for other payment methods, checking the data of the choosed plugin
					$dispatcher = JDispatcher::getInstance();
					$retValues = $dispatcher->trigger('plgVmOnCheckoutCheckDataPayment', array($cart));
					foreach ($retValues as $retVal) 
          {
						if ($retVal === true) 
            {
							break; // Plugin completed succesful; nothing else to do
						} 
            elseif ($retVal === false) 
            {
							$error = 1;
							$verifyMsg[] = JText::_('COM_VIRTUEMART_CART_SETPAYMENT_PLUGIN_FAILED');
							$msgs = JFactory::getApplication()->getMessageQueue();
							foreach($msgs as $msg) 
              {
								$verifyMsg[]=str_replace("<br/>","\n",$msg["message"]);
							}							
							// 	NOTE: inactive plugins will always return null, so that value cannot be used for anything else!
						}
					}
				}
			}	
			// Check min Purchase Price
      if (!class_exists('CurrencyDisplay')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
      $vendor = VmModel::getModel('vendor');
      $vendor->setId($this->cart->vendorId);
      $store = $vendor->getVendor();
      if ($store->vendor_min_pov > 0) 
      {
				$prices = $cart->getCartPrices();
				if ($prices['salesPrice'] < $store->vendor_min_pov) 
        {
					$error = 1;
					$currency = CurrencyDisplay::getInstance();
					$verifyMsg[] = JText::sprintf('COM_VIRTUEMART_CART_MIN_PURCHASE', $currency->priceDisplay($store->vendor_min_pov));
				}
      }				
			$messages = implode($verifyMsg, '<br/>');
			$return = array("error"=> $error, "msg" => $messages);
			$this->jsonReturn($return);
		}
		/* End of ProOPC Script */		
		

		if ($layoutName == 'select_shipment') 
    {
			$cart->prepareCartViewData();
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$this->lSelectShipment();
			$pathway->addItem(JText::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));
			$pathway->addItem(JText::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
			$document->setTitle(JText::_('COM_VIRTUEMART_CART_SELECTSHIPMENT'));
		} 
		else if ($layoutName == 'select_payment') 
    {
			$cart->prepareCartViewData();
			$this->lSelectPayment();
			$pathway->addItem(JText::_('COM_VIRTUEMART_CART_OVERVIEW'), JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));
			$pathway->addItem(JText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
			$document->setTitle(JText::_('COM_VIRTUEMART_CART_SELECTPAYMENT'));
		} 
    else if ($layoutName == 'order_done') 
    {
			$this->lOrderDone();
			$pathway->addItem(JText::_('COM_VIRTUEMART_CART_THANKYOU'));
			$document->setTitle(JText::_('COM_VIRTUEMART_CART_THANKYOU'));
		} 
    else if ($layoutName == 'default') 
    {
			$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
			$params = new JParameter($plugin->params);				
			if($mainframe->getUserState('proopc.btasst', $params->get('check_shipto_address'))) 
      {
				$cart->STsameAsBT = 1;
				$cart->ST = 0;
			} 
      else 
      {
				$cart->STsameAsBT = 0;
			}
			$cart->prepareCartViewData();
			$cart->prepareAddressRadioSelection();
			$this->prepareContinueLink();
			$this->lSelectCoupon();
			$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);
			$this->assignRef('currencyDisplay',$currencyDisplay);
			$totalInPaymentCurrency =$this->getTotalInPaymentCurrency();
			$checkoutAdvertise =$this->getCheckoutAdvertise();
			
			/* Start - Address Edit */			
			$cart->prepareAddressDataInCart("BT",false);
			$EditBTaddress = $cart->BTaddress;
			unset($EditBTaddress['fields']['delimiter_userinfo']);
			unset($EditBTaddress['fields']['delimiter_billto']);
			unset($EditBTaddress['fields']['agreed']);
			$user_params = JComponentHelper::getParams('com_users');
			$captcha = $user_params->get('captcha', 0);
			$captchaEnabled =  FALSE;
			if($captcha === 'recaptcha' && $params->get('enable_recaptcha', 0)) 
      {
				JPluginHelper::importPlugin('captcha');
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onInit','dynamic_recaptcha_1');
				$EditBTaddress['fields']['captcha'] = Array
										                (
										                    'name' => 'captcha',
										                    'value' => '',
										                    'title' => 'COM_USERS_CAPTCHA_LABEL',
										                    'type' => 'captcha',
										                    'required' => 1,
										                    'hidden' => 0,
										                    'formcode' => '<div id="dynamic_recaptcha_1"></div>',
										                    'description' => 'COM_USERS_CAPTCHA_DESC' 
										                );		
				$captchaEnabled = true;
			}							
			$this->assignRef('EditBTaddress', $EditBTaddress);
			$cart->prepareAddressDataInCart("ST",false);
			$EditSTaddress=$cart->STaddress;
			$this->assignRef('EditSTaddress', $EditSTaddress);			
			$this->assignRef('params', $params);
			$selectSTName = $this->getUserSTList();			
			$this->assignRef('selectSTName', $selectSTName);
			$RegistrationFields = array('email', 'name', 'username', 'password', 'password2');
			if($captchaEnabled) 
      {
				$RegistrationFields[] = 'captcha';
			}
			$this->assignRef('RegistrationFields', $RegistrationFields);
			
			$customregfields = $params->get('custom_registration_fields', '');
			if(!empty($customregfields)) 
      {
				$customregfields = explode(',', $customregfields);
				$customregfields = array_map('trim', $customregfields);
			} 
      else 
      {
				$customregfields = array();
			}
			$this->assignRef('customregfields', $customregfields);
			
			if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
			JPluginHelper::importPlugin('vmshipment');
			$this->lSelectShipment();
			$this->lSelectPayment();
			/* End - Address Edit */	
			
			if ($cart && !VmConfig::get('use_as_catalog', 0)) 
      {
				$cart->checkout(false);
			}
			if ($cart->getDataValidated()) 
      {
				$pathway->addItem(JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$document->setTitle(JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU'));
				$text = JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU');
				$checkout_task = 'confirm';
			} 
      else 
      {
				$pathway->addItem(JText::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$document->setTitle(JText::_('COM_VIRTUEMART_CART_OVERVIEW'));
				$text = JText::_('COM_VIRTUEMART_CHECKOUT_TITLE');
				$checkout_task = 'checkout';
			}
			$this->assignRef('checkout_task', $checkout_task);
			$this->checkPaymentMethodsConfigured();
			$this->checkShipmentMethodsConfigured();
			if ($cart->virtuemart_shipmentmethod_id) 
      {
				$shippingText =  JText::_('COM_VIRTUEMART_CART_CHANGE_SHIPPING');
			} 
      else 
      {
				$shippingText = JText::_('COM_VIRTUEMART_CART_EDIT_SHIPPING');
			}
			$this->assignRef('select_shipment_text', $shippingText);

			if ($cart->virtuemart_paymentmethod_id) 
      {
				$paymentText = JText::_('COM_VIRTUEMART_CART_CHANGE_PAYMENT');
			} 
      else 
      {
				$paymentText = JText::_('COM_VIRTUEMART_CART_EDIT_PAYMENT');
			}
			$this->assignRef('select_payment_text', $paymentText);

			if (!VmConfig::get('use_as_catalog')) 
      {
				$checkout_link_html = '<a class="vm-button-correct" href="javascript:document.checkoutForm.submit();" ><span>' . $text . '</span></a>';
			} 
      else 
      {
				$checkout_link_html = '';
			}
			$this->assignRef('checkout_link_html', $checkout_link_html);
			
			//set order language
			$lang = JFactory::getLanguage();
			$order_language = $lang->getTag();
			$this->assignRef('order_language', $order_language);			
		}
		$useSSL = VmConfig::get('useSSL', 0);
		$useXHTML = true;
		$this->assignRef('useSSL', $useSSL);
		$this->assignRef('useXHTML', $useXHTML);
		$this->assignRef('totalInPaymentCurrency', $totalInPaymentCurrency);
		$this->assignRef('checkoutAdvertise', $checkoutAdvertise);
		$cart->setCartIntoSession();	
		
		$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
		$params = new JParameter($plugin->params);			
		if($params->get('hide_system_msg', 1)) 
    {		
			$system_msgs = JFactory::getApplication()->getMessageQueue();
			JFactory::getApplication()->set('_messageQueue', ''); 
			if(count($system_msgs))	{
				foreach($system_msgs as $key=>$system_msg) 
        {
					if(	$system_msg['type'] == 'info' and (
							$system_msg['message'] == JText::_('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS') or 
							$system_msg['message'] == 'Please accept the terms of service to confirm' or 
							strpos($system_msg['message'], JText::sprintf('COM_VIRTUEMART_MISSING_VALUE_FOR_FIELD', '')) !== false or 
							strpos($system_msg['message'], 'Missing value for') !== false or 
							$system_msg['message'] == JText::_('COM_VIRTUEMART_CHECKOUT_PLEASE_ENTER_ADDRESS')
							)
						) 
					{						
						unset($system_msgs[$key]);
					} else {
						JFactory::getApplication()->enqueueMessage($system_msg['message'], $system_msg['type']);
					} 
				}
			}
		}			
		shopFunctionsF::setVmTemplate($this, 0, 0, $layoutName);
		parent::display($tpl);
	}


	public function prepareContinueLink() 
  {
		// Get a continue link */
		$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
		$app = JFactory::getApplication();
		$continue_link = JURI::root(true);
		$last_visited_page = $app->getUserState('proopc.lastvisited.url', '');
		if($last_visited_page) 
    {
			$continue_link = $last_visited_page;
		}
		if ((int)$virtuemart_category_id > 0) 
    {
			$continue_link = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$virtuemart_category_id, FALSE);
		}
		$continue_link_html = '<a class="continue_link" href="' . $continue_link . '" ><span>' . JText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</span></a>';
		$this->assignRef('continue_link_html', $continue_link_html);
		$this->assignRef('continue_link', $continue_link);
	}

	private function lSelectCoupon() 
  {
		$this->couponCode = (isset($this->cart->couponCode) ? $this->cart->couponCode : '');
		$coupon_text = $this->cart->couponCode ? JText::_('COM_VIRTUEMART_COUPON_CODE_CHANGE') : JText::_('COM_VIRTUEMART_COUPON_CODE_ENTER');
		$this->assignRef('coupon_text', $coupon_text);
	}

	/*
	 * lSelectShipment
	* find al shipment rates available for this cart
	*
	* @author Valerie Isaksen
	*/

	private function lSelectShipment() 
  {		
		$plugin = JPluginHelper::getPlugin('system','vponepagecheckout');
		$params = new JParameter($plugin->params);			
		
		$found_shipment_method=false;
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);
		$this->assignRef('found_shipment_method', $found_shipment_method);

		$shipments_shipment_rates=array();
		if (!$this->checkShipmentMethodsConfigured()) 
    {
			$this->assignRef('shipments_shipment_rates',$shipments_shipment_rates);
			return;
		}
		
		$selectedShipment = (empty($this->cart->virtuemart_shipmentmethod_id) ? 0 : $this->cart->virtuemart_shipmentmethod_id);		

		$shipments_shipment_rates = array();
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		
		$temp_st_zip = false;
		$temp_st_address = false;
		$temp_bt_zip = false;
		$temp_bt_address = false;
		$cart_st_array = true;
		$cart_br_array = true;		
		if($this->cart->STsameAsBT==0 && empty($this->cart->ST["zip"])) 
    {
				if(!is_array($this->cart->ST)) 
        {
					$this->cart->ST = array();
					$cart_st_array = false;
				}
		    $this->cart->ST["zip"]=1;
		    $temp_st_zip=true;
		}
		if($this->cart->STsameAsBT==0 && empty($this->cart->ST["virtuemart_country_id"])) 
    {
				if(!is_array($this->cart->ST)) 
        {
					$this->cart->ST = array();
					$cart_st_array = false;
				}
				if(!empty($this->cart->BT["virtuemart_country_id"])) 
        {
					$this->cart->ST["virtuemart_country_id"] = isset($this->cart->BT["virtuemart_country_id"]) ? $this->cart->BT["virtuemart_country_id"] : 0;
					$this->cart->ST["virtuemart_state_id"] = isset($this->cart->BT["virtuemart_state_id"]) ? $this->cart->BT["virtuemart_state_id"] : 0;
				} 
        else 
        {
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					$query->select(array('b.virtuemart_country_id', 'b.virtuemart_state_id'));
					$query->from('`#__virtuemart_vmusers` AS a');
					$query->leftJoin('`#__virtuemart_userinfos` AS b ON a.virtuemart_user_id = b.virtuemart_user_id');
					$query->where('a.virtuemart_vendor_id	= '.$this->cart->vendor->virtuemart_vendor_id);
					$query->where('b.address_type	= '.$db->quote('BT'));
					$db->setQuery($query);
					$vendor = $db->loadObject();
					if(empty($vendor)) 
          {
						$this->cart->ST["virtuemart_country_id"] = $params->get('default_country', 1);
						$this->cart->ST["virtuemart_state_id"] = 0;						
					} 
          else 
          {
						$this->cart->ST["virtuemart_country_id"] = $vendor->virtuemart_country_id;
						$this->cart->ST["virtuemart_state_id"] = $vendor->virtuemart_state_id;						
					}
				}
		    $temp_st_address = true;
		}		
		
		if($this->cart->STsameAsBT==1 && empty($this->cart->BT["zip"])) 
    {
				if(!is_array($this->cart->BT)) 
        {
					$this->cart->BT = array();
					$cart_bt_array = false;
				}				
		    $this->cart->BT["zip"]=1;
		    $temp_bt_zip=true;
		}
		if($this->cart->STsameAsBT==1 && empty($this->cart->BT["virtuemart_country_id"])) 
    {
				if(!is_array($this->cart->BT)) 
        {
					$this->cart->BT = array();
					$cart_bt_array = false;
				}
				$db = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select(array('b.virtuemart_country_id', 'b.virtuemart_state_id'));
				$query->from('`#__virtuemart_vmusers` AS a');
				$query->leftJoin('`#__virtuemart_userinfos` AS b ON a.virtuemart_user_id = b.virtuemart_user_id');
				$query->where('a.virtuemart_vendor_id	= '.$this->cart->vendor->virtuemart_vendor_id);
				$query->where('b.address_type	= '.$db->quote('BT'));
				$db->setQuery($query);
				$vendor = $db->loadObject();
				if(empty($vendor)) 
        {
					$this->cart->BT["virtuemart_country_id"] = $params->get('default_country', 1);
					$this->cart->BT["virtuemart_state_id"] = 0;	
				} 
        else 
        {
					$this->cart->BT["virtuemart_country_id"] = $vendor->virtuemart_country_id;
					$this->cart->BT["virtuemart_state_id"] = $vendor->virtuemart_state_id;						
				}
		    $temp_bt_address = true;
		}			
		
		
		$this->cart->getCartPrices();
		
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $this->cart, $selectedShipment, &$shipments_shipment_rates));
		
		// If temp address defined
		if($temp_st_zip) {
		   $this->cart->ST["zip"]='';
		}
		if($temp_bt_zip) {
		  $this->cart->BT["zip"]='';
		}	
		if($cart_st_array = false) {
			$this->cart->ST = 0;
		}
		if($cart_bt_array = false) {
			$this->cart->BT = 0;
		}	
		
		// if no shipment rate defined
		$found_shipment_method =count($shipments_shipment_rates);
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->assignRef('shipment_not_found_text', $shipment_not_found_text);
		$this->assignRef('shipments_shipment_rates', $shipments_shipment_rates);
		$this->assignRef('found_shipment_method', $found_shipment_method);
		return;
	}

	/*
	 * lSelectPayment
	* find al payment available for this cart
	*
	* @author Valerie Isaksen
	*/

	private function lSelectPayment() 
  {
		$payment_not_found_text='';
		$payments_payment_rates=array();
		if (!$this->checkPaymentMethodsConfigured()) 
    {
			$this->assignRef('paymentplugins_payments', $payments_payment_rates);
			$this->assignRef('found_payment_method', $found_payment_method);
		}

		$selectedPayment = empty($this->cart->virtuemart_paymentmethod_id) ? 0 : $this->cart->virtuemart_paymentmethod_id;

		$paymentplugins_payments = array();
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array($this->cart, $selectedPayment, &$paymentplugins_payments));
		// if no payment defined
		$found_payment_method =count($paymentplugins_payments);

		if (!$found_payment_method) 
    {
			$link=''; // todo
			$payment_not_found_text = JText::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '<a href="'.$link.'">'.$link.'</a>');
		}

		$this->assignRef('payment_not_found_text', $payment_not_found_text);
		$this->assignRef('paymentplugins_payments', $paymentplugins_payments);
		$this->assignRef('found_payment_method', $found_payment_method);
	}

	private function getTotalInPaymentCurrency() 
  {
		if (empty($this->cart->virtuemart_paymentmethod_id)) 
    {
			return null;
		}

		if (!$this->cart->paymentCurrency or ($this->cart->paymentCurrency==$this->cart->pricesCurrency)) 
    {
			return null;
		}

		$paymentCurrency = CurrencyDisplay::getInstance($this->cart->paymentCurrency);
		$totalInPaymentCurrency = $paymentCurrency->priceDisplay( $this->cart->pricesUnformatted['billTotal'],$this->cart->paymentCurrency) ;
		$currencyDisplay = CurrencyDisplay::getInstance($this->cart->pricesCurrency);

		return $totalInPaymentCurrency;
	}
	/*
	 * Trigger to place Coupon, payment, shipment advertisement on the cart
	 */
	private function getCheckoutAdvertise() 
  {
		$checkoutAdvertise=array();
		JPluginHelper::importPlugin('vmcoupon');
		JPluginHelper::importPlugin('vmpayment');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		$returnValues = $dispatcher->trigger('plgVmOnCheckoutAdvertise', array( $this->cart, &$checkoutAdvertise));
		return $checkoutAdvertise;
  }

	private function lOrderDone() 
  {
		$html = JRequest::getVar('html', JText::_('COM_VIRTUEMART_ORDER_PROCESSED'), 'default', 'STRING', JREQUEST_ALLOWRAW);
		$this->assignRef('html', $html);
		//Show Thank you page or error due payment plugins like paypal express
	}

	private function checkPaymentMethodsConfigured() 
  {
		//For the selection of the payment method we need the total amount to pay.
		$paymentModel = VmModel::getModel('Paymentmethod');
		$payments = $paymentModel->getPayments(true, false);
		if (empty($payments)) {

			$text = '';
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) 
      {
				$uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=paymentmethod';
				$text = JText::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			}

			vmInfo('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text);

			$tmp = 0;
			$this->assignRef('found_payment_method', $tmp);

			return false;
		}
		return true;
	}

	private function checkShipmentMethodsConfigured() 
  {
		//For the selection of the shipment method we need the total amount to pay.
		$shipmentModel = VmModel::getModel('Shipmentmethod');
		$shipments = $shipmentModel->getShipments();
		if (empty($shipments)) 
    {
			$text = '';
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) 
      {
				$uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=shipmentmethod';
				$text = JText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			}

			vmInfo('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);

			$tmp = 0;
			$this->assignRef('found_shipment_method', $tmp);

			return false;
		}
		return true;
	}
  
	private function sendRegistrationEmail($user, $password, $doUserActivation)
  {
		if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
		$vars = array('user' => $user);
		// Send registration confirmation mail
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password); //Disallow control chars in the email
		$vars['password'] = $password;

		if ($doUserActivation) {
			jimport('joomla.user.helper');
			if(JVM_VERSION === 2) {
				$com_users = 'com_users';
				$activationLink = 'index.php?option='.$com_users.'&task=registration.activate&token='.$user->get('activation');
			} else {
				$com_users = 'com_user';
				$activationLink = 'index.php?option='.$com_users.'&task=activate&activation='.$user->get('activation');
			}
			$vars['activationLink'] = $activationLink;
		}
		$vars['doVendor']=true;
		// public function renderMail ($viewName, $recipient, $vars=array(),$controllerName = null)
		shopFunctionsF::renderMail('user', $user->get('email'), $vars);
    $db = JFactory::getDBO();
		//get all super administrator
		$query = 'SELECT name, email, sendEmail' .
				' FROM #__users' .
				' WHERE LOWER( usertype ) = "super administrator"';
		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		$vars['doVendor']=false;
		// get superadministrators id
		foreach ( $rows as $row )
		{
			if ($row->sendEmail)
			{
				//$message2 = sprintf ( JText::_( 'COM_VIRTUEMART_SEND_MSG_ADMIN' ), $row->name, $sitename, $name, $email, $username);
				//$message2 = html_entity_decode($message2, ENT_QUOTES);
				//JUtility::sendMail($mailfrom, $fromname, $row->email, $subject2, $message2);
				//shopFunctionsF::renderMail('user', $row->email, $vars);
			}
		}
	}  
	
	public static function ajaxResponse($message)
  {
		$obLevel = ob_get_level();
		if($obLevel)
    {
			while ($obLevel > 0 ) {
				ob_end_clean();
				$obLevel --;
			}
		}
    else
    {
			ob_clean();
		}
		echo $message;
		die;
	}	
	
	private static function jsonReturn($message=array()) 
  {
		$mainframe = JFactory::getApplication();
		ob_end_clean();
		header('Content-type: application/json');	
		header('Content-type: application/json');	
		header('Cache-Control: public,max-age=1,must-revalidate');
		header('Expires: '.gmdate('D, d M Y H:i:s',($_SERVER['REQUEST_TIME']+1)).' GMT');
		header('Last-modified: '.gmdate('D, d M Y H:i:s',$_SERVER['REQUEST_TIME']).' GMT');	
		if(function_exists('header_remove')) 
    {
			header_remove('Pragma');
		}					
		echo json_encode($message);
		flush();
		$mainframe->close();
	}	
  
  private function str_lreplace($search, $replace, $subject)
  {
      $pos = strrpos($subject, $search);
      if($pos !== false)
      {
          $subject = substr_replace($subject, $replace, $pos, strlen($search));
      }
      return $subject;
  }  
	
	// When logged in get the saved user BT addresses	
	private function getUserBTinfo($userID) 
  {	
		$db = JFactory::getDBO();
		// Get userfields
		$query = $db->getQuery(true);
		$query->select('field.name');
		$query->from('`#__virtuemart_userfields` AS field');
		$query->where('field.type NOT IN ('.$db->quote('password').', '.$db->quote('delimiter').')');
		$query->where('field.registration = 1');
		$query->where('field.name NOT IN ('.$db->quote('username').', '.$db->quote('email').', '.$db->quote('name').')');
		$query->where('field.readonly = 0');
		$query->where('field.published = 1');
		$db->setQuery($query);		
		$userFields = $db->loadResultArray();
		foreach($userFields as &$userField) 
    {
			$userField = $db->nameQuote($userField);
		}
		unset($userField);
		if(empty($userFields)) {
			return false;
		}
		$query = $db->getQuery(true);
		$query->select($userFields);
		$query->from('`#__virtuemart_userinfos`');
		$query->where('virtuemart_user_id = '.(int)$userID);
		$query->where('address_type = '.$db->Quote('BT'));
		$db->setQuery($query);
		$BTinfo = $db->loadObject();				
		if(empty($BTinfo)) 
    {
			return false;
		}
		return $BTinfo;		
	}
	
	// When logged in get the saved user ST addresses	
	private function getUserSTinfo($userID) 
  {	
		$db = JFactory::getDBO();
		// Get userfields
		$query = $db->getQuery(true);
		$query->select('field.name');
		$query->from('`#__virtuemart_userfields` AS field');
		$query->where('field.type NOT IN ('.$db->quote('password').', '.$db->quote('delimiter').')');
		$query->where('field.shipment = 1');
		$query->where('field.name NOT IN ('.$db->quote('username').', '.$db->quote('email').', '.$db->quote('name').')');
		$query->where('field.readonly = 0');	
		$query->where('field.published = 1');	
		$db->setQuery($query);		
		$userFields = $db->loadResultArray();		
		foreach($userFields as &$userField) 
    {
			$userField = $db->nameQuote($userField);
		}
		unset($userField);
		if(empty($userFields)) {
			return false;
		}
		$query = $db->getQuery(true);
		$query->select($userFields);
		$query->select('virtuemart_userinfo_id');
		$query->from('`#__virtuemart_userinfos`');
		$query->where('virtuemart_user_id = ' . (int) $userID);
		$query->where('address_type = ' . $db->quote('ST'));
		$query->order('virtuemart_userinfo_id DESC');
		$db->setQuery($query);
		$STinfo = $db->loadObject();		
		if(empty($STinfo)) 
    {
			return false;
		}
		return $STinfo;		
	}	
	
	// When logged in get the saved user ST addresses	list
	private function getUserSTList() 
  {	
		$user = JFactory::getUser();
		$options = array();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('address_type_name', 'virtuemart_userinfo_id'));
		$query->from('`#__virtuemart_userinfos`');
		$query->where('virtuemart_user_id = ' . (int) $user->id);
		$query->where('address_type = ' . $db->quote('ST'));
		$db->setQuery($query);
		$STnames = $db->loadObjectList();		
		if(empty($STnames)) 
    {
			return false;
		}
		if (!class_exists('VirtueMartCart')) require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart();
		if(isset($cart->ST['virtuemart_userinfo_id'])) 
    {
			$selectedAddress = $cart->ST['virtuemart_userinfo_id'];
		}	
    else 
    {
			$selectedAddress = 0;
		}			
		$options[] = JHtml::_('select.option', (int) 0,	JText::_('JNEW'));
		foreach ($STnames as $STname)
		{			
			$options[] = JHtml::_('select.option', (int) $STname->virtuemart_userinfo_id,	$STname->address_type_name);
		}		
		$html = JHtml::_('select.genericlist', $options, 'proopc-select-st', 'onchange="return ProOPC.selectSTAddress(this);" class="vm-chzn-select"', 'value', 'text', (int) $selectedAddress, 'proopc-select-st');
		return $html;		
	}	
	
	// When logged in get the saved user ST addresses	
	private function getUserSTaddress($virtuemart_userinfo_id = 0) 
  {		
		$db = JFactory::getDBO();
		// Get userfields
		$query = $db->getQuery(true);
		$query->select('field.name');
		$query->from('`#__virtuemart_userfields` AS field');
		$query->where('field.type NOT IN ('.$db->quote('password').', '.$db->quote('delimiter').')');
		$query->where('field.shipment = 1');
		$query->where('field.name NOT IN ('.$db->quote('username').', '.$db->quote('email').', '.$db->quote('name').')');
		$query->where('field.readonly = 0');
		$query->where('field.published = 1');
		$db->setQuery($query);
		$userFields = $db->loadResultArray();
		if(empty($userFields)) 
    {
			return false;
		}		
		foreach($userFields as &$userField) 
    {
			$userField = $db->nameQuote($userField);
		}
		unset($userField);

		$query = $db->getQuery(true);
		$query->select($userFields);
		$query->select('virtuemart_userinfo_id');
		$query->from('`#__virtuemart_userinfos`');
		$query->where('virtuemart_userinfo_id = '. (int) $virtuemart_userinfo_id);
		$db->setQuery($query);
		$STinfo = $db->loadObject();		
		if(empty($STinfo)) 
    {
			return false;
		}
		return $STinfo;		
	}
	
	private function registerJUser($data, $checkToken = true)
	{
		$message = '';
		$user = '';
		$newId = 0;
		
		$mainframe = JFactory::getApplication() ;

		if($checkToken)
    {
			JRequest::checkToken() or jexit( 'Invalid Token, while trying to save user' );			
		}

		if(empty($data))
    {
			$mainframe->enqueueMessage('Developer notice, no data to store for user', 'error');
			return false;
		}

		//To find out, if we have to register a new user, we take a look on the id of the usermodel object.
		//The constructor sets automatically the right id.
		$new = true;

		$user = JFactory::getUser();
		
		if($user->id > 0)
		{
			$new = false;
			$mainframe->enqueueMessage('Already Logged In', 'error');
			return false;
		}

		$gid = $user->get('gid'); // Save original gid

		// Preformat and control user datas by plugin
		JPluginHelper::importPlugin('vmuserfield');
		$dispatcher = JDispatcher::getInstance();

		$valid = true ;
		$dispatcher->trigger('plgVmOnBeforeUserfieldDataSave',array(&$valid,$user->id,&$data,$user ));
		// $valid must be false if plugin detect an error
		if( $valid == false ) 
    {
			return false;
		}

		if(empty ($data['email']))
    {
			$email = $user->get('email');
			if(!empty($email))
      {
				$data['email'] = $email;
			}
		} 
    else 
    {
			$data['email'] =  JRequest::getString('email', '', 'post', 'email');
		}
		$data['email'] = str_replace(array('\'','"',',','%','*','/','\\','?','^','`','{','}','|','~'),array(''),$data['email']);

		$user->set('email',$data['email']);

		if(empty ($data['name']))
    {
			$name = $user->get('name');
			if(!empty($name))
      {
				$data['name'] = $name;
			}
		} 
    else 
    {
			$data['name'] = JRequest::getString('name', '', 'post', 'name');
		}
		$data['name'] = str_replace(array('\'','"',',','%','*','/','\\','?','^','`','{','}','|','~'),array(''),$data['name']);

		if(empty ($data['username']))
    {
			$username = $user->get('username');
			if(!empty($username))
      {
				$data['username'] = $username;
			} 
      else 
      {
				$data['username'] = JRequest::getVar('username', '', 'post', 'username');
			}
		}

		if(empty ($data['password']))
    {
			$data['password'] = JRequest::getVar('password', '', 'post', 'string' ,JREQUEST_ALLOWRAW);
		}

		if(empty ($data['password2']))
    {
			$data['password2'] = JRequest::getVar('password2', '', 'post', 'string' ,JREQUEST_ALLOWRAW);
		}

		if(!$new && !empty($data['password']) && empty($data['password2']))
    {
			unset($data['password']);
			unset($data['password2']);
		}

		// Bind Joomla userdata
		if (!$user->bind($data)) 
    {
			foreach($user->getErrors() as $error) 
      {
				$mainframe->enqueueMessage(JText::sprintf('COM_VIRTUEMART_USER_STORE_ERROR', $error), 'error');
			}
			$message = 'Couldnt bind data to joomla user';
			return array('user'=>$user,'password'=>$data['password'],'message'=>$message,'newId'=>$newId,'success'=>false);
		}

		if($new)
    {
			// If user registration is not allowed, show 403 not authorized.
			// But it is possible for admins and storeadmins to save
			$usersConfig = JComponentHelper::getParams( 'com_users' );
			if(!class_exists('Permissions')) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'permissions.php');

			if (!Permissions::getInstance()->check("admin,storeadmin") && $usersConfig->get('allowUserRegistration') == '0') 
      {
				VmConfig::loadJLang('com_virtuemart');
				JError::raiseError( 403, JText::_('COM_VIRTUEMART_ACCESS_FORBIDDEN'));
				return;
			}
			$authorize	= JFactory::getACL();

			// Initialize new usertype setting
			$newUsertype = $usersConfig->get( 'new_usertype' );
			if (!$newUsertype) 
      {
				if ( JVM_VERSION===1)
        {
					$newUsertype = 'Registered';

				} 
        else 
        {
					$newUsertype=2;
				}
			}
			// Set some initial user values
			$user->set('usertype', $newUsertype);

			if ( JVM_VERSION===1)
      {
				$user->set('gid', $authorize->get_group_id( '', $newUsertype, 'ARO' ));
			} 
      else 
      {
				$user->groups[] = $newUsertype;
			}

			$date = JFactory::getDate();
			$user->set('registerDate', $date->toMySQL());

			// If user activation is turned on, we need to set the activation information
			$useractivation = $usersConfig->get( 'useractivation' );
			$doUserActivation=false;
			if ( JVM_VERSION===1)
      {
				if ($useractivation == '1' ) 
        {
					$doUserActivation=true;
				}
			} 
      else 
      {
				if ($useractivation == '1' or $useractivation == '2') {
					$doUserActivation=true;
				}
			}
			vmdebug('user',$useractivation , $doUserActivation);
			if ($doUserActivation )
			{
				jimport('joomla.user.helper');
				$user->set('activation', JUtility::getHash( JUserHelper::genRandomPassword()) );
				$user->set('block', '1');
			}
		}

		$option = JRequest::getCmd( 'option');
		// If an exising superadmin gets a new group, make sure enough admins are left...
		if (!$new && $user->get('gid') != $gid && $gid == __SUPER_ADMIN_GID) 
    {
			if ($this->getSuperAdminCount() <= 1) 
      {
				$mainframe->enqueueMessage(JText::_('COM_VIRTUEMART_USER_ERR_ONLYSUPERADMIN'));
				return false;
			}
		}

		// Save the JUser object
		if (!$user->save()) 
    {
			$mainframe->enqueueMessage(JText::_( $user->getError()), 'error');
			return false;
		}

		$newId = $user->get('id');
		$data['virtuemart_user_id'] = $newId;	//We need this in that case, because data is bound to table later
		
		return array('user'=>$user,'password'=>$data['password'],'message'=>$message,'newId'=>$newId,'success'=>true, 'data'=>$data);
	}

	private function getSuperAdminCount()
	{
		$db = JFactory::getDBO();
		$db->setQuery('SELECT COUNT(id) FROM #__users'
		. ' WHERE gid = ' . __SUPER_ADMIN_GID . ' AND block = 0');
		return ($db->loadResult());
	}	
  
  protected function renderPlgLayout($layoutName)
  {
    $path = JPath::clean(dirname(__FILE__) . '/tmpl/' . $layoutName . '.php');
    
    if($templatePath = $this->getTemplatePath())
    {
      $layoutPath = JPath::clean($templatePath . DS . $layoutName . '.php');
      
      if(is_file($layoutPath))
      {
        $path = $layoutPath;
      }
    }
    
		ob_start();
		require_once($path);
		$layout = ob_get_contents();
		ob_end_clean();	
        
    return $layout;
  }
  
  protected function getTemplatePath()
  {
    $app = JFactory::getApplication();
    $template = $app->getTemplate(true);
    $templatePath = JPath::clean(JPATH_ROOT . '/templates/' . $template->template . '/html/plg_vponepagecheckout');
    
    if(!is_dir($templatePath))
    {
      return false;
    }
    
    return $templatePath;    
  }
  
  protected function loadPlgScripts()
  {
    static $loaded = false;
    
    if(!$loaded)
    {
      $app = JFactory::getApplication();
      $template = $app->getTemplate(true);
      $document = JFactory::getDocument();
            
      // Load language files
      $language = JFactory::getLanguage();
      $language_tag = $language->getTag(); // loads the current language-tag
      JFactory::getLanguage()->load('lib_joomla', JPATH_SITE, $language_tag, true);
      JFactory::getLanguage()->load('com_users', JPATH_SITE, $language_tag, true);
      JFactory::getLanguage()->load('plg_vponepagecheckout', JPATH_ADMINISTRATOR, $language_tag, true);
      JFactory::getLanguage()->load('com_virtuemart_shoppers', JPATH_SITE, $language_tag, true);
      JFactory::getLanguage()->load('plg_vponepagecheckout_override', JPATH_SITE, $language_tag, true);      

      $db = JFactory::getDBO();
      $query = $db->getQuery(true);
      $query->select('COUNT(virtuemart_paymentmethod_id)');
      $query->from('#__virtuemart_paymentmethods');
      $query->where('published = 1');
      $query->where('payment_element = ' . $db->quote('klarna'));
      $db->setQuery($query);
      $KlarnaCount = $db->loadResult();

      // jQuery and Bootstrap Checking
      $bootstrapLoaded = false;
      foreach($document->_scripts as $key=>$jscript) 
      {
      	if(strpos($key, 'com_virtuemart/assets/js/jquery.min.js') || strpos($key, 'com_virtuemart/assets/js/jquery.noConflict.js') || strpos($key, 'jquery/1.6.4/jquery.min.js') || strpos($key, 'jquery/1.8.1/jquery.min.js')) 
      	{
      		unset($document->_scripts[$key]);
      	}
      	if(strpos($key, 'bootstrap.min.js')) 
      	{
      		$bootstrapLoaded = true;
      	}
      }

      // Load Klarna CSS and JS if payment method exists
      if($KlarnaCount> 0) 
      {
      	$klarnaAssetsPath = JURI::root(true).'/plugins/vmpayment/klarna/klarna/assets/';
      	$document->addStyleSheet($klarnaAssetsPath . 'css/style.css');
      	$document->addStyleSheet($klarnaAssetsPath . 'css/klarna.css');
      	$document->addScript(JURI::root().'plugins/vmpayment/klarna/klarna/assets/js/klarna_pp.js');
      	$document->addScript('https://static.klarna.com:444/external/js/klarnapart.js');
      	$document->addScript($klarnaAssetsPath . 'js/klarna_general.js');
      	$document->addScript('http://static.klarna.com/external/js/klarnaConsentNew.js');
      }

      if($this->params->get('load_jquery_plugins', 2) == 2 and strpos($template->template, 'vp_') === false) 
      {
      	$document->addScript($this->getStaticFiles('jquery.hoverIntent.minified.js', $type = 'js'));
      	$document->addScript($this->getStaticFiles('jquery.easing.1.3.min.js', $type = 'js'));
      }
      elseif($this->params->get('load_jquery_plugins', 2) == 1) 
      {
      	$document->addScript($this->getStaticFiles('jquery.hoverIntent.minified.js', $type = 'js'));
      	$document->addScript($this->getStaticFiles('jquery.easing.1.3.min.js', $type = 'js'));
      }
      
      if($this->params->get('load_boot_modal', 2) == 2 && strpos($template->template, 'vp_') === false && !$bootstrapLoaded && !$this->params->get('tos_fancybox', 0)) 
      {
        $document->addScript($this->getStaticFiles('bootstrap.min.js', $type = 'js'));
      }
      elseif($this->params->get('load_boot_modal', 2) == 1 && !$this->params->get('tos_fancybox', 0)) 
      {
        $document->addScript($this->getStaticFiles('bootstrap.min.js', $type = 'js'));
      }
      
      if($this->params->get('tos_fancybox', 1))
      {
      	$vmFancyJS = "/components/com_virtuemart/assets/js/fancybox/jquery.fancybox-1.3.4.pack.js";
      	$vmFancyCSS = "/components/com_virtuemart/assets/css/jquery.fancybox-1.3.4.css";
      	
      	if(is_file(JPATH_SITE.str_replace('/', DS, $vmFancyJS)) && is_file(JPATH_SITE.str_replace('/', DS, $vmFancyCSS)))
      	{
      		$document->addScript(JURI::root(true).$vmFancyJS);
      		$document->addStyleSheet(JURI::root(true).$vmFancyCSS);
      	}
      	else
      	{
          $document->addScript($this->getStaticFiles('jquery.fancybox-1.3.4.pack.js', $type = 'js'));
      		$document->addStyleSheet($this->getStaticFiles('jquery.fancybox-1.3.4.css', $type = 'css'));		
      	}
      }
      
      $document->addScript($this->getStaticFiles('spin.min.js', $type = 'js'));
      $document->addScript($this->getStaticFiles('plugin-min.js', $type = 'js', '2.3a'));
      if($this->params->get('color', 1) == 1) 
      {
      	$SPINNER_COLOR = '#000';
      	$document->addStyleSheet($this->getStaticFiles('light-checkout.css', $type = 'css', '2.3'));
      }
      if($this->params->get('color', 1) == 2) 
      {
      	$SPINNER_COLOR = '#FFF';
      	$document->addStyleSheet($this->getStaticFiles('dark-checkout.css', $type = 'css', '2.3'));
      }
      if($this->params->get('responsive', 1)) 
      {	
      	$document->addStyleSheet($this->getStaticFiles('responsive-procheckout.css', $type = 'css', '2.3'));
      }
      
      $CheckoutURI = JURI::root(true)."/index.php?option=com_virtuemart&view=cart";
      $ASSETPATH = JURI::root(true)."/plugins/system/vponepagecheckout/assets/";
      $userFieldsModel = VmModel::getModel ('userfields');
      $VMCONFIGTOS = ($userFieldsModel->getIfRequired ('agreed') && VmConfig::get ('oncheckout_show_legal_info', 1)) || VmConfig::get('agree_to_tos_onorder') ? 1 : 0;
      
      if($this->params->get('check_shipto_address', 1)) 
      {
      	$BTASST = TRUE;
      } 
      else 
      {
      	$BTASST = FALSE;
      }
      if($this->params->get('field_grouping', 1)) 
      {
      	$GROUPING = TRUE;
      } 
      else 
      {
      	$GROUPING = FALSE;
      }
      
      $AUTOSHIPMENT = (int) VmConfig::get('automatic_shipment');
      $AUTOPAYMENT = (int) VmConfig::get('automatic_payment');
      $AJAXVALIDATION = (int) $this->params->get('ajax_validation', 0);
      $RELOAD = $this->params->get('reload', 0);
      $TOSFANCY = (int) $this->params->get('tos_fancybox', 1);
      $EDITPAYMENTURI = JRoute::_('index.php?view=cart&task=editpayment',$this->useXHTML,$this->useSSL);
      $STYLERADIOCHEBOX = (int) $this->params->get('style_radio_checkbox', 1);
      $REMOVEUNNECESSARYLINKS = (int) $this->params->get('remove_unnecessary_links', 1);
      $RELOADPAYMENTS = (int) $this->params->get('reload_payment_on_shipment_selection', 0);
      $RELOADALLFORCOUPON = (int) $this->params->get('reload_all_on_apply_coupon', 0);
      
      $document->addScriptDeclaration("
      //<![CDATA[ 
      window.URI = '$CheckoutURI';
      window.ASSETPATH = '$ASSETPATH';
      window.RELOAD = $RELOAD;
      window.BTASST = '$BTASST';
      window.GROUPING = '$GROUPING';
      window.VMCONFIGTOS = $VMCONFIGTOS,
      window.SPINNER_COLOR = '$SPINNER_COLOR';
      window.AUTOSHIPMENT = $AUTOSHIPMENT;
      window.AUTOPAYMENT = $AUTOPAYMENT;
      window.AJAXVALIDATION = $AJAXVALIDATION;
      window.EDITPAYMENTURI = '$EDITPAYMENTURI';	
      window.TOSFANCY = $TOSFANCY;
      window.STYLERADIOCHEBOX = $STYLERADIOCHEBOX;
      window.REMOVEUNNECESSARYLINKS = $REMOVEUNNECESSARYLINKS;
      window.RELOADPAYMENTS = $RELOADPAYMENTS;
      window.RELOADALLFORCOUPON = $RELOADALLFORCOUPON;
      //]]>
      ");
      
      JText::script('JLIB_LOGIN_AUTHENTICATE');
      JText::script('COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS');
      JText::script('COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS');
      JText::script('COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED');
      JText::script('COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED'); 
      JText::script('PLG_VPONEPAGECHECKOUT_REQUIRED_FIELD'); 
      JText::script('PLG_VPONEPAGECHECKOUT_REQUIRED_FIELDS_MISSING'); 
      JText::script('PLG_VPONEPAGECHECKOUT_WEAK'); 
      JText::script('PLG_VPONEPAGECHECKOUT_TOO_SHORT');
      JText::script('PLG_VPONEPAGECHECKOUT_GOOD'); 
      JText::script('PLG_VPONEPAGECHECKOUT_STRONG'); 
      JText::script('PLG_VPONEPAGECHECKOUT_INVALID');
      JText::script('PLG_VPONEPAGECHECKOUT_VALIDATED');
      JText::script('PLG_VPONEPAGECHECKOUT_EMAIL_INVALID');
      JText::script('PLG_VPONEPAGECHECKOUT_EMAIL_ALREADY_REGISTERED');
      JText::script('PLG_VPONEPAGECHECKOUT_USERNAME_INVALID');
      JText::script('PLG_VPONEPAGECHECKOUT_USERNAME_ALREADY_REGISTERED');
      JText::script('PLG_VPONEPAGECHECKOUT_REGISTRATION_COMPLETED');
      JText::script('PLG_VPONEPAGECHECKOUT_EMAIL_SAVED');
      JText::script('PLG_VPONEPAGECHECKOUT_LOGIN_COMPLETED');
      JText::script('PLG_VPONEPAGECHECKOUT_SAVING_BILLING_ADDRESS');
      JText::script('PLG_VPONEPAGECHECKOUT_BILLING_ADDRESS_SAVED');
      JText::script('PLG_VPONEPAGECHECKOUT_SAVING_SHIPPING_ADDRESS');
      JText::script('PLG_VPONEPAGECHECKOUT_SHIPPING_ADDRESS_SAVED');
      JText::script('PLG_VPONEPAGECHECKOUT_SAVING_CREDIT_CARD');
      JText::script('PLG_VPONEPAGECHECKOUT_CREDIT_CARD_SAVED');
      JText::script('PLG_VPONEPAGECHECKOUT_VERIFYING_ORDER');
      JText::script('PLG_VPONEPAGECHECKOUT_PLACING_ORDER');
      JText::script('PLG_VPONEPAGECHECKOUT_PLEASE_WAIT');
      JText::script('PLG_VPONEPAGECHECKOUT_COUPON_EMPTY');
      JText::script('VMPAYMENT_PAYPAL_REDIRECT_MESSAGE'); 
      
      // Set scripts are loaded
      $loaded = true;    
    }    
  }
  
  protected function getStaticFiles($fileName, $type = 'css', $ver = null)
  {
      $app = JFactory::getApplication();
      $template = $app->getTemplate(true);
          
      $corePath = '/plugins/system/vponepagecheckout/assets/' . $type . '/';
      $templatePath = '/templates/' . $template->template . '/' . $type . '/plg_vponepagecheckout/';

      if(is_file(JPath::clean(JPATH_ROOT . $templatePath . $fileName)))
      {
        $return = JUri::root(true) . $templatePath . $fileName;
      }
      else
      {
        $return = JUri::root(true) . $corePath . $fileName;
      }
      
      if(!empty($ver))
      {
        $return .= '?ver=' . trim($ver);
      }
      
      return $return;
  }
	
} 