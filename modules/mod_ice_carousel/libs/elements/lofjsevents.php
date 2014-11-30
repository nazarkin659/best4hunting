<?php 
/**
 * $ModDesc
 * 
 * @version		$Id: helper.php $Revision
 * @package		modules
 * @subpackage	mod_lofflashcontent
 * @copyright	Copyright (C) JAN 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @license		GNU General Public License version 2
 */
defined('_JEXEC') or die( 'Restricted access' );
/**
 * Get a collection of categories
 */
 if( file_exists( JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'community.php')){
		require_once ( JPATH_ROOT.DS.'components'.DS.'com_community'.DS.'defines.community.php');

		// Require the base controller
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'error.php');
		require_once (COMMUNITY_COM_PATH.DS.'controllers'.DS.'controller.php');
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'apps.php' );
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'core.php');
		require_once (COMMUNITY_COM_PATH.DS.'libraries'.DS.'template.php');
		require_once (COMMUNITY_COM_PATH.DS.'views'.DS.'views.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'url.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'ajax.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'time.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'owner.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'azrul.php');
		require_once (COMMUNITY_COM_PATH.DS.'helpers'.DS.'string.php');
		require_once (COMMUNITY_COM_PATH.DS.'events'.DS.'router.php');

		JTable::addIncludePath( COMMUNITY_COM_PATH . DS . 'tables' );
	}
class JFormFieldLofJsevents extends JFormField {
	
	/*
	 * Category name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'LofJsevents';
	
	/**
	 * fetch Element 
	 */
	function getInput(){
		if(class_exists("CFactory")){
			$model		= CFactory::getModel('events'); 
			$rows = $model->getCategories();
			$categories = array();
			$categories[0] = new stdClass();
			$categories[0]->id = '';
			$categories[0]->name = JText::_("---------- Select All ----------");
			$data = array_merge($categories,$rows);
			$class = isset( $this->element["class"] )?$this->element["class"]:"";
			return JHtml::_( 'select.genericlist',
							 $data, ''.$this->name.'[]',
							 'class="'.$class.' inputbox"   multiple="multiple" size="10"',
							 'id',
							 'name',
							 $this->value,$this->id );
		}
		else{
			return "";
		}
	}
}

?>
