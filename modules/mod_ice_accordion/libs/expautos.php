<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
//


/**
 * $ModDesc
 * 
 * @version   $Id: helper.php $Revision
 * @package   modules
 * @subpackage  $Subpackage
 * @copyright Copyright (C) May 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @website   htt://landofcoder.com
 * @license   GNU General Public License version 2
 */
if( !class_exists('IceGroupAccordionExpautos') ) {
	if(file_exists(JPATH_BASE .'/components/com_expautospro/helpers/helper.php'))
		require_once JPATH_BASE .'/components/com_expautospro/helpers/helper.php';
	if(file_exists(JPATH_BASE .'/components/com_expautospro/helpers/expparams.php'))
		require_once JPATH_BASE .'/components/com_expautospro/helpers/expparams.php';
	$params_file = JPATH_BASE .'/components/com_expautospro/skins/explist/default/parameters/params.php';
	if(file_exists($params_file))
	require_once $params_file;
	if( is_file( JPATH_SITE.DS.  "components" . DS . "com_expautospro" . DS . "expautospro.php" ) ){
		// Load the language file of com_expautospro.
		JFactory::getLanguage()->load('com_expautospro');
		ExpAutosProHelper::expskin_lang('explist','default');
		$expskin = '';
		$expparams = ExpAutosProExpparams::getExpParams('config', 1);
		if($expparams->get('c_admanager_lspage_showskin')){
			$expgetpagecookie = JRequest::getVar('explist', null,  $hash= 'COOKIE');
			$expskin_post = (string) JRequest::getString('expskin', 0);
			if($expskin_post){
				$expskin = $expskin_post; 
			}else{
				$expskin = $expgetpagecookie;
			}
		}
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . 'components/com_expautospro/assets/css/expautospro.css');
		if($expskin && file_exists(JPATH_BASE . '/components/com_expautospro/skins/explist/'.$expskin.'/css/default.css')){
			$document->addStyleSheet(JURI::root() . 'components/com_expautospro/skins/explist/'.$expskin.'/css/default.css');
		}else{
			if (file_exists(JPATH_BASE . '/components/com_expautospro/skins/explist/'.$expparams->get('c_admanager_lspage_skin').'/css/default.css')) {
				$document->addStyleSheet(JURI::root() . 'components/com_expautospro/skins/explist/'.$expparams->get('c_admanager_lspage_skin').'/css/default.css');
			} else {
				$document->addStyleSheet(JURI::root() . 'components/com_expautospro/skins/explist/default/css/default.css');
			}
		}
	}
	/**
   * IceGroupAccordionExpautos Class  extends the LofSliderGroupBase Class
   */   
   
   class IceGroupAccordionExpautos extends IceAccordionGroupBase{
    
    /**
     * @var string $__name 
     *
     * @access private;
     */
    var $__name = 'expautos';
    
    /**
     * @var static $regex is a pattern using for maching imag tag.
     * 
     * @access public.
     */
    static $regex = "#<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>#iU"; 
    
    /**
     * override get List of Item by the module's parameters
     */
    public function getListByParameters( $params ){
      if( !IceGroupAccordionExpautos::isExpExisted() ){
        return array();
      }
      return $this->__getList( $params );
    }
    
    
    /**
     * check expautospro Existed ?
     */
    public function isExpExisted(){
      return is_file( JPATH_SITE.DS.  "components" . DS . "com_expautospro" . DS . "expautospro.php" ); 
    }
    
	function getOrdering( $string = ""){
		$ordering = "catid";
		$dir = "ASC";
		if( !empty($string )){
				$tmp = explode("__", $string);	
				$ordering = $string;
				if( count($tmp) == 2){
					$ordering = $tmp[0];
					$dir = $tmp[1];
				}
		}
		
		$order["ordering"] = $ordering;
		$order["dir"] = $dir;	
		return $order;
	}
    /**
     * get the list of virtuemart items
     * 
     * @param JParameter $params;
     * @return Array
     */
    public function __getList( $params ){
      $mainframe = &JFactory::getApplication();
	  
      $titleMaxChars      = $params->get( 'title_max_chars', '100' );
      $openTarget    = $params->get( 'open_target', 'parent' );
      $descriptionMaxChars = $params->get( 'description_max_chars', 100 );
   
      $ordering      = $params->get( 'exp_sort_item', 'catid__asc');
      $limit         = $params->get( 'limit_items',  5 );
      $orders      = $this->getOrdering( $ordering );
	  $dir = $orders["dir"];
	  $ordering = $orders["ordering"];
      $my          = &JFactory::getUser();
      $aid         = $my->get( 'aid', 0 );
      $thumbWidth    = (int)$params->get( 'exp_preview_width', 200 );
      $thumbHeight   = (int)$params->get( 'exp_preview_height', 210 );
      $imageHeight   = (int)$params->get( 'main_height', 300 ) ;
      $imageWidth    = (int)$params->get( 'main_width', 660 ) ;
	  $show_preview = (int)$params->get( 'exp_show_preview', 1);
      $isThumb       = $params->get( 'auto_renderthumb',1);
	  $image_quanlity = $params->get('image_quanlity', 100);
      $isStripedTags = $params->get( 'auto_strip_tags', 0 );
	  $show_fuel = $params->get('exp_show_fuel',1);
      $extraURL     = $params->get('open_target')!='modalbox'?'':'&tmpl=component'; 
      $db     = &JFactory::getDBO();
      $date   =& JFactory::getDate();
      $now    = $date->toMySQL();
		
		$query_join = "";
		$query_filed = "";
		/*Use this code for check ordering*/
		switch( $ordering ){
			case 'catid':
				$ordering = 'p.`catid` '.$dir;
			break;
			case 'makeid':
				$ordering = "p.`make` ".$dir;
			break;
			case 'userid':
				$ordering = "p.`user` ".$dir;
			break;
			case 'price':
				$ordering = "p.`price` ".$dir;
			break;
			case 'random':
				$ordering = " RAND()";
			break;
		}
		if(!empty($ordering)){
			$ordering = " ORDER BY ".$ordering;
		}
		$joinedTables = " LEFT JOIN #__expautos_make as mk ON p.make = mk.id ";
		$joinedTables .=" LEFT JOIN #__expautos_model AS md ON p.model = md.id ";
		$joinedTables .=" LEFT JOIN #__expautos_images AS im ON p.id = im.catid ";
		if( $show_fuel ){
			$query_filed =", f.name AS fuel_name";
			$joinedTables .=" LEFT JOIN #__expautos_fuel AS f ON p.fuel = f.id ";
		}
		$where = array();
		$where[] = ' p.`state`="1" ';
	
		$where[] = $this->buildConditionQuery($params);
		$select = ' p.*,mk.name AS make_name,md.name AS model_name,im.name AS img_name '.$query_filed.' FROM `#__expautos_admanager` as p';
		
		$groupby = " GROUP BY p.id ";
		if(count($where)>0){
			$whereString = ' WHERE ('.implode(' AND ', $where ).') ';
		} else {
			$whereString = '';
		}
		/*End*/

		$q = 'SELECT '.$select.$joinedTables.$whereString .$groupby.$ordering;
		$db->setQuery($q,0,$limit);
	   $products= $db->loadObjectList();
	 
      if( empty($products) ) return array();
	  $check = array();
	  $data = array();
	 $expitemid = ExpAutosProExpparams::getExpLinkItemid();
      foreach( $products as $key => $item ){
		if(in_array($item->id, $check)){
			continue;
		}
		$item->fuel_name = isset($item->fuel_name)?$item->fuel_name:"";
		$item->link                   = JRoute::_( 'index.php?option=com_expautospro&amp;view=expdetail&amp;id=' . (int) $item->id . '&amp;catid=' . (int) $item->catid . '&amp;makeid=' . (int) $item->make . '&amp;modelid=' . (int) $item->model.'&amp;Itemid='.(int) $expitemid);
        $item->date = JHtml::_('date', $item->creatdate, JText::_('DATE_FORMAT_LC2'));
		$item->title = $item->make_name." &nbsp;".$item->model_name;
		if ($item->specificmodel){
			$item->title .="&nbsp;".$item->specificmodel;
		}
		if ($item->displacement > 0){
			$item->title .= " &nbsp;".$item->displacement." ".JText::_( 'COM_EXPAUTOSPRO_LITER_S_TEXT' );
		}
		if ($item->engine){
			$item->title .= "&nbsp;".$item->engine." ".JText::_( 'COM_EXPAUTOSPRO_KW_TEXT' );
		}
        $item->subtitle = self::substring( $item->title, $titleMaxChars );
        $item->description = self::substring( $item->otherinfo, $descriptionMaxChars,true);
		$item->price = ExpAutosProExpparams::price_formatdata($item->price);
		
        $item->mainImage = ExpAutosProExpparams::ImgUrlPatchBig() . $item->img_name;
		$item->thumbnail =  $item->mainImage;
		$item->introtext = $item->description;
		$item->product_full_image = $item->mainImage;
		
        if( $item->mainImage &&  $image=$this->renderThumb($item->mainImage, $imageWidth, $imageHeight, $item->title, $isThumb, $image_quanlity ) ){
          $item->mainImage = $image;
        }
		if( $show_preview ){
			
			if( $item->thumbnail &&  $image = $this->renderThumb($item->thumbnail, $thumbWidth, $thumbHeight, $item->title, $isThumb, $image_quanlity, true ) ){
			  $item->thumbnail = $image;
			}
			
		}
		$check[] = $item->id;
		$data[] = $item;
      }
      return $data; 
    }
    
    /**
     * build condition query base parameter  
     * 
     * @param JParameter $params;
     * @return string.
     */
    public  function buildConditionQuery( $params ){
      $source = trim($params->get( 'exp_source_from', 'expcategory' ) );
	  $filter_type = $params->get( 'exp_filtering_type','1');
	  $exp_show_sold = $params->get("exp_show_sold",1);
	  $exp_display = $params->get( 'exp_display','');
	  $exp_display = is_array($exp_display)?$exp_display:explode(",",$exp_display);
	  $condition = "";
	  
      if( $source == 'expcategory' ){
        $catids = $params->get( 'expcategory','');
        $catids = !is_array($catids) ? $catids : implode(",",$catids);
		if( empty( $catids ) ){
          return '';
        }
		if($filter_type){
			$condition = '  p.catid IN( '.$catids.' )';
		}
		else{
			$condition = '  p.catid NOT IN( '.$catids.' )';
		}
      }
	  elseif( $source == "expuser"){
		$users = $params->get( 'expuser','');
		$ids = !is_array($users)?$users:implode(',', $users);  
		if($filter_type){
			$condition = '  p.user IN( '.$ids.' )';
		}
		else{
			$condition = '  p.user NOT IN( '.$ids.' )';
		}
	  }
	  if(!$exp_show_sold){
		$condition .= ' AND p.solid = 0 ';
	  }
	  if(!empty($exp_display)){
		$ctmp = array();
		foreach($exp_display as $val){
			if($val == "top"){
				$ctmp[] = ' p.ftop = 1 ';
			}elseif($val == "commercial"){
				$ctmp[] = ' p.fcommercial = 1 ';
			}elseif( $val == "special"){
				$ctmp[] = ' p.special = 1 ';
			}
		}
		if(empty($condition)) $condition = 1;
		$condition = !empty($ctmp)?$condition." AND (".implode(" OR ",$ctmp).")":$condition;
	  }
      return $condition;
    }
  }
}
?>
