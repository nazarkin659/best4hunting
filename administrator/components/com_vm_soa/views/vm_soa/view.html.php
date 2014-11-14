<?php

/**
 * @package    	com_vm_soa (WebServices for virtuemart 2)
 * @author		Mickael Cabanas (cabanas.mickael|at|gmail.com)
 * @link 		http://www.virtuemart-datamanager.com
 * @license    	GNU/GPL
*/


defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class vm_soaViewvm_soa extends JView{
	
  function config($tpl = null){
	
  }
  
  function display($tpl = null){
  	
	$this->loadVM();
	$version = $this->getVersion();
		
	$task = JRequest::getCmd( 'task' );
	$act = JRequest::getCmd( 'act' );

 	// Set the toolbar
	$this->addToolBar();
 
    $items = $this->get( 'Data');
    $this->assignRef('items', $items);
	
	$this->assignRef('version',$version);
	
    parent::display($tpl);
	
  }
  
	protected function addToolBar() 
	{
		JToolBarHelper::title( JText::_( 'COM_VM_SOA_ADMIN_VIEW_NAME' ),  'head vm_config_48' );
		JToolBarHelper::divider();
        JToolBarHelper::save();
        JToolBarHelper::apply();
        JToolBarHelper::cancel();
        
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
	
	function getVersion(){
			$version ="";
			$_VERSION = new JVersion();
			
			if ($_VERSION->RELEASE == "1.5"){
				$version ="2.0.0";
			}else{
				
				$db = JFactory::getDBO();
				
				$query  = "SELECT manifest_cache FROM `#__extensions` ext ";
				$query .= "WHERE element ='com_vm_soa'  ";
				
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				
				foreach ($rows as $row)	{

					$data = explode(',', $row->manifest_cache);
					$count = count($data);
					for ($i = 0; $i < $count; $i++) {
						//echo "data ".$data[$i];
						$data2 = explode(':',$data[$i]);
						//echo "data :".$data2[0].' -> '.$data2[1].'\n';
						if ($data2[0]=='"version"'){
						
							$version = $data2[1];
						}
					}
				
				}
				$version = str_replace('"', '', $version);
			
			}
			
			
			return $version;

	}
}
?>
		
