<?php
/**
 * @package    	com_vm_soa (WebServices for virtuemart)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/

defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
class vm_soaViewconfig extends JView{
	
  function display($tpl = null){
	
  	$this->loadVM();
  	
	$task = JRequest::getCmd( 'task' );
	$act = JRequest::getCmd( 'act' );
 	 
 	$model = $this->getModel('config');
 	if ($task == "renewConfig"){
		$model->renewConfig();
	}else{
		$model->saveConf();
	}
	
    JToolBarHelper::title( JText::_( 'COM_VM_SOA_ADMIN_VIEW_NAME' ), 'head vm_config_48' );

    $items = $this->get( 'Data');
    $this->assignRef('items', $items);
    parent::display($tpl);

  }
  
  function loadVM(){
  	
  	if(!class_exists('AdminUIHelper'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'adminui.php');
  	if(!class_exists('ShopFunctions'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctions.php');
  	if(!class_exists('VmHTML'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'html.php');
  	if(!class_exists('VmConfig'))require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
  	  	
  	$config = VmConfig::loadConfig();
  	$this->assignRef('config', $config);
  	
  	$this->loadHelper('adminui');
	$this->loadHelper('image');
	$this->loadHelper('html');
	$this->loadHelper('shopFunctions');
	
	$front = JURI::root(true).'/components/com_virtuemart/assets/';
	$admin = JURI::base().'components/com_virtuemart/assets/';	
	$document = JFactory::getDocument();

	//loading defaut admin CSS
	$document->addStyleSheet($admin.'css/admin_ui.css');
	$document->addStyleSheet($admin.'css/admin_menu.css');
	$document->addStyleSheet($admin.'css/admin.styles.css');
	$document->addStyleSheet($admin.'css/toolbar_images.css');
	$document->addStyleSheet($admin.'css/menu_images.css');
	$document->addStyleSheet($front.'css/chosen.css');
	$document->addStyleSheet($front.'css/vtip.css');
	$document->addStyleSheet($front.'js/fancybox/jquery.fancybox-1.3.4.css');
	//$document->addStyleSheet($admin.'css/jqtransform.css');

	//loading defaut script

	$document->addScript($front.'js/fancybox/jquery.mousewheel-3.0.4.pack.js');
	$document->addScript($front.'js/fancybox/jquery.easing-1.3.pack.js');
	$document->addScript($front.'js/fancybox/jquery.fancybox-1.3.4.pack.js');
	$document->addScript($admin.'js/jquery.cookie.js');
	$document->addScript($front.'js/chosen.jquery.min.js');
	$document->addScript($admin.'js/vm2admin.js');
	
	$document->addScriptDeclaration ( '
		var tip_image="'.JURI::root(true).'/components/com_virtuemart/assets/js/images/vtip_arrow.png";
		');
  	
  }
}
?>