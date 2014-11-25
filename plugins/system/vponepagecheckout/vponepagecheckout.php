<?php
/*------------------------------------------------------------------------------------------------------------
# VP One Page Checkout! Joomla 2.5 Plugin for VirtueMart 2.0
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 - 2014 VirtuePlanet Services LLP. All Rights Reserved.
# License - GNU General Public License version 2. http://www.gnu.org/licenses/gpl-2.0.html
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Websites:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');

class plgSystemVponepagecheckout extends JPlugin
{

    /**
    * @var        object    The user registration data.
    * @since    1.6
    */
    protected $data;

		
		/**
		* Joomla 2.5 Standard Constructor
		* @param undefined $subject
		* @param undefined $params
		* 
		* @return
		*/
    function __construct( & $subject, $params )
    {
			parent::__construct($subject, $params);
    }

		
		/**
		* On after Dispatch Events
		* 
		* @return
		*/		
		public function onAfterDispatch()
		{
      $app = JFactory::getApplication();

      // If Admin do nothing
      if ($app->isAdmin()) 
			{
				return;
      }		
			
      if (!class_exists ('VmConfig')) {
          require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
      }
			
      VmConfig::loadConfig();
			
      $uri = JFactory::getURI();			
			$_option = JRequest::getString('option');
			$_view = JRequest::getString('view');
			$_format = JRequest::getString('format', '');
			$_task = JRequest::getString('task', '');
			$_tmpl = JRequest::getString('tmpl', '');			

      // SSL redirection
      if ($_option == 'com_virtuemart' && $_view == 'cart' && $_format != 'json') 
			{
        // Force SSL reidrect
        if ($uri->isSSL() == false && VmConfig::get('useSSL', 0)) 
				{
          $uri->setScheme('https');
          $app->redirect($uri->toString());
          return $app->close();
        }
      }
			
      elseif($_option == 'com_virtuemart' && $_view == 'pluginresponse' && $_task == 'pluginUserPaymentCancel') 
			{
        // Force SSL reidrect
        if ($uri->isSSL() == false && VmConfig::get('useSSL', 0)) 
				{
          $uri->setScheme('https');
          $app->redirect($uri->toString());
          return $app->close();
        }
      } 
		}

		
		/**
		* On After Route Events
		* 
		* @return
		*/
    public function onAfterRoute()
    {
			$app = JFactory::getApplication();

			// If Admin do nothing
			if ($app->isAdmin()) 
			{
				return;
			}

			$document = JFactory::getDocument();
			$template = $app->getTemplate(true);

			if (!class_exists ('VmConfig')) {
			    require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'helpers' . DS . 'config.php');
			}

			VmConfig::loadConfig();
			$uri = JFactory::getURI();
			
			$post = JRequest::get('post');
			
			$_option = JRequest::getString('option');
			$_view = JRequest::getString('view');
			$_format = JRequest::getString('format', '');
			$_task = JRequest::getString('task', '');
			$_tmpl = JRequest::getString('tmpl', '');
			

      // Use One Page Checkout Plugin for Cart View
      if ($_option == 'com_virtuemart' && $_view == 'cart' && $_format != 'json') 
			{
        // Force SSL reidrect
        if ($uri->isSSL() == false && VmConfig::get('useSSL', 0) && empty($post)) 
				{
          $uri->setScheme('https');
          $app->redirect($uri->toString());
          return $app->close();
        }
				
        // Load jQuery at top
        if ($this->params->get('load_jquery', 2) == 2 && strpos($template->template, 'vp_') === false) 
				{
					$document->addScript(JURI::root(true)."/plugins/system/vponepagecheckout/assets/js/jquery-1.7.2.min.js");
        }
				
        if ($this->params->get('load_jquery', 2) == 1) {
					$document->addScript(JURI::root(true)."/plugins/system/vponepagecheckout/assets/js/jquery-1.7.2.min.js");
        }
				
        // Get new Checkout Class
        require_once(dirname(__FILE__) . DS . 'cart' . DS . 'cartview.html.php');
      }
			
      elseif ($_option == 'com_virtuemart' && $_view == 'pluginresponse' && $_task == 'pluginUserPaymentCancel') 
			{
        // Force SSL reidrect
        if ($uri->isSSL() == false && VmConfig::get('useSSL', 0) && empty($post)) 
				{
					$uri->setScheme('https');
					$app->redirect($uri->toString());
					return $app->close();
        }
				
        // Load jQuery at top
        if ($this->params->get('load_jquery', 2) == 2 && strpos($template->template, 'vp_') === false) 
				{
            $document->addScript(JURI::root(true)."/plugins/system/vponepagecheckout/assets/js/jquery-1.7.2.min.js");
        }
				
        if ($this->params->get('load_jquery', 2) == 1) 
				{
            $document->addScript(JURI::root(true)."/plugins/system/vponepagecheckout/assets/js/jquery-1.7.2.min.js");
        }
				
        // Get new Checkout Class
        require_once(dirname(__FILE__) . DS . 'cart' . DS . 'cartview.html.php');
      } 
			
			elseif($_option == 'com_virtuemart' && $_view != 'pluginresponse' && $_view != 'cart' && $_format != 'json' && $_tmpl == '' && $this->params->get('disable_ssl', 1)) 
			{
				if($uri->isSSL() == true && empty($post)) {
        	$uri->setScheme('http');
       		$app->redirect($uri->toString());
        	return $app->close();			
				}			
			}

			if(($_option != 'com_virtuemart' && $_view != 'pluginresponse') && ($_option != 'com_virtuemart' && $_view != 'cart') && ($_option != 'com_virtuemart' && $_format != 'json') && $_tmpl == '') 
			{
				$app->setUserState('proopc.lastvisited.url', $uri->toString());
			}				


      // Fix User Activation Problem of VirtueMart
      if ($this->params->get('user_verification_fix', 1) and JRequest::getVar('task', '') == 'registration.activate' and JRequest::getVar('option', '') == 'com_users') 
			{
        $lang    = JFactory::getLanguage();
        $lang->load("com_users");
        $uParams = JComponentHelper::getParams('com_users');

        // If user registration or account activation is disabled, throw a 403.
        if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0) 
				{
          JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
          return false;
        }

        $token = JRequest::getVar('token', null, 'request', 'alnum');

        // Check that the token is in a valid format.
        if ($token === null || strlen($token) !== 32) 
				{
          JError::raiseError(403, JText::_('JINVALID_TOKEN'));
          return false;
        }

        // Attempt to activate the user.
        $return = $this->activate($token);

        // Check for errors.
        if ($return === false) 
				{
          // Redirect back to the homepage.
          $app->enqueueMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $this->getError()), 'warning');
          $app->redirect('index.php');
          return false;
        }

        $useractivation = $uParams->get('useractivation');

        // Redirect to the login screen.
        if ($useractivation == 0) 
				{
          $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'), 'message');
          $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
        }
        elseif ($useractivation == 1) 
				{
          $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'), 'message');
          $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
        }
        elseif ($return->getParam('activate')) 
				{
          $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS'), 'message');
          $app->redirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
        }
        else 
				{
          $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS'), 'message');
          $app->redirect(JRoute::_('index.php?option=com_users&view=registration&layout=complete', false));
        }
      }

    }


    /**
    * Method to activate a user account.
    *
    * @param    string        The activation token.
    * @return    mixed        False on failure, user object on success.
    * @since    1.6
    */
    protected function activate($token)
    {
        $app        = JFactory::getApplication();
        $config     = JFactory::getConfig();
        $lang       = JFactory::getLanguage();
        $lang->load("com_users");
        $userParams = JComponentHelper::getParams('com_users');
        $db         = JFactory::getDBO();

        // Get the user id based on the token.
        $db->setQuery(
            'SELECT '.$db->quoteName('id').' FROM '.$db->quoteName('#__users') .
            ' WHERE '.$db->quoteName('activation').' = '.$db->Quote($token) .
            ' AND '.$db->quoteName('block').' = 1'
        );
        $userId     = (int) $db->loadResult();

        // Check for a valid user id.
        if (!$userId) {
            $app->enqueueMessage(JText::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'), 'error');
            return false;
        }

        // Load the users plugin group.
        JPluginHelper::importPlugin('user');

        // Activate the user.
        $user       = JFactory::getUser($userId);

        $activeUser = JFactory::getUser();

        if ($activeUser->get('id') and $activeUser->get('id') !== $userId) {
            $app->enqueueMessage(JText::_('COM_USERS_ACTIVATION_TOKEN_NOT_FOUND'), 'error');
            return false;
        }

        // Admin activation is on and user is verifying their email
        if (($userParams->get('useractivation') == 2) && !$user->getParam('activate', 0)) {
            $uri = JURI::getInstance();

            // Compile the admin notification mail values.
            $data= $user->getProperties();
            $data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
            $user->set('activation', $data['activation']);
            $data['siteurl'] = JUri::base();
            $base = $uri->toString(array('scheme','user','pass','host','port'));
            $data['activate'] = $base.JRoute::_('index.php?option=com_users&task=registration.activate&token='.$data['activation'], false);
            $data['fromname'] = $config->get('fromname');
            $data['mailfrom'] = $config->get('mailfrom');
            $data['sitename'] = $config->get('sitename');
            $user->setParam('activate', 1);
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_SUBJECT',
                $data['name'],
                $data['sitename']
            );

            $emailBody    = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATE_WITH_ADMIN_ACTIVATION_BODY',
                $data['sitename'],
                $data['name'],
                $data['email'],
                $data['username'],
                $data['siteurl'].'index.php?option=com_users&task=registration.activate&token='.$data['activation']
            );

            // get all admin users
            $query        = $db->getQuery(true);
            $query->select(array('a.name','a.email','a.sendEmail','a.id'));
            $query->from('`#__users` AS a');
            $query->where('a.sendEmail=1');
            $db->setQuery($query);
            $rows = $db->loadObjectList();

            // Send mail to all users with users creating permissions and receiving system emails
            foreach ( $rows as $row ) {
                $usercreator = JFactory::getUser($id          = $row->id);
                if ($usercreator->authorise('core.create', 'com_users')) {
                    $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBody);

                    // Check for an error.
                    if ($return !== true) {
                        $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'), 'error');
                        return false;
                    }
                }
            }
        }

        //Admin activation is on and admin is activating the account
        elseif (($userParams->get('useractivation') == 2) && $user->getParam('activate', 0)) {
            $user->set('activation', '');
            $user->set('block', '0');

            $uri = JURI::getInstance();

            // Compile the user activated notification mail values.
            $data= $user->getProperties();
            $user->setParam('activate', 0);
            $data['fromname'] = $config->get('fromname');
            $data['mailfrom'] = $config->get('mailfrom');
            $data['sitename'] = $config->get('sitename');
            $data['siteurl'] = JUri::base();
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_SUBJECT',
                $data['name'],
                $data['sitename']
            );

            $emailBody    = JText::sprintf(
                'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
                $data['name'],
                $data['siteurl'],
                $data['username']
            );

            $return       = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

            // Check for an error.
            if ($return !== true) {
                $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'), 'error');
                return false;
            }
        }
        else {
            $user->set('activation', '');
            $user->set('block', '0');
        }

        // Store the user object.
        if (!$user->save()) {
            $app->enqueueMessage(JText::sprintf('COM_USERS_REGISTRATION_ACTIVATION_SAVE_FAILED', $user->getError()), 'error');
            return false;
        }

        return $user;
    }

}