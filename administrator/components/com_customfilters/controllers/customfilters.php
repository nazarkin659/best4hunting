<?php
/**
 *
 * The Customfilters controller file
 *
 * @package 	customfilters
 * @author		Sakis Terz
 * @link		http://breakdesigns.net
 * @copyright	Copyright (c) 2010 - 2011 breakdesigns.net. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *				customfilters is free software. This version may have been modified
 *				pursuant to the GNU General Public License, and as distributed
 *				it includes or is derivative of works licensed under the GNU
 *				General Public License or other free or open source software
 *				licenses.
 * @version $Id: controller.php 1 2011-10-21 18:36:00Z sakis $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controlleradmin');

/**
 * main controller class
 * @package		customfilters
 * @author		Sakis Terz
 * @since		1.0
 */
class CustomfiltersControllerCustomfilters extends JControllerAdmin{



	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.0
	 */
	public function getModel($name = 'Customfilter', $prefix = 'CustomfiltersModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * savefilters task
	 *
	 *
	 * @return	void
	 * @since	1.0
	 * @author	Sakis Terz
	 */
	public function savefilters()
	{
		$model=$this->getModel();
		$type_ids=JRequest::getVar('type_id',array(),'post','array');
		$alias=JRequest::getVar('alias',array(),'post','array');
		//sanitize the input to be int
		JArrayHelper::toInteger($type_ids);
		JArrayHelper::toString($alias);

		if($type_ids){
			if(!$model->savefilters($type_ids,$alias)){
				JError::raiseWarning(500, $model->getError());
			}else $this->setMessage(JText::_('COM_CUSTOMFILTERS_FILTERS_SAVED_SUCCESS'));
		}
		$this->setRedirect('index.php?option=com_customfilters&view=customfilters');
	}
	
}